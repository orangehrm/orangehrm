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
class displayAttendanceSummaryReportCriteriaAction extends baseTimeAction {

    public function execute($request) {
        $hasRight = false;

        $this->attendancePermissions = $this->getDataGroupPermissions('attendance_summary');
        
        if($this->attendancePermissions->canRead()){
            $hasRight = true;
        }

        if (!$hasRight) {
            return $this->renderText(__("You are not allowed to view this page").'!');
        }

        $this->reportId = $request->getParameter("reportId");
        
        $userRoleManager = $this->getContext()->getUserRoleManager();
        
        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
        $requiredPermissions = array(
            BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP => array(
                'attendance_summary' => new ResourcePermission(true, false, false, false)
            )
        );
        
        $employeeList = $userRoleManager->getAccessibleEntityProperties('Employee', $properties,
                null, null, array(), array(), $requiredPermissions);

        if (is_array($employeeList)) {
            $lastRecord = end($employeeList);
            $this->lastEmpNumber = $lastRecord['empNumber'];
        }

        $this->form = new AttendanceTotalSummaryReportForm(array(), array('employeeList' => $employeeList));
    }

}

