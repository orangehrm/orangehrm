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
use OrangeHRM\Slack\Command\SendSlackNotificationsCommand;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * The command is a thin wrapper that calls SlackNotificationService::dispatchDueNotifications
 * and prints a summary. The orchestrator's branching is exhaustively covered by
 * SlackNotificationServiceTest with mocks. What this test pins is the
 * command-level wiring:
 *
 *   - getCommandName() matches the cron / scheduler entry point
 *   - SUCCESS exit code when the toggle is off (the most common steady-state run)
 *   - the "disabled or no active registrations" note appears when the service
 *     returns an empty summary — important because that note is what oncall reads
 *     to confirm the cron is actually running.
 *
 * @group Slack
 * @group Command
 */
class SendSlackNotificationsCommandTest extends TestCase
{
    public function testCommandNameMatchesSchedulerEntryPoint(): void
    {
        // The cron / queue runner targets this name verbatim. Renaming the
        // command without updating the scheduler would silently break delivery.
        $this->assertSame(
            'orangehrm:send-slack-notifications',
            (new SendSlackNotificationsCommand())->getCommandName()
        );
    }

    public function testRunWithFeatureDisabledReturnsSuccessAndPrintsNote(): void
    {
        // The Slack tables ship in a V5_9_0 migration that the test-DB dump
        // pre-dates; populate the base fixture so TestDataService schema-syncs
        // them. Then nuke any rows so we hit the disabled-or-empty branch.
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmSlackPlugin/test/fixtures/SlackBaseFixture.yaml';
        TestDataService::populate($fixture);
        $this->ensureSlackEnabledRowAbsent();

        $tester = new CommandTester(new SendSlackNotificationsCommand());
        $exitCode = $tester->execute([]);

        $this->assertSame(0, $exitCode);
        $this->assertStringContainsString(
            'Slack notifications disabled or no active registrations',
            $tester->getDisplay()
        );
    }

    private function ensureSlackEnabledRowAbsent(): void
    {
        // Belt-and-braces: nuke the row if any earlier test created it.
        $this->getEntityManager()->getConnection()->executeStatement(
            "DELETE FROM hs_hr_config WHERE `name` = 'slack.notifications.enabled'"
        );
        // Also clear registrations so the alternate path ("no active rows") is
        // dependably the one taken.
        $this->getEntityManager()->getConnection()->executeStatement(
            'DELETE FROM ohrm_slack_log'
        );
        $this->getEntityManager()->getConnection()->executeStatement(
            'DELETE FROM ohrm_slack_registration_subunit'
        );
        $this->getEntityManager()->getConnection()->executeStatement(
            'DELETE FROM ohrm_slack_registration'
        );
    }
}
