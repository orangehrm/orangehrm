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
use OrangeHRM\WorkspaceNotifications\Command\SendWorkspaceNotificationsCommand;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group Slack
 * @group Command
 */
class SendWorkspaceNotificationsCommandTest extends TestCase
{
    public function testCommandNameMatchesSchedulerEntryPoint(): void
    {
        $this->assertSame(
            'orangehrm:send-workspace-notifications',
            (new SendWorkspaceNotificationsCommand())->getCommandName()
        );
    }

    public function testRunWithFeatureDisabledReturnsSuccessAndPrintsNote(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmWorkspaceNotificationsPlugin/test/fixtures/WorkspaceNotificationBaseFixture.yaml';
        TestDataService::populate($fixture);
        $this->ensureSlackEnabledRowAbsent();

        $tester = new CommandTester(new SendWorkspaceNotificationsCommand());
        $exitCode = $tester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString(
            'Workspace notifications disabled or no active registrations',
            $tester->getDisplay()
        );
    }

    private function ensureSlackEnabledRowAbsent(): void
    {
        $this->getEntityManager()->getConnection()->executeStatement(
            "DELETE FROM hs_hr_config WHERE `name` = 'workspace.notifications.enabled'"
        );
        $this->getEntityManager()->getConnection()->executeStatement(
            'DELETE FROM ohrm_workspace_notification_log'
        );
        $this->getEntityManager()->getConnection()->executeStatement(
            'DELETE FROM ohrm_workspace_notification_registration_subunit'
        );
        $this->getEntityManager()->getConnection()->executeStatement(
            'DELETE FROM ohrm_workspace_notification_registration'
        );
    }
}
