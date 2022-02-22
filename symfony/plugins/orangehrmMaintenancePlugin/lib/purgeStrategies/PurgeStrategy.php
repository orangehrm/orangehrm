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

use OrangeHRM\Maintenance\Service\MaintenanceService;

/**
 * Class PurgeStrategy
 */
abstract class PurgeStrategy
{

    protected $parameters = array();
    protected $entityClassName = '';
    protected $entityFieldMap = array();
    protected $maintenanceService = null;

    /**
     * PurgeStrategy constructor.
     * @param $entityClassName
     * @param $infoArray
     */
    public function __construct($entityClassName, $infoArray)
    {
        $this->setEntityClassName($entityClassName);
        if (isset($infoArray['match_by'])) {
            $this->setEntityFieldMap($infoArray['match_by']);
        }
        if (isset($infoArray['parameters'])) {
            $this->setParameters($infoArray['parameters']);
        }
    }

    /**
     * @param $employeeNumber
     * @return mixed
     */
    public abstract function purge($employeeNumber);

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
    public function getEntityFieldMap()
    {
        return $this->entityFieldMap;
    }

    /**
     * @param $entityFieldMap
     */
    public function setEntityFieldMap($entityFieldMap)
    {

        if (sizeof($entityFieldMap)) {
            $this->entityFieldMap = $entityFieldMap[0];
        }
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

        $entityFieldMap[$this->getEntityFieldMap()['match']] = $employeeNumber;
        if ($this->getEntityFieldMap()['join']) {
            $entityFieldMap['join'] = $this->getEntityFieldMap()['join'];
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
}
