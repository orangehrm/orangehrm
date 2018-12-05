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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class APIManagerService
 */
class APIManagerService
{
    /**
     * Grant type to get token
     */
    const GRANT_TYPE = 'client_credentials';
    /**
     * url for get addons from marketplace
     */
    const ADDON_LIST = '/api/v1/addon';
    /**
     * url for get access token from marketplace
     */
    const API_TOKEN = '/oauth/v2/token';

    private $apiClient = null;
    private $marketplaceService = null;
    public $clientId = null;
    public $clientSecret = null;
    public $baseURL = null;

    /**
     * @return array
     * @throws CoreServiceException
     */
    public function getAddons()
    {
        $addons = $this->getAddonsFromMP();
        return $addons;
    }

    /**
     * @return mixed|string
     * @throws CoreServiceException
     */
    private function getAddonsFromMP()
    {
        $token = $this->getApiToken();
        if ($token == 'Network Error') {
            return $token;
        }
        $headers = array(
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        );
        try {
            $response = $this->getApiClient()->get(self::ADDON_LIST,
                array(
                    'headers' => $headers
                )
            );
            if ($response->getStatusCode() == 200) {
                $body = json_decode($response->getBody(), true);
                return $body;
            }
        } catch (GuzzleHttp\Exception\ConnectException $w) {
            return "Network Error";
        } catch (Exception $e) {
            Logger::getRootLogger()->error('Exception in Marketplace token authentification' . $e);
            throw new CoreServiceException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param int $addonId
     * @return array
     */
    public function getDescription($addonId)
    {
        return $this->getDescriptionFromMP($addonId);
    }

    /**
     * @param int $addonId
     * @return array
     */
    private function getDescriptionFromMP($addonId)
    {
        $addons = array();
        return $addons;
    }

    /**
     * @return \GuzzleHttp\Client|null
     * @throws CoreServiceException
     */
    private function getApiClient()
    {
        if (!isset($this->apiClient)) {
            $this->apiClient = new GuzzleHttp\Client(['base_uri' => $this->getBaseURL()]);
        }
        return $this->apiClient;
    }

    /**
     * @return string
     * @throws CoreServiceException
     */
    private function getApiToken()
    {
        $headers = array('Accept' => 'application/json');
        $body = array(
            'grant_type' => self::GRANT_TYPE,
            'client_id' => $this->getClientId(),
            'client_secret' => $this->getClientSecret(),
        );
        try {
            $response = $this->getApiClient()->post(self::API_TOKEN,
                array(
                    'form_params' => $body,
                    'headers' => $headers
                )
            );
            if ($response->getStatusCode() == 200) {
                $body = json_decode($response->getBody(), true);
                return $body['access_token'];
            }
        } catch (GuzzleHttp\Exception\ConnectException $w) {
            return "Network Error";
        }
    }

    /**
     * @return MarketplaceService
     */
    private function getMarketplaceService()
    {
        if (!isset($this->marketplaceService)) {
            $this->marketplaceService = new MarketplaceService();
        }
        return $this->marketplaceService;
    }

    /**
     * @return String
     * @throws CoreServiceException
     */
    private function getClientId()
    {
        if (!isset($this->clientId)) {
            $this->clientId = $this->getMarketplaceService()->getClientId();
        }
        return $this->clientId;
    }

    /**
     * @return String
     * @throws CoreServiceException
     */
    private function getClientSecret()
    {
        if (!isset($this->clientSecret)) {
            $this->clientSecret = $this->getMarketplaceService()->getClientSecret();
        }
        return $this->clientSecret;
    }

    /**
     * @return String
     * @throws CoreServiceException
     */
    private function getBaseURL()
    {
        if (!isset($this->baseURL)) {
            $this->baseURL = $this->getMarketplaceService()->getBaseURL();
        }
        return $this->baseURL;
    }

    /**
     * @param $addonURL
     * @param $version
     * @return string
     * @throws CoreServiceException
     */
    private function getAddonFileFromMP($addonURL)
    {
        $token = $this->getApiToken();
        if ($token == 'Network Error') {
            return $token;
        }
        $headers = array(
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        );
        try {
            $response = $this->getApiClient()->get($addonURL,
                array(
                    'headers' => $headers,
                    'stream' => true
                )
            );
            if ($response->getStatusCode() == 200) {
                $contents = $response->getBody()->getContents();
                return base64_encode($contents);
            }
        } catch (GuzzleHttp\Exception\ConnectException $w) {
            return "Network Error";
        }
    }

    /**
     * @param $addonURL
     * @param $version
     * @return string
     * @throws CoreServiceException
     */
    public function getAddonFile($addonURL)
    {
        $addon = $this->getAddonFileFromMP($addonURL);
        return $addon;
    }
}

