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

namespace OrangeHRM\Core\Registration\Processor;

use DateTime;
use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Admin\Service\OrganizationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Registration\Dao\RegistrationEventQueueDao;
use OrangeHRM\Core\Registration\Helper\SystemConfigurationHelper;
use OrangeHRM\Core\Registration\Service\RegistrationAPIClientService;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Traits\LoggerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\RegistrationEventQueue;
use OrangeHRM\Pim\Dao\EmployeeDao;
use Throwable;

abstract class AbstractRegistrationEventProcessor
{
    use LoggerTrait;

    public ?RegistrationEventQueueDao $registrationEventQueueDao = null;
    public ?ConfigService $configService = null;
    public ?RegistrationAPIClientService $registrationAPIClientService = null;
    public ?OrganizationService $organizationService = null;
    public ?UserDao $userDao = null;

    /**
     * @return RegistrationEventQueueDao
     */
    public function getRegistrationEventQueueDao(): RegistrationEventQueueDao
    {
        if (!($this->registrationEventQueueDao instanceof RegistrationEventQueueDao)) {
            $this->registrationEventQueueDao = new RegistrationEventQueueDao();
        }
        return $this->registrationEventQueueDao;
    }

    /**
     * @return EmployeeDao
     */
    public function getEmployeeDao(): EmployeeDao
    {
        if (!isset($this->employeeDao)) {
            $this->employeeDao = new EmployeeDao();
        }
        return $this->employeeDao;
    }

    /**
     * @return UserDao
     */
    public function getUserDao(): UserDao
    {
        if (!isset($this->userDao)) {
            $this->userDao = new UserDao();
        }
        return $this->userDao;
    }

    /**
     * @return ConfigService
     */
    public function getConfigService(): ConfigService
    {
        if (!($this->configService instanceof ConfigService)) {
            $this->configService = new ConfigService();
        }
        return $this->configService;
    }

    /**
     * @return RegistrationAPIClientService
     */
    public function getRegistrationAPIClientService(): RegistrationAPIClientService
    {
        if (!($this->registrationAPIClientService instanceof RegistrationAPIClientService)) {
            $this->registrationAPIClientService = new RegistrationAPIClientService();
        }
        return $this->registrationAPIClientService;
    }

    /**
     * @return OrganizationService
     */
    public function getOrganizationService(): OrganizationService
    {
        if (!($this->organizationService instanceof OrganizationService)) {
            $this->organizationService = new OrganizationService();
        }
        return $this->organizationService;
    }

    /**
     * @return string
     * @throws CoreServiceException
     */
    private function getInstanceIdentifier(): string
    {
        return $this->getConfigService()->getInstanceIdentifier();
    }

    /**
     * @return RegistrationEventQueue|void
     */
    public function saveRegistrationEvent($eventTime)
    {
        if ($this->getEventToBeSavedOrNot()) {
            $registrationEvent = $this->processRegistrationEventToSave($eventTime);
            return $this->getRegistrationEventQueueDao()->saveRegistrationEvent($registrationEvent);
        }
    }

    /**
     * @return array
     */
    public function getRegistrationEventGeneralData(): array
    {
        $registrationData = [];
        try {
            $adminUser = $this->getUserDao()->getDefaultAdminUser();
            $adminEmployee = $adminUser->getEmployee();
            $language = $this->getConfigService()->getAdminLocalizationDefaultLanguage()
                ? $this->getConfigService()->getAdminLocalizationDefaultLanguage()
                : 'Not captured';
            $country = $this->getOrganizationService()->getOrganizationGeneralInformation()->getCountry()
                ? $this->getOrganizationService()->getOrganizationGeneralInformation()->getCountry()
                : null;
            $instanceIdentifier = $this->getInstanceIdentifier();
            $organizationName = $this->getOrganizationService()->getOrganizationGeneralInformation()->getName();
            $systemDetailsHelper = new SystemConfigurationHelper();
            $systemDetails = $systemDetailsHelper->getSystemDetailsAsJson();
            $organizationEmail = '';
            $adminFirstName = '';
            $adminLastName = '';
            $adminContactNumber = '';
            $username = 'Not Captured';
            $timeZone = date_default_timezone_get();
            if ($adminEmployee instanceof Employee) {
                $organizationEmail = $adminEmployee->getWorkEmail();
                $adminFirstName = $adminEmployee->getFirstName();
                $adminLastName = $adminEmployee->getLastName();
                $adminContactNumber = $adminEmployee->getWorkTelephone();
            }

            return [
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
            ];
        } catch (Throwable $e) {
            $this->getLogger()->error($e->getMessage());
            $this->getLogger()->error($e->getTraceAsString());
            return $registrationData;
        }
    }

    /**
     * @param DateTime $eventTime
     * @return RegistrationEventQueue
     */
    public function processRegistrationEventToSave(DateTime $eventTime): RegistrationEventQueue
    {
        $registrationData = $this->getEventData();
        $registrationEvent = new RegistrationEventQueue();
        $registrationEvent->setEventTime($eventTime);
        $registrationEvent->setEventType($this->getEventType());
        $registrationEvent->setPublished(0);
        $registrationEvent->setData($registrationData);
        return $registrationEvent;
    }

    public function publishRegistrationEvents(): void
    {
        if (Config::PRODUCT_MODE === Config::MODE_PROD) {
            $eventsToPublish = $this->getRegistrationEventQueueDao()
                ->getUnpublishedRegistrationEvents(RegistrationEventQueue::PUBLISH_EVENT_BATCH_SIZE);
            if ($eventsToPublish) {
                foreach ($eventsToPublish as $event) {
                    $postData = $this->getRegistrationEventPublishDataPrepared($event);
                    $result = $this->getRegistrationAPIClientService()->publishData($postData);
                    if ($result) {
                        $event->setPublished(1);
                        $event->setPublishTime(new DateTime());
                        $this->getRegistrationEventQueueDao()->saveRegistrationEvent($event);
                    }
                }
            }
        }
    }

    /**
     * @param RegistrationEventQueue $event
     * @return array
     */
    public function getRegistrationEventPublishDataPrepared(RegistrationEventQueue $event): array
    {
        $eventData = $event->getData();
        $eventData['type'] = $event->getEventType();
        $eventData['event_time'] = $event->getEventTime();
        return $eventData;
    }

    /**
     * @return int
     */
    abstract public function getEventType(): int;

    /**
     * @return array
     */
    abstract public function getEventData(): array;

    /**
     * @return bool
     */
    abstract public function getEventToBeSavedOrNot(): bool;
}
