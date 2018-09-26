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

/**
 * Class ReplaceWithValuePurgeStrategy
 */
class ReplaceWithValuePurgeStrategy extends PurgeStrategy
{

    /**
     * @param $employeeNumber
     * @throws DaoException
     */
    public function purge($employeeNumber)
    {
        $fieldValues = $this->getFieldValues($employeeNumber);
        $matchByValues = $this->getMatchByValues($employeeNumber);
        $this->getMaintenanceService()->replaceEntityValues($this->getEntityClassName(), $fieldValues, $matchByValues);
    }

    /**
     * @param $employeeNumber
     * @return array
     * @throws DaoException
     */
    public function getFieldValues($employeeNumber)
    {
        $filedValueArray = array();
        $replaceFields = $this->getParameters();
        $purgeEntity = $this->getEmployeeService()->getEmployee($employeeNumber);

        foreach ($replaceFields as $replaceColumnArrayData) {
            $currentField = $replaceColumnArrayData['field'];
            $replaceStrategy = $this->getReplaceStrategy($replaceColumnArrayData['class']);
            $currentFieldValue = $purgeEntity->$currentField;
            $replace = $replaceStrategy->getFormattedValue($currentFieldValue);
            $filedValueArray[$replaceColumnArrayData['field']] = $replace;
        }
        return $filedValueArray;
    }

    /**
     * @param $strategy
     * @return mixed
     */
    public function getReplaceStrategy($strategy)
    {
        return new $strategy;
    }

    /**
     * @return EmployeeService
     */
    public function getEmployeeService()
    {
        if (!isset($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

}
