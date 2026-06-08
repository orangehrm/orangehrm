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

namespace OrangeHRM\Tests\WorkspaceNotifications\Service;

use DateTime;
use DateTimeZone;
use OrangeHRM\Entity\WorkspaceNotificationLog;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\Framework\Services;
use OrangeHRM\WorkspaceNotifications\Dao\WorkspaceNotificationLogDao;
use OrangeHRM\WorkspaceNotifications\Dto\WorkspaceNotificationRecipient;
use OrangeHRM\WorkspaceNotifications\Service\Formatter\MessageFormatterInterface;
use OrangeHRM\WorkspaceNotifications\Service\Resolver\RecipientResolverInterface;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationService;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationRegistrationService;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationSettingsService;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookDeliveryResult;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookProviderInterface;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookProviderRegistry;
use OrangeHRM\Tests\Util\KernelTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use RuntimeException;

/**
 * @group Slack
 * @group Service
 */
class WorkspaceNotificationServiceTest extends KernelTestCase
{
    /** @var WorkspaceNotificationSettingsService&MockObject */ private $settings;
    /** @var WorkspaceNotificationRegistrationService&MockObject */ private $registrations;
    /** @var WorkspaceNotificationLogDao&MockObject */ private $logDao;
    /** @var MessageFormatterInterface&MockObject */ private $formatter;
    /** @var WebhookProviderRegistry&MockObject */ private $registry;

    protected function setUp(): void
    {
        $this->settings = $this->createMock(WorkspaceNotificationSettingsService::class);
        $this->registrations = $this->createMock(WorkspaceNotificationRegistrationService::class);
        $this->logDao = $this->createMock(WorkspaceNotificationLogDao::class);
        $this->formatter = $this->createMock(MessageFormatterInterface::class);
        $this->registry = $this->createMock(WebhookProviderRegistry::class);

        $this->createKernelWithMockServices([
            Services::WORKSPACE_NOTIFICATION_SETTINGS_SERVICE => $this->settings,
            Services::WORKSPACE_NOTIFICATION_REGISTRATION_SERVICE => $this->registrations,
            Services::WEBHOOK_PROVIDER_REGISTRY => $this->registry,
        ]);
    }

    public function testReturnsEmptyWhenFeatureDisabled(): void
    {
        $this->settings->method('isEnabled')->willReturn(false);
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

    public function testRegistrationOutsideSendWindowIsSkippedWithoutDispatch(): void
    {
        $reg = $this->makeRegistration(11, $this->utcTimeOffsetMinutes(+10), 'UTC');
        $this->enableFor([$reg]);

        $this->logDao->expects($this->never())->method('hasSuccessfulDeliveryForDate');
        $this->logDao->expects($this->never())->method('recordLog');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_SKIPPED, $result[11]['status']);
        $this->assertSame('Outside send-time window', $result[11]['error']);
        $this->assertSame(0, $result[11]['recipientCount']);
    }

