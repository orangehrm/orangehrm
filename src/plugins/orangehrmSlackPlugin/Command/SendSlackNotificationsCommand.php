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

namespace OrangeHRM\Slack\Command;

use OrangeHRM\Entity\SlackLog;
use OrangeHRM\Framework\Console\Command;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\Slack\Service\SlackNotificationService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Two execution modes, both running daily via `orangehrm:run-schedule`:
 *
 *   1. Per-row mode (the scheduler's default — SlackPluginConfiguration::schedule()
 *      registers one task per enabled registration, each with `--registration-id`).
 *      Each invocation dispatches exactly one registration; if 4 rows are enabled,
 *      the cron evaluates 4 separate tasks at their respective times.
 *
 *   2. Fleet mode (when neither option is passed — manual / fallback). Walks every
 *      enabled registration and gates each one with the per-row send-time window.
 *      Useful for ad-hoc "fire whatever's due now" runs from the shell.
 *
 * `--event-type` is informational (the canonical source is the row itself) — it's
 * surfaced in the log line so ops can grep the slack.log for "BIRTHDAY" runs.
 */
class SendSlackNotificationsCommand extends Command
{
    public const OPT_REGISTRATION_ID = 'registration-id';
    public const OPT_EVENT_TYPE = 'event-type';

    public function getCommandName(): string
    {
        return 'orangehrm:send-slack-notifications';
    }

    protected function configure(): void
    {
        $this->setDescription('Send pending Slack notifications. Without options, walks every enabled registration; with --registration-id, dispatches exactly one.');
        $this->addOption(
            self::OPT_REGISTRATION_ID,
            null,
            InputOption::VALUE_REQUIRED,
            'Dispatch only the registration with this id. The scheduler passes this on every per-row cron tick.'
        );
        $this->addOption(
            self::OPT_EVENT_TYPE,
            null,
            InputOption::VALUE_REQUIRED,
            'Informational tag — surfaces in slack.log alongside the registration id. The canonical event type is read from the row itself.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = LoggerFactory::getLogger('slack');
        $registrationId = $input->getOption(self::OPT_REGISTRATION_ID);
        $eventType = $input->getOption(self::OPT_EVENT_TYPE);

        if ($registrationId !== null && $registrationId !== '') {
            return $this->runPerRow((int)$registrationId, $eventType, $logger);
        }
        return $this->runFleet($logger);
    }

    /**
     * Per-row mode — the scheduler's primary path. Dispatches exactly one
     * registration, freshly loaded from the DB inside the command (per the
     * "re-load needed services inside the command" guidance).
     *
     * @param \Monolog\Logger $logger
     */
    private function runPerRow(int $registrationId, ?string $eventType, $logger): int
    {
        $tag = sprintf('id=%d%s', $registrationId, $eventType ? " event={$eventType}" : '');
        $logger->info("Slack scheduler tick — starting [{$tag}]");

        try {
            $service = new SlackNotificationService();
            $entry = $service->dispatchSingleRegistration($registrationId);
        } catch (Throwable $e) {
            $logger->error("Slack scheduler tick failed [{$tag}]: " . $e->getMessage());
            $this->getIO()->error('Slack scheduler failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $line = sprintf(
            'Slack scheduler tick — completed [%s]: status=%s recipients=%d error=%s',
            $tag,
            $entry['status'],
            $entry['recipientCount'],
            $entry['error'] ?? ''
        );
        if ($entry['status'] === SlackLog::STATUS_FAILED) {
            $logger->warning($line);
            $this->getIO()->warning("Registration {$registrationId}: " . ($entry['error'] ?? 'failed'));
        } else {
            $logger->info($line);
            $this->getIO()->success("Registration {$registrationId}: {$entry['status']}.");
        }
        return self::SUCCESS;
    }

    /**
     * Fleet mode — legacy / manual path. Walks every enabled registration,
     * gating each by its own send-time window. Kept so an operator can fire
     * `bin/console orangehrm:send-slack-notifications` ad-hoc and see what's
     * due right now without touching the scheduler config.
     *
     * @param \Monolog\Logger $logger
     */
    private function runFleet($logger): int
    {
        $logger->info('Slack scheduler tick — starting (fleet mode)');

        try {
            $service = new SlackNotificationService();
            $summary = $service->dispatchDueNotifications();
        } catch (Throwable $e) {
            $logger->error('Slack scheduler tick failed: ' . $e->getMessage());
            $this->getIO()->error('Slack scheduler failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (count($summary) === 0) {
            $logger->info('Slack scheduler tick — completed: feature disabled or no due registrations');
            $this->getIO()->note('Slack notifications disabled or no active registrations');
            return self::SUCCESS;
        }

        $rows = [];
        $failed = 0;
        foreach ($summary as $registrationId => $entry) {
            $rows[] = [
                'registration' => (string)$registrationId,
                'status' => $entry['status'],
                'recipients' => (string)$entry['recipientCount'],
                'error' => $entry['error'] ?? '',
            ];
            if ($entry['status'] === SlackLog::STATUS_FAILED) {
                $failed++;
            }
        }
        $this->getIO()->table(['Registration', 'Status', 'Recipients', 'Error'], $rows);

        $summaryLine = sprintf(
            'Slack scheduler tick — completed (fleet): %d processed, %d failed',
            count($summary),
            $failed
        );
        if ($failed > 0) {
            $logger->warning($summaryLine);
            $this->getIO()->warning(sprintf('%d registration(s) failed.', $failed));
        } else {
            $logger->info($summaryLine);
            $this->getIO()->success('Slack notification run completed.');
        }
        return self::SUCCESS;
    }
}
