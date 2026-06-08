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

use DateTime;
use DateTimeZone;
use OrangeHRM\Framework\Console\Command;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\WorkspaceNotifications\Dao\WorkspaceNotificationLogDao;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class PurgeWorkspaceNotificationLogsCommand extends Command
{
    public const OPT_DAYS = 'days';
    public const DEFAULT_RETENTION_DAYS = 30;

    public function getCommandName(): string
    {
        return 'orangehrm:purge-workspace-notification-logs';
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Delete ohrm_workspace_notification_log rows older than the retention window (default 30 days).'
        );
        $this->addOption(
            self::OPT_DAYS,
            null,
            InputOption::VALUE_REQUIRED,
            'Retention window in days. Rows with created_at < (now - N days) are deleted.',
            (string)self::DEFAULT_RETENTION_DAYS
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logger = LoggerFactory::getLogger('slack');
        $days = (int)$input->getOption(self::OPT_DAYS);
        if ($days < 0) {
            $this->getIO()->error('--days must be a non-negative integer.');
            return self::FAILURE;
        }

        $cutoff = (new DateTime('now', new DateTimeZone('UTC')))
            ->modify("-{$days} days");

        try {
            $deleted = (new WorkspaceNotificationLogDao())->purgeOlderThan($cutoff);
        } catch (Throwable $e) {
            $logger->error('Workspace notification log purge failed: ' . $e->getMessage());
            $this->getIO()->error('Failed to purge workspace notification log: ' . $e->getMessage());
            return self::FAILURE;
        }

        $line = sprintf(
            'Workspace notification log purge — %d row(s) older than %s deleted',
            $deleted,
            $cutoff->format('Y-m-d H:i:s \U\T\C')
        );
        $logger->info($line);
        $this->getIO()->success($line);
        return self::SUCCESS;
    }
}