    public function testRegistrationAlreadyDeliveredTodayIsSkippedWithoutDispatch(): void
    {
        $reg = $this->makeRegistration(12, $this->utcTimeOffsetMinutes(-1), 'UTC');
        $this->enableFor([$reg]);

        $this->logDao->expects($this->once())
            ->method('hasSuccessfulDeliveryForDate')
            ->with(12, $this->isInstanceOf(DateTime::class))
            ->willReturn(true);
        $this->logDao->expects($this->never())->method('recordLog');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_SKIPPED, $result[12]['status']);
        $this->assertSame('Already delivered today', $result[12]['error']);
    }

    public function testNoBirthdayRecipientsLogsSkippedWithBirthdayMessage(): void
    {
        $reg = $this->makeRegistration(21, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);

        $this->injectResolver(WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY, $this->fakeResolverReturning([]));

        $this->expectLogWith(WorkspaceNotificationLog::STATUS_SKIPPED, 0, 'No employees have birthdays today');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_SKIPPED, $result[21]['status']);
        $this->assertSame('No employees have birthdays today', $result[21]['error']);
    }

    public function testNoLeaveRecipientsLogsSkippedWithLeaveMessage(): void
    {
        $reg = $this->makeRegistration(
            31,
            $this->utcTimeOffsetMinutes(-1),
            'UTC',
            WorkspaceNotificationRegistration::EVENT_TYPE_LEAVE_TODAY
        );
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);

        $this->injectResolver(WorkspaceNotificationRegistration::EVENT_TYPE_LEAVE_TODAY, $this->fakeResolverReturning([]));

        $this->expectLogWith(WorkspaceNotificationLog::STATUS_SKIPPED, 0, 'No employees are on leave today');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_SKIPPED, $result[31]['status']);
        $this->assertSame('No employees are on leave today', $result[31]['error']);
    }

    public function testUnsupportedEventTypeLogsFailed(): void
    {
        $reg = $this->makeRegistration(22, $this->utcTimeOffsetMinutes(-1), 'UTC', 'NEW_HIRE');
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);

        $this->expectLogWith(WorkspaceNotificationLog::STATUS_FAILED, 0, 'Unsupported event type: NEW_HIRE');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_FAILED, $result[22]['status']);
        $this->assertStringContainsString('NEW_HIRE', (string)$result[22]['error']);
    }

    public function testEmptyDecryptedWebhookUrlLogsFailed(): void
    {
        $reg = $this->makeRegistration(23, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$reg]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->with($reg)->willReturn(null);

        $this->injectResolver(
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new WorkspaceNotificationRecipient('Alex Carter', 'Engineering')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $this->expectLogWith(WorkspaceNotificationLog::STATUS_FAILED, 1, 'Webhook URL is empty');
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_FAILED, $result[23]['status']);
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
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([
                new WorkspaceNotificationRecipient('Alex Carter', 'Engineering'),
                new WorkspaceNotificationRecipient('Priya Singh', 'Engineering'),
            ])
        );
        $this->formatter->expects($this->once())
            ->method('format')
            ->with(
                WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
                $this->isInstanceOf(DateTime::class),
                $this->countOf(2),
                $this->isNull()
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

        $this->expectLogWith(WorkspaceNotificationLog::STATUS_SUCCESS, 2, null);

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_SUCCESS, $result[24]['status']);
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
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new WorkspaceNotificationRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $provider = $this->makeProviderMock();
        $provider->method('send')->willReturn(WebhookDeliveryResult::failure('Slack rejected: channel_not_found'));
        $this->registry->method('getForRegistration')->willReturn($provider);

        $this->expectLogWith(WorkspaceNotificationLog::STATUS_FAILED, 1, 'Slack rejected: channel_not_found');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_FAILED, $result[25]['status']);
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
        $this->injectResolver(WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY, $throwingResolver);

        $this->expectLogWith(WorkspaceNotificationLog::STATUS_FAILED, 0, 'Doctrine boom');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_FAILED, $result[26]['status']);
        $this->assertSame('Doctrine boom', $result[26]['error']);
    }

    public function testOneRegistrationFailingDoesNotPreventOtherFromDispatching(): void
    {
        $bad = $this->makeRegistration(91, $this->utcTimeOffsetMinutes(-1), 'UTC', 'NEW_HIRE');
        $good = $this->makeRegistration(92, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$bad, $good]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->willReturn('https://hooks.slack.com/services/T/B/secret');

        $this->injectResolver(
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new WorkspaceNotificationRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $provider = $this->makeProviderMock();
        $provider->method('send')->willReturn(WebhookDeliveryResult::success());
        $this->registry->method('getForRegistration')->with($good)->willReturn($provider);

        $this->logDao->expects($this->exactly(2))->method('recordLog');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_FAILED, $result[91]['status']);
        $this->assertSame(WorkspaceNotificationLog::STATUS_SUCCESS, $result[92]['status']);
    }

    public function testInvalidWebhookOnOneRowDoesNotBlockOtherRows(): void
    {
        $broken = $this->makeRegistration(81, $this->utcTimeOffsetMinutes(-1));
        $working = $this->makeRegistration(82, $this->utcTimeOffsetMinutes(-1));
        $this->enableFor([$broken, $working]);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->willReturn('https://hooks.slack.com/services/T/B/secret');

        $this->injectResolver(
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new WorkspaceNotificationRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

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

        $this->logDao->expects($this->exactly(2))->method('recordLog');

        $result = $this->makeService()->dispatchDueNotifications();
        $this->assertSame(WorkspaceNotificationLog::STATUS_FAILED, $result[81]['status']);
        $this->assertSame('Slack rejected: invalid_token', $result[81]['error']);
        $this->assertSame(WorkspaceNotificationLog::STATUS_SUCCESS, $result[82]['status']);
        $this->assertNull($result[82]['error']);
    }

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
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new WorkspaceNotificationRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $provider = $this->makeProviderMock();
        $provider->method('send')->willReturn(WebhookDeliveryResult::success());
        $this->registry->method('getForRegistration')->willReturn($provider);

        $this->logDao->expects($this->exactly(2))->method('recordLog');

        $service = $this->makeService();
        $colomboResult = $service->dispatchSingleRegistration(101);
        $newYorkResult = $service->dispatchSingleRegistration(102);

        $this->assertSame(WorkspaceNotificationLog::STATUS_SUCCESS, $colomboResult['status']);
        $this->assertSame(WorkspaceNotificationLog::STATUS_SUCCESS, $newYorkResult['status']);
    }

    public function testDispatchSingleRegistrationShortCircuitsWhenGloballyDisabled(): void
    {
        $this->settings->method('isEnabled')->willReturn(false);
        $this->registrations->expects($this->never())->method('getRegistration');

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(WorkspaceNotificationLog::STATUS_SKIPPED, $result['status']);
        $this->assertStringContainsString('globally disabled', (string)$result['error']);
    }

    public function testDispatchSingleRegistrationReturnsFailedForUnknownId(): void
    {
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('getRegistration')->with(42)->willReturn(null);

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(WorkspaceNotificationLog::STATUS_FAILED, $result['status']);
        $this->assertStringContainsString('not found', (string)$result['error']);
    }

    public function testDispatchSingleRegistrationSkipsInactiveRow(): void
    {
        $reg = $this->makeRegistration(42, '09:00');
        $reg->setActive(false);
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('getRegistration')->with(42)->willReturn($reg);
        $this->logDao->expects($this->never())->method('recordLog');

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(WorkspaceNotificationLog::STATUS_SKIPPED, $result['status']);
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
        $this->registry->expects($this->never())->method('getForRegistration');

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(WorkspaceNotificationLog::STATUS_SKIPPED, $result['status']);
        $this->assertSame('Already delivered today', $result['error']);
    }

    public function testDispatchSingleRegistrationHappyPathBypassesSendWindow(): void
    {
        $reg = $this->makeRegistration(42, $this->utcTimeOffsetMinutes(+10));
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('getRegistration')->with(42)->willReturn($reg);
        $this->logDao->method('hasSuccessfulDeliveryForDate')->willReturn(false);
        $this->registrations->method('decryptWebhookUrl')->willReturn('https://hooks.slack.com/services/T/B/secret');

        $this->injectResolver(
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY,
            $this->fakeResolverReturning([new WorkspaceNotificationRecipient('Alex Carter')])
        );
        $this->formatter->method('format')->willReturn('msg');

        $provider = $this->makeProviderMock();
        $provider->expects($this->once())->method('send')->willReturn(WebhookDeliveryResult::success());
        $this->registry->method('getForRegistration')->willReturn($provider);

        $this->expectLogWith(WorkspaceNotificationLog::STATUS_SUCCESS, 1, null);

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(WorkspaceNotificationLog::STATUS_SUCCESS, $result['status']);
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
        $this->injectResolver(WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY, $throwingResolver);

        $this->expectLogWith(WorkspaceNotificationLog::STATUS_FAILED, 0, 'Doctrine boom');

        $result = $this->makeService()->dispatchSingleRegistration(42);
        $this->assertSame(WorkspaceNotificationLog::STATUS_FAILED, $result['status']);
        $this->assertSame('Doctrine boom', $result['error']);
    }

    public function testSendTestMessageRoutesThroughDefaultProviderWhenIdMissing(): void
    {
        $this->formatter->expects($this->once())
            ->method('formatTestMessage')
            ->with(WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY)
            ->willReturn('test-msg');

        $provider = $this->makeProviderMock();
        $provider->expects($this->once())
            ->method('send')
            ->with('https://hooks.slack.com/services/T/B/secret', 'test-msg')
            ->willReturn(WebhookDeliveryResult::success());

        $this->registry->expects($this->once())
            ->method('get')
            ->with(WorkspaceNotificationRegistration::PROVIDER_SLACK)
            ->willReturn($provider);

        $result = $this->makeService()->sendTestMessage(
            'https://hooks.slack.com/services/T/B/secret',
            WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY
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

    private function makeService(): WorkspaceNotificationService
    {
        if ($this->preBuiltService !== null) {
            return $this->preBuiltService;
        }
        $service = new WorkspaceNotificationService();
        $service->setWorkspaceNotificationLogDao($this->logDao);
        return $service;
    }

    /**
     * @return WebhookProviderInterface&MockObject
     */
    private function makeProviderMock()
    {
        $provider = $this->createMock(WebhookProviderInterface::class);
        $provider->method('getFormatter')->willReturn($this->formatter);
        return $provider;
    }

    /**
     * @param WorkspaceNotificationRegistration[] $regs
     */
    private function enableFor(array $regs): void
    {
        $this->settings->method('isEnabled')->willReturn(true);
        $this->registrations->method('listActiveRegistrations')->willReturn($regs);
    }

    private function expectLogWith(string $status, int $recipientCount, ?string $error): void
    {
        $log = new WorkspaceNotificationLog();
        $this->logDao->expects($this->atLeastOnce())
            ->method('makeLogFor')
            ->with(
                $this->isInstanceOf(WorkspaceNotificationRegistration::class),
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
     * @param WorkspaceNotificationRecipient[] $recipients
     */
    private function fakeResolverReturning(array $recipients): RecipientResolverInterface
    {
        $resolver = $this->createMock(RecipientResolverInterface::class);
        $resolver->method('resolve')->willReturn($recipients);
        return $resolver;
    }

    private function injectResolver(string $eventType, RecipientResolverInterface $resolver): void
    {
        $service = $this->makeService();
        $ref = new \ReflectionClass(WorkspaceNotificationService::class);
        $prop = $ref->getProperty('resolvers');
        $prop->setAccessible(true);
        $resolvers = $prop->getValue($service);
        $resolvers[$eventType] = $resolver;
        $prop->setValue($service, $resolvers);
        $this->preBuiltService = $service;
    }

    /** @var WorkspaceNotificationService|null */
    private ?WorkspaceNotificationService $preBuiltService = null;

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
        string $eventType = WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY
    ): WorkspaceNotificationRegistration {
        $reg = new WorkspaceNotificationRegistration();
        $ref = new \ReflectionClass(WorkspaceNotificationRegistration::class);
        $prop = $ref->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($reg, $id);

        $reg->setProvider(WorkspaceNotificationRegistration::PROVIDER_SLACK);
        $reg->setEventType($eventType);
        $reg->setWebhookUrl('encrypted-blob');
        $reg->setTimezone($timezone);
        $reg->setDailySendTime($sendTime);
        $reg->setActive(true);
        return $reg;
    }
}
