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
class AttendanceActions extends sfActions {

    private $attendanceService;

    public function getAttendanceService() {

        if (is_null($this->attendanceService)) {

            $this->attendanceService = new AttendanceService();
        }

        return $this->attendanceService;
    }

    public function setAttendanceService(AttendanceService $attendanceService) {

        $this->attendanceService = $attendanceService;
    }

    public function executeHello($request) {

        $this->currentTime = date('H:i:s');
    }

    public function executeValidatePunchOutOverLapping($request) {

        $temppunchInTime = $request->getParameter('punchInTime');
        $temppunchOutTime = $request->getParameter('punchOutTime');
        $timezone = $request->getParameter('timezone');
        $recordId = $request->getParameter('recordId');

        $ti = strtotime($temppunchInTime);
        $to = strtotime($temppunchOutTime) - $timezone;


        $punchInDate = date("Y-m-d", $ti);
        $punchInTime = date("H:i:s", $ti);
        $punchIn = $punchInDate . " " . $punchInTime;

        $punchOutDate = date("Y-m-d", $to);
        $punchOutTime = date("H:i:s", $to);
        $punchOut = $punchOutDate . " " . $punchOutTime;


        $employeeId = $request->getParameter('employeeId');
        $this->isValid = $this->getAttendanceService()->checkForPunchOutOverLappingRecords($punchIn, $punchOut, $employeeId,$recordId);
    }

    public function executeValidatePunchInOverLapping($request) {

        $temppunchInTime = $request->getParameter('punchInTime');
        $timezone = $request->getParameter('timezone');

        $ti = strtotime($temppunchInTime) - $timezone;
        $punchInDate = date("Y-m-d", $ti);
        $punchInTime = date("H:i:s", $ti);
        $punchIn = $punchInDate . " " . $punchInTime;

        $employeeId = $request->getParameter('employeeId');

        $this->isValid = $this->getAttendanceService()->checkForPunchInOverLappingRecords($punchIn, $employeeId);
    }

    public function executeValidatePunchInOverLappingWhenEditing($request) {

        $temppunchInTime = $request->getParameter('punchInTime');
        $timezone = $request->getParameter('timezone');
        $recordId = $request->getParameter('recordId');
        $punchOutUtcTime = $request->getParameter('punchOutUtcTime');

        $ti = strtotime($temppunchInTime) - $timezone * 3600;
        $punchInDate = date("Y-m-d", $ti);
        $punchInTime = date("H:i:s", $ti);
        $punchIn = $punchInDate . " " . $punchInTime;

        $to = $punchOutUtcTime / 1000;
        $punchOutDate = date("Y-m-d", $to);
        $punchOutTime = date("H:i:s", $to);
        $punchOut = $punchOutDate . " " . $punchOutTime;

        $employeeId = $request->getParameter('employeeId');
// $this->isValid=$punchIn;
        $this->isValid = $this->getAttendanceService()->checkForPunchInOverLappingRecordsWhenEditing($punchIn, $employeeId, $recordId, $punchOut);
    }

    public function executeValidatePunchOutOverLappingWhenEditing($request) {

        $temppunchInTime = $request->getParameter('punchInTime');
        $temppunchOutTime = $request->getParameter('punchOutTime');
        $inTimezone = $request->getParameter('inTimezone');
        $outTimezone = $request->getParameter('outTimezone');
        $recordId = $request->getParameter('recordId');
        $ti = strtotime($temppunchInTime) - $inTimezone;
        $to = strtotime($temppunchOutTime) - $outTimezone;


        $punchInDate = date("Y-m-d", $ti);
        $punchInTime = date("H:i:s", $ti);
        $punchIn = $punchInDate . " " . $punchInTime;

        $punchOutDate = date("Y-m-d", $to);
        $punchOutTime = date("H:i:s", $to);
        $punchOut = $punchOutDate . " " . $punchOutTime;


        $employeeId = $request->getParameter('employeeId');
        $this->isValid = $this->getAttendanceService()->checkForPunchOutOverLappingRecordsWhenEditing($punchIn, $punchOut, $employeeId, $recordId);
    }

    public function executeGetCurrentTime($request) {
        $timeZoneOffset = $request->getParameter('timeZone');
        $timeStampDiff = $timeZoneOffset - date('Z');
        $currentDate = date('Y-m-d', time() + $timeStampDiff);
        $currentTime = date('H:i', time() + $timeStampDiff);

        $this->values = $currentDate . "_" . $currentTime;
    }

