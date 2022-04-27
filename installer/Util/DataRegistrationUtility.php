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
use GuzzleHttp\Exception\GuzzleException;
use OrangeHRM\Config\Config;
use OrangeHRM\Installer\Util\Service\DataRegistrationService;
use OrangeHRM\Installer\Util\SystemConfig\SystemConfiguration;

class DataRegistrationUtility
{
    public const REGISTRATION_TYPE_INSTALLER_STARTED = 0;
    public const REGISTRATION_TYPE_UPGRADER_STARTED = 4;
    public const REGISTRATION_TYPE_SUCCESS = 3;

    public const NOT_PUBLISHED = 0;
    public const PUBLISHED = 1;

    public const IS_INITIAL_REG_DATA_SENT = 'isInitialRegDataSent';
    public const INITIAL_REGISTRATION_DATA_BODY = 'initialRegistrationDataBody';

    private SystemConfiguration $systemConfiguration;
    private DataRegistrationService $dataRegistrationService;
    private SystemConfig $systemConfig;
    private array $initialRegistrationDataBody = [];

    public function __construct()
    {
        $this->systemConfiguration = new SystemConfiguration();
        $this->dataRegistrationService = new DataRegistrationService();
        $this->systemConfig = new SystemConfig();
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function setInitialRegistrationDataBody(string $type, string $uniqueIdentifier = null): void
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

        $this->initialRegistrationDataBody = [
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
            'system_details' => json_encode($this->systemConfig->getSystemDetails())
        ];
    }

    /**
     * @return array
     */
    public function getInitialRegistrationDataBody(): array
    {
        return $this->initialRegistrationDataBody;
    }

    /**
     * @return array
     */
    public function getSuccessRegistrationDataBody(): array
    {
        return [
            'instance_identifier' => $this->systemConfiguration->getInstanceIdentifier(),
            'type' => self::REGISTRATION_TYPE_SUCCESS
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
        if (is_null($this->systemConfiguration->getInstanceIdentifier())) {
            $this->systemConfiguration->setInstanceIdentifier(
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
        return $this->systemConfiguration->getInstanceIdentifier();
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
        if (is_null($this->systemConfiguration->getInstanceIdentifierChecksum())) {
            $this->systemConfiguration->setInstanceIdentifierChecksum(
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
        return $this->systemConfiguration->getInstanceIdentifierChecksum();
    }

    /**
     * @param string $type
     * @throws \Doctrine\DBAL\Exception
     * @throws GuzzleException
     */
    public function sendRegistrationDataOnFailure(string $type)
    {
        $this->systemConfiguration->setRegistrationEventQueue(
            $type,
            self::NOT_PUBLISHED
        );
        $this->setInitialRegistrationDataBody($type);
        $initialRegistrationDataBody = $this->getInitialRegistrationDataBody();
        $result = $this->dataRegistrationService->sendRegistrationData($initialRegistrationDataBody);

        if ($result) {
            $this->systemConfiguration->updateRegistrationEventQueue(
                $type,
                self::PUBLISHED,
                json_encode($initialRegistrationDataBody)
            );
            StateContainer::getInstance()->removeAttribute(
                DataRegistrationUtility::IS_INITIAL_REG_DATA_SENT
            );
        }
    }

    /**
     * @throws GuzzleException
     * @throws \Doctrine\DBAL\Exception
     */
    public function sendRegistrationDataOnSuccess(): void
    {
        $this->systemConfiguration->setRegistrationEventQueue(
            self::REGISTRATION_TYPE_SUCCESS,
            self::NOT_PUBLISHED
        );
        $successRegistrationDataBody = $this->getSuccessRegistrationDataBody();
        $result = $this->dataRegistrationService->sendRegistrationData($successRegistrationDataBody);
        if ($result) {
            $this->systemConfiguration->updateRegistrationEventQueue(
                self::REGISTRATION_TYPE_SUCCESS,
                self::PUBLISHED,
                json_encode($successRegistrationDataBody)
            );
        }
    }
}
