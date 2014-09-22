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
class BeaconCommunicationsService extends BaseService implements StateAccessibleByExecutionFilters {

    const BEACON_ACTIVATION_REQUIRED = 'beacon.activation';
    const BEACON_FLASH_REQUIRED = 'beacon.flash';

    private static $state = self::EMPTY_STATE;
    protected $beaconConfigService;
    protected $beaconDataPointService;

    /**
     * 
     * @return BeaconDatapointService
     */
    protected function getBeaconDatapointService() {
        if (is_null($this->beaconDataPointService)) {
            $this->beaconDataPointService = new BeaconDatapointService();
        }

        return $this->beaconDataPointService;
    }

    public static function getState() {
        return self::$state;
    }

    public function getBeaconConfigurationService() {
        if (is_null($this->beaconConfigService)) {
            $this->beaconConfigService = new BeaconConfigurationService();
        }
        return $this->beaconConfigService;
    }

    public function acquireLock() {


        if ($this->getBeaconConfigurationService()->setBeaconLock('locked') > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function releaseLock() {
        if ($this->getBeaconConfigurationService()->getBeaconLock() == 'locked') {
            $this->getBeaconConfigurationService()->setBeaconLock('unlocked');
        }
    }

    public function setBeaconActivation() {

        if ($this->getBeaconConfigurationService()->getBeaconActivationStatus() == 'off') {

            if ($this->acquireLock()) {
                sfContext::getInstance()->getUser()->setAttribute(self::BEACON_ACTIVATION_REQUIRED, true);
            }
        } else if ($this->getBeaconConfigurationService()->getBeaconActivationAcceptanceStatus() == 'on') {

            $this->sendDataIfRequired();
        }
    }

    public function sendDataIfRequired() {
        //the value is a unix timestamp storing the minimum next flash time

        $nextFlashDate = (int) $this->getBeaconConfigurationService()->getBeaconNextFlashTime();

        if (time() > $nextFlashDate) {

            if ($this->acquireLock()) {
                sfContext::getInstance()->getUser()->setAttribute(self::BEACON_FLASH_REQUIRED, true);
            }
        }
    }

    protected function resolveDatapointMessages($messages) {
        $idArray = array();
        foreach ($messages as $message) {
            $messageBody = new SimpleXMLElement($message['definition']);
            $idArray[] = $message['id'];
            $dataPoint = new DataPoint();
            if ($message['operation'] == 'DELETE') {
                $result = $this->getBeaconDatapointService()->deleteDatapointByName(trim($messageBody->settings->name . ""));
                if ($result > 1) {
                    $idArray[] = $message['id'];
                }
                continue;
            }
            if ($message['operation'] == 'UPDATE') {
                $dataPoint = $this->getBeaconDatapointService()->getDatapointByName(trim($messageBody->settings->name . ""));
            }
            $dataPoint->setName($messageBody->settings->name . "");

            $dataPoint->setDefinition($message['definition']);
            $dataPoint->setDataPointType($this->getBeaconDatapointService()->getDatapointTypeByName($messageBody['type'])->getFirst());

            $dataPoint->save();
        }

        return $idArray;
    }

    protected function resolveConfigMessages($messages) {
        $idArray = array();
        foreach ($messages as $message) {
            $this->getBeaconConfigurationService()->changeConfigTable($message['definition']);
            $idArray[] = $message['id'];
        }

        return $idArray;
    }

    public function resolveNotificationMessages($messages) {
        $idArray = array();
        $beaconNotificationService = new BeaconNotificationService();

        foreach ($messages as $message) {

            $messageBody = new SimpleXMLElement($message['definition']);
            $name = trim($messageBody->settings->name . "");
            $idArray[] = $message['id'];
            $notification = new BeaconNotification();
            if ($message['operation'] == 'DELETE') {

                $result = $beaconNotificationService->deleteNotificationByName($name);
                continue;
            } else if ($message['operation'] == 'UPDATE') {
                $notification = $beaconNotificationService->getNotificationByName($name);
            }

            $notification->setName($name);
            $notification->setDefinition($message['definition']);
            $period = trim($messageBody->settings->period . "");
            $expiry = time() + (int) $period;
            $time = new DateTime();
            $time->setTimestamp($expiry);

            $notification->setExpiryDate($time->format('Y-m-d H:i:s'));
            $notification->save();
        }

        return $idArray;
    }

    public function resolveMessages($messages) {

        $idArray = array();
        foreach ($messages as $name => $messageArray) {
            switch ($name) {
                case 'datapoints':

                    $idArray['datapoints'] = $this->resolveDatapointMessages($messageArray);


                    break;
                case 'configurations':
                    $idArray['configurations'] = $this->resolveConfigMessages($messageArray);
                    break;
                case 'notifications':
                    $idArray['notifications'] = $this->resolveNotificationMessages($messageArray);
                    break;
            }
        }


        return $idArray;
    }

}
