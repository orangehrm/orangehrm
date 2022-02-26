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
 * Class PurgeEmployeeForm
 */
class PurgeEmployeeForm extends sfForm
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
     * @throws DaoException
     * @throws sfException
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
        $lableList['employee'] = __('Past Employee') . $requiredMarker;
        return $lableList;
    }

    /**
     * @return string
     * @throws DaoException
     * @throws sfException
     */
    protected function getEmployeeListAsJson()
    {
        $purgeEmployeeList = $this->getMaintenanceService()->getPurgeEmployeeList();
        $jsonArray = array();
        foreach ($purgeEmployeeList as $employee) {
            $empNumber = $employee['empNumber'];
            $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName'] . '(Past Employee)');
            $jsonArray[] = array('name' => $name, 'id' => $empNumber);
        }
        $jsonString = json_encode($jsonArray);
        return $jsonString;
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
}
