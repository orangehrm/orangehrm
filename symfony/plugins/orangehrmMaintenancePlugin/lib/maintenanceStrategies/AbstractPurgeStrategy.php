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

abstract class AbstractPurgeStrategy implements PurgeStrategy {



    protected $parameters = array();
    protected $entityClassName;
    protected $matchByArray = array();
    protected $matchingCriteria;
    protected $maintenanceService;

    public function __construct($entityClassName, $infoArray) {
        $this->setEntityClassName($entityClassName);
        $this->setMatchingCriteria($infoArray['matching_criteria']);
        if(isset($infoArray['match_by'])) {
            $this->setMatchBy($infoArray['match_by']);
        }
        if(isset($infoArray['parameters'])) {
            $this->setParameters($infoArray['parameters']);
        }
    }

    public function getParameters() {
        return $this->parameters;
    }

    public function setParameters($parametersArray) {
        $this->parameters = $parametersArray;
    }

    public function getEntityClassName() {
        return $this->entityClassName;
    }

    public function setEntityClassName($entityClassName) {
        $this->entityClassName = $entityClassName;
    }

    public function getMatchingCriteria() {
        return $this->matchingCriteria;
    }

    public function setMatchingCriteria($matchingCriteria) {
        $this->matchingCriteria = $matchingCriteria;
    }

    public function getMatchBy() {
        return $this->matchByArray;
    }

    public function setMatchBy($matchByArray) {
        $this->matchByArray = $matchByArray;
    }

    public function isSingle() {
        return $this->getMatchingCriteria() == PurgeStrategy::MATCHING_CRITERIA_ONE;
    }

    /**
     * @return MaintenanceService
     */
    public function getMaintenanceService() {
        if(!isset($this->maintenanceService)) {
            $this->maintenanceService = new MaintenanceService();
        }
        return $this->maintenanceService;
    }

    /**
     * @param MaintenanceService $maintenanceService
     */
    public function setMaintenanceService($maintenanceService) {
        $this->maintenanceService = $maintenanceService;
    }

    public function getMatchByValues($purgeEntity, $optionalValues = array()) {
        $matchValueArray = array();
        foreach ($this->getMatchBy() as $matchBy) {
            $matchValueArray[$matchBy['match']] = $purgeEntity[$matchBy['to']];
        }
        return $matchValueArray;
    }

}