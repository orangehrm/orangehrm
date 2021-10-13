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
    const INSTALLATION_STARTED = 0;
    const ACTIVE_EMPLOYEE_COUNT = 1;
    const INACTIVE_EMPLOYEE_COUNT = 2;
    const INSTALLATION_SUCCESS = 3;
    const UPGRADE_STARTED = 4;
    const PUBLISH_EVENT_COUNT = 5;

    protected $registrationEventQueueDao;
    protected $registrationPortalAPIClientService;
    protected $sysConf;
    protected $configService;
    protected $systemDetailHelper;
    protected $organizationService;

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

    /**
     * Get ConfigService instance
     * @return ConfigService
     */
    private function getConfigService() {
        if (!($this->configService instanceof ConfigService)) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    /**
     * Return system details as a JSON string
     * @return string
     */
    private function getSystemDetails() {
        require_once(sfConfig::get('sf_root_dir') . "/../installer/SystemDetailHelper.php");
        $sysDetailHelper = new SystemDetailHelper();
        return $sysDetailHelper->getSystemDetailsAsJson();
    }

    /**
     * Get OrganizationService instance
     * @return OrganizationService
     */
    private function getOrganizationService() {
        if (!($this->organizationService instanceof OrganizationService)) {
            $this->organizationService = new OrganizationService();
        }
        return $this->organizationService;
    }

    /**
     * Get the instance Identifier value
     * @return String
     * @throws CoreServiceException
     */
    private function getInstanceIdentifier() {
        return $this->getConfigService()->getInstanceIdentifier();
    }

    /**
     * @return MarketplaceDao
     */
    public function getMarketplaceDao()
    {
        if (!isset($this->marketplaceDao)) {
            $this->marketplaceDao = new MarketplaceDao();
        }
        return $this->marketplaceDao;
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
        $mode = $this->getSysConf()->getMode();
        if ($mode === sysConf::PROD_MODE) {
            $eventsToPublish = $this->getRegistrationEventQueueDao()->getUnpublishedRegistrationEventQueueEvents(self::PUBLISH_EVENT_COUNT);
            if ($eventsToPublish) {
                foreach ($eventsToPublish as $event) {
                    $postData = $this->getPublishData($event);
                    $result = $this->getRegistrationPortalAPIClientService()->publishData($postData);
                    if ($result) {
                        $event->setPublished(1);
                        $this->getRegistrationEventQueueDao()->saveRegistrationEventQueue($event);
                    }
                }
            }
        }
    }

    public function getPublishData($event)
    {
        $eventType = $event->getEventTypeId();
        $adminEmployee = $this->getMarketplaceDao()->getAdmin();
        $language = $this->getConfigService()->getAdminLocalizationDefaultLanguage()? $this->getConfigService()->getAdminLocalizationDefaultLanguage(): 'Not captured';
        $country = $this->getOrganizationService()->getOrganizationGeneralInformation()->getCountry()? $this->getOrganizationService()->getOrganizationGeneralInformation()->getCountry(): null;
        $instanceIdentifier = $this->getInstanceIdentifier();
        $organizationName = $this->getOrganizationService()->getOrganizationGeneralInformation()->getName();
        $systemDetails = '';
//        $systemDetails = $this->getSystemDetails();
        $additionalData = $event->getExtraDetails();
        $eventTime = $event->getEventTime();
        $organizationEmail = '';
        $adminFirstName = '';
        $adminLastName = '';
        $adminContactNumber = '';
        $username = '';
        if($adminEmployee instanceof Employee){
            $organizationEmail = $adminEmployee->getEmpWorkEmail();
            $adminFirstName = $adminEmployee->getFirstName();
            $adminLastName = $adminEmployee->getLastName();
            $adminContactNumber = $adminEmployee->getEmpWorkTelephone();
            $username = $adminEmployee->getSystemUser()? $adminEmployee->getSystemUser()->getFirst()->getUserName(): '';
        }

        $data = '';
        if ($eventType == RegistrationEventQueueProcessor::INSTALLATION_STARTED) {
            $data = $this->getInstallationStartedAndSucceedEventData($adminFirstName, $adminLastName, $username, $organizationEmail, $adminContactNumber, $language, $country, $instanceIdentifier, $organizationName, $systemDetails, $eventTime, 'Installation Start');
        }elseif ($eventType == RegistrationEventQueueProcessor::INSTALLATION_SUCCESS){
            $data = $this->getInstallationStartedAndSucceedEventData($adminFirstName, $adminLastName, $username, $organizationEmail, $adminContactNumber, $language, $country, $instanceIdentifier, $organizationName, $systemDetails, $eventTime, 'Installation Success');
        }elseif ($eventType == RegistrationEventQueueProcessor::ACTIVE_EMPLOYEE_COUNT){
            $data = $this->getEmployeeCountUpdateEventData($adminFirstName, $adminLastName, $username, $organizationEmail, $adminContactNumber, $language, $country, $instanceIdentifier, $organizationName, $additionalData, $eventTime,  'addition');
        }
        return $data;
    }

    public function getInstallationStartedAndSucceedEventData($adminFirstName, $adminLastName, $username, $organizationEmail, $adminContactNumber, $language, $country, $instanceIdentifier, $organizationName, $systemDetails, $eventTime, $type)
    {
        $requestData = array(
            'username'            => $username,
            'email'               => $organizationEmail,
            'telephone'           => $adminContactNumber,
            'admin_first_name'    => $adminFirstName,
            'admin_last_name'     => $adminLastName,
            'timezone'            => 'Not captured',
            'language'            => $language,
            'country'             => $country,
            'organization_name'   => $organizationName,
            'type'                => $type === 'Installation Start' ? self::INSTALLATION_STARTED: self::INSTALLATION_SUCCESS,
            'instance_identifier' => $instanceIdentifier,
            'event_time' => $eventTime,
            'system_details'      => $systemDetails
        );




        return $requestData;
    }

    public function getEmployeeCountUpdateEventData($adminFirstName, $adminLastName, $username, $organizationEmail, $adminContactNumber, $language, $country, $instanceIdentifier, $organizationName, $employeeCount, $eventTime, $type)
    {
        $requestData = array(
            'username'            => $username,
            'email'               => $organizationEmail,
            'telephone'           => $adminContactNumber,
            'admin_first_name'    => $adminFirstName,
            'admin_last_name'     => $adminLastName,
            'timezone'            => 'Not captured',
            'language'            => $language,
            'country'             => $country,
            'organization_name'   => $organizationName,
            'type'                => $type === 'addition'? self::ACTIVE_EMPLOYEE_COUNT: self::INACTIVE_EMPLOYEE_COUNT,
            'instance_identifier' => $instanceIdentifier,
            'event_time' => $eventTime,
            'employee_count'      => $employeeCount
        );
        return $requestData;
    }


}
