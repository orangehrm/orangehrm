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

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Console\Console;
use OrangeHRM\Framework\Console\ConsoleConfigurationInterface;
use OrangeHRM\Framework\Console\Scheduling\CommandInfo;
use OrangeHRM\Framework\Console\Scheduling\Schedule;
use OrangeHRM\Framework\Console\Scheduling\SchedulerConfigurationInterface;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Slack\Command\SendSlackNotificationsCommand;
use OrangeHRM\Slack\Service\SlackSettingsService;

class SlackPluginConfiguration implements
    PluginConfigurationInterface,
    ConsoleConfigurationInterface,
    SchedulerConfigurationInterface
{
    use ServiceContainerTrait;

    public function initialize(Request $request): void
    {
    }

    public function registerCommands(Console $console): void
    {
        $console->add(new SendSlackNotificationsCommand());
    }

    public function schedule(Schedule $schedule): void
    {
        $settings = (new SlackSettingsService())->getSettings();
        if (!$settings->isEnabled()) {
            return;
        }

        [$utcHour, $utcMinute] = $settings->getSendTimeInUtc();
        $schedule->add(new CommandInfo('orangehrm:send-slack-notifications'))
            ->cron("$utcMinute $utcHour * * *");
    }
}
