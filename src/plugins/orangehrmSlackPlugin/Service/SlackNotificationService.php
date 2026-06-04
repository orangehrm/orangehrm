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

namespace OrangeHRM\Slack\Service;

use DateTime;
use DateTimeZone;
use OrangeHRM\Entity\SlackLog;
use OrangeHRM\Entity\SlackRegistration;
use OrangeHRM\Slack\Service\Resolver\BirthdayResolver;
use OrangeHRM\Slack\Service\Resolver\LeaveTodayResolver;
use OrangeHRM\Slack\Service\Resolver\RecipientResolverInterface;
use OrangeHRM\Slack\Service\Webhook\WebhookDeliveryResult;
use OrangeHRM\Slack\Traits\Dao\SlackLogDaoTrait;
use OrangeHRM\Slack\Traits\Service\SlackRegistrationServiceTrait;
use OrangeHRM\Slack\Traits\Service\SlackSettingsServiceTrait;
use OrangeHRM\Slack\Traits\Service\WebhookProviderRegistryTrait;
use Throwable;

/**
 * Orchestrator for the daily Slack/Teams/Google-Chat dispatch run.
 *
 * Every collaborator is fetched from the OHRM service container via traits:
 * {@see SlackSettingsServiceTrait}, {@see SlackRegistrationServiceTrait},
 * {@see SlackLogDaoTrait}, {@see WebhookProviderRegistryTrait}. The constructor
 * is parameterless — tests mock by registering a replacement service in the
 * container (`createKernelWithMockServices([Services::SLACK_LOG_DAO => $mock])`)
 * the same way LDAP / Buzz / every other OHRM service is mocked.
 *
 * The per-platform message formatter is owned by each webhook provider
 * (see {@see \OrangeHRM\Slack\Service\Webhook\WebhookProviderInterface::getFormatter()})
 * so adding a new platform never touches this class.
 */
class SlackNotificationService
{
    use SlackSettingsServiceTrait;
    use SlackRegistrationServiceTrait;
    use SlackLogDaoTrait;
    use WebhookProviderRegistryTrait;

    private const SEND_WINDOW_MINUTES = 5;

    /** @var array<string, RecipientResolverInterface> */
    private array $resolvers = [];

    public function __construct()
    {
        $this->resolvers[SlackRegistration::EVENT_TYPE_BIRTHDAY] = new BirthdayResolver();
        $this->resolvers[SlackRegistration::EVENT_TYPE_LEAVE_TODAY] = new LeaveTodayResolver();
    }

    /**
     * Entry point invoked from the scheduler/console command.
     *
     * Returns a summary of what happened, keyed by registration id:
     *   [id => ['status' => SlackLog::STATUS_*, 'recipientCount' => int, 'error' => string|null]]
     *
     * @return array<int, array{status:string,recipientCount:int,error:?string}>
     */
    public function dispatchDueNotifications(): array
    {
        $summary = [];
        if (!$this->getSlackSettingsService()->isEnabled()) {
            return $summary;
        }

        $nowUtc = new DateTime('now', new DateTimeZone('UTC'));

        foreach ($this->getSlackRegistrationService()->listActiveRegistrations() as $registration) {
            $id = $registration->getId();

            try {
                $tz = $this->resolveTimezone($registration->getTimezone());
                $today = (clone $nowUtc)->setTimezone($tz)->setTime(0, 0, 0);

                if (!$this->isWithinSendWindow($registration, $nowUtc, $tz)) {
                    $summary[$id] = [
                        'status' => SlackLog::STATUS_SKIPPED,
                        'recipientCount' => 0,
                        'error' => 'Outside send-time window',
                    ];
                    continue;
                }

                if ($this->getSlackLogDao()->hasSuccessfulDeliveryForDate($id, $today)) {
                    $summary[$id] = [
                        'status' => SlackLog::STATUS_SKIPPED,
                        'recipientCount' => 0,
                        'error' => 'Already delivered today',
                    ];
                    continue;
                }
                $summary[$id] = $this->dispatchRegistration($registration, $today);
            } catch (Throwable $e) {
                $today = $today ?? new DateTime('today', new DateTimeZone('UTC'));
                $summary[$id] = [
                    'status' => SlackLog::STATUS_FAILED,
                    'recipientCount' => 0,
                    'error' => $e->getMessage(),
                ];
                $this->writeFailureLog($registration, $today, $e->getMessage());
            }
        }
        return $summary;
    }

