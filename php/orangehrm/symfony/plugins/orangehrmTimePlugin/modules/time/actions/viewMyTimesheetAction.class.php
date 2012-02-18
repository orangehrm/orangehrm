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
class viewMyTimesheetAction extends sfAction {

    private $timesheetService;
    private $timesheetActionLog;
    public $timesheetStartingDate;
    private $timesheetPeriodService;

    public function execute($request) {

        //timesheetStartDateFromDropDown is from the drop down
        //timesheetStartDate is from the edit timesheet
        //$startDateSelectedFromDropDown set when the user is accessing the view from the search drop down
        //$startDateOfTheTimesheetForUpdates is set when the user performs an action on the timesheet,and it is used to update the timesheet
        $this->createTimesheetForm = new CreateTimesheetForm();
        $this->currentDate = date('Y-m-d');
        $this->headingText = $this->getTimesheetPeriodService()->getTimesheetHeading();

        $this->successMessage = array($request->getParameter('message[0]'), $request->getParameter('message[1]'));
        $startDateSelectedFromDropDown = $request->getParameter('timesheetStartDateFromDropDown');
        $this->userObj = $this->getContext()->getUser()->getAttribute('user');
        $userId = $this->userObj->getUserId();
        $this->format = $this->getTimesheetService()->getTimesheetTimeFormat();
        $this->timeService = $this->getTimesheetService();
        $clientTimeZoneOffset = $this->userObj->getUserTimeZoneOffset();
        $serverTimezoneOffset = ((int) date('Z'));
        $timeStampDiff = $clientTimeZoneOffset * 3600 - $serverTimezoneOffset;

        if ($request->isMethod('post')) {
            if ($request->getParameter('updateActionLog')) {
                $timesheet = $this->setTimesheetState($request);
                $comment = $request->getParameter('Comment');




                if ($request->getParameter('resetAction')) {

                    $this->setTimesheetActionLog(Timesheet::RESET_ACTION, $comment, $timesheet->getTimesheetId(), $userId);
                } else {
                    $this->setTimesheetActionLog($timesheet->getState(), $comment, $timesheet->getTimesheetId(), $userId);
                }
            }
        }
        /* Decorated user object in the user session, which can be used only to get user's employee number, user id, employee list and accessible Time menus */

        $employeeId = $this->userObj->getEmployeeNumber();
        $this->currentDate = date('Y-m-d');
        $this->actionName = $this->getActionName();

        $submitted = $request->getParameter('submitted');
        if (isset($submitted)) {
            $this->successMessage = array('SUCCESS', __("Timesheet Submitted"));
        }

        $startDateOfTheTimesheetForUpdates = $request->getParameter('timesheetStartDate');
        $this->dateForm = new startDaysListForm(array(), array('employeeId' => $employeeId));
        $dateOptions = $this->dateForm->getDateOptions();



        if (isset($startDateSelectedFromDropDown)) {                                   // timesheet is access via the search drop down
            $this->toggleDate = $startDateSelectedFromDropDown;
            $timesheetStartingDate = $startDateSelectedFromDropDown;
        } elseif (isset($startDateOfTheTimesheetForUpdates)) {                            // if the the user is redirecting in the same timesheet(edit,submit)
            $startDatesOfTimeSheetsAccessible = $this->getAcessibleTimesheetStartDates($dateOptions);

            if ($startDatesOfTimeSheetsAccessible == null) {

                $this->messageData = array('NOTICE', __("No Accessible Timesheets"));
                $this->redirect('time/viewMyTimesheet');
            } elseif (in_array($startDateOfTheTimesheetForUpdates, $startDatesOfTimeSheetsAccessible)) {

                $this->toggleDate = $startDateOfTheTimesheetForUpdates;
                $timesheetStartingDate = $startDateOfTheTimesheetForUpdates;
            } else {
                $timesheetStartingDate = $startDatesOfTimeSheetsAccessible[0];
            }
        } else {                                                                                                 // if the timesheet is access from the menu "My Timesheets"
            if ($dateOptions == null) {

                $statusArray = $this->getTimesheetService()->createTimesheet($employeeId, $this->currentDate);

                switch ($statusArray['state']) {
                    case $statusArray['state'] == 1:
                        $this->redirect('time/overLappingTimesheetError');
                        break;
                    case $statusArray['state'] == 2:
                        $timesheetStartingDate = $statusArray['message'];
                        break;
                    case $statusArray['state'] == 3:
                        $timesheetStartingDate = $statusArray['message'];
                        break;
                    case $statusArray['state'] == 4:
                        $timesheetStartingDate = $statusArray['message'];
                        $this->messageData = array('NOTICE', __("No Accessible Timesheets"));
                        break;
                }
            } else {

                $statusArray = $this->getTimesheetService()->createTimesheet($employeeId, $this->currentDate);

                switch ($statusArray['state']) {
                    case $statusArray['state'] == 1:
                        $this->redirect('time/overLappingTimesheetError');
                        break;
                    case $statusArray['state'] == 2:
                        $timesheetStartingDate = $statusArray['message'];
                        break;
                    case $statusArray['state'] == 3:
                        $timesheetStartingDate = $statusArray['message'];
                        $this->getTimesheetService()->createPreviousTimesheets($timesheetStartingDate, $employeeId);                                    //this creates the timesheets automatically for the past weeks, if the user have not created them
                        break;
                    case $statusArray['state'] == 4:
                        $latestDate = $this->getlatestStartDate($dateOptions);
                        $timesheetStartingDate = $latestDate;
                        break;
                }
            }
        }

        $this->timesheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($timesheetStartingDate, $employeeId);
        $this->currentState = $this->timesheet->getState();
        $this->dateForm = new startDaysListForm(array(), array('employeeId' => $employeeId));
        $dateOptions = $this->dateForm->getDateOptions();

        if ($request->getParameter('selectedIndex') != null) {

            $selectedIndex = $request->getParameter('selectedIndex');
        } else {
            $selectedIndex = $this->dateForm->returnSelectedIndex($timesheetStartingDate, $employeeId);
        }

        if (isset($selectedIndex)) {
            $this->dateForm->setDefault('startDates', $selectedIndex);
        }

        $noOfDays = $this->timesheetService->dateDiff($this->timesheet->getStartDate(), $this->timesheet->getEndDate());

        $values = array('date' => $timesheetStartingDate, 'employeeId' => $employeeId, 'timesheetId' => $this->timesheet->getTimesheetId(), 'noOfDays' => $noOfDays);
        $form = new TimesheetForm(array(), $values);

        $this->timesheetRows = $form->getTimesheet($timesheetStartingDate, $employeeId, $this->timesheet->getTimesheetId());
        $this->currentState = $this->timesheet->getState();

        $user = new User();
        $decoratedUser = new EssUserRoleDecorator($user);
        $this->allowedActions = $decoratedUser->getAllowedActions(WorkflowStateMachine::FLOW_TIME_TIMESHEET, $this->currentState);
        $this->allowedToCreateTimesheets = $decoratedUser->getAllowedActions(PluginWorkflowStateMachine::FLOW_TIME_TIMESHEET, PluginTimesheet::STATE_INITIAL);
        $this->submitNextState = $decoratedUser->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, $this->currentState, WorkflowStateMachine::TIMESHEET_ACTION_SUBMIT);
        $this->approveNextState = $decoratedUser->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, $this->currentState, WorkflowStateMachine::TIMESHEET_ACTION_APPROVE);
        $this->rejectNextState = $decoratedUser->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, $this->currentState, WorkflowStateMachine::TIMESHEET_ACTION_REJECT);
        $this->resetNextState = $decoratedUser->getNextState(WorkflowStateMachine::FLOW_TIME_TIMESHEET, $this->currentState, WorkflowStateMachine::TIMESHEET_ACTION_RESET);
        $this->rowDates = $form->getDatesOfTheTimesheetPeriod($this->timesheet->getStartDate(), $this->timesheet->getEndDate());
        $this->actionLogRecords = $this->getTimesheetService()->getTimesheetActionLogByTimesheetId($this->timesheet->getTimesheetId());

        $this->setTemplate("viewTimesheet");
    }

    protected function setTimesheetActionLog($action, $comment, $timesheetId, $employeeId) {

        $timesheetActionLog = $this->getTimesheetActionLog();
        $timesheetActionLog->setAction($action);
        $timesheetActionLog->setComment($comment);
        $timesheetActionLog->setTimesheetId($timesheetId);
        $timesheetActionLog->setDateTime(date("Y-m-d"));
        $timesheetActionLog->setPerformedBy($employeeId);

        $this->getTimesheetService()->saveTimesheetActionLog($timesheetActionLog);
    }

    protected function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

    protected function getTimesheetActionLog() {

        if (is_null($this->timesheetActionLog)) {

            $this->timesheetActionLog = new TimesheetActionLog();
        }

        return $this->timesheetActionLog;
    }

    protected function getlatestStartDate($dateOptions) {
        if ($dateOptions != null) {
            $temp = $dateOptions[0];
            $tempArray = explode(" ", $temp);
            return $tempArray[0];
        } else {
            return null;
        }
    }

    protected function getAcessibleTimesheetStartDates($dateOptions) {
        if ($dateOptions != null) {
            for ($i = 0; $i < sizeof($dateOptions); $i++) {
                $options = explode(" ", $dateOptions[$i]);
                $datesArray[$i] = $options[0];
            }
            return $datesArray;
        } else {
            return null;
        }
    }

    protected function setTimesheetState($request) {
        $timesheetStartDate = $request->getParameter('timesheetStartDate');
        $employeeId = $request->getParameter('employeeId');
        $timesheet = $this->getTimesheetService()->getTimesheetByStartDateAndEmployeeId($timesheetStartDate, $employeeId);
        $state = $request->getParameter('state');

        $timesheet->setState($state);
        return $this->getTimesheetService()->saveTimesheet($timesheet);
    }

    protected function getTimesheetPeriodService() {

        if (is_null($this->timesheetPeriodService)) {

            $this->timesheetPeriodService = new TimesheetPeriodService();
        }

        return $this->timesheetPeriodService;
    }

}
