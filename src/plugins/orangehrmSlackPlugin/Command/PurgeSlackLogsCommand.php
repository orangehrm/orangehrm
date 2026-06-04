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

use DateTime;
use DateTimeZone;
use OrangeHRM\Framework\Console\Command;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\Slack\Dao\SlackLogDao;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Daily purge of stale rows from `ohrm_slack_log`. Retention is 30 days by
 * default (the spec for B-4); the `--days` option lets an operator override
 * for ad-hoc runs (`--days=7` to clean up after a noisy dev push, `--days=0`
 * to wipe everything for a reset).
 *
 * The scheduler hook in {@see \SlackPluginConfiguration::schedule()} registers
 * this to fire once per day. Runs independently of the Slack global enable
 * flag — if an admin disables Slack notifications, old log rows should still
 * expire so the table doesn't accumulate forever.
 */
class PurgeSlackLogsCommand extends Command
{
    public const OPT_DAYS = 'days';
    public const DEFAULT_RETENTION_DAYS = 30;

    public function getCommandName(): string
    {
        return 'orangehrm:purge-slack-logs';
    }

    protected function configure(): void
    {
        $this->setDescription(
            'Delete ohrm_slack_log rows older than the retention window (default 30 days).'
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

        // Cutoff is computed in UTC because `slack_log.created_at` is a UTC
        // datetime (no timezone column on the row). Using UTC here keeps the
        // boundary consistent across DST shifts and across server moves.
        $cutoff = (new DateTime('now', new DateTimeZone('UTC')))
            ->modify("-{$days} days");

        try {
            $deleted = (new SlackLogDao())->purgeOlderThan($cutoff);
        } catch (Throwable $e) {
            $logger->error('Slack log purge failed: ' . $e->getMessage());
            $this->getIO()->error('Failed to purge slack log: ' . $e->getMessage());
            return self::FAILURE;
        }

        $line = sprintf(
            'Slack log purge — %d row(s) older than %s deleted',
            $deleted,
            $cutoff->format('Y-m-d H:i:s \U\T\C')
        );
        $logger->info($line);
        $this->getIO()->success($line);
        return self::SUCCESS;
    }
}
