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

namespace OrangeHRM\Installer\Util;

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Installer\Util\Service\DataRegistrationService;
use OrangeHRM\Installer\Util\SystemConfig\SystemConfiguration;

class DataRegistrationUtility
{
    public const REGISTRATION_TYPE_INSTALLER_STARTED = 0;
    public const REGISTRATION_TYPE_UPGRADER_STARTED = 4;
    public const REGISTRATION_TYPE_SUCCESS = 3;

    private SystemConfiguration $systemConfiguration;
    private DataRegistrationService $dataRegistrationService;
    private SystemCheck $systemCheck;

    public function __construct()
    {
        $this->systemConfiguration = new SystemConfiguration();
        $this->dataRegistrationService = new DataRegistrationService();
        $this->systemCheck = new SystemCheck();
    }

    /**
     * @return array
     */
    private function getInitialRegistrationData(): array
    {
        $organizationName = $this->systemConfiguration->getOrganizationName();
        $country = $this->systemConfiguration->getCountry();
        $language = $this->systemConfiguration->getLanguage();
        $adminFirstName = $this->systemConfiguration->getAdminFirstName();
        $adminLastName = $this->systemConfiguration->getAdminLastName();
        $adminEmail = $this->systemConfiguration->getAdminEmail();
        $adminContactNumber = $this->systemConfiguration->getAdminContactNumber();
        $adminUserName = $this->systemConfiguration->getAdminUserName();
        $dateTime = new DateTime();
        $currentTimestamp = $dateTime->getTimestamp();

        $instanceIdentifier = $this->setInstanceIdentifier(
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
     * @return array
     */
    private function getInstallerInitialRegistrationData(): array
    {
        $adminUserData = StateContainer::getInstance()->getAdminUserData();
        $instanceData = StateContainer::getInstance()->getInstanceData();
        $instanceIdentifierData = StateContainer::getInstance()->getInstanceIdentifierData();

        return [
            $instanceData[StateContainer::INSTANCE_ORG_NAME],
            $instanceData[StateContainer::INSTANCE_COUNTRY_CODE],
            $instanceData[StateContainer::INSTANCE_LANG_CODE] ?? SystemConfiguration::DEFAULT_LANGUAGE,
            $adminUserData[StateContainer::ADMIN_FIRST_NAME],
            $adminUserData[StateContainer::ADMIN_LAST_NAME],
            $adminUserData[StateContainer::ADMIN_EMAIL],
            $adminUserData[StateContainer::ADMIN_CONTACT],
            $adminUserData[StateContainer::ADMIN_USERNAME],
            $instanceIdentifierData[StateContainer::INSTANCE_IDENTIFIER]
        ];
    }

    /**
     * If the uniqueIdentifier is null, that means the method is for upgrader and data is fetched from the database
     * else the method is for installer and data is fetched from the session
     * @param string $type
     * @param string|null $uniqueIdentifier
     * @return array
     */
    public function getInitialRegistrationDataBody(string $type, ?string $uniqueIdentifier = null): array
    {
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
        ) = is_null($uniqueIdentifier) ?
            $this->getInitialRegistrationData() :
            $this->getInstallerInitialRegistrationData();

        return [
            'username' => $adminUserName,
            'email' => $adminEmail,
            'telephone' => $adminContactNumber,
            'admin_first_name' => $adminFirstName,
            'admin_last_name' => $adminLastName,
            'timezone' => SystemConfiguration::NOT_CAPTURED,
            'language' => $language,
            'country' => $country,
            'organization_name' => $organizationName,
            'type' => $type,
            'instance_identifier' => $instanceIdentifier,
            'system_details' => json_encode($this->systemCheck->getSystemDetails())
        ];
    }

    /**
     * @return array
     */
    public function getSuccessRegistrationDataBody(): array
    {
        return [
            'instance_identifier' => $this->systemConfiguration->getInstanceIdentifier(),
            'type' => self::REGISTRATION_TYPE_SUCCESS,
            'system_details' => json_encode($this->systemCheck->getSystemDetails()),
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
     */
    protected function setInstanceIdentifier(
        string $adminFirstName,
        string $adminLastName,
        string $organizationName,
        string $organizationEmail,
        string $country,
        int $currentTimestamp
    ): string {
        if (is_null($this->systemConfiguration->getInstanceIdentifier())) {
            $this->systemConfiguration->setInstanceIdentifier(
                $organizationName,
                $organizationEmail,
                $adminFirstName,
                $adminLastName,
                $_SERVER['HTTP_HOST'] ?? null,
                $country,
                Config::PRODUCT_VERSION,
                $currentTimestamp
            );
        }
        return $this->systemConfiguration->getInstanceIdentifier();
    }

    public function sendRegistrationDataOnSuccess(): void
    {
        $successRegistrationDataBody = $this->getSuccessRegistrationDataBody();
        $published = $this->dataRegistrationService->sendRegistrationData($successRegistrationDataBody);

        $this->systemConfiguration->saveRegistrationEvent(
            self::REGISTRATION_TYPE_SUCCESS,
            $published,
            json_encode($successRegistrationDataBody)
        );
    }
}
