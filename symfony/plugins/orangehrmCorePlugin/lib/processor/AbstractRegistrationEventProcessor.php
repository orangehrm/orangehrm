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
abstract class AbstractRegistrationEventProcessor
{

    public $registrationEventQueueDao;
    public $configService;
    public $registrationAPIClientService;
    public $organizationService;

    public function getRegistrationEventQueueDao()
    {
        if (!($this->registrationEventQueueDao instanceof RegistrationEventQueueDao)) {
            $this->registrationEventQueueDao = new RegistrationEventQueueDao();
        }
        return $this->registrationEventQueueDao;
    }

    public function getEmployeeDao()
    {
        if (!isset($this->employeeDao)) {
            $this->employeeDao = new EmployeeDao();
        }
        return $this->employeeDao;
    }

    public function getUserDao()
    {
        if (!isset($this->userDao)) {
            $this->userDao = new UserDao();
        }
        return $this->userDao;
    }

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

    private function getSystemDetails() {
        require_once(sfConfig::get('sf_root_dir') . "/../installer/SystemDetailHelper.php");
        $sysDetailHelper = new SystemDetailHelper();
        return $sysDetailHelper->getSystemDetailsAsJson();
    }

    /**
     * @return ConfigService
     */
    public function getConfigService()
    {
        if (!($this->configService instanceof ConfigService)) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    public function getRegistrationAPIClientService()
    {
        if (!($this->registrationAPIClientService instanceof RegistrationAPIClientService)) {
            $this->registrationAPIClientService = new RegistrationAPIClientService();
        }
        return $this->registrationAPIClientService;
    }

    public function getOrganizationService()
    {
        if (!($this->organizationService instanceof OrganizationService)) {
            $this->organizationService = new OrganizationService();
        }
        return $this->organizationService;
    }

    public function getMarketplaceDao()
    {
        if (!isset($this->marketplaceDao)) {
            $this->marketplaceDao = new MarketplaceDao();
        }
        return $this->marketplaceDao;
    }

    /**
     * @return string
     * @throws CoreServiceException
     */
    private function getInstanceIdentifier()
    {
        return $this->getConfigService()->getInstanceIdentifier();
    }

    public function saveRegistrationEvent($eventTime)
    {
        if ($this->getEventToBeSavedOrNot()) {
            $registrationEvent = $this->processRegistrationEventToSave($eventTime);
            return $this->getRegistrationEventQueueDao()->saveRegistrationEventQueue($registrationEvent);
        }
    }

    public function getRegistrationEventGeneralData()
    {
        $registrationData = [];
        try {
            $adminEmployee = $this->getMarketplaceDao()->getAdmin();
            $language = $this->getConfigService()->getAdminLocalizationDefaultLanguage() ? $this->getConfigService(
            )->getAdminLocalizationDefaultLanguage() : 'Not captured';
            $country = $this->getOrganizationService()->getOrganizationGeneralInformation()->getCountry(
            ) ? $this->getOrganizationService()->getOrganizationGeneralInformation()->getCountry() : null;
            $instanceIdentifier = $this->getInstanceIdentifier();
            $organizationName = $this->getOrganizationService()->getOrganizationGeneralInformation()->getName();
            $systemDetails = $this->getSystemDetails();
            $organizationEmail = '';
            $adminFirstName = '';
            $adminLastName = '';
            $adminContactNumber = '';
            $username = 'Not Captured';
            $timeZone = date_default_timezone_get();
            if ($adminEmployee instanceof Employee) {
                $organizationEmail = $adminEmployee->getEmpWorkEmail();
                $adminFirstName = $adminEmployee->getFirstName();
                $adminLastName = $adminEmployee->getLastName();
                $adminContactNumber = $adminEmployee->getEmpWorkTelephone();
            }

            return array(
                'username' => $username,
                'email' => $organizationEmail,
                'telephone' => $adminContactNumber,
                'admin_first_name' => $adminFirstName,
                'admin_last_name' => $adminLastName,
                'timezone' => $timeZone,
                'language' => $language,
                'country' => $country,
                'organization_name' => $organizationName,
                'instance_identifier' => $instanceIdentifier,
                'system_details' => $systemDetails
            );
        } catch (Exception $ex) {
            return $registrationData;
        }
    }

    public function processRegistrationEventToSave($eventTime)
    {
        $registrationData = $this->getEventData();
        $registrationEvent = new RegistrationEventQueue();
        $registrationEvent->setEventTime($eventTime);
        $registrationEvent->setEventType($this->getEventType());
        $registrationEvent->setPublished(0);
        $registrationEvent->setEventData(json_encode($registrationData));
        return $registrationEvent;
    }

    public function publishRegistrationEvents()
    {
        $mode = $this->getSysConf()->getMode();
        if ($mode === sysConf::PROD_MODE) {
            $eventsToPublish = $this->getRegistrationEventQueueDao()->getUnpublishedRegistrationEventQueueEvents(
                RegistrationEventQueue::PUBLISH_EVENT_BATCH_SIZE
            );
            if ($eventsToPublish) {
                foreach ($eventsToPublish as $event) {
                    $postData = $this->getRegistrationEventPublishDataPrepared($event);
                    $result = $this->getRegistrationAPIClientService()->publishRegistrationData($postData);
                    if ($result) {
                        $event->setPublished(1);
                        $event->setPublishTime(new DateTime());
                        $this->getRegistrationEventQueueDao()->saveRegistrationEventQueue($event);
                    }
                }
            }
        }
    }

    public function getRegistrationEventPublishDataPrepared(RegistrationEventQueue $event)
    {
        $eventData = $event->getEventData();
        $eventData['type'] = $event->getEventType();
        $eventData['event_time'] = $event->getEventTime();
        return $eventData;
    }

    abstract public function getEventType();

    abstract public function getEventData();

    abstract public function getEventToBeSavedOrNot();
}
