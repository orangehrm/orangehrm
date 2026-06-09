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

namespace OrangeHRM\WorkspaceNotifications\Service;

use DateTime;
use DateTimeZone;
use OrangeHRM\Entity\WorkspaceNotificationLog;
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\WorkspaceNotifications\Dao\WorkspaceNotificationLogDao;
use OrangeHRM\WorkspaceNotifications\Service\Resolver\BirthdayResolver;
use OrangeHRM\WorkspaceNotifications\Service\Resolver\LeaveTodayResolver;
use OrangeHRM\WorkspaceNotifications\Service\Resolver\RecipientResolverInterface;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookDeliveryResult;
use OrangeHRM\WorkspaceNotifications\Traits\Service\WorkspaceNotificationRegistrationServiceTrait;
use OrangeHRM\WorkspaceNotifications\Traits\Service\WorkspaceNotificationSettingsServiceTrait;
use OrangeHRM\WorkspaceNotifications\Traits\Service\WebhookProviderRegistryTrait;
use Throwable;

class WorkspaceNotificationService
{
    use WorkspaceNotificationSettingsServiceTrait;
    use WorkspaceNotificationRegistrationServiceTrait;
    use WebhookProviderRegistryTrait;

    private const SEND_WINDOW_MINUTES = 5;

    /** @var array<string, RecipientResolverInterface> */
    private array $resolvers = [];

    private ?WorkspaceNotificationLogDao $workspaceNotificationLogDao = null;

    public function __construct()
    {
        $this->resolvers[WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY] = new BirthdayResolver();
        $this->resolvers[WorkspaceNotificationRegistration::EVENT_TYPE_LEAVE_TODAY] = new LeaveTodayResolver();
    }

    public function getWorkspaceNotificationLogDao(): WorkspaceNotificationLogDao
    {
        if (!($this->workspaceNotificationLogDao instanceof WorkspaceNotificationLogDao)) {
            $this->workspaceNotificationLogDao = new WorkspaceNotificationLogDao();
        }
        return $this->workspaceNotificationLogDao;
    }

    public function setWorkspaceNotificationLogDao(WorkspaceNotificationLogDao $dao): void
    {
        $this->workspaceNotificationLogDao = $dao;
    }

    /**
     * @return array<int, array{status:string,recipientCount:int,error:?string}>
     */
    public function dispatchDueNotifications(): array
    {
        $summary = [];
        if (!$this->getWorkspaceNotificationSettingsService()->isEnabled()) {
            return $summary;
        }

        $nowUtc = new DateTime('now', new DateTimeZone('UTC'));

        foreach ($this->getWorkspaceNotificationRegistrationService()->listActiveRegistrations() as $registration) {
            $id = $registration->getId();

            try {
                $tz = $this->resolveTimezone($registration->getTimezone());
                $today = (clone $nowUtc)->setTimezone($tz)->setTime(0, 0, 0);

                if (!$this->isWithinSendWindow($registration, $nowUtc, $tz)) {
                    $summary[$id] = [
                        'status' => WorkspaceNotificationLog::STATUS_SKIPPED,
                        'recipientCount' => 0,
                        'error' => 'Outside send-time window',
                    ];
                    continue;
                }

                if ($this->getWorkspaceNotificationLogDao()->hasSuccessfulDeliveryForDate($id, $today)) {
                    $summary[$id] = [
                        'status' => WorkspaceNotificationLog::STATUS_SKIPPED,
                        'recipientCount' => 0,
                        'error' => 'Already delivered today',
                    ];
                    continue;
                }
                $summary[$id] = $this->dispatchRegistration($registration, $today);
            } catch (Throwable $e) {
                $today = $today ?? new DateTime('today', new DateTimeZone('UTC'));
                $summary[$id] = [
                    'status' => WorkspaceNotificationLog::STATUS_FAILED,
                    'recipientCount' => 0,
                    'error' => $e->getMessage(),
                ];
                $this->writeFailureLog($registration, $today, $e->getMessage());
            }
        }
        return $summary;
    }

