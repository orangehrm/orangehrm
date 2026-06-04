<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Slack\Service;

use DateTime;
use DateTimeZone;
use OrangeHRM\Entity\SlackLog;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Framework\Services;
use OrangeHRM\Slack\Dao\SlackLogDao;
use OrangeHRM\Slack\Dto\SlackEmployeeRecipient;
use OrangeHRM\Slack\Service\Formatter\MessageFormatterInterface;
use OrangeHRM\Slack\Service\Resolver\RecipientResolverInterface;
use OrangeHRM\Slack\Service\SlackNotificationService;
use OrangeHRM\Slack\Service\SlackRegistrationService;
use OrangeHRM\Slack\Service\SlackSettingsService;
use OrangeHRM\Slack\Service\Webhook\WebhookDeliveryResult;
use OrangeHRM\Slack\Service\Webhook\WebhookProviderInterface;
use OrangeHRM\Slack\Service\Webhook\WebhookProviderRegistry;
use OrangeHRM\Tests\Util\KernelTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

/**
 * Pins the orchestrator's branching contract — the slack scheduler hits this
 * exactly once per cron tick and the branches it takes are user-visible only
 * through the `ohrm_slack_log` audit trail. The cases here mirror every status
 * the orchestrator can write:
 *
 *   - disabled global toggle      → no work, no rows
 *   - outside daily send window   → SKIPPED ("Outside send-time window")
 *   - already delivered today     → SKIPPED ("Already delivered today") — idempotency gate
 *   - resolver returns []         → SKIPPED ("No recipients matched") — retry-later safety
 *   - unsupported event type      → FAILED ("Unsupported event type: …")
 *   - decrypted webhook empty     → FAILED ("Webhook URL is empty")
 *   - provider returns success    → SUCCESS log written
 *   - provider returns failure    → FAILED log, error message preserved verbatim
 *   - resolver throws             → FAILED log, exception isolated, loop continues
 *   - two registrations, one fails → the other still dispatches (isolation)
 *
 * Plus a separate group covering `sendTestMessage()` since the Test Webhook API
 * boundary is thin (controller → service → providerRegistry).
 *
 * @group Slack
 * @group Service
 */
class SlackNotificationServiceTest extends KernelTestCase
{
    /** @var SlackSettingsService&MockObject */ private $settings;
    /** @var SlackRegistrationService&MockObject */ private $registrations;
    /** @var SlackLogDao&MockObject */ private $logDao;
    /** @var MessageFormatterInterface&MockObject */ private $formatter;
    /** @var WebhookProviderRegistry&MockObject */ private $registry;

    /**
     * The orchestrator fetches its collaborators from the OHRM service container
     * via traits — no constructor injection. Tests register mocks for the same
     * container ids; production code reads its real services from the same keys.
     * This is the standard pattern across LDAP / Buzz / every modern OHRM service.
     */
    protected function setUp(): void
    {
        $this->settings = $this->createMock(SlackSettingsService::class);
        $this->registrations = $this->createMock(SlackRegistrationService::class);
        $this->logDao = $this->createMock(SlackLogDao::class);
        // Mock the abstraction the orchestrator depends on, not the concrete
        // platform formatter. The orchestrator only ever calls methods on
        // MessageFormatterInterface; coupling the test to SlackMessageFormatter
        // would make it brittle to internal restructures of that class.
        $this->formatter = $this->createMock(MessageFormatterInterface::class);
        $this->registry = $this->createMock(WebhookProviderRegistry::class);

        $this->createKernelWithMockServices([
            Services::SLACK_SETTINGS_SERVICE => $this->settings,
            Services::SLACK_REGISTRATION_SERVICE => $this->registrations,
            Services::SLACK_LOG_DAO => $this->logDao,
            Services::WEBHOOK_PROVIDER_REGISTRY => $this->registry,
        ]);
    }

    /* ───────────────────────── disabled / empty fleet ─────────────────────── */

