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
use OrangeHRM\Slack\Dao\SlackLogDao;
use OrangeHRM\Slack\Service\Formatter\SlackMessageFormatter;
use OrangeHRM\Slack\Service\Resolver\BirthdayResolver;
use OrangeHRM\Slack\Service\Resolver\LeaveTodayResolver;
use OrangeHRM\Slack\Service\Resolver\RecipientResolverInterface;
use OrangeHRM\Slack\Service\Webhook\SlackDeliveryResult;
use OrangeHRM\Slack\Service\Webhook\SlackWebhookClient;
use Throwable;

class SlackNotificationService
{
    private const SEND_WINDOW_MINUTES = 5;

    private SlackSettingsService $settingsService;
    private SlackRegistrationService $registrationService;
    private SlackLogDao $logDao;
    private SlackMessageFormatter $formatter;
    private SlackWebhookClient $webhookClient;

    /** @var array<string, RecipientResolverInterface> */
    private array $resolvers = [];

    public function __construct(
        ?SlackSettingsService $settingsService = null,
        ?SlackRegistrationService $registrationService = null,
        ?SlackLogDao $logDao = null,
        ?SlackMessageFormatter $formatter = null,
        ?SlackWebhookClient $webhookClient = null
    ) {
        $this->settingsService = $settingsService ?? new SlackSettingsService();
        $this->registrationService = $registrationService ?? new SlackRegistrationService();
        $this->logDao = $logDao ?? new SlackLogDao();
        $this->formatter = $formatter ?? new SlackMessageFormatter();
        $this->webhookClient = $webhookClient ?? new SlackWebhookClient();

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
        if (!$this->settingsService->isEnabled()) {
            return $summary;
        }

        $nowUtc = new DateTime('now', new DateTimeZone('UTC'));

        foreach ($this->registrationService->listActiveRegistrations() as $registration) {
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

                if ($this->logDao->hasSuccessfulDeliveryForDate($id, $today)) {
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

        $message = $this->formatter->format(
            $registration->getEventType(),
            $today,
            $recipients,
            $subunitLabel
        );

        $webhookUrl = $this->registrationService->decryptWebhookUrl($registration);
        if ($webhookUrl === null || $webhookUrl === '') {
            return $this->finish($registration, $today, SlackLog::STATUS_FAILED, count($recipients), 'Webhook URL is empty');
        }

        $result = $this->webhookClient->send($webhookUrl, $message);
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
        $log = $this->logDao->makeLogFor($registration, $today, $status, $count, $error);
        $this->logDao->recordLog($log);


        return [
            'status' => $status,
            'recipientCount' => $count,
            'error' => $error,
        ];
    }

    private function writeFailureLog(SlackRegistration $registration, DateTime $today, string $message): void
    {
        try {
            $log = $this->logDao->makeLogFor($registration, $today, SlackLog::STATUS_FAILED, 0, $message);
            $this->logDao->recordLog($log);
        } catch (Throwable $ignored) {
            // Don't let logging failures cascade.
        }
    }

    /**
     * Used by SlackTestWebhookAPI for ad-hoc test sends.
     */
    public function sendTestMessage(string $webhookUrl, string $eventType): SlackDeliveryResult
    {
        $text = $this->formatter->formatTestMessage($eventType);
        return $this->webhookClient->send($webhookUrl, $text);
    }
}
