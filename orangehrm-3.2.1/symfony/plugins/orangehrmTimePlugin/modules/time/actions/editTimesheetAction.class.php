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
class editTimesheetAction extends baseTimeAction {

    private $timesheetService;
    private $timesheetPeriodService;
    private $totalRows = 0;
    private $employeeService;

    public function getEmployeeService() {

        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }

        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {

        if ($employeeService instanceof EmployeeService) {
            $this->employeeService = $employeeService;
        }
    }

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

    public function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {

            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    public function execute($request) {
        
        $this->listForm = new DefaultListForm();

        $this->backAction = $request->getParameter('actionName');
        $this->timesheetId = $request->getParameter('timesheetId');
        $this->employeeId = $request->getParameter('employeeId');

        $loggedInEmpNumber = $this->getContext()->getUser()->getEmployeeNumber();

        /* For highlighting corresponding menu item */
        if ($this->employeeId == $loggedInEmpNumber) {
            $request->setParameter('initialActionName', 'viewMyTimesheet');
        } else {
            $request->setParameter('initialActionName', 'viewEmployeeTimesheet');            
        }


        $this->timesheetPermissions = $this->getDataGroupPermissions('time_employee_timesheets', $this->employeeId);

        $this->_checkAuthentication($this->employeeId);

        if ($this->employeeId == $loggedInEmpNumber) {
            $this->employeeName = null;
        } else {
            $this->employeeName = $this->getEmployeeName($this->employeeId);
        }



        $timesheet = $this->getTimesheetService()->getTimesheetById($this->timesheetId);

        $this->date = $timesheet->getStartDate();
        $this->endDate = $timesheet->getEndDate();
        $this->startDate = $this->date;
        $this->noOfDays = $this->timesheetService->dateDiff($this->startDate, $this->endDate);
        $values = array('date' => $this->startDate, 'employeeId' => $this->employeeId, 'timesheetId' => $this->timesheetId, 'noOfDays' => $this->noOfDays);
        $this->timesheetForm = new TimesheetForm(array(), $values);
        $this->currentWeekDates = $this->timesheetForm->getDatesOfTheTimesheetPeriod($this->startDate, $this->endDate);
        $this->timesheetItemValuesArray = $this->timesheetForm->getTimesheet($this->startDate, $this->employeeId, $this->timesheetId);

        $this->messageData = array($request->getParameter('message[0]'), $request->getParameter('message[1]'));

        if ($this->timesheetItemValuesArray == null) {

            $this->totalRows = 0;
            $this->timesheetForm = new TimesheetForm(array(), $values);
        } else {

            $this->totalRows = sizeOf($this->timesheetItemValuesArray);
            $this->timesheetForm = new TimesheetForm(array(), $values);
        }
        $this->formToImplementCsrfToken = new TimesheetFormToImplementCsrfTokens();

        if ($request->isMethod('post')) {

            if ($request->getParameter('btnSave')) {
                
                if( $this->timesheetForm->getCSRFtoken() == $request->getParameter('_csrf_token')){
                    $backAction = $this->backAction;
                    $this->getTimesheetService()->saveTimesheetItems($request->getParameter('initialRows'), $this->employeeId, $this->timesheetId, $this->currentWeekDates, $this->totalRows);
                    $this->messageData = array('success', __(TopLevelMessages::SAVE_SUCCESS));
                    
                    $timeSheet = $this->getTimesheetService()->getTimesheetById($this->timesheetId);
                    
                    $resultingState = $this->getResultingState($timeSheet, 
                            PluginWorkflowStateMachine::TIMESHEET_ACTION_MODIFY,
                            $loggedInEmpNumber == $timesheet->getEmployeeId());
                    
                    if ($resultingState != $timeSheet->getState()) {
                        $timesheet->setState($resultingState);
                        $this->getTimesheetService()->saveTimesheet($timesheet);
                    }
                    
                    $startingDate = $timeSheet->getStartDate();
                    $this->redirect('time/' . $backAction . '?' . http_build_query(array('message' => $this->messageData, 'timesheetStartDate' => $startingDate, 'employeeId' => $this->employeeId)));
                 }

            }

            if ($request->getParameter('buttonRemoveRows')) {
                $this->messageData = array('success', __('Successfully Removed'));
            }
        }
    }
    
    protected function _checkAuthentication($empNumber, $user) {

        $loggedInEmpNumber = $this->getUser()->getEmployeeNumber();

        if ($loggedInEmpNumber == $empNumber) {
            return;
        }

        $userRoleManager = $this->getContext()->getUserRoleManager();
        if (!$userRoleManager->isEntityAccessible('Employee', $empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }

    }

    private function getEmployeeName($employeeId) {

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($employeeId);

        $name = $employee->getFirstName() . " " . $employee->getLastName();

        if ($employee->getTerminationId()) {
            $name = $name . ' (' . __('Past Employee') . ')';
        }

        return $name;
    }

}

