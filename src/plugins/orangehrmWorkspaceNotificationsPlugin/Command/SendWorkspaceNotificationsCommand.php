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

namespace OrangeHRM\WorkspaceNotifications\Command;

use OrangeHRM\Entity\WorkspaceNotificationLog;
use OrangeHRM\Framework\Console\Command;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class SendWorkspaceNotificationsCommand extends Command
{
    public const OPT_REGISTRATION_ID = 'registration-id';
    public const OPT_EVENT_TYPE = 'event-type';

    public function getCommandName(): string
    {
        return 'orangehrm:send-workspace-notifications';
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
            'Informational tag — surfaces in workspace_notifications.log alongside the registration id. The canonical event type is read from the row itself.'
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
     * @param \Monolog\Logger $logger
     */
    private function runPerRow(int $registrationId, ?string $eventType, $logger): int
    {
        $tag = sprintf('id=%d%s', $registrationId, $eventType ? " event={$eventType}" : '');
        $logger->info("Workspace notification scheduler tick — starting [{$tag}]");

        try {
            $service = new WorkspaceNotificationService();
            $entry = $service->dispatchSingleRegistration($registrationId);
        } catch (Throwable $e) {
            $logger->error("Workspace notification scheduler tick failed [{$tag}]: " . $e->getMessage());
            $this->getIO()->error('Workspace notification scheduler failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $line = sprintf(
            'Workspace notification scheduler tick — completed [%s]: status=%s recipients=%d error=%s',
            $tag,
            $entry['status'],
            $entry['recipientCount'],
            $entry['error'] ?? ''
        );
        if ($entry['status'] === WorkspaceNotificationLog::STATUS_FAILED) {
            $logger->warning($line);
            $this->getIO()->warning("Registration {$registrationId}: " . ($entry['error'] ?? 'failed'));
        } else {
            $logger->info($line);
            $this->getIO()->success("Registration {$registrationId}: {$entry['status']}.");
        }
        return self::SUCCESS;
    }

    /**
     * @param \Monolog\Logger $logger
     */
    private function runFleet($logger): int
    {
        $logger->info('Workspace notification scheduler tick — starting (fleet mode)');

        try {
            $service = new WorkspaceNotificationService();
            $summary = $service->dispatchDueNotifications();
        } catch (Throwable $e) {
            $logger->error('Workspace notification scheduler tick failed: ' . $e->getMessage());
            $this->getIO()->error('Workspace notification scheduler failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (count($summary) === 0) {
            $logger->info('Workspace notification scheduler tick — completed: feature disabled or no due registrations');
            $this->getIO()->note('Workspace notifications disabled or no active registrations');
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
            if ($entry['status'] === WorkspaceNotificationLog::STATUS_FAILED) {
                $failed++;
            }
        }
        $this->getIO()->table(['Registration', 'Status', 'Recipients', 'Error'], $rows);

        $summaryLine = sprintf(
            'Workspace notification scheduler tick — completed (fleet): %d processed, %d failed',
            count($summary),
            $failed
        );
        if ($failed > 0) {
            $logger->warning($summaryLine);
            $this->getIO()->warning(sprintf('%d registration(s) failed.', $failed));
        } else {
            $logger->info($summaryLine);
            $this->getIO()->success('Workspace notification run completed.');
        }
        return self::SUCCESS;
    }
}
