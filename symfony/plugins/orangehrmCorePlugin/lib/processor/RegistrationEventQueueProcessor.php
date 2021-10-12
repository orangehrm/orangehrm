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
class RegistrationEventQueueProcessor
{
    const INSTALLATION_STARTED = "0";
    const ACTIVE_EMPLOYEE_COUNT = "1";
    const INACTIVE_EMPLOYEE_COUNT = "2";
    const INSTALLATION_SUCCESS = "3";
    const UPGRADE_STARTED = "4";
    const PUBLISH_EVENT_COUNT = 5;

    protected $registrationEventQueueDao;
    protected $registrationPortalAPIClientService;
    protected $sysConf;

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

    public function getRegistrationEventQueueDao()
    {
        if (!($this->registrationEventQueueDao instanceof RegistrationEventQueueDao)) {
            $this->registrationEventQueueDao = new RegistrationEventQueueDao();
        }
        return $this->registrationEventQueueDao;
    }

    public function getRegistrationPortalAPIClientService()
    {
        if (!($this->registrationPortalAPIClientService instanceof RegistrationPortalAPIClientService)) {
            $this->registrationPortalAPIClientService = new RegistrationPortalAPIClientService();
        }
        return $this->registrationPortalAPIClientService;
    }

    public function checkAndPublishEventData()
    {
        $installationSucceedEvent = $this->getRegistrationEventQueueDao()->getRegistrationEventQueueEventByType(self::INSTALLATION_SUCCESS);
        if (!$installationSucceedEvent) {
            $this->updateInstallationSuccessEvent();
        }
        $this->publishEvents();
    }

    private function updateInstallationSuccessEvent()
    {
        $registrationEvent = new RegistrationEventQueue();
        $registrationEvent->setEventTypeId(3);
        $registrationEvent->setEventTime(date("Y-m-d H:i:s"));
        $registrationEvent->setPublished(0);
        return $this->getRegistrationEventQueueDao()->saveRegistrationEventQueue($registrationEvent);
    }

    public function updateEmployeeCountEvent($eventType, $count)
    {
        $registrationEvent = new RegistrationEventQueue();
        $registrationEvent->setEventTypeId($eventType);
        $registrationEvent->setEventTime(date("Y-m-d H:i:s"));
        $registrationEvent->setPublished(0);
        $registrationEvent->setExtraDetails($count);
        return $this->getRegistrationEventQueueDao()->saveRegistrationEventQueue($registrationEvent);
    }

    protected function publishEvents()
    {
        Logger::getLogger('orangehrm.log')->error('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
        $mode = $this->getSysConf()->getMode();
        if ($mode === sysConf::PROD_MODE) {
            $eventsToPublish = $this->getRegistrationEventQueueDao()->getUnpublishedRegistrationEventQueueEvents(self::PUBLISH_EVENT_COUNT);
            if ($eventsToPublish) {
                foreach ($eventsToPublish as $event) {
                    $postData = $this->getPublishData($event->getEventTypeId());
                    $result = $this->getRegistrationPortalAPIClientService()->publishData($event, $postData);
                    if ($result) {
                        $event->setPublished(1);
                        $this->getRegistrationEventQueueDao()->saveRegistrationEventQueue($event);
                    }
                }
            }
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
            $requestData = 'username=&userEmail=&telephone=&admin_first_name=&admin_last_name=&timezone=&language=&country=&organization_name=&instance_identifier=X19fXzEyNy4wLjAuMV9fNC44&type=2&employee_count=10';
        }
        return $requestData;
    }


}