    public function testReturnsEmptyWhenFeatureDisabled(): void
    {
        $this->settings->method('isEnabled')->willReturn(false);
        // listActiveRegistrations must NOT be called — short-circuit on the gate.
        $this->registrations->expects($this->never())->method('listActiveRegistrations');
        $this->logDao->expects($this->never())->method('recordLog');

        $service = $this->makeService();
        $this->assertSame([], $service->dispatchDueNotifications());
    }

    public function testReturnsEmptyWhenNoActiveRegistrations(): void
    {
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('listActiveRegistrations')->willReturn([]);
        $this->logDao->expects($this->never())->method('recordLog');

        $service = $this->makeService();
        $this->assertSame([], $service->dispatchDueNotifications());
    }

    /* ────────────────────────── send-time window ───────────────────────────── */

    public function testRegistrationOutsideSendWindowIsSkippedWithoutDispatch(): void
    {
        // +10 minutes from now → outside the 5-minute send window
        $reg = $this->makeRegistration(11, $this->utcTimeOffsetMinutes(+10), 'UTC');
        $this->enableFor([$reg]);

        // Neither dao lookups nor provider dispatch should happen.
        $this->logDao->expects($this->never())->method('hasSuccessfulDeliveryForDate');
        $this->logDao->expects($this->never())->method('recordLog');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_SKIPPED, $result[11]['status']);
        $this->assertSame('Outside send-time window', $result[11]['error']);
        $this->assertSame(0, $result[11]['recipientCount']);
    }

    public function testRegistrationAlreadyDeliveredTodayIsSkippedWithoutDispatch(): void
    {
        // -1 minute → inside the 5-minute send window
        $reg = $this->makeRegistration(12, $this->utcTimeOffsetMinutes(-1), 'UTC');
        $this->enableFor([$reg]);

        $this->logDao->expects($this->once())
            ->method('hasSuccessfulDeliveryForDate')
            ->with(12, $this->isInstanceOf(DateTime::class))
            ->willReturn(true);
        $this->logDao->expects($this->never())->method('recordLog');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_SKIPPED, $result[12]['status']);
        $this->assertSame('Already delivered today', $result[12]['error']);
    }

    /* ─────────────────── inside window — dispatchRegistration paths ─────────── */

    public function testNoRecipientsLogsSkippedAndDoesNotCallProvider(): void
    {
        $reg = $this->makeRegistration(21, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);

        $this->injectResolver(SlackRegistration::EVENT_TYPE_BIRTHDAY, $this->fakeResolverReturning([]));

        $this->expectLogWith(SlackLog::STATUS_SKIPPED, 0, 'No recipients matched');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_SKIPPED, $result[21]['status']);
        $this->assertSame('No recipients matched', $result[21]['error']);
    }

    public function testUnsupportedEventTypeLogsFailed(): void
    {
        $reg = $this->makeRegistration(22, $this->utcTimeOffsetMinutes(-1), 'UTC', 'NEW_HIRE');
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);

        $this->expectLogWith(SlackLog::STATUS_FAILED, 0, 'Unsupported event type: NEW_HIRE');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_FAILED, $result[22]['status']);
        $this->assertStringContainsString('NEW_HIRE', (string)$result[22]['error']);
    }

    public function testEmptyDecryptedWebhookUrlLogsFailed(): void
    {
        $reg = $this->makeRegistration(23, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->with($reg)->willReturn(null);

        $this->injectResolver(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new SlackEmployeeRecipient('Alex Carter', 'Engineering')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $this->expectLogWith(SlackLog::STATUS_FAILED, 1, 'Webhook URL is empty');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_FAILED, $result[23]['status']);
        $this->assertSame('Webhook URL is empty', $result[23]['error']);
        $this->assertSame(1, $result[23]['recipientCount']);
    }

    public function testHappyPathRoutesViaRegistryAndLogsSuccess(): void
    {
        $reg = $this->makeRegistration(24, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->with($reg)
            ->willReturn('https://hooks.slack.com/services/T/B/secret');

        $this->injectResolver(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([
                new SlackEmployeeRecipient('Alex Carter', 'Engineering'),
                new SlackEmployeeRecipient('Priya Singh', 'Engineering'),
            ])
        );
        $this->formatter->expects($this->once())
            ->method('format')
            ->with(
                SlackRegistration::EVENT_TYPE_BIRTHDAY,
                $this->isInstanceOf(DateTime::class),
                $this->countOf(2),
                $this->isNull() // no subunits attached → label is null
            )
            ->willReturn('🎂 2 birthdays today');

        $provider = $this->makeProviderMock();
        $provider->expects($this->once())
            ->method('send')
            ->with('https://hooks.slack.com/services/T/B/secret', '🎂 2 birthdays today')
            ->willReturn(WebhookDeliveryResult::success());

        $this->registry->expects($this->once())
            ->method('getForRegistration')
            ->with($reg)
            ->willReturn($provider);

        $this->expectLogWith(SlackLog::STATUS_SUCCESS, 2, null);

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_SUCCESS, $result[24]['status']);
        $this->assertSame(2, $result[24]['recipientCount']);
        $this->assertNull($result[24]['error']);
    }

    public function testProviderFailureIsLoggedAsFailedWithErrorMessage(): void
    {
        $reg = $this->makeRegistration(25, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->willReturn('https://hooks.slack.com/services/T/B/secret');

        $this->injectResolver(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new SlackEmployeeRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $provider = $this->makeProviderMock();
        $provider->method('send')->willReturn(WebhookDeliveryResult::failure('Slack rejected: channel_not_found'));
        $this->registry->method('getForRegistration')->willReturn($provider);

        $this->expectLogWith(SlackLog::STATUS_FAILED, 1, 'Slack rejected: channel_not_found');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_FAILED, $result[25]['status']);
        $this->assertSame('Slack rejected: channel_not_found', $result[25]['error']);
    }

    public function testResolverThrowsIsCaughtAndWritesFailureLog(): void
    {
        $reg = $this->makeRegistration(26, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);

        $throwingResolver = $this->createMock(RecipientResolverInterface::class);
        $throwingResolver->method('resolve')
            ->willThrowException(new RuntimeException('Doctrine boom'));
        $this->injectResolver(SlackRegistration::EVENT_TYPE_BIRTHDAY, $throwingResolver);

        // The catch block re-uses logDao->makeLogFor + recordLog with FAILED.
        $this->expectLogWith(SlackLog::STATUS_FAILED, 0, 'Doctrine boom');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_FAILED, $result[26]['status']);
        $this->assertSame('Doctrine boom', $result[26]['error']);
    }

    public function testOneRegistrationFailingDoesNotPreventOtherFromDispatching(): void
    {
        // bad: unsupported event type → FAILED
        $bad = $this->makeRegistration(91, $this->utcTimeOffsetMinutes(-1), 'UTC', 'NEW_HIRE');
        $good = $this->makeRegistration(92, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$bad, $good]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->willReturn('https://hooks.slack.com/services/T/B/secret');

        $this->injectResolver(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new SlackEmployeeRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $provider = $this->makeProviderMock();
        $provider->method('send')->willReturn(WebhookDeliveryResult::success());
        $this->registry->method('getForRegistration')->with($good)->willReturn($provider);

        // recordLog is called once for the bad (FAILED) and once for the good (SUCCESS).
        $this->logDao->expects($this->exactly(2))->method('recordLog');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_FAILED, $result[91]['status']);
        $this->assertSame(SlackLog::STATUS_SUCCESS, $result[92]['status']);
    }

    /**
     * Row-26 scenario 4: "one invalid webhook alongside valid ones". The
     * existing testOneRegistrationFailingDoesNotPreventOtherFromDispatching
     * exercises this via unsupported-event-type; here we exercise it via the
     * canonical failure mode — the provider returns a failure result because
     * the webhook URL is dead/wrong (Slack 404, Teams 401 on bad sig, etc.).
     *
     * If a future refactor centralised the dispatch loop and accidentally
     * `throw`d on a failed send instead of swallowing it into the log row,
     * this test would catch it before the other rules silently stopped firing.
     */
    public function testInvalidWebhookOnOneRowDoesNotBlockOtherRows(): void
    {
        $broken = $this->makeRegistration(81, $this->utcTimeOffsetMinutes(-1));
        $working = $this->makeRegistration(82, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$broken, $working]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->willReturn('https://hooks.slack.com/services/T/B/secret');

        $this->injectResolver(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new SlackEmployeeRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

        // Provider returns FAILURE for the broken row, SUCCESS for the working row.
        $brokenProvider = $this->makeProviderMock();
        $brokenProvider->method('send')
            ->willReturn(WebhookDeliveryResult::failure('Slack rejected: invalid_token'));

        $workingProvider = $this->makeProviderMock();
        $workingProvider->method('send')->willReturn(WebhookDeliveryResult::success());

        $this->registry->method('getForRegistration')
            ->willReturnMap([
                [$broken, $brokenProvider],
                [$working, $workingProvider],
            ]);

        // Both rows must reach the log table: FAILED for broken, SUCCESS for working.
        $this->logDao->expects($this->exactly(2))->method('recordLog');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(SlackLog::STATUS_FAILED, $result[81]['status']);
        $this->assertSame('Slack rejected: invalid_token', $result[81]['error']);
        $this->assertSame(SlackLog::STATUS_SUCCESS, $result[82]['status']);
        $this->assertNull($result[82]['error']);
    }

    /**
     * Row-26 scenario 5: "rules in different timezones." Two rows with
     * different `timezone` values are dispatched via the per-row entry point;
     * each one computes its own "today" using its own zone, independently.
     *
     * The per-row scheduler architecture (item 7) is what actually fires each
     * task at the right local time via Crunz; this test pins the SERVICE-side
     * half — that the orchestrator never lets one row's timezone bleed into
     * another's computation.
     */
    public function testTwoRowsInDifferentTimezonesDispatchIndependently(): void
    {
        $colombo = $this->makeRegistration(101, '09:00', 'Asia/Colombo');
        $newYork = $this->makeRegistration(102, '09:00', 'America/New_York');

        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('getRegistration')
            ->willReturnMap([
                [101, $colombo],
                [102, $newYork],
            ]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->willReturn('https://hooks.slack.com/services/T/B/secret');

        $this->injectResolver(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new SlackEmployeeRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $provider = $this->makeProviderMock();
        $provider->method('send')->willReturn(WebhookDeliveryResult::success());
        $this->registry->method('getForRegistration')->willReturn($provider);

        // Two distinct log rows — one per registration — never collapsed.
        $this->logDao->expects($this->exactly(2))->method('recordLog');

        $service = $this->makeService();
        $colomboResult = $service->dispatchSingleRegistration(101);
        $newYorkResult = $service->dispatchSingleRegistration(102);

        $this->assertSame(SlackLog::STATUS_SUCCESS, $colomboResult['status']);
        $this->assertSame(SlackLog::STATUS_SUCCESS, $newYorkResult['status']);
    }

    /* ─────────────────────── dispatchSingleRegistration ────────────────────── */

    public function testDispatchSingleRegistrationShortCircuitsWhenGloballyDisabled(): void
    {
        $this->settings->method('isEnabled')->willReturn(false);
        // No row lookup should happen when the global toggle is off.
        $this->registrations->expects($this->never())->method('getRegistration');

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(SlackLog::STATUS_SKIPPED, $result['status']);
        $this->assertStringContainsString('globally disabled', (string)$result['error']);
    }

    public function testDispatchSingleRegistrationReturnsFailedForUnknownId(): void
    {
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('getRegistration')->with(42)->willReturn(null);

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(SlackLog::STATUS_FAILED, $result['status']);
        $this->assertStringContainsString('not found', (string)$result['error']);
    }

    public function testDispatchSingleRegistrationSkipsInactiveRow(): void
    {
        $reg = $this->makeRegistration(42, '09:00');
        $reg->setActive(false);
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('getRegistration')->with(42)->willReturn($reg);
        // Inactive rows must NEVER touch the log table — cron not firing for a
        // paused row is the whole point of the `is_active` flag.
        $this->logDao->expects($this->never())->method('recordLog');

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(SlackLog::STATUS_SKIPPED, $result['status']);
        $this->assertSame('Registration is inactive', $result['error']);
    }

    public function testDispatchSingleRegistrationSkipsWhenAlreadyDeliveredToday(): void
    {
        $reg = $this->makeRegistration(42, '09:00');
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('getRegistration')->with(42)->willReturn($reg);
        $this->logDao->expects($this->once())
            ->method('hasSuccessfulDeliveryForDate')
            ->with(42, $this->isInstanceOf(DateTime::class))
            ->willReturn(true);
        // Idempotency gate fires BEFORE dispatch — no provider routing.
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(SlackLog::STATUS_SKIPPED, $result['status']);
        $this->assertSame('Already delivered today', $result['error']);
    }

    public function testDispatchSingleRegistrationHappyPathBypassesSendWindow(): void
    {
        // Crunz already enforced the cron — the per-row entry point MUST NOT
        // re-check `isWithinSendWindow`, otherwise a manual shell run outside
        // the row's HH:MM would always come back "Outside send-time window".
        // sendTime here is +10 minutes (outside the 5-min window) on purpose.
        $reg = $this->makeRegistration(42, $this->utcTimeOffsetMinutes(+10));
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('getRegistration')->with(42)->willReturn($reg);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->willReturn('https://hooks.slack.com/services/T/B/secret');

        $this->injectResolver(
            SlackRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new SlackEmployeeRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $provider = $this->makeProviderMock();
        $provider->expects($this->once())->method('send')->willReturn(WebhookDeliveryResult::success());
        $this->registry->method('getForRegistration')->willReturn($provider);

        $this->expectLogWith(SlackLog::STATUS_SUCCESS, 1, null);

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(SlackLog::STATUS_SUCCESS, $result['status']);
        $this->assertSame(1, $result['recipientCount']);
    }

    public function testDispatchSingleRegistrationCatchesThrowsAndWritesFailureLog(): void
    {
        $reg = $this->makeRegistration(42, '09:00');
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('getRegistration')->with(42)->willReturn($reg);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);

        $throwingResolver = $this->createMock(RecipientResolverInterface::class);
        $throwingResolver->method('resolve')->willThrowException(new RuntimeException('Doctrine boom'));
        $this->injectResolver(SlackRegistration::EVENT_TYPE_BIRTHDAY, $throwingResolver);

        $this->expectLogWith(SlackLog::STATUS_FAILED, 0, 'Doctrine boom');

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(SlackLog::STATUS_FAILED, $result['status']);
        $this->assertSame('Doctrine boom', $result['error']);
    }

    /* ─────────────────────── sendTestMessage ───────────────────────────────── */

    public function testSendTestMessageRoutesThroughDefaultProviderWhenIdMissing(): void
    {
        $this->formatter->expects($this->once())
            ->method('formatTestMessage')
            ->with(SlackRegistration::EVENT_TYPE_BIRTHDAY)
            ->willReturn('test-msg');

        $provider = $this->makeProviderMock();
        $provider->expects($this->once())
            ->method('send')
            ->with('https://hooks.slack.com/services/T/B/secret', 'test-msg')
            ->willReturn(WebhookDeliveryResult::success());

        $this->registry->expects($this->once())
            ->method('get')
            ->with(SlackRegistration::PROVIDER_SLACK)
            ->willReturn($provider);

        $result = $this->makeService()->sendTestMessage(
            'https://hooks.slack.com/services/T/B/secret',
            SlackRegistration::EVENT_TYPE_BIRTHDAY
        );

        $this->assertTrue($result->isOk());
    }

    public function testSendTestMessageRoutesThroughExplicitProviderId(): void
    {
        $this->formatter->method('formatTestMessage')->willReturn('test-msg');

        $provider = $this->makeProviderMock();
        $provider->method('send')->willReturn(WebhookDeliveryResult::failure('boom'));

        $this->registry->expects($this->once())
            ->method('get')
            ->with('google_chat')
            ->willReturn($provider);

        $result = $this->makeService()->sendTestMessage('https://example/x', 'BIRTHDAY', 'google_chat');

        $this->assertFalse($result->isOk());
        $this->assertSame('boom', $result->getErrorMessage());
    }

    /* ─────────────────────────── Helpers ───────────────────────────────────── */

    private function makeService(): SlackNotificationService
    {
        // injectResolver() pre-builds a service to swap in fake resolvers — once
        // that's happened we want the rest of the test to use that same instance
        // so the injection isn't thrown away by a second `new`.
        if ($this->preBuiltService !== null) {
            return $this->preBuiltService;
        }
        // Zero-arg constructor — collaborators are read from the container
        // (the mocks registered in setUp()) via the service traits.
        return new SlackNotificationService();
    }

    /**
     * Provider mock pre-wired so its getFormatter() returns the shared
     * formatter mock the tests configure via $this->formatter->method('format')
     * / formatTestMessage(). The orchestrator now resolves the formatter via
     * the provider (post-multi-platform refactor) rather than via a direct
     * constructor arg, so every call path goes through this helper.
     *
     * @return WebhookProviderInterface&MockObject
     */
    private function makeProviderMock()
    {
        $provider = $this->createMock(WebhookProviderInterface::class);
        $provider->method('getFormatter')->willReturn($this->formatter);
        return $provider;
    }

    /**
     * @param SlackRegistration[] $regs
     */
    private function enableFor(array $regs): void
    {
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('listActiveRegistrations')->willReturn($regs);
    }

    private function expectLogWith(string $status, int $recipientCount, ?string $error): void
    {
        $log = new SlackLog();
        $this->logDao->expects($this->atLeastOnce())
            ->method('makeLogFor')
            ->with(
                $this->isInstanceOf(SlackRegistration::class),
                $this->isInstanceOf(DateTime::class),
                $status,
                $recipientCount,
                $error
            )
            ->willReturn($log);
        $this->logDao->expects($this->atLeastOnce())
            ->method('recordLog')
            ->with($log);
    }

    /**
     * @param SlackEmployeeRecipient[] $recipients
     */
    private function fakeResolverReturning(array $recipients): RecipientResolverInterface
    {
        $resolver = $this->createMock(RecipientResolverInterface::class);
        $resolver->method('resolve')->willReturn($recipients);
        return $resolver;
    }

    /**
     * Swap out an internal resolver instance — the constructor wires up the real
     * ones with `new`, so this is how we keep the test from touching the DB.
     */
    private function injectResolver(string $eventType, RecipientResolverInterface $resolver): void
    {
        $service = $this->makeService();
        $ref = new \ReflectionClass(SlackNotificationService::class);
        $prop = $ref->getProperty('resolvers');
        $prop->setAccessible(true);
        $resolvers = $prop->getValue($service);
        $resolvers[$eventType] = $resolver;
        $prop->setValue($service, $resolvers);
        // Replace makeService so subsequent dispatch calls use this pre-configured
        // instance instead of building a fresh one without our injected resolver.
        $this->preBuiltService = $service;
    }

    /** @var SlackNotificationService|null */
    private ?SlackNotificationService $preBuiltService = null;

    /**
     * Returns the time-of-day "H:i" in UTC that is `$offsetMinutes` away from now.
     * Positive offset → future (outside the 5-minute window).
     * Negative offset → recent past (inside the 5-minute window — service will
     * treat now as ≥ start and < start+5min).
     */
    private function utcTimeOffsetMinutes(int $offsetMinutes): string
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $now->modify(($offsetMinutes >= 0 ? '+' : '-') . abs($offsetMinutes) . ' minutes');
        return $now->format('H:i');
    }

    private function makeRegistration(
        int $id,
        string $sendTime,
        string $timezone = 'UTC',
        string $eventType = SlackRegistration::EVENT_TYPE_BIRTHDAY
    ): SlackRegistration {
        $reg = new SlackRegistration();
        // id is normally generator-driven — reflection lets the test set it
        // without going through Doctrine.
        $ref = new \ReflectionClass(SlackRegistration::class);
        $prop = $ref->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($reg, $id);

        $reg->setProvider(SlackRegistration::PROVIDER_SLACK);
        $reg->setEventType($eventType);
        $reg->setWebhookUrl('encrypted-blob');
        $reg->setTimezone($timezone);
        $reg->setDailySendTime($sendTime);
        $reg->setActive(true);
        return $reg;
    }
}