    /**
     * Per-row dispatch entry point. The scheduler calls this once per
     * `orangehrm:run-schedule` tick that matches the row's cron expression,
     * passing the row id via `--registration-id`. Skips the send-time-window
     * gate because Crunz already enforced it via the per-row cron + timezone;
     * still enforces the global-enable + per-row-active + same-day-dedupe
     * invariants so a manual run from the shell behaves identically.
     *
     * @return array{status:string,recipientCount:int,error:?string}
     */
    public function dispatchSingleRegistration(int $registrationId): array
    {
        if (!$this->getSlackSettingsService()->isEnabled()) {
            return [
                'status' => SlackLog::STATUS_SKIPPED,
                'recipientCount' => 0,
                'error' => 'Slack notifications globally disabled',
            ];
        }

        $registration = $this->getSlackRegistrationService()->getRegistration($registrationId);
        if (!$registration instanceof SlackRegistration) {
            return [
                'status' => SlackLog::STATUS_FAILED,
                'recipientCount' => 0,
                'error' => "Registration {$registrationId} not found",
            ];
        }
        if (!$registration->isActive()) {
            return [
                'status' => SlackLog::STATUS_SKIPPED,
                'recipientCount' => 0,
                'error' => 'Registration is inactive',
            ];
        }

        $tz = $this->resolveTimezone($registration->getTimezone());
        $today = (new DateTime('now', new DateTimeZone('UTC')))
            ->setTimezone($tz)
            ->setTime(0, 0, 0);

        if ($this->getSlackLogDao()->hasSuccessfulDeliveryForDate($registrationId, $today)) {
            return [
                'status' => SlackLog::STATUS_SKIPPED,
                'recipientCount' => 0,
                'error' => 'Already delivered today',
            ];
        }

        try {
            return $this->dispatchRegistration($registration, $today);
        } catch (Throwable $e) {
            $this->writeFailureLog($registration, $today, $e->getMessage());
            return [
                'status' => SlackLog::STATUS_FAILED,
                'recipientCount' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function resolveTimezone(string $name): DateTimeZone
    {
        try {
            return new DateTimeZone($name);
        } catch (Throwable $e) {
            return new DateTimeZone('UTC');
        }
    }

    /**
     * Return true iff the current moment falls inside the registration's daily send window,
     * computed in the registration's own timezone.
     */
    private function isWithinSendWindow(SlackRegistration $registration, DateTime $nowUtc, DateTimeZone $tz): bool
    {
        $sendTime = $registration->getDailySendTime();
        $parts = explode(':', $sendTime, 2);
        if (count($parts) !== 2) {
            return false;
        }
        $hour = (int)$parts[0];
        $minute = (int)$parts[1];

        $nowLocal = (clone $nowUtc)->setTimezone($tz);
        $windowStart = (clone $nowLocal)->setTime($hour, $minute, 0);
        $windowEnd = (clone $windowStart)->modify('+' . self::SEND_WINDOW_MINUTES . ' minutes');

        return $nowLocal >= $windowStart && $nowLocal < $windowEnd;
    }

    /**
     * @return array{status:string,recipientCount:int,error:?string}
     */
    private function dispatchRegistration(SlackRegistration $registration, DateTime $today): array
    {
        $resolver = $this->resolvers[$registration->getEventType()] ?? null;
        if ($resolver === null) {
            return $this->finish(
                $registration,
                $today,
                SlackLog::STATUS_FAILED,
                0,
                'Unsupported event type: ' . $registration->getEventType()
            );
        }

        $subunitIds = [];
        foreach ($registration->getSubunits() as $subunit) {
            $subunitIds[] = $subunit->getId();
        }
        $recipients = $resolver->resolve($today, $subunitIds);

        // FR-16: no data → don't send, and don't write a SUCCESS row (so we try again on a later tick
        // if data lands during the day). A SKIPPED row keeps an audit trail.
        if (count($recipients) === 0) {
            return $this->finish($registration, $today, SlackLog::STATUS_SKIPPED, 0, 'No recipients matched');
        }

        $subunitNames = [];
        foreach ($registration->getSubunits() as $subunit) {
            $subunitNames[] = $subunit->getName();
        }
        $subunitLabel = empty($subunitNames) ? null : implode(', ', $subunitNames);

        $webhookUrl = $this->getSlackRegistrationService()->decryptWebhookUrl($registration);
        if ($webhookUrl === null || $webhookUrl === '') {
            return $this->finish($registration, $today, SlackLog::STATUS_FAILED, count($recipients), 'Webhook URL is empty');
        }

        // Resolve provider FIRST so its formatter renders the message in the
        // target platform's native markup (Slack-mrkdwn for Slack/Google Chat,
        // MessageCard markdown for Teams, etc.). Sending Slack-mrkdwn to Teams
        // would surface raw `*asterisks*` in the channel.
        $provider = $this->getWebhookProviderRegistry()->getForRegistration($registration);
        $message = $provider->getFormatter()->format(
            $registration->getEventType(),
            $today,
            $recipients,
            $subunitLabel
        );
        $result = $provider->send($webhookUrl, $message);
        if ($result->isOk()) {
            return $this->finish($registration, $today, SlackLog::STATUS_SUCCESS, count($recipients), null);
        }
        return $this->finish(
            $registration,
            $today,
            SlackLog::STATUS_FAILED,
            count($recipients),
            $result->getErrorMessage()
        );
    }

    /**
     * @return array{status:string,recipientCount:int,error:?string}
     */
    private function finish(SlackRegistration $registration, DateTime $today, string $status, int $count, ?string $error): array
    {
        $log = $this->getSlackLogDao()->makeLogFor($registration, $today, $status, $count, $error);
        $this->getSlackLogDao()->recordLog($log);


        return [
            'status' => $status,
            'recipientCount' => $count,
            'error' => $error,
        ];
    }

    private function writeFailureLog(SlackRegistration $registration, DateTime $today, string $message): void
    {
        try {
            $log = $this->getSlackLogDao()->makeLogFor($registration, $today, SlackLog::STATUS_FAILED, 0, $message);
            $this->getSlackLogDao()->recordLog($log);
        } catch (Throwable $ignored) {
            // Don't let logging failures cascade.
        }
    }

    /**
     * Used by SlackTestWebhookAPI for ad-hoc test sends.
     *
     * Until per-row provider selection lands in the UI, ad-hoc tests dispatched
     * before a registration is saved go through the default provider
     * (`SlackRegistration::PROVIDER_SLACK`). Saved-row tests route via the
     * registration's own provider.
     */
    public function sendTestMessage(string $webhookUrl, string $eventType, ?string $providerId = null): WebhookDeliveryResult
    {
        $provider = $this->getWebhookProviderRegistry()->get($providerId ?? SlackRegistration::PROVIDER_SLACK);
        $text = $provider->getFormatter()->formatTestMessage($eventType);
        return $provider->send($webhookUrl, $text);
    }
}
