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
 * Class AccessStrategy
 */
abstract class AccessStrategy
{

    protected $parameters = array();
    protected $entityClassName = null;
    protected $matchByArray = array();
    protected $matchingCriteria = array();
    protected $maintenanceService = null;
    protected $getRealValueClass = null;

    /**
     * AccessStrategy constructor.
     * @param $entityClassName
     * @param $infoArray
     */
    public function __construct($entityClassName, $infoArray)
    {
        $this->setEntityClassName($entityClassName);
        if (isset($infoArray['match_by'])) {
            $this->setMatchBy($infoArray['match_by']);
        }
        if (isset($infoArray['parameters'])) {
            $this->setParameters($infoArray['parameters']);
        }
    }

    /**
     * @param $employeeNumber
     * @return mixed
     */
    public abstract function access($employeeNumber);

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
        $entityFieldMap = array();

        $entityFieldMap[$this->getMatchBy()[0]['match']] = $employeeNumber;
        if ($this->getMatchBy()[0]['join']) {
            $entityFieldMap['join'] = $this->getMatchBy()[0]['join'];
        }
        return $entityFieldMap;
    }

    /**
     * @param $matchByValues
     * @param $table
     * @return mixed
     * @throws DaoException
     */
    public function getEntityRecords($matchByValues, $table)
    {
        return $this->getMaintenanceService()->extractDataFromEmpNumber($matchByValues, $table);
    }

    /**
     * @param $aceessClassName
     * @param $currentValue
     * @return mixed
     */
    public function getFormattedValue($aceessClassName, $currentValue)
    {
        $this->getRealValueClass = new $aceessClassName();
        return $this->getRealValueClass->getFormattedValue($currentValue);
    }

    /**
     * @param $accessEntity
     * @return array
     */
    public function addRecordsToArray($accessEntity)
    {

        $parameters = $this->getParameters();
        $data = array();
        foreach ($parameters as $field) {
            $columnName = $field['field'];
            if ($accessEntity->$columnName) {
                $value = $accessEntity->$columnName;
                if ($field['class']) {
                    $value = $this->getFormattedValue($field['class'], $value);
                }
                $data[$columnName] = $value;
            }
        }
        return $data;
    }
}
