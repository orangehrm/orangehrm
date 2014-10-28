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
class BeaconConfigurationService extends ConfigService {

    const KEY_BEACON_ACTIVATION_ACCEPTANCE_STATUS = 'beacon.activation_acceptance_status';
    const KEY_BEACON_ACTIVATION_STATUS = 'beacon.activiation_status';
    const KEY_BEACON_UUID = 'beacon.uuid';
    const KEY_BEACON_NEXT_FLASH_TIME = 'beacon.next_flash_time';
    const KEY_BEACON_LOCK = 'beacon.lock';
    const KEY_BEACON_FLASH_PERIOD = 'beacon.flash_period';
    const KEY_BEACON_COMPANY_NAME = 'beacon.company_name';

    /**
     * 
     * @return BeaconConfigurationDao
     */
    public function getConfigDao() {
        if (is_null($this->configDao)) {
            $this->configDao = new BeaconConfigurationDao();
        }

        return $this->configDao;
    }

    public function getBeaconActivationAcceptanceStatus() {
        return $this->_getConfigValue(self::KEY_BEACON_ACTIVATION_ACCEPTANCE_STATUS);
    }

    public function setBeaconActivationAcceptanceStatus($value) {
        $this->_setConfigValue(self::KEY_BEACON_ACTIVATION_ACCEPTANCE_STATUS, $value);
    }

    public function getBeaconActivationStatus() {
        return $this->_getConfigValue(self::KEY_BEACON_ACTIVATION_STATUS);
    }

    public function setBeaconActivationStatus($value = 'off') {
        return $this->_setConfigValue(self::KEY_BEACON_ACTIVATION_STATUS, $value);
    }

    public function getBeaconUuid() {
        return $this->_getConfigValue(self::KEY_BEACON_UUID);
    }

    public function setBeaconUuid($value = 0) {
        return $this->_setConfigValue(self::KEY_BEACON_UUID, $value);
    }

    public function getBeaconNextFlashTime() {
        return $this->_getConfigValue(self::KEY_BEACON_NEXT_FLASH_TIME);
    }

    public function setBeaconNextFlashTime($value = '0') {
        return $this->_setConfigValue(self::KEY_BEACON_NEXT_FLASH_TIME, $value);
    }

    public function getBeaconLock() {
        return $this->_getConfigValue(self::KEY_BEACON_LOCK);
    }

    public function setBeaconLock($value = 'locked') {
        if ($value == 'locked') {
            return $this->getConfigDao()->setBeaconLock();
        } else {
            return $this->_setConfigValue(self::KEY_BEACON_LOCK, $value);
        }
    }

    public function getBeaconFlashPeriod() {
        return $this->_getConfigValue(self::KEY_BEACON_FLASH_PERIOD);
    }

    /**
     * 
     * @param int $value period between two beacon flashes in seconds
     * @return type
     */
    public function setBeaconFlashPeriod($value = 7200) {
        return $this->_setConfigValue(self::KEY_BEACON_FLASH_PERIOD, $value);
    }

    public function getConfigValue($key) {
        return $this->_getConfigValue($key);
    }

    public function setConfigValue($key, $value) {
        return $this->_setConfigValue($key, $value);
    }

    public function changeConfigTable($definition) {

        try {
            $change = new SimpleXMLElement($definition);
            $operation = $change->operation;
            $operation = trim($operation . "");
            switch ($operation) {
                case 'UPDATE':
                    $key = trim($change->key . "");
                    $value = trim($change->value . "");
                    $result = $this->_setConfigValue($key, $value);
                    break;
                case 'ADD':
                    $key = trim($change->key . "");
                    $value = trim($change->value . "");
                    $config = new Config();
                    $config->setKey($key);
                    $config->setValue($value);
                    $config->save();

                default:
            }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public function getBeaconCompanyName() {
        return $this->_getConfigValue(self::KEY_BEACON_COMPANY_NAME);
    }

    public function setBeaconCompanyName($value) {
        return $this->_setConfigValue(self::KEY_BEACON_COMPANY_NAME, $value);
    }

}
