<?php

/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */
class RegistrationPortalAPIClientService
{
    private $sysConf = null;

    /**
     * Get instance of sysConf
     * @return null|sysConf
     */
    private function getSysConf()
    {
        if (!defined('ROOT_PATH')) {
            $rootPath = realpath(dirname(__FILE__));
            define('ROOT_PATH', $rootPath);
        }
        require_once(ROOT_PATH . '/lib/confs/sysConf.php');
        if (is_null($this->sysConf)) {
            $this->sysConf = new sysConf();
        }
        return $this->sysConf;
    }

    /**
     * Get the registration URL
     * @return null|string
     */
    private function getRegistrationUrl()
    {
        return $this->getSysConf()->getRegistrationUrl();
    }

    /**
     * @return \GuzzleHttp\Client
     * @throws CoreServiceException
     */
    private function getApiClient()
    {
        if (!isset($this->apiClient)) {
            $this->apiClient = new GuzzleHttp\Client(['base_uri' => $this->getRegistrationUrl(), 'verify' => false]);
        }
        return $this->apiClient;
    }


    public function publishData($event, $data)
    {
        try {
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            );
            $response = $this->getApiClient()->post(
                '',
                array(
                    'headers'     => $headers,
//                    'form_params' => $data,
                    'body'        => $data,
                )
            );
            if ($response->getStatusCode() == 200) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            Logger::getLogger('orangehrm.log')->error('aaaaaaaaaaaaaaaaaaaaaaaaa');
            Logger::getLogger('orangehrm.log')->error($e);
        }
    }
}
