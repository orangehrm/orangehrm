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

namespace OrangeHRM\Installer\Util\Service;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OrangeHRM\Config\Config;
use OrangeHRM\Installer\Util\Logger;

class DataRegistrationService
{
    private ?Client $httpClient = null;

    /**
     * @return Client
     */
    private function getHttpClient(): Client
    {
        if (!isset($this->httpClient)) {
            $this->httpClient = new Client(['base_uri' => Config::REGISTRATION_URL]);
        }
        return $this->httpClient;
    }

    /**
     * @param array $body
     * @return bool
     * @throws GuzzleException
     */
    public function sendRegistrationData(array $body): bool
    {
        try {
            if (Config::PRODUCT_MODE === Config::MODE_PROD) {
                $headers = ['Accept' => 'application/json'];
                $this->getHttpClient()->post(
                    '/',
                    [
                        'form_params' => $body,
                        'headers' => $headers
                    ]
                );
                return true;
            }
            return false;
        } catch (Exception $exception) {
            Logger::getLogger()->error($exception->getMessage());
            Logger::getLogger()->error($exception->getTraceAsString());
            return false;
        }
    }
}
