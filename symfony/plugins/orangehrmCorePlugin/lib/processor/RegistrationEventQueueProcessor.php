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
//        $registrationEvent->setEventTime(date("Y-m-d"));
        $registrationEvent->setPublished(0);
        return $this->getRegistrationEventQueueDao()->saveRegistrationEventQueue($registrationEvent);
    }

    public function updateEmployeeCountEvent($eventType, $count)
    {
        $registrationEvent = new RegistrationEventQueue();
        $registrationEvent->setEventTypeId($eventType);
//        $registrationEvent->setEventTime(date("Y-m-d"));
        $registrationEvent->setPublished(0);
        $registrationEvent->setExtraDetails($count);
        return $this->getRegistrationEventQueueDao()->saveRegistrationEventQueue($registrationEvent);
    }

    protected function publishEvents()
    {
        $eventsToPublish = $this->getRegistrationEventQueueDao()->getUnpublishedRegistrationEventQueueEvents(self::PUBLISH_EVENT_COUNT);
        if($eventsToPublish){
            foreach ($eventsToPublish as $event){
                $result = $this->getRegistrationPortalAPIClientService()->publishData($event);
                if($result){
                    $event->setPublished(1);
                    $this->getRegistrationEventQueueDao()->saveRegistrationEventQueue($event);
                }
            }
        }
    }


}
