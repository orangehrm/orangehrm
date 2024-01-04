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

use OrangeHRM\Authentication\Auth\AuthProviderChain;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Console\Console;
use OrangeHRM\Framework\Console\ConsoleConfigurationInterface;
use OrangeHRM\Framework\Console\Scheduling\CommandInfo;
use OrangeHRM\Framework\Console\Scheduling\Schedule;
use OrangeHRM\Framework\Console\Scheduling\SchedulerConfigurationInterface;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Logger\LoggerFactory;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;
use OrangeHRM\LDAP\Auth\LDAPAuthProvider;
use OrangeHRM\LDAP\Command\LDAPSyncUserCommand;
use OrangeHRM\LDAP\Dto\LDAPSetting;

class LDAPAuthenticationPluginConfiguration implements
    PluginConfigurationInterface,
    ConsoleConfigurationInterface,
    SchedulerConfigurationInterface
{
    use ServiceContainerTrait;
    use ConfigServiceTrait;

    /**
     * @inheritDoc
     */
    public function initialize(Request $request): void
    {
        $this->getContainer()->register(Services::LDAP_LOGGER)
            ->setFactory([LoggerFactory::class, 'getLogger'])
            ->addArgument('LDAP')
            ->addArgument('ldap.log');
        $ldapSettings = $this->getConfigService()->getLDAPSetting();
        if ($ldapSettings instanceof LDAPSetting && $ldapSettings->isEnable()) {
            /** @var AuthProviderChain $authProviderChain */
            $authProviderChain = $this->getContainer()->get(Services::AUTH_PROVIDER_CHAIN);
            $authProviderChain->addProvider(new LDAPAuthProvider());
        }
    }

    /**
     * @inheritDoc
     */
    public function registerCommands(Console $console): void
    {
        $console->add(new LDAPSyncUserCommand());
    }

    /**
     * @inheritDoc
     */
    public function schedule(Schedule $schedule): void
    {
        $ldapSettings = $this->getConfigService()->getLDAPSetting();
        if ($ldapSettings instanceof LDAPSetting && $ldapSettings->isEnable()) {
            $interval = 1;
            if ($ldapSettings->getSyncInterval() <= 23 && $ldapSettings->getSyncInterval() >= 1) {
                $interval = $ldapSettings->getSyncInterval();
            }

            $schedule->add(new CommandInfo('orangehrm:ldap-sync-user'))
                ->cron("0 */$interval * * *");
        }
    }
}
