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
class BeaconDatapointService extends BaseService {

    protected $beaconDatapointDao;

    protected function getBeaconDatapointDao() {
        if (is_null($this->beaconDatapointDao)) {
            $this->beaconDatapointDao = new BeaconDatapointDao();
        }
        return $this->beaconDatapointDao;
    }

    /**
     * 
     * @return Doctrine_Collection DataPoint
     */
    public function getAllDatapoints() {
        return $this->getBeaconDatapointDao()->getAllDatapoints();
    }

    /**
     * @return array associative array with datapoint names as jeys and respective values inserted
     */
    public function resolveAllDatapoints() {
        $datapoints = $this->getAllDatapoints();
        $results = array();
        foreach ($datapoints as $point) {
            $datapointProcessor = $point->getDataPointType()->getActionClass();
            $name = $point->getName();
            if(isset($datapointProcessor)) {
                $processor = new $datapointProcessor();                
                $currentResult = $processor->process($point->getDefinition());
                
                if(isset($currentResult)) {
                    $results[$name] =  $currentResult;
                }
            }
        }
        
        return $results;
    }
    
    
    public function getDatapointTypeByName($name) {
        return $this->getBeaconDatapointDao()->getDatapointTypeByName($name);
    }
    
    public function getDatapointByName($name) {
        return $this->getBeaconDatapointDao()->getDatapointByName($name);
    }
    
    public function deleteDatapointByName($name) {
        return $this->getBeaconDatapointDao()->deleteDatapointByName($name);
    }    
    
}
