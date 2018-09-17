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
class PurgeForm extends sfForm
{
    /**
     *
     */
    public function configure()
    {
        $this->setWidgets($this->getWidgetList());
        $this->setValidators($this->getValidatorList());
        $this->getWidgetSchema()->setLabels($this->getLabelList());
    }

    /**
     * @return array
     */
    public function getWidgetList()
    {
        $widgets = array();
        $widgets['employee'] = new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson()));
        return $widgets;
    }

    /**
     * @return array
     */
    public function getValidatorList()
    {
        $validators = array();
        $validators['employee'] = new ohrmValidatorEmployeeNameAutoFill(array('required' => true));
        return $validators;
    }

    /**
     * @return array
     */
    public function getLabelList()
    {
        $requiredMarker = ' <em>*</em>';
        $lableList = array();
        $lableList['employee'] = __('Select Terminated Employee') . $requiredMarker;
        return $lableList;
    }

    /**
     * @return string
     * @throws DaoException
     * @throws sfException
     */
    protected function getEmployeeListAsJson()
    {
        $jsonArray = array();
        $properties = array("empNumber", "firstName", "middleName", "lastName", 'termination_id', 'purged_at');
        $goalPermissions = $this->getOption('goalPermissions');
        $requiredPermissions = array(
            BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP => $goalPermissions);
        $employeeList = UserRoleManagerFactory::getUserRoleManager()
            ->getAccessibleEntityProperties('Employee', $properties, null, null, array(), array(), $requiredPermissions);
        $empNo = sfContext::getInstance()->getUser()->getAttribute('user')->getEmployeeNumber();

        $employeeService = new EmployeeService();

        if (!is_null($empNo)) {
            $currentUser = $employeeService->getEmployee($empNo);
            $currentEmployee = array(
                'termination_id' => $currentUser->getTerminationId(),
                'empNumber' => $currentUser->getEmpNumber(),
                'firstName' => $currentUser->getFirstName(),
                'middleName' => $currentUser->getMiddleName(),
                'lastName' => $currentUser->getLastName(),
                'purged_at' => $currentUser->getPurgedAt()
            );
            $employeeList[] = $currentEmployee;
        }
        $employeeUnique = array();
        foreach ($employeeList as $employee) {

            $terminationId = $employee['termination_id'];
            $empNumber = $employee['empNumber'];
            $purge = $employee['purged_at'];

//            if (!isset($employeeUnique[$empNumber]) && !empty($terminationId) && empty($purge)) {
            if (!isset($employeeUnique[$empNumber]) && !empty($terminationId)) {
                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName'] . '(Past Employee)');
                $employeeUnique[$empNumber] = $name;
                $jsonArray[] = array('name' => $name, 'id' => $empNumber);
            }
        }
        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }
}
