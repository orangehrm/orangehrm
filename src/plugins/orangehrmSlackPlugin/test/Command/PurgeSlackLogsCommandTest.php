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

namespace OrangeHRM\Tests\Slack\Command;

use OrangeHRM\Config\Config;
use OrangeHRM\Slack\Command\PurgeSlackLogsCommand;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Pins B-4 (30-day log retention + auto-delete):
 *
 *   - The command exposes the exact name the scheduler hooks (cron drift
 *     would silently break retention).
 *   - The default retention window is 30 days — the spec value. Pinning the
 *     constant guards against an "optimisation" PR that quietly drops it
 *     to e.g. 7 days, which would silently delete the prior 23 days of
 *     audit history on the next run.
 *   - `--days=N` overrides the window and deletes EVERY row when N=0
 *     (a useful operator escape hatch).
 *   - The DELETE runs even when the global slack-enabled toggle is off —
 *     a disabled feature still needs its table bounded.
 *   - The output reports the deleted count so ops can grep the slack.log
 *     for "row(s) older than … deleted".
 *
 * @group Slack
 * @group Command
 */
class PurgeSlackLogsCommandTest extends KernelTestCase
{
    public function testCommandNameMatchesSchedulerEntryPoint(): void
    {
        $this->assertSame(
            'orangehrm:purge-slack-logs',
            (new PurgeSlackLogsCommand())->getCommandName()
        );
    }

    public function testDefaultRetentionConstantIsThirtyDays(): void
    {
        // The 30-day window is contract per the B-4 spec, not an arbitrary
        // tuning knob. A change here is a meaningful policy change and
        // should not happen incidentally.
        $this->assertSame(30, PurgeSlackLogsCommand::DEFAULT_RETENTION_DAYS);
    }

    public function testRunOnEmptyTableSucceedsAndReportsZeroDeleted(): void
    {
        $this->populateBaseFixture();
        $this->truncateSlackLog();

        $tester = new CommandTester(new PurgeSlackLogsCommand());
        $exitCode = $tester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('0 row(s) older than', $tester->getDisplay());
    }

    public function testRunDeletesOnlyOlderRowsAtDefaultRetention(): void
    {
        $this->populateBaseFixture();
        $this->truncateSlackLog();

        // Insert relative to NOW so the test isn't sensitive to system clock —
        // the DAO uses `now() - 30d` as the cutoff, and we want rows that are
        // unambiguously on each side of that line (>= 1 day of headroom).
        // The "boundary at 30 days exactly" case is covered by SlackLogDaoTest
        // where the cutoff can be mocked precisely; here we just pin the
        // command-level "old goes, new stays" contract.
        $this->insertLogDaysAgo(60); // well-past — must be deleted
        $this->insertLogDaysAgo(45); // well-past — must be deleted
        $this->insertLogDaysAgo(1);  // recent — must be kept

        $tester = new CommandTester(new PurgeSlackLogsCommand());
        $tester->execute([]);

        $this->assertSame(1, $this->countSlackLog(), 'The two old rows should be deleted');
        $this->assertStringContainsString('2 row(s) older than', $tester->getDisplay());
    }

    public function testDaysZeroWipesEverything(): void
    {
        // `--days=0` makes cutoff = now, so every row with created_at < now
        // gets purged. Useful for resetting after noisy test/dev runs.
        $this->populateBaseFixture();
        $this->truncateSlackLog();
        $this->insertLogDaysAgo(30);
        $this->insertLogDaysAgo(1);

        $tester = new CommandTester(new PurgeSlackLogsCommand());
        $tester->execute(['--days' => '0']);

        $this->assertSame(0, $this->countSlackLog());
    }

    public function testNegativeDaysIsRejected(): void
    {
        // Belt-and-braces: a future-dated cutoff would delete every row
        // including freshly-inserted ones. Refuse rather than silently DELETE
        // everything when an operator passes `--days=-1`.
        $this->populateBaseFixture();
        $this->truncateSlackLog();
        $this->insertLogDaysAgo(0);

        $tester = new CommandTester(new PurgeSlackLogsCommand());
        $exitCode = $tester->execute(['--days' => '-5']);

        $this->assertNotSame(0, $exitCode);
        $this->assertSame(1, $this->countSlackLog(), 'Bad input must NOT delete rows');
    }

    public function testRunsEvenWhenSlackToggleIsOff(): void
    {
        // The whole point of B-4 is that retention is feature-independent —
        // a disabled Slack plugin still has stale rows that need cleanup.
        $this->populateBaseFixture();
        $this->truncateSlackLog();
        $this->ensureSlackToggleOff();
        $this->insertLogDaysAgo(60); // way older than 30 days

        $tester = new CommandTester(new PurgeSlackLogsCommand());
        $exitCode = $tester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertSame(0, $this->countSlackLog());
    }

    /* ─────────────────────────── Helpers ─────────────────────────────────────── */

    private function populateBaseFixture(): void
    {
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR)
            . '/orangehrmSlackPlugin/test/fixtures/SlackBaseFixture.yaml'
        );
    }

    private function truncateSlackLog(): void
    {
        $this->getEntityManager()->getConnection()->executeStatement('DELETE FROM ohrm_workspace_notification_log');
    }

    private function insertLogDaysAgo(int $daysAgo): void
    {
        // Insert a row with created_at = NOW - $daysAgo days, so the test
        // doesn't depend on what calendar date the test happens to run.
        $ts = (new \DateTime('now', new \DateTimeZone('UTC')))
            ->modify("-{$daysAgo} days");
        $this->getEntityManager()->getConnection()->executeStatement(
            'INSERT INTO ohrm_workspace_notification_log (event_type, event_date, status, recipient_count, created_at) '
            . 'VALUES (:eventType, :eventDate, :status, :count, :createdAt)',
            [
                'eventType' => 'BIRTHDAY',
                'eventDate' => $ts->format('Y-m-d'),
                'status' => 'SUCCESS',
                'count' => 0,
                'createdAt' => $ts->format('Y-m-d H:i:s'),
            ]
        );
    }

    private function countSlackLog(): int
    {
        return (int)$this->getEntityManager()->getConnection()
            ->executeQuery('SELECT COUNT(*) FROM ohrm_workspace_notification_log')
            ->fetchOne();
    }

    private function ensureSlackToggleOff(): void
    {
        $this->getEntityManager()->getConnection()->executeStatement(
            "DELETE FROM hs_hr_config WHERE `name` = 'slack.notifications.enabled'"
        );
    }
}
