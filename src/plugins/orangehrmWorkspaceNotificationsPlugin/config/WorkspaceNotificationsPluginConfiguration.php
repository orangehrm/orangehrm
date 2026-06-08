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
use OrangeHRM\Entity\WorkspaceNotificationRegistration;
use OrangeHRM\Framework\Console\ArrayInput;
use OrangeHRM\Framework\Console\Console;
use OrangeHRM\Framework\Console\ConsoleConfigurationInterface;
use OrangeHRM\Framework\Console\Scheduling\CommandInfo;
use OrangeHRM\Framework\Console\Scheduling\Schedule;
use OrangeHRM\Framework\Console\Scheduling\SchedulerConfigurationInterface;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;
use OrangeHRM\WorkspaceNotifications\Command\PurgeWorkspaceNotificationLogsCommand;
use OrangeHRM\WorkspaceNotifications\Command\SendWorkspaceNotificationsCommand;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationService;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationRegistrationService;
use OrangeHRM\WorkspaceNotifications\Service\WorkspaceNotificationSettingsService;
use OrangeHRM\WorkspaceNotifications\Service\Webhook\WebhookProviderRegistry;

class WorkspaceNotificationsPluginConfiguration implements
    PluginConfigurationInterface,
    ConsoleConfigurationInterface,
    SchedulerConfigurationInterface
{
    use ServiceContainerTrait;

    public function initialize(Request $request): void
    {
        $this->getContainer()->register(Services::WORKSPACE_NOTIFICATION_SETTINGS_SERVICE, WorkspaceNotificationSettingsService::class);
        $this->getContainer()->register(Services::WORKSPACE_NOTIFICATION_REGISTRATION_SERVICE, WorkspaceNotificationRegistrationService::class);
        $this->getContainer()->register(Services::WORKSPACE_NOTIFICATION_SERVICE, WorkspaceNotificationService::class);
        $this->getContainer()->register(Services::WEBHOOK_PROVIDER_REGISTRY, WebhookProviderRegistry::class);
    }

    public function registerCommands(Console $console): void
    {
        $console->add(new SendWorkspaceNotificationsCommand());
        $console->add(new PurgeWorkspaceNotificationLogsCommand());
    }

    public function schedule(Schedule $schedule): void
    {
        $schedule->add(new CommandInfo('orangehrm:purge-workspace-notification-logs'))
            ->cron('0 2 * * *');

        if (!(new WorkspaceNotificationSettingsService())->isEnabled()) {
            return;
        }

        $registrationService = new WorkspaceNotificationRegistrationService();
        foreach ($registrationService->listActiveRegistrations() as $registration) {
            try {
                $this->scheduleOne($schedule, $registration);
            } catch (Throwable $e) {
                LoggerFactory::getLogger('slack')->error(sprintf(
                    'Failed to schedule workspace-notification registration %d: %s',
                    (int)$registration->getId(),
                    $e->getMessage()
                ));
            }
        }
    }

    private function scheduleOne(Schedule $schedule, WorkspaceNotificationRegistration $registration): void
    {
        $sendTime = $registration->getDailySendTime();
        $parts = explode(':', $sendTime, 2);
        if (count($parts) !== 2) {
            throw new \RuntimeException("Invalid dailySendTime '{$sendTime}'");
        }
        $hour = (int)$parts[0];
        $minute = (int)$parts[1];
        if ($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            throw new \RuntimeException("Out-of-range dailySendTime '{$sendTime}'");
        }

        $cron = sprintf('%d %d * * *', $minute, $hour);

        $info = new CommandInfo(
            'orangehrm:send-workspace-notifications',
            new ArrayInput([
                '--' . SendWorkspaceNotificationsCommand::OPT_REGISTRATION_ID => (string)$registration->getId(),
                '--' . SendWorkspaceNotificationsCommand::OPT_EVENT_TYPE => $registration->getEventType(),
            ])
        );

        $schedule->add($info)
            ->cron($cron)
            ->timezone($registration->getTimezone());
    }
}