    public function executeGetRelatedAttendanceRecords($request) {


        $this->allowedToDelete = array();
        $this->allowedActions = array();

        $this->allowedActions['Delete'] = false;
        $this->allowedActions['Edit'] = false;
        $this->allowedActions['PunchIn'] = false;
        $this->allowedActions['PunchOut'] = false;
        $this->userObj = $this->getContext()->getUser()->getAttribute('user');
        $userId = $this->userObj->getUserId();
        $userEmployeeNumber = $this->userObj->getEmployeeNumber();
        $this->employeeId = $request->getParameter('employeeId');
        $this->date = $request->getParameter('date');
        $this->actionRecorder = $request->getParameter('actionRecorder');

        if ($this->actionRecorder == "viewEmployee") {
            $userRoleFactory = new UserRoleFactory();
            $decoratedUser = $userRoleFactory->decorateUserRole($userId, $this->employeeId, $userEmployeeNumber);
        }
        if ($this->actionRecorder == "viewMy") {

            $user = new User();
            $decoratedUser = new EssUserRoleDecorator($user);
        }

        $this->records = $this->getAttendanceService()->getAttendanceRecord($this->employeeId, $this->date);
        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME, PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME);
        $actionableStates = $decoratedUser->getActionableAttendanceStates($actions);
        $recArray = array();
        if ($this->records != null) {

            if ($actionableStates != null) {

                foreach ($actionableStates as $state) {

                    foreach ($this->records as $record) {

                        if ($state == $record->getState()) {

                            $this->allowedActions['Edit'] = true;
                            break;
                        }
                    }
                }
            }

            $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE);
            $actionableStates = $decoratedUser->getActionableAttendanceStates($actions);

            if ($actionableStates != null) {
                foreach ($actionableStates as $state) {

                    foreach ($this->records as $record) {

                        if ($state == $record->getState()) {

                            $this->allowedActions['Delete'] = true;
                            break;
                        }
                    }
                }
            }

