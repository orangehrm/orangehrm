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
class viewTimesheetAction extends baseTimeAction {

    private $timesheetService;
    private $timesheetPeriodService;
    private $timesheetActionLog;
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

    public function execute($request) {
        /* For highlighting corresponding menu item */  
        $request->setParameter('initialActionName', 'viewEmployeeTimesheet');  

        $employeeId = $request->getParameter('employeeId');
                    
        $loggedInEmpNumber = $this->getUser()->getEmployeeNumber();
                    
        $this->timesheetPermissions = $this->getDataGroupPermissions('time_employee_timesheets', $employeeId);

        $this->_checkAuthentication($employeeId);

        $userRoleManager = $this->getContext()->getUserRoleManager();
        $user = $userRoleManager->getUser();
        $userId = $user->getId();

        $this->employeeName = $this->getEmployeeName($employeeId);

        $this->createTimesheetForm = new CreateTimesheetForm();
        $this->currentDate = date('Y-m-d');

        $this->headingText = $this->getTimesheetPeriodService()->getTimesheetHeading();
        $this->successMessage = array($request->getParameter('message[0]'), $request->getParameter('message[1]'));
        $this->timeService = $this->getTimesheetService();

        /* This action is called from viewTimesheetAction, when the user serches a previous timesheet, if not finds a start date from
         * back btn from editTimesheet. */

        $selectedTimesheetStartDate = $request->getParameter('timesheetStartDateFromDropDown');
        if (!isset($selectedTimesheetStartDate)) {
            $selectedTimesheetStartDate = $request->getParameter('timesheetStartDate');
        }

        $this->actionName = $this->getActionName();
        $this->format = $this->getTimesheetService()->getTimesheetTimeFormat();


        /* Error message when there is no timesheet to view */
        if ($this->getContext()->getUser()->hasFlash('errorMessage')) {

            $this->messageData = array('warning', __($this->getContext()->getUser()->getFlash('errorMessage')));
        } else {


            $this->dateForm = new startDaysListForm(array(), array('employeeId' => $employeeId));
            $dateOptions = $this->dateForm->getDateOptions();

            if ($dateOptions == null) {

                $this->messageData = array('warning', __("No Accessible Timesheets"));
            }

            if ($this->getContext()->getUser()->hasFlash('TimesheetStartDate')) {                 //this is admin or supervisor accessing the viewTimesheet from by clicking the "view" button
                $startDate = $this->getContext()->getUser()->getFlash('TimesheetStartDate');
            } elseif (!isset($selectedTimesheetStartDate)) {                                      // admin or the supervisor enters the name of the employee and clicks on the view button
                $startDate = $this->getStartDate($dateOptions);
            } else {

                $startDate = $selectedTimesheetStartDate;

                // this sets the start day as the start date set by the search drop down or the coming back from the edit action
            }

            /* This action is checks whether the start date set. If not the current date is set. */
            if (isset($startDate)) {
                $this->toggleDate = $startDate;
            }

            $this->timesheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($startDate, $employeeId);

            $this->currentState = $this->timesheet->getState();

            if (isset($startDate)) {
                $selectedIndex = $this->dateForm->returnSelectedIndex($startDate, $employeeId);
            }

            if (isset($selectedIndex)) {
                $this->dateForm->setDefault('startDates', $selectedIndex);
            }

            $noOfDays = $this->timesheetService->dateDiff($this->timesheet->getStartDate(), $this->timesheet->getEndDate());
            $values = array('date' => $startDate, 'employeeId' => $employeeId, 'timesheetId' => $this->timesheet->getTimesheetId(), 'noOfDays' => $noOfDays);
            $form = new TimesheetForm(array(), $values);
            $this->timesheetRows = $form->getTimesheet($startDate, $employeeId, $this->timesheet->getTimesheetId());
            $this->formToImplementCsrfToken = new TimesheetFormToImplementCsrfTokens();
            if ($request->isMethod('post')) {
                $this->formToImplementCsrfToken->bind($request->getParameter('time'));

                if ($this->formToImplementCsrfToken->isValid()) {

                    $action = $request->getParameter('act');

                    // check if action allowed and get next state
                    $excludeRoles = array();
                    $includeRoles = array();
                    
                    if ($loggedInEmpNumber == $employeeId && $userRoleManager->essRightsToOwnWorkflow()) {
                        $includeRoles = array('ESS');
                    }
             
                    $entities = array('Employee' => $employeeId);
                                           
                    $allowedActions = $userRoleManager->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, $this->currentState, $excludeRoles, $includeRoles, $entities);
                    
                    if (isset($allowedActions[$action])) {
                        
                        $state = $allowedActions[$action]->getResultingState();
                        $this->successMessage = array('success', __("Timesheet " . ucwords(strtolower($state))));
                        
                        $comment = $request->getParameter('Comment');
                        $this->timesheet->setState($state);
                        $this->timesheet = $this->getTimesheetService()->saveTimesheet($this->timesheet);

                        if ($request->getParameter('updateActionLog')) {

                            if ($action == WorkflowStateMachine::TIMESHEET_ACTION_RESET) {

                                $this->setTimesheetActionLog(Timesheet::RESET_ACTION, $comment, $this->timesheet->getTimesheetId(), $userId);
                            } else {
                                $this->setTimesheetActionLog($state, $comment, $this->timesheet->getTimesheetId(), $userId);
                            }

                            if ($action == WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT) {
                                $this->successMessage = array('success', __("Timesheet Submitted"));
                            }
                        }
                    }                    
                }
            }

            $this->currentState = $this->timesheet->getState();
            
            $excludeRoles = array();
            $includeRoles = array();
            if ($loggedInEmpNumber == $employeeId && $userRoleManager->essRightsToOwnWorkflow()) {
                $includeRoles = array('ESS');
            }
                    
            $entities = array('Employee' => $employeeId);
                                           
            $initialStateActions = $userRoleManager->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, PluginTimesheet::STATE_INITIAL, $excludeRoles, $includeRoles, $entities);
            $this->allowedToCreateTimesheets = isset($initialStateActions[WorkflowStateMachine::TIMESHEET_ACTION_CREATE]);
            
