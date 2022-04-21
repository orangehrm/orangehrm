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

namespace OrangeHRM\Installer\Controller\Upgrader\Api;

use Doctrine\DBAL\Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Installer\Controller\AbstractInstallerRestController;
use OrangeHRM\Installer\Util\Services\DataRegistrationService;
use OrangeHRM\Installer\Util\SystemConfigs\SystemConfigurations;

class RegisterDataOnStartAPI extends AbstractInstallerRestController
{
    public const REGISTRATION_TYPE_UPGRADER_STARTED = 4;

    private SystemConfigurations $systemConfigurations;
    private DataRegistrationService $dataRegistrationService;

    public function __construct()
    {
        $this->systemConfigurations = new SystemConfigurations();
        $this->dataRegistrationService = new DataRegistrationService();
    }

    /**
     * @inheritDoc
     */
    protected function handlePost(Request $request): array
    {
        $organizationName = $this->systemConfigurations->getOrganizationName();
        $country = $this->systemConfigurations->getCountry();
        $language = $this->systemConfigurations->getLanguage();
        $adminFirstName = $this->systemConfigurations->getAdminFirstName();
        $adminLastName = $this->systemConfigurations->getAdminLastName();
        $adminEmail = $this->systemConfigurations->getAdminEmail();
        $adminContactNumber = $this->systemConfigurations->getAdminContactNumber();
        $adminUserName = $this->systemConfigurations->getAdminUserName();
        $timezone = SystemConfigurations::NOT_CAPTURED;
        $randomNumber = mt_rand(1, 100);
        $type = self::REGISTRATION_TYPE_UPGRADER_STARTED;

        $instanceIdentifier = $this->setInstanceIdentifier(
            $adminFirstName,
            $adminLastName,
            $organizationName,
            $adminEmail,
            $country
        );
        $this->setInstanceIdentifierChecksum(
            $adminFirstName,
            $adminLastName,
            $organizationName,
            $adminEmail,
            $country
        );

        $result = $this->dataRegistrationService->sendDataWhenRegistrationStarted(
            $adminUserName,
            $adminEmail,
            $adminContactNumber,
            $adminFirstName,
            $adminLastName,
            $timezone,
            $language,
            $country,
            $organizationName,
            $type,
            $instanceIdentifier
        );

        $response = $this->getResponse();
        $message = $result ? 'Registration Data Sent Successfully!' : 'Failed To Send Registration Data';

        return [
            'status' => $response->getStatusCode(),
            'message' => $message
        ];
    }

    /**
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $organizationName
     * @param string $organizationEmail
     * @param string $country
     * @return string
     * @throws Exception
     */
    protected function setInstanceIdentifier(
        string $adminFirstName,
        string $adminLastName,
        string $organizationName,
        string $organizationEmail,
        string $country
    ): string {
        if (is_null($this->systemConfigurations->getInstanceIdentifier())) {
            $this->systemConfigurations->setInstanceIdentifier(
                $organizationName,
                $organizationEmail,
                $adminFirstName,
                $adminLastName,
                $_SERVER['HTTP_HOST'],
                $country,
                Config::PRODUCT_VERSION
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
     * @return string
     * @throws Exception
     */
    protected function setInstanceIdentifierChecksum(
        string $adminFirstName,
        string $adminLastName,
        string $organizationName,
        string $organizationEmail,
        string $country
    ): string {
        if (is_null($this->systemConfigurations->getInstanceIdentifierChecksum())) {
            $this->systemConfigurations->setInstanceIdentifierChecksum(
                $organizationName,
                $organizationEmail,
                $adminFirstName,
                $adminLastName,
                $_SERVER['HTTP_HOST'],
                $country,
                Config::PRODUCT_VERSION
            );
        }
        return $this->systemConfigurations->getInstanceIdentifierChecksum();
    }
}