    /**
     * @return array{status:string,recipientCount:int,error:?string}
     */
    public function dispatchSingleRegistration(int $registrationId): array
    {
        if (!$this->getWorkspaceNotificationSettingsService()->isEnabled()) {
            return [
                'status' => WorkspaceNotificationLog::STATUS_SKIPPED,
                'recipientCount' => 0,
                'error' => 'Workspace notifications globally disabled',
            ];
        }

        $registration = $this->getWorkspaceNotificationRegistrationService()->getRegistration($registrationId);
        if (!$registration instanceof WorkspaceNotificationRegistration) {
            return [
                'status' => WorkspaceNotificationLog::STATUS_FAILED,
                'recipientCount' => 0,
                'error' => "Registration {$registrationId} not found",
            ];
        }
        if (!$registration->isActive()) {
            return [
                'status' => WorkspaceNotificationLog::STATUS_SKIPPED,
                'recipientCount' => 0,
                'error' => 'Registration is inactive',
            ];
        }

        $tz = $this->resolveTimezone($registration->getTimezone());
        $today = (new DateTime('now', new DateTimeZone('UTC')))
            ->setTimezone($tz)
            ->setTime(0, 0, 0);

        if ($this->getWorkspaceNotificationLogDao()->hasSuccessfulDeliveryForDate($registrationId, $today)) {
            return [
                'status' => WorkspaceNotificationLog::STATUS_SKIPPED,
                'recipientCount' => 0,
                'error' => 'Already delivered today',
            ];
        }

        try {
            return $this->dispatchRegistration($registration, $today);
        } catch (Throwable $e) {
            $this->writeFailureLog($registration, $today, $e->getMessage());
            return [
                'status' => WorkspaceNotificationLog::STATUS_FAILED,
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

    private function isWithinSendWindow(WorkspaceNotificationRegistration $registration, DateTime $nowUtc, DateTimeZone $tz): bool
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
    private function dispatchRegistration(WorkspaceNotificationRegistration $registration, DateTime $today): array
    {
        $resolver = $this->resolvers[$registration->getEventType()] ?? null;
        if ($resolver === null) {
            return $this->finish(
                $registration,
                $today,
                WorkspaceNotificationLog::STATUS_FAILED,
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
            $reason = $registration->getEventType() === WorkspaceNotificationRegistration::EVENT_TYPE_BIRTHDAY
                ? 'No employees have birthdays today'
                : 'No employees are on leave today';
            return $this->finish($registration, $today, WorkspaceNotificationLog::STATUS_SKIPPED, 0, $reason);
        }

        $subunitNames = [];
        foreach ($registration->getSubunits() as $subunit) {
            $subunitNames[] = $subunit->getName();
        }
        $subunitLabel = empty($subunitNames) ? null : implode(', ', $subunitNames);

        $webhookUrl = $this->getWorkspaceNotificationRegistrationService()->decryptWebhookUrl($registration);
        if ($webhookUrl === null || $webhookUrl === '') {
            return $this->finish($registration, $today, WorkspaceNotificationLog::STATUS_FAILED, count($recipients), 'Webhook URL is empty');
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
            return $this->finish($registration, $today, WorkspaceNotificationLog::STATUS_SUCCESS, count($recipients), null);
        }
        return $this->finish(
            $registration,
            $today,
            WorkspaceNotificationLog::STATUS_FAILED,
            count($recipients),
            $result->getErrorMessage()
        );
    }

    /**
     * @return array{status:string,recipientCount:int,error:?string}
     */
    private function finish(WorkspaceNotificationRegistration $registration, DateTime $today, string $status, int $count, ?string $error): array
    {
        // Audit-log noise guard: a registration with no matching recipients
        // gets re-evaluated every cron tick (every 5 min). Without this
        // check, a single zero-recipient registration would accumulate
        // dozens of identical SKIPPED rows per day. We keep ONE SKIPPED per
        // (registration, date) — the first one captures the audit; later
        // ticks re-check (in case data lands during the day) but don't
        // re-log if the state hasn't changed to SUCCESS/FAILED.
        $shouldLog = true;
        if ($status === WorkspaceNotificationLog::STATUS_SKIPPED) {
            $regId = $registration->getId();
            if ($regId !== null && $this->getWorkspaceNotificationLogDao()
                    ->hasLogForDateWithStatus($regId, $today, WorkspaceNotificationLog::STATUS_SKIPPED)) {
                $shouldLog = false;
            }
        }

        if ($shouldLog) {
            $log = $this->getWorkspaceNotificationLogDao()->makeLogFor($registration, $today, $status, $count, $error);
            $this->getWorkspaceNotificationLogDao()->recordLog($log);
        }

        return [
            'status' => $status,
            'recipientCount' => $count,
            'error' => $error,
        ];
    }

    private function writeFailureLog(WorkspaceNotificationRegistration $registration, DateTime $today, string $message): void
    {
        try {
            $log = $this->getWorkspaceNotificationLogDao()->makeLogFor($registration, $today, WorkspaceNotificationLog::STATUS_FAILED, 0, $message);
            $this->getWorkspaceNotificationLogDao()->recordLog($log);
        } catch (Throwable $ignored) {
        }
    }

    public function sendTestMessage(string $webhookUrl, string $eventType, ?string $providerId = null): WebhookDeliveryResult
    {
        $provider = $this->getWebhookProviderRegistry()->get($providerId ?? WorkspaceNotificationRegistration::PROVIDER_SLACK);
        $text = $provider->getFormatter()->formatTestMessage($eventType);
        return $provider->send($webhookUrl, $text);
    }
}
