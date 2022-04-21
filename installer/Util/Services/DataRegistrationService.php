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

namespace OrangeHRM\Installer\Util\Services;

use Exception;
use GuzzleHttp\Client;
use OrangeHRM\Config\Config;
use OrangeHRM\Installer\Util\Logger;
use OrangeHRM\Installer\Util\SystemConfig;

class DataRegistrationService
{

    private ?Client $apiClient = null;
    private ?SystemConfig $systemConfig = null;

    public function __construct()
    {
        $this->systemConfig = new SystemConfig();
    }

    /**
     * @return Client
     */
    private function getHttpClient(): Client
    {
        if (!isset($this->apiClient)) {
            $this->apiClient = new Client(['base_uri' => Config::REGISTRATION_BETA_URL]);
        }
        return $this->apiClient;
    }

    /**
     * @param string $userName
     * @param string $email
     * @param string $telephone
     * @param string $adminFirstName
     * @param string $adminLastName
     * @param string $timezone
     * @param string $language
     * @param string $country
     * @param string $organizationName
     * @param string $type
     * @param string $instanceIdentifier
     * @return bool
     */
    public function sendDataWhenRegistrationStarted(
        string $userName,
        string $email,
        string $telephone,
        string $adminFirstName,
        string $adminLastName,
        string $timezone,
        string $language,
        string $country,
        string $organizationName,
        string $type,
        string $instanceIdentifier
    ): bool {
        $headers = ['Accept' => 'application/json'];
        $body = [
            'username' => $userName,
            'email' => $email,
            'telephone' => $telephone,
            'admin_first_name' => $adminFirstName,
            'admin_last_name' => $adminLastName,
            'timezone' => $timezone,
            'language' => $language,
            'country' => $country,
            'organization_name' => $organizationName,
            'type' => $type,
            'instance_identifier' => $instanceIdentifier,
            'system_details' => json_encode($this->systemConfig->getSystemDetails())
        ];

        try {
            $this->getHttpClient()->post('/', [
                    'form_params' => $body,
                    'headers' => $headers
                ]
            );
            return true;
        } catch (Exception $exception) {
            Logger::getLogger()->error($exception->getMessage());
            Logger::getLogger()->error($exception->getTraceAsString());
            return false;
        }
    }

    public function sendDataWhenRegistrationSuccess(
        string $userName,
        string $email,
        string $telephone,
        string $adminFirstName,
        string $adminLastName,
        string $timezone,
        string $language,
        string $country,
        string $organizationName,
        string $instanceIdentifier,
        string $type,
        string $employeeCount
    ): bool {
        //TODO: finalize what data need to be sent
        $headers = ['Accept' => 'application/json'];
        $body = [
            'username' => $userName,
            'userEmail' => $email,
            'telephone' => $telephone,
            'admin_first_name' => $adminFirstName,
            'admin_last_name' => $adminLastName,
            'timezone' => $timezone,
            'language' => $language,
            'country' => $country,
            'organization_name' => $organizationName,
            'instance_identifier' => $instanceIdentifier,
            'type' => $type,
            'employee_count' => $employeeCount
        ];

        try {
            $this->getHttpClient()->post('/', [
                    'form_params' => $body,
                    'headers' => $headers
                ]
            );
            return true;
        } catch (Exception $exception) {
            dump($exception);
            Logger::getLogger()->error($exception->getMessage());
            Logger::getLogger()->error($exception->getTraceAsString());
            return false;
        }
    }
}