            foreach ($this->records as $record) {
                $this->allowedToDelete[] = $this->allowedToPerformAction(WorkflowStateMachine::FLOW_ATTENDANCE, PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE, $record->getState(), $decoratedUser);
                $recArray[] = $record;
            }
        } else {
            $attendanceRecord = null;
        }

        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT);
        $allowedActionsList = array();

        $actionableStates = $decoratedUser->getActionableAttendanceStates($actions);

        if ($actionableStates != null) {

            if (!empty($recArray)) {
                $lastRecordPunchOutTime = $recArray[count($this->records) - 1]->getPunchOutUserTime();
                if (empty($lastRecordPunchOutTime)) {
                    $attendanceRecord = "";
                } else {
                    $attendanceRecord = null;
                }
            }

            foreach ($actionableStates as $actionableState) {

                $allowedActionsArray = $decoratedUser->getAllowedActions(PluginWorkflowStateMachine::FLOW_ATTENDANCE, $actionableState);
                if (!is_null($allowedActionsArray)) {

                    $allowedActionsList = array_unique(array_merge($allowedActionsArray, $allowedActionsList));
                }
            }

            if ((is_null($attendanceRecord)) && (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, $allowedActionsList))) {

                $this->allowedActions['PunchIn'] = true;
            }
            if ((!is_null($attendanceRecord)) && (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT, $allowedActionsList))) {

                $this->allowedActions['PunchOut'] = true;
            }
        }
    }

    public function executeDeleteAttendanceRecords($request) {

        $attendanceRecordId = $request->getParameter('id');

        $this->isDeleted = $this->getAttendanceService()->deleteAttendanceRecords($attendanceRecordId);

        return $this->renderText($this->isDeleted);
    }

    public function executeProxyPunchInPunchOut($request) {


        $this->punchInTime = null;
        $this->punchInUtcTime = null;
        $this->punchInNote = null;
        $this->action = array();
        $this->action['PunchIn'] = false;
        $this->action['PunchOut'] = false;
        $this->employeeId = $request->getParameter('employeeId');
        $this->date = $request->getParameter('date');
        $this->actionRecorder = $request->getParameter('actionRecorder');


        $this->userObj = $this->getContext()->getUser()->getAttribute('user');
        $timeZoneOffset = $this->userObj->getUserTimeZoneOffset();

        $timeStampDiff = $timeZoneOffset * 3600 - date('Z');
        $this->currentDate = date('Y-m-d', time() + $timeStampDiff);

        $this->currentTime = date('H:i', time() + $timeStampDiff);

        $this->timezone = $timeZoneOffset * 3600;

        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT);
        $actionableStates = $this->userObj->getActionableAttendanceStates($actions);

        $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($this->employeeId, $actionableStates);

        if (is_null($attendanceRecord)) {

            $this->action['PunchIn'] = true;
        } else {

            $this->action['PunchOut'] = true;
        }
        $param = array('timezone' => $timeZoneOffset, 'date' => $this->date);
        $this->form = new ProxyPunchInPunchOutForm(array(), $param, true);

        if ($this->action['PunchIn']) {

            $this->allowedActions = $this->userObj->getAllowedActions(WorkflowStateMachine::FLOW_ATTENDANCE, AttendanceRecord::STATE_INITIAL);
            if ($request->getParameter('path')) {

                if ($request->isMethod('post')) {

                    $accessFlowStateMachineService = new AccessFlowStateMachineService();
                    $attendanceRecord = new AttendanceRecord();
                    $attendanceRecord->setEmployeeId($this->employeeId);


                    $this->form->bind($request->getParameter('attendance'));

                    if ($this->form->isValid()) {

                        $punchInDate = $this->form->getValue('date');
                        $punchIntime = $this->form->getValue('time');
                        $punchInNote = $this->form->getValue('note');
                        $timeValue = $this->form->getValue('timezone');
                        $employeeTimezone = $this->getAttendanceService()->getTimezone($timeValue);
                        if ($employeeTimezone == 'GMT') {
                            $employeeTimezone = 0;
                        }
                        $punchInEditModeTime = mktime(date('H', strtotime($punchIntime)), date('i', strtotime($punchIntime)), 0, date('m', strtotime($punchInDate)), date('d', strtotime($punchInDate)), date('Y', strtotime($punchInDate)));


                        $nextState = $this->userObj->getNextState(WorkflowStateMachine::FLOW_ATTENDANCE, AttendanceRecord::STATE_INITIAL, WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN);

                        $attendanceRecord->setState($nextState);
                        $attendanceRecord->setPunchInUtcTime(date('Y-m-d H:i', $punchInEditModeTime - $employeeTimezone * 3600));
                        $attendanceRecord->setPunchInNote($punchInNote);
                        $attendanceRecord->setPunchInUserTime(date('Y-m-d H:i', $punchInEditModeTime));
                        $attendanceRecord->setPunchInTimeOffset($employeeTimezone);

                        $this->getAttendanceService()->savePunchRecord($attendanceRecord);

                        $this->redirect("attendance/viewAttendanceRecord?employeeId=" . $this->employeeId . "&date=" . $this->date . "&trigger=" . true . "&actionRecorder=" . $this->actionRecorder);
                    }
                }
            }
        }

        if ($this->action['PunchOut']) {

            $this->allowedActions = $this->userObj->getAllowedActions(WorkflowStateMachine::FLOW_ATTENDANCE, AttendanceRecord::STATE_PUNCHED_IN);

            $tempPunchInTime = $attendanceRecord->getPunchInUserTime();
            $this->punchInTime = date('Y-m-d H:i', strtotime($tempPunchInTime));
            $this->punchInUtcTime = date('Y-m-d H:i', strtotime($attendanceRecord->getPunchInUtcTime()));
            $this->punchInNote = $attendanceRecord->getPunchInNote();
            if ($request->getParameter('path')) {
                if ($request->isMethod('post')) {
                    $this->form->bind($request->getParameter('attendance'));
                    if ($this->form->isValid()) {

                        $punchOutTime = $this->form->getValue('time');
                        $punchOutNote = $this->form->getValue('note');
                        $punchOutDate = $this->form->getValue('date');
                        $timeValue = $this->form->getValue('timezone');
                        $employeeTimezone = $this->getAttendanceService()->getTimezone($timeValue);
                        if ($employeeTimezone == 'GMT') {
                            $employeeTimezone = 0;
                        }

                        $punchOutEditModeTime = mktime(date('H', strtotime($punchOutTime)), date('i', strtotime($punchOutTime)), 0, date('m', strtotime($punchOutDate)), date('d', strtotime($punchOutDate)), date('Y', strtotime($punchOutDate)));
                        $nextState = $this->userObj->getNextState(PluginWorkflowStateMachine::FLOW_ATTENDANCE, PluginAttendanceRecord::STATE_PUNCHED_IN, PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT);
                        $attendanceRecord->setState($nextState);
                        $attendanceRecord->setPunchOutUtcTime(date('Y-m-d H:i', $punchOutEditModeTime - $employeeTimezone * 3600));
                        $attendanceRecord->setPunchOutNote($punchOutNote);
                        $attendanceRecord->setPunchOutUserTime(date('Y-m-d H:i', $punchOutEditModeTime));
                        $attendanceRecord->setPunchOutTimeOffset($employeeTimezone);
                        $this->getAttendanceService()->savePunchRecord($attendanceRecord);
                        $this->getUser()->setFlash('templateMessage', array('success', __(TopLevelMessages::SAVE_SUCCESS)));
                        $this->redirect("attendance/viewAttendanceRecord?employeeId=" . $this->employeeId . "&date=" . $this->date . "&trigger=" . true);
                    }
                }
            }
        }
    }

    public function executeUpdatePunchInOutNote($request) {
        $comment = $request->getParameter('comment');
        $id = $request->getParameter('id');
        $punchInOut = $request->getParameter('punchInOut');

        $attendanceRecord = $this->getAttendanceService()->getAttendanceRecordById($id);

        if ($punchInOut == 3) {

            $attendanceRecord->setPunchInNote($comment);
            $this->getAttendanceService()->savePunchRecord($attendanceRecord);
        }

        if ($punchInOut == 4) {

            $attendanceRecord->setPunchOutNote($comment);
            $this->getAttendanceService()->savePunchRecord($attendanceRecord);
        }
    }

    public function allowedToPerformAction($flow, $action, $state, $userObject) {
        //  $userObj = $this->getContext()->getUser()->getAttribute('user');
        $actionsArray = $userObject->getAllowedActions($flow, $state);

        if (in_array($action, $actionsArray)) {
            return true;
        } else {
            return false;
        }
    }

    public function executeGetDaylightSavingTimeZone($request) {
        
    }

}