            $this->allowedActions = $userRoleManager->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, $this->currentState, $excludeRoles, $includeRoles, $entities);
            
            $this->rowDates = $form->getDatesOfTheTimesheetPeriod($this->timesheet->getStartDate(), $this->timesheet->getEndDate());
            $this->actionLogRecords = $this->getTimesheetService()->getTimesheetActionLogByTimesheetId($this->timesheet->getTimesheetId());
        }
    }

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

    public function getTimesheetActionLog() {

        if (is_null($this->timesheetActionLog)) {

            $this->timesheetActionLog = new TimesheetActionLog();
        }

        return $this->timesheetActionLog;
    }

    public function setTimesheetActionLog($state, $comment, $timesheetId, $employeeId) {

        $timesheetActionLog = $this->getTimesheetActionLog();
        $timesheetActionLog->setAction($state);
        $timesheetActionLog->setComment($comment);
        $timesheetActionLog->setTimesheetId($timesheetId);
        $timesheetActionLog->setDateTime(date("Y-m-d"));
        $timesheetActionLog->setPerformedBy($employeeId);

        $this->getTimesheetService()->saveTimesheetActionLog($timesheetActionLog);
    }

    public function getStartDate($dateOptions) {

        $temp = $dateOptions[0];
        $tempArray = explode(" ", $temp);
        return $tempArray[0];
    }

    public function getEmployeeName($employeeId) {

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($employeeId);

        $name = $employee->getFirstName() . " " . $employee->getLastName();

        if ($employee->getTerminationId()) {
            $name = $name . ' ('. __('Past Employee') . ')';
        }

        return $name;
    }

    protected function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {

            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

    protected function _checkAuthentication($empNumber) {

        $userRoleManager = $this->getContext()->getUserRoleManager();
        
        if (!$userRoleManager->isEntityAccessible('Employee', $empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }

    }

}
