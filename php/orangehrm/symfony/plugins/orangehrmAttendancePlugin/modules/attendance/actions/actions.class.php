<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of actions
 *
 * @author orangehrm
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
        // echo("helloAttendance");

        $this->currentTime = date('H:i:s');
    }

    public function executeValidatePunchOutOverLapping($request) {

        $temppunchInTime = $request->getParameter('punchInTime');
        $temppunchOutTime = $request->getParameter('punchOutTime');
        $timezone = $request->getParameter('timezone');

        $ti = strtotime($temppunchInTime) - $timezone;
        $to = strtotime($temppunchOutTime) - $timezone;


        $punchInDate = date("Y-m-d", $ti);
        $punchInTime = date("H:i:s", $ti);
        $punchIn = $punchInDate . " " . $punchInTime;

        $punchOutDate = date("Y-m-d", $to);
        $punchOutTime = date("H:i:s", $to);
        $punchOut = $punchOutDate . " " . $punchOutTime;


        $employeeId = $request->getParameter('employeeId');
        $this->isValid = $this->getAttendanceService()->checkForPunchOutOverLappingRecords($punchIn, $punchOut, $employeeId);
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

    public function executeGetCurrentTime($request) {

        $userObj = $this->getContext()->getUser()->getAttribute('user');
        $timeZoneOffset = $userObj->getUserTimeZoneOffset();
        $timeStampDiff = $timeZoneOffset * 3600 - date('Z');
        $currentDate = date('Y-m-d', time() + $timeStampDiff);
        $currentTime = date('H:i', time() + $timeStampDiff);

        $this->values = $currentDate . "_" . $currentTime;
    }

    public function executeGetRelatedAttendanceRecords($request) {


        $this->r = array();
        $this->allowedActions = array();

        $this->allowedActions['Delete'] = false;
        $this->allowedActions['Edit'] = false;
        $this->allowedActions['PunchIn'] = false;
        $this->allowedActions['PunchOut'] = false;
        $this->employeeId = $request->getParameter('employeeId');
        $this->date = $request->getParameter('date');
        $this->records = $this->getAttendanceService()->getAttendanceRecord($this->employeeId, $this->date);
        $this->userObj = $this->getContext()->getUser()->getAttribute('user');
        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME, PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME);
        $actionableStates = $this->userObj->getActionableAttendanceStates($actions);
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
            $actionableStates = $this->userObj->getActionableAttendanceStates($actions);

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



            $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT);

            $actionableStates = $this->userObj->getActionableAttendanceStates($actions);
            if ($actionableStates != null) {
                $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($this->employeeId, $actionableStates);


                if (is_null($attendanceRecord)) {

                    $this->allowedActions['PunchIn'] = true;
                } else {
                    $this->allowedActions['PunchOut'] = true;
                }
            }
            $i = 0;
            foreach ($this->records as $record) {
                $this->r[$i] = $this->attendanceService->allowedToPerformAction(WorkflowStateMachine::FLOW_ATTENDANCE, PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE, $record->getState());
                $i++;
            }
        }
        // $allowedActions= $userObj->getAllowedActions(PluginWorkflowStateMachine::FLOW_ATTENDANCE, $state);
        //return $this->renderPartial('recordsTable', array('records' => $this->records));
    }

    public function executeDeleteAttendanceRecords($request) {

        $attendanceRecordId = $request->getParameter('id');

        $this->isDeleted = $this->getAttendanceService()->deleteAttendanceRecords($attendanceRecordId);
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

        $this->form = new ProxyPunchInPunchOutForm();

        if ($this->action['PunchIn']) {

            $this->allowedActions = $this->userObj->getAllowedActions(WorkflowStateMachine::FLOW_ATTENDANCE, AttendanceRecord::STATE_INITIAL);
            if ($request->getParameter('path')) {

                if ($request->isMethod('post')) {

                    $accessFlowStateMachineService = new AccessFlowStateMachineService();
                    $attendanceRecord = new AttendanceRecord();
                    $attendanceRecord->setEmployeeId($this->employeeId);


//            if (!in_array(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME, $this->allowedActions)) {
//                $punchInDate = $this->request->getParameter('date');
//                $punchIntime = $this->request->getParameter('time');
//                $punchInNote = $this->request->getParameter('note');
////                print_r($punchIntime);
////                print_r($punchInDate);
//              
//                
//                $nextState = $this->userObj->getNextState(WorkflowStateMachine::FLOW_ATTENDANCE, AttendanceRecord::STATE_INITIAL, WorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_IN);
////                $userDateTime = new DateTime($punchIntime);
////                print_r($userDateTime);
////  die;
////                $attendanceRecord->setState($nextState);
//                $attendanceRecord->setPunchInUtcTime(date('Y-m-d H:i', time() + $timeStampDiff - $timeZoneOffset * 3600));
//                $attendanceRecord->setPunchInNote($punchInNote);
//                $attendanceRecord->setPunchInUserTime(date('Y-m-d H:i', time() + $timeStampDiff));
//                $attendanceRecord->setPunchInTimeOffset($timeZoneOffset);
//
//                $this->getAttendanceService()->savePunchRecord($attendanceRecord);
//
//                $this->redirect("attendance/punchOut");
//            } else {

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

                        $this->redirect("attendance/viewAttendanceRecord?employeeId=" . $this->employeeId . "&date=" . $this->date . "&trigger=" . true);
                    }
                }
            }
        }


        // }
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
//                    if ($punchOutDate != date('Y-m-d', strtotime($punchOutTime))) {
//                        $userDateTime = new DateTime($punchOutTime);
//                        $userDateTime->setDate(date('Y', strtotime($punchOutDate)), date('m', strtotime($punchOutDate)), date('d', strtotime($punchOutDate)));
//
//                    } else {
//
//                        $userDateTime = new DateTime($punchOutTime);
//                    }
                        $nextState = $this->userObj->getNextState(PluginWorkflowStateMachine::FLOW_ATTENDANCE, PluginAttendanceRecord::STATE_PUNCHED_IN, PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT);
                        $attendanceRecord->setState($nextState);
                        $attendanceRecord->setPunchOutUtcTime(date('Y-m-d H:i', $punchOutEditModeTime - $employeeTimezone * 3600));
                        $attendanceRecord->setPunchOutNote($punchOutNote);
                        $attendanceRecord->setPunchOutUserTime(date('Y-m-d H:i', $punchOutEditModeTime));
                        $attendanceRecord->setPunchOutTimeOffset($employeeTimezone);
                        $this->getAttendanceService()->savePunchRecord($attendanceRecord);
                        $this->getUser()->setFlash('templateMessage', array('success', __('Record Saved Successfully')));
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

}

?>
