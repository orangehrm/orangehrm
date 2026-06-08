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

namespace OrangeHRM\Tests\WorkspaceNotifications\Command;

use OrangeHRM\Config\Config;
use OrangeHRM\WorkspaceNotifications\Command\PurgeWorkspaceNotificationLogsCommand;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group Slack
 * @group Command
 */
class PurgeWorkspaceNotificationLogsCommandTest extends KernelTestCase
{
    public function testCommandNameMatchesSchedulerEntryPoint(): void
    {
        $this->assertSame(
            'orangehrm:purge-workspace-notification-logs',
            (new PurgeWorkspaceNotificationLogsCommand())->getCommandName()
        );
    }

    public function testDefaultRetentionConstantIsThirtyDays(): void
    {
        $this->assertSame(30, PurgeWorkspaceNotificationLogsCommand::DEFAULT_RETENTION_DAYS);
    }

    public function testRunOnEmptyTableSucceedsAndReportsZeroDeleted(): void
    {
        $this->populateBaseFixture();
        $this->truncateWorkspaceNotificationLog();

        $tester = new CommandTester(new PurgeWorkspaceNotificationLogsCommand());
        $exitCode = $tester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString('0 row(s) older than', $tester->getDisplay());
    }

    public function testRunDeletesOnlyOlderRowsAtDefaultRetention(): void
    {
        $this->populateBaseFixture();
        $this->truncateWorkspaceNotificationLog();

        $this->insertLogDaysAgo(60);
        $this->insertLogDaysAgo(45);
        $this->insertLogDaysAgo(1);

        $tester = new CommandTester(new PurgeWorkspaceNotificationLogsCommand());
        $tester->execute([]);

        $this->assertSame(1, $this->countWorkspaceNotificationLog(), 'The two old rows should be deleted');
        $this->assertStringContainsString('2 row(s) older than', $tester->getDisplay());
    }

    public function testDaysZeroWipesEverything(): void
    {
        $this->populateBaseFixture();
        $this->truncateWorkspaceNotificationLog();
        $this->insertLogDaysAgo(30);
        $this->insertLogDaysAgo(1);

        $tester = new CommandTester(new PurgeWorkspaceNotificationLogsCommand());
        $tester->execute(['--days' => '0']);

        $this->assertSame(0, $this->countWorkspaceNotificationLog());
    }

    public function testNegativeDaysIsRejected(): void
    {
        $this->populateBaseFixture();
        $this->truncateWorkspaceNotificationLog();
        $this->insertLogDaysAgo(0);

        $tester = new CommandTester(new PurgeWorkspaceNotificationLogsCommand());
        $exitCode = $tester->execute(['--days' => '-5']);

        $this->assertNotSame(0, $exitCode);
        $this->assertSame(1, $this->countWorkspaceNotificationLog(), 'Bad input must NOT delete rows');
    }

    public function testRunsEvenWhenSlackToggleIsOff(): void
    {
        $this->populateBaseFixture();
        $this->truncateWorkspaceNotificationLog();
        $this->ensureSlackToggleOff();
        $this->insertLogDaysAgo(60);

        $tester = new CommandTester(new PurgeWorkspaceNotificationLogsCommand());
        $exitCode = $tester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertSame(0, $this->countWorkspaceNotificationLog());
    }

    private function populateBaseFixture(): void
    {
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR)
            . '/orangehrmWorkspaceNotificationsPlugin/test/fixtures/WorkspaceNotificationBaseFixture.yaml'
        );
    }

    private function truncateWorkspaceNotificationLog(): void
    {
        $this->getEntityManager()->getConnection()->executeStatement('DELETE FROM ohrm_workspace_notification_log');
    }

    private function insertLogDaysAgo(int $daysAgo): void
    {
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

    private function countWorkspaceNotificationLog(): int
    {
        return (int)$this->getEntityManager()->getConnection()
            ->executeQuery('SELECT COUNT(*) FROM ohrm_workspace_notification_log')
            ->fetchOne();
    }

    private function ensureSlackToggleOff(): void
    {
        $this->getEntityManager()->getConnection()->executeStatement(
            "DELETE FROM hs_hr_config WHERE `name` = 'workspace.notifications.enabled'"
        );
    }
}
