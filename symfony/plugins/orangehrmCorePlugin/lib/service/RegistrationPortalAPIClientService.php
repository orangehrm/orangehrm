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


    public function publishData($event)
    {
        try {
            $headers = array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            );

            $postData = $this->getPublishData($event->getEventTypeId());
            $response = $this->getApiClient()->post(
                '',
                array(
                    'headers'     => $headers,
//                    'form_params' => $postData,
                    'body'        => $postData,
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

    public function getPublishData($eventType)
    {
        $data = '';
        if ($eventType == RegistrationEventQueueProcessor::INSTALLATION_STARTED) {
            $data = $this->getInstallationStartedEventData();
        }elseif ($eventType == RegistrationEventQueueProcessor::INSTALLATION_SUCCESS){
            $data = $this->getInstallationSucceedEventData();
        }elseif ($eventType == RegistrationEventQueueProcessor::ACTIVE_EMPLOYEE_COUNT){
            $data = $this->getEmployeeCountUpdateEventData('addition');
        }
        return $data;
    }

    public function getInstallationStartedEventData()
    {
//        $requestData = array(
//            'username'            => 'Admin',
//            'email'               => 'yasiru@orangehrmlive.com',
//            'telephone'           => '0702132850',
//            'admin_first_name'    => 'Yasiru',
//            'admin_last_name'     => 'Nilan',
//            'timezone'            => 'Not captured',
//            'language'            => 'Not Captured',
//            'country'             => 'Not Captured',
//            'organization_name'   => 'YasiruCompany',
//            'type'                => '0',
//            'instance_identifier' => 'X19fXzEyNy4wLjAuMV9fNC44',
//            'system_details'      => '{"os":{"os":"Linux","release_name":"5.11.0-36-generic","version_info":"#40~20.04.1-Ubuntu SMP Sat Sep 18 02:14:19 UTC 2021"},"php":{"version":"7.2.34"},"mysql":{"client_version":"mysqlnd 5.0.12-dev - 20150407 - $Id: 3591daad22de08524295e1bd073aceeff11e6579 $","server_version":"5.5.5-10.4.21-MariaDB-1:10.4.21+maria~focal","conn_type":"os_dev_mariadb104 via TCP\\/IP"},"server":null,"ohrm":{"version":"4.8"}}'
//        );
        $requestData = 'username=Admin&email=yasiru@orangehrmlive.com&telephone=0702132850&admin_first_name=Yasiru&admin_last_name=Nilan&timezone=Not captured&language=Not captured&country=Not Captured&organization_name=YasiruCompany&type=0&instance_identifier=X19fXzEyNy4wLjAuMV9fNC44&system_details={"os":{"os":"Linux","release_name":"5.11.0-36-generic","version_info":"#40~20.04.1-Ubuntu SMP Sat Sep 18 02:14:19 UTC 2021"},"php":{"version":"7.2.34"},"mysql":{"client_version":"mysqlnd 5.0.12-dev - 20150407 - $Id: 3591daad22de08524295e1bd073aceeff11e6579 $","server_version":"5.5.5-10.4.21-MariaDB-1:10.4.21+maria~focal","conn_type":"os_dev_mariadb104 via TCP\\/IP"},"server":null,"ohrm":{"version":"4.8"}}';
        return $requestData;
    }

    public function getInstallationSucceedEventData()
    {
//        $requestData = array(
//            'username'            => 'Admin',
//            'email'               => 'yasiru@orangehrmlive.com',
//            'telephone'           => '0702132850',
//            'admin_first_name'    => 'Yasiru',
//            'admin_last_name'     => 'Nilan',
//            'timezone'            => 'Not captured',
//            'language'            => 'Not Captured',
//            'country'             => 'Not Captured',
//            'organization_name'   => 'YasiruCompany',
//            'type'                => '0',
//            'instance_identifier' => 'X19fXzEyNy4wLjAuMV9fNC44',
//            'system_details'      => '{"os":{"os":"Linux","release_name":"5.11.0-36-generic","version_info":"#40~20.04.1-Ubuntu SMP Sat Sep 18 02:14:19 UTC 2021"},"php":{"version":"7.2.34"},"mysql":{"client_version":"mysqlnd 5.0.12-dev - 20150407 - $Id: 3591daad22de08524295e1bd073aceeff11e6579 $","server_version":"5.5.5-10.4.21-MariaDB-1:10.4.21+maria~focal","conn_type":"os_dev_mariadb104 via TCP\\/IP"},"server":null,"ohrm":{"version":"4.8"}}'
//        );
        $requestData = 'username=Admin&email=rajitha@orangehrm.us.com&telephone=Not captured&admin_first_name=rajitha&admin_last_name=kumara&timezone=Africa/Abidjan&language=en_US&country=LK&organization_name=OHRM-prod[PHP Upgrade]&type=3&instance_identifier=X19fXzEyNy4wLjAuMV9fNC44&system_details={"os":{"os":"Linux","release_name":"5.0.0-23-generic","version_info":"#24~18.04.1-Ubuntu SMP Mon Jul 29 16:12:28 UTC 2019"},"php":{"version":"7.3.4"},"mysql":{"client_version":"mysqlnd 5.0.12-dev - 20150407 - $Id: 7cc7cc96e675f6d72e5cf0f267f48e167c2abb23 $","server_version":"5.5.5-10.4.12-MariaDB-1:10.4.12+maria~bionic","conn_type":"mariadb104 via TCP\/IP"},"server":"nginx\/1.13.12","ohrm":{"version":"4.7"}}';
        return $requestData;
    }

    public function getEmployeeCountUpdateEventData($type)
    {
//        $requestData = array(
//            'username'            => 'Admin',
//            'email'               => 'yasiru@orangehrmlive.com',
//            'telephone'           => '0702132850',
//            'admin_first_name'    => 'Yasiru',
//            'admin_last_name'     => 'Nilan',
//            'timezone'            => 'Not captured',
//            'language'            => 'Not Captured',
//            'country'             => 'Not Captured',
//            'organization_name'   => 'YasiruCompany',
//            'type'                => '0',
//            'instance_identifier' => 'X19fXzEyNy4wLjAuMV9fNC44',
//            'system_details'      => '{"os":{"os":"Linux","release_name":"5.11.0-36-generic","version_info":"#40~20.04.1-Ubuntu SMP Sat Sep 18 02:14:19 UTC 2021"},"php":{"version":"7.2.34"},"mysql":{"client_version":"mysqlnd 5.0.12-dev - 20150407 - $Id: 3591daad22de08524295e1bd073aceeff11e6579 $","server_version":"5.5.5-10.4.21-MariaDB-1:10.4.21+maria~focal","conn_type":"os_dev_mariadb104 via TCP\\/IP"},"server":null,"ohrm":{"version":"4.8"}}'
//        );
        if($type === 'addition'){
            $requestData = 'username=&userEmail=&telephone=&admin_first_name=&admin_last_name=&timezone=&language=&country=&organization_name=&instance_identifier=X19fXzEyNy4wLjAuMV9fNC44&type=1&employee_count=200';
        }else {
            $requestData = 'username=&userEmail=&telephone=&admin_first_name=&admin_last_name=&timezone=&language=&country=&organization_name=&instance_identifier={X19fXzEyNy4wLjAuMV9fNC44&type=2&employee_count=10';
        }
        return $requestData;
    }
}
