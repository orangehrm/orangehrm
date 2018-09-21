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

/**
 * Class AbstractPurgeStrategy
 */
abstract class AbstractPurgeStrategy
{

    protected $parameters = array();
    protected $entityClassName;
    protected $matchByArray = array();
    protected $matchingCriteria;
    protected $maintenanceService;

    /**
     * AbstractPurgeStrategy constructor.
     * @param $entityClassName
     * @param $infoArray
     */
    public function __construct($entityClassName, $infoArray)
    {
        $this->setEntityClassName($entityClassName);
        $this->setMatchingCriteria($infoArray['matching_criteria']);
        if (isset($infoArray['match_by'])) {
            $this->setMatchBy($infoArray['match_by']);
        }
        if (isset($infoArray['parameters'])) {
            $this->setParameters($infoArray['parameters']);
        }
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param $parametersArray
     */
    public function setParameters($parametersArray)
    {
        $this->parameters = $parametersArray;
    }

    /**
     * @return mixed
     */
    public function getEntityClassName()
    {
        return $this->entityClassName;
    }

    /**
     * @param $entityClassName
     */
    public function setEntityClassName($entityClassName)
    {
        $this->entityClassName = $entityClassName;
    }

    /**
     * @return mixed
     */
    public function getMatchingCriteria()
    {
        return $this->matchingCriteria;
    }

    /**
     * @param $matchingCriteria
     */
    public function setMatchingCriteria($matchingCriteria)
    {
        $this->matchingCriteria = $matchingCriteria;
    }

    /**
     * @return array
     */
    public function getMatchBy()
    {
        return $this->matchByArray;
    }

    /**
     * @param $matchByArray
     */
    public function setMatchBy($matchByArray)
    {
        $this->matchByArray = $matchByArray;
    }

    /**
     * @return bool
     */
    public function isSingle()
    {
        return $this->getMatchingCriteria() == PurgeStrategy::MATCHING_CRITERIA_ONE;
    }

    /**
     * @return MaintenanceService
     */
    public function getMaintenanceService()
    {
        if (!isset($this->maintenanceService)) {
            $this->maintenanceService = new MaintenanceService();
        }
        return $this->maintenanceService;
    }

    /**
     * @param MaintenanceService $maintenanceService
     */
    public function setMaintenanceService($maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    /**
     * @param $employeeNumber
     * @return array
     */
    public function getMatchByValues($employeeNumber)
    {
        $matchValueArray = array();
        foreach ($this->getMatchBy() as $matchBy) {
            $matchValueArray[$matchBy['match']] = $employeeNumber;
        }
        return $matchValueArray;
    }

}
