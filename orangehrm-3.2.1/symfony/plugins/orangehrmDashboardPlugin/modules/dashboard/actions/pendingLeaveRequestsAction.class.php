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

/**
 * Description of pendingLeaveRequestsAction
 */
class pendingLeaveRequestsAction extends BaseDashboardAction {

    public function preExecute() {
        $this->setLayout(false);
        parent::preExecute();
    }

    public function execute($request) {
        $empNumber = null;
        $mode = LeaveListForm::MODE_ADMIN_LIST;
        $employeeFilter = $this->getEmployeeFilter($mode, $empNumber);
        $searchParams = new ParameterObject(array(
            'dateRange' => $this->getDateRange(),
            'statuses' => array(PluginLeave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL),
            'leaveTypeId' => null,
            'employeeFilter' => $employeeFilter,
            'noOfRecordsPerPage' => 100, // avoid pagination
            'cmbWithTerminated' => null,
            'subUnit' => null,
            'employeeName' => null
        ));
        $result = $this->searchLeaveRequests($searchParams);
        $this->leaveList = array();

        //Start sorting
        foreach ($result['list'] as $arraykey => $leaveRequest) {
            $key = count($this->leaveList);
            $leaveData = array();
            $leaveData['leaveRequestId'] = $leaveRequest->getId();
            $leaveData['dateApplied'] = $leaveRequest->getDateApplied();
            $employee = $leaveRequest->getEmployee();
            $leaveData['firstName'] = $employee->getFirstName();
            $leaveData['lastName'] = $employee->getLastName();
            $employeeLastName[$key] = $leaveData['lastName'];
            $leaveStartDate[$key] = $leaveData['dateApplied'];
            $this->leaveList[$key] = $leaveData;
        }
        $this->recordCount = $result['meta']['record_count'];

        (array_multisort($leaveStartDate, SORT_ASC, $employeeLastName, SORT_ASC, $this->leaveList));
    }

    public function getEmployeeFilter($mode, $empNumber) {
        $userDetails = $this->getLoggedInUserDetails();
        $loggedInEmpNumber = $userDetails['loggedUserEmpId'];
        $employeeFilter = null;

        if ($mode == LeaveListForm::MODE_MY_LEAVE_LIST) {
            $employeeFilter = $loggedInEmpNumber;
        } else {
            $requiredPermissions = array(
                BasicUserRoleManager::PERMISSION_TYPE_DATA_GROUP => array(
                    'leave_list' => new ResourcePermission(true, false, false, false)
                )
            );
            $manager = $this->getContext()->getUserRoleManager();
            $accessibleEmpIds = $manager->getAccessibleEntityIds('Employee', null, null, array(), array(), $requiredPermissions);

            if (empty($empNumber)) {
                $employeeFilter = $accessibleEmpIds;
            } else {
                if (in_array($empNumber, $accessibleEmpIds)) {
                    $employeeFilter = $empNumber;
                } else {
                    // Requested employee is not accessible. 
                    $employeeFilter = array();
                }
            }
        }
        return $employeeFilter;
    }

    public function getDateRange() {
        return new DateRange(date("Y-m-01", strtotime("- 1 month")), date("Y-m-t", strtotime("+ 1 month")));
    }

    public function searchLeaveRequests($searchParams, $page = 1) {
        $result = $this->getLeaveRequestService()->searchLeaveRequests($searchParams, $page);
        return $result;
    }

}
