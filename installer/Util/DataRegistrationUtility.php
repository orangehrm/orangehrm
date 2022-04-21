<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Installer\Util;

use DateTime;
use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Installer\Util\Services\DataRegistrationService;
use OrangeHRM\Installer\Util\SystemConfigs\SystemConfigurations;

class DataRegistrationUtility
{
    public const REGISTRATION_TYPE_UPGRADER_STARTED = 4;
    public const REGISTRATION_TYPE_UPGRADER_SUCCESS = 3;

    public const NOT_PUBLISHED = 0;
    public const PUBLISHED = 1;

    public const IS_INITIAL_REG_DATA_SENT = 'isInitialRegDataSent';

    private SystemConfigurations $systemConfigurations;
    private DataRegistrationService $dataRegistrationService;

    public function __construct()
    {
        $this->systemConfigurations = new SystemConfigurations();
        $this->dataRegistrationService = new DataRegistrationService();
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    public function getInitialRegistrationData(): array
    {
        $organizationName = $this->systemConfigurations->getOrganizationName();
        $country = $this->systemConfigurations->getCountry();
        $language = $this->systemConfigurations->getLanguage();
        $adminFirstName = $this->systemConfigurations->getAdminFirstName();
        $adminLastName = $this->systemConfigurations->getAdminLastName();
        $adminEmail = $this->systemConfigurations->getAdminEmail();
        $adminContactNumber = $this->systemConfigurations->getAdminContactNumber();
        $adminUserName = $this->systemConfigurations->getAdminUserName();
        $dateTime = new DateTime();
        $currentTimestamp = $dateTime->getTimestamp();

        $this->systemConfigurations->updateInitialRegistrationEventQueue(4, 1);
        $instanceIdentifier = $this->setInstanceIdentifier(
            $adminFirstName,
            $adminLastName,
            $organizationName,
            $adminEmail,
            $country,
            $currentTimestamp
        );
        $this->setInstanceIdentifierChecksum(
            $adminFirstName,
            $adminLastName,
            $organizationName,
            $adminEmail,
            $country,
            $currentTimestamp
        );
        return [
            $organizationName,
            $country,
            $language,
            $adminFirstName,
            $adminLastName,
            $adminEmail,
            $adminContactNumber,
            $adminUserName,
            $instanceIdentifier
        ];
    }

    /**
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $organizationName
     * @param string $organizationEmail
     * @param string $country
     * @param int $currentTimestamp
     * @return string
     * @throws Exception
     */
    protected function setInstanceIdentifier(
        string $adminFirstName,
        string $adminLastName,
        string $organizationName,
        string $organizationEmail,
        string $country,
        int $currentTimestamp
    ): string {
        if (is_null($this->systemConfigurations->getInstanceIdentifier())) {
            $this->systemConfigurations->setInstanceIdentifier(
                $organizationName,
                $organizationEmail,
                $adminFirstName,
                $adminLastName,
                $_SERVER['HTTP_HOST'],
                $country,
                Config::PRODUCT_VERSION,
                $currentTimestamp
            );
        }
        return $this->systemConfigurations->getInstanceIdentifier();
    }

    /**
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $organizationName
     * @param string $organizationEmail
     * @param string $country
     * @param int $currentTimestamp
     * @return string
     * @throws Exception
     */
    protected function setInstanceIdentifierChecksum(
        string $adminFirstName,
        string $adminLastName,
        string $organizationName,
        string $organizationEmail,
        string $country,
        int $currentTimestamp
    ): string {
        if (is_null($this->systemConfigurations->getInstanceIdentifierChecksum())) {
            $this->systemConfigurations->setInstanceIdentifierChecksum(
                $organizationName,
                $organizationEmail,
                $adminFirstName,
                $adminLastName,
                $_SERVER['HTTP_HOST'],
                $country,
                Config::PRODUCT_VERSION,
                $currentTimestamp
            );
        }
        return $this->systemConfigurations->getInstanceIdentifierChecksum();
    }

    public function sendRegistrationDataOnFailure()
    {
        $this->systemConfigurations->setInitialRegistrationEventQueue(
            self::REGISTRATION_TYPE_UPGRADER_STARTED,
            self::NOT_PUBLISHED
        );
        list(
            $organizationName,
            $country,
            $language,
            $adminFirstName,
            $adminLastName,
            $adminEmail,
            $adminContactNumber,
            $adminUserName,
            $instanceIdentifier
            ) = $this->getInitialRegistrationData();

        $result = $this->dataRegistrationService->sendInitialRegistrationData(
            $adminUserName,
            $adminEmail,
            $adminContactNumber,
            $adminFirstName,
            $adminLastName,
            SystemConfigurations::NOT_CAPTURED,
            $language,
            $country,
            $organizationName,
            self::REGISTRATION_TYPE_UPGRADER_STARTED,
            $instanceIdentifier
        );

        if ($result) {
            $this->systemConfigurations->updateInitialRegistrationEventQueue(
                self::REGISTRATION_TYPE_UPGRADER_STARTED,
                self::PUBLISHED
            );
            StateContainer::getInstance()->removeAttribute(
                DataRegistrationUtility::IS_INITIAL_REG_DATA_SENT
            );
        }
    }
}
