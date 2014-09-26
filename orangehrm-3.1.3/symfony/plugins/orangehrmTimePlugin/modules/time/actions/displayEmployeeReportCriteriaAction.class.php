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
 * Boston, MA  02110-1301, USA
 */
class displayEmployeeReportCriteriaAction extends displayReportCriteriaAction {

    public function execute($request) {

        $hasRight = false;
        
        $this->employeeReportsPermissions = $this->getDataGroupPermissions('time_employee_reports');
        
        if($this->employeeReportsPermissions->canRead()){
            $hasRight = true;
        }

        if (!$hasRight) {
            return $this->renderText(__("You are not allowed to view this page")."!");
        }
        parent::execute($request);
    }

    public function setReportCriteriaInfoInRequest($formValues) {

        $employee = $formValues["employee"];
        $empName = $employee['empName'];
        
        $this->getRequest()->setParameter('empName', $empName);
    }

    public function setForward() {

        $this->forward('time', 'displayEmployeeReport');
    }

    public function setStaticColumns($formValues) {
        
    }
    
    public function getDataGroups() {
        return array('time_employee_reports');
    }

}

