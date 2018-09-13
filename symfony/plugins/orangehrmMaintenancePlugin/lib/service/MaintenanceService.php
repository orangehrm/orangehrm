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
class MaintenanceService
{

    public $employeeDao;

    public function purgeEmployee($empNumber)
    {
        $connection = Doctrine_Manager::getInstance()->getCurrentConnection();
        try {
            $connection->beginTransaction();
            $purgeableEntities = $this->getPurgeableEntities();
            $employee = $this->getPurgeDao()->getSoftDeletedEmployee($empNumber);
            $optionalValues = array();
            $optionalValues['employeePurgeName'] = $this->getPurgedEmployeeName($employee);

//            var_dump($optionalValues);die;
            foreach ($purgeableEntities as $purgeableEntityClassName => $purgeStrategies) {
                foreach ($purgeStrategies as $strategy => $strategyInfoArray) {
                    $strategy = $this->getPurgeStrategy($purgeableEntityClassName, $strategy, $strategyInfoArray);
                    $strategy->purge($employee, $optionalValues);
                }
            }
            $connection->commit();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $connection->rollback();
            Logger::getLogger('maintenance')->error($e->getCode() . ' - ' . $e->getMessage(), $e);
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    // read yml and return all data
    public function getPurgeableEntities()
    {
        if (!isset($this->purgeableEntities)) {
            $this->purgeableEntities = sfYaml::load(realpath(dirname(__FILE__) . '/../../config/gdpr_purge_employee_strategy.yml'));
        }
        return $this->purgeableEntities;
    }
    // get PurgeDao Object
    public function getPurgeDao()
    {
        if (!isset($this->purgeDao)) {
            $this->purgeDao = new PurgeDao();
        }
        return $this->purgeDao;
    }
    // set purge Employee Name to emp num+ Job Title
    public function getPurgedEmployeeName($purgeEmployee)
    {
        if (!isset($this->purgedEmployeeName)) {
            $employeeNumber = $purgeEmployee->getEmpNumber();
            $jobTitle = $purgeEmployee->getJobTitle();
            $jobTitleName = "No Job Title";
            if ($jobTitle && $jobTitle->getId()) {
                $jobTitleName = $jobTitle->getJobTitleName();
            }
            $this->purgedEmployeeName = $employeeNumber . " " . $jobTitleName;
        }
        return $this->purgedEmployeeName;
    }

//    Get strategy neme ane return new Object of Each strategy
    public function getPurgeStrategy($purgeableEntityClassName, $strategy, $strategyInfoArray)
    {
        $purgeStrategy = $strategy . "PurgeStrategy";
        return new $purgeStrategy($purgeableEntityClassName, $strategyInfoArray);
    }


//    methods for save entity
    public function saveEntity($entity)
    {
        return $this->getPurgeDao()->saveEntity($entity);
    }

//     get random Id which Is not already exits
    public function getEmployeePurgeId($employeeNumber)
    {
        do {
            $purgeEmployeeId = $this->getEmployeeIdWithRandomString($employeeNumber);
        } while ($this->getPurgeDao()->isEmployeeIdExists($purgeEmployeeId));
        return $purgeEmployeeId;
    }
    public function getEmployeeIdWithRandomString($employeeNumber)
    {
        $randomText = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
        $purgeEmployeeId = $randomText . "-" . $employeeNumber;
        return $purgeEmployeeId;
    }

    // replace value with each value given
    public function replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray)
    {
        return $this->getPurgeDao()->replaceEntityValues($entityClassName, $fieldValueArray, $matchByValuesArray);
    }
//    Remove and destroy given data
    public function removeEntities($entityClassName, $matchValuesArray) {
        return $this->getPurgeDao()->removeEntities($entityClassName, $matchValuesArray);
    }

}