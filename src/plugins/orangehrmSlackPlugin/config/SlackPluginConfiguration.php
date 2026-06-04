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
use OrangeHRM\Entity\SlackRegistration;
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
use OrangeHRM\Slack\Command\PurgeSlackLogsCommand;
use OrangeHRM\Slack\Command\SendSlackNotificationsCommand;
use OrangeHRM\Slack\Dao\SlackLogDao;
use OrangeHRM\Slack\Service\SlackNotificationService;
use OrangeHRM\Slack\Service\SlackRegistrationService;
use OrangeHRM\Slack\Service\SlackSettingsService;
use OrangeHRM\Slack\Service\Webhook\WebhookProviderRegistry;

// NB: this file has no `namespace` declaration — it lives in the global
// namespace by OHRM plugin-config convention. That means `use Throwable;`
// would be a no-op (and emits a warning on PHP 7.4: "use statement with
// non-compound name 'Throwable' has no effect"). Don't add it; the catch
// blocks below reference `\Throwable` implicitly via global namespace.

class SlackPluginConfiguration implements
    PluginConfigurationInterface,
    ConsoleConfigurationInterface,
    SchedulerConfigurationInterface
{
    use ServiceContainerTrait;

    /**
     * Services are registered as singletons in the DI container so consumers
     * (APIs, models, command) reuse the same instance — important for the
     * registration model whose `toArray()` runs once per row in collection
     * responses and would otherwise instantiate the service N times.
     *
     * The {@see WebhookProviderRegistry} self-bootstraps with `SlackWebhookProvider`
     * baked in. Adding another webhook provider in a future release is a single
     * `$registry->register(new XyzWebhookProvider())` call here.
     */
    public function initialize(Request $request): void
    {
        $this->getContainer()->register(Services::SLACK_SETTINGS_SERVICE, SlackSettingsService::class);
        $this->getContainer()->register(Services::SLACK_REGISTRATION_SERVICE, SlackRegistrationService::class);
        $this->getContainer()->register(Services::SLACK_NOTIFICATION_SERVICE, SlackNotificationService::class);
        $this->getContainer()->register(Services::WEBHOOK_PROVIDER_REGISTRY, WebhookProviderRegistry::class);
        $this->getContainer()->register(Services::SLACK_LOG_DAO, SlackLogDao::class);
    }

    public function registerCommands(Console $console): void
    {
        $console->add(new SendSlackNotificationsCommand());
        $console->add(new PurgeSlackLogsCommand());
    }

    /**
     * Register one scheduled task per enabled registration. Modelled on the
     * LDAP plugin's pattern (which registers a single hardcoded task) but
     * loops the rows here — every active registration gets its own cron
     * expression derived from `dailySendTime` (HH:MM) and runs in the row's
     * own `timezone` via Crunz's per-task timezone support. The plugin's
     * global enable flag short-circuits the whole loop.
     *
     * Each task invokes:
     *   orangehrm:send-slack-notifications --registration-id={id} --event-type={type}
     *
     * which lands in {@see SendSlackNotificationsCommand::runPerRow()} →
     * {@see SlackNotificationService::dispatchSingleRegistration()}. The
     * command re-loads its services per the "re-load the needed services
     * inside the command" guidance, so each tick is independent.
     */
    public function schedule(Schedule $schedule): void
    {
        // Log purge runs *unconditionally* — independent of the global enable
        // flag and independent of whether any registrations exist. An admin
        // who disables Slack still wants the table to bound itself; a fleet
        // with zero rows still needs orphan-row cleanup. Daily at 02:00 UTC
        // (server time) — low-traffic window for most timezones, well clear
        // of the 09:00-local sends most rows fire at.
        $schedule->add(new CommandInfo('orangehrm:purge-slack-logs'))
            ->cron('0 2 * * *');

        if (!(new SlackSettingsService())->isEnabled()) {
            return;
        }

        $registrationService = new SlackRegistrationService();
        foreach ($registrationService->listActiveRegistrations() as $registration) {
            try {
                $this->scheduleOne($schedule, $registration);
            } catch (Throwable $e) {
                // One bad row must not poison the whole scheduler — log and
                // continue. Crunz won't see the task, but the next 5-min
                // run-schedule cycle re-evaluates and may pick it up if the
                // admin fixes the row.
                LoggerFactory::getLogger('slack')->error(sprintf(
                    'Failed to schedule slack registration %d: %s',
                    (int)$registration->getId(),
                    $e->getMessage()
                ));
            }
        }
    }

    private function scheduleOne(Schedule $schedule, SlackRegistration $registration): void
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

        // Daily cron at the row's HH:MM. Spec: "It always runs daily; only the
        // time is configurable" — so we hardcode "every day of every month".
        $cron = sprintf('%d %d * * *', $minute, $hour);

        $info = new CommandInfo(
            'orangehrm:send-slack-notifications',
            new ArrayInput([
                '--' . SendSlackNotificationsCommand::OPT_REGISTRATION_ID => (string)$registration->getId(),
                '--' . SendSlackNotificationsCommand::OPT_EVENT_TYPE => $registration->getEventType(),
            ])
        );

        // Crunz evaluates the cron expression in the timezone passed on the
        // task. Setting the row's own zone here means a row configured for
        // "09:00 Asia/Colombo" fires when it's 09:00 in Colombo, regardless
        // of what timezone the server (or run-schedule's UTC default) uses.
        $schedule->add($info)
            ->cron($cron)
            ->timezone($registration->getTimezone());
    }
}
