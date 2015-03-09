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
class sendBeaconMessageAjaxAction extends sfAction {

    protected $beaconConfigService;
    protected $beaconDatapointService;
    protected $beaconCommunicationService;
    

    /**
     * 
     *
     * @return BeaconCommunicationsService
     */
    protected function getBeaconCommunicationService() {
        if (is_null($this->beaconCommunicationService)) {
            $this->beaconCommunicationService = new BeaconCommunicationsService();
        }

        return $this->beaconCommunicationService;
    }

    /**
     * 
     * @return BeaconDatapointService
     */
    protected function getBeaconDatapointService() {
        if (is_null($this->beaconDatapointService)) {
            $this->beaconDatapointService = new BeaconDatapointService();
        }
        return $this->beaconDatapointService;
    }
    
    /**
     * 
     * @return BeaconConfigurationService
     */
    protected function getBeaconConfigService() {
        if (is_null($this->beaconConfigService)) {
            $this->beaconConfigService = new BeaconConfigurationService();
        }
        return $this->beaconConfigService;
    }
    
    
    public function execute($request) {
        
        if ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED) && $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED)) {
            $this->getUser()->setAttribute(BeaconCommunicationsService::BEACON_ACTIVATION_REQUIRED, false);
            $result = $this->getBeaconCommunicationService()->sendRegistrationMessage();
            if ($result && $this->getBeaconConfigService()->getBeaconActivationAcceptanceStatus()=='on') {
                $this->getBeaconCommunicationService()->getBeaconMessages();
                $this->getBeaconCommunicationService()->sendBeaconFlash();
            }
            $this->getBeaconCommunicationService()->releaseLock();
        } else if ($this->getUser()->hasAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED) && $this->getUser()->getAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED)) {
            $this->getUser()->setAttribute(BeaconCommunicationsService::BEACON_FLASH_REQUIRED, false);
            $this->getBeaconCommunicationService()->getBeaconMessages();
            $this->getBeaconCommunicationService()->sendBeaconFlash();
            $this->getBeaconCommunicationService()->releaseLock();
        }

        return sfView::NONE;
    }

    

}
