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

namespace OrangeHRM\Core\Registration\Service;

use Exception;
use GuzzleHttp\Client;
use OrangeHRM\Config\SysConf;
use OrangeHRM\Core\Traits\LoggerTrait;

class RegistrationAPIClientService
{

    use LoggerTrait;

    /**
     * @return SysConf
     */
    public function getSysConf(): SysConf
    {
        if (!isset($this->sysConf)) {
            $this->sysConf = new SysConf();
        }
        return $this->sysConf;
    }

    private function getRegistrationUrl(): ?string
    {
        return $this->getSysConf()->getSysConfigs()['registrationUrl'];
    }

    private function getApiClient(): Client
    {
        if (!isset($this->apiClient)) {
            $this->apiClient = new Client(['base_uri' => $this->getRegistrationUrl(), 'verify' => false]);
        }
        return $this->apiClient;
    }

    public function publishData($data)
    {
        try {
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
            $response = $this->getApiClient()->post(
                '',
                array(
                    'headers'     => $headers,
                    'form_params' => $data,
                )
            );
            if ($response->getStatusCode() == 200) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            $this->getLogger()->error('Exception in Registration Data Sync');
        }
    }
}
