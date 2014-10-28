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
class punchOutAction extends sfAction {

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

    public function execute($request) {
        
        $this->_checkAuthentication();
        
        /* For highlighting corresponding menu item */  
        $request->setParameter('initialActionName', 'punchIn');          

        $userRoleManager = $this->getContext()->getUserRoleManager();
        
        $inputDatePattern = $this->getUser()->getDateFormat();
        
        $this->employeeId = $this->getUser()->getEmployeeNumber();
        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT);
        
        $actionableStatesList = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_ATTENDANCE, 
                            $actions, array(), array(), array('Employee' => $this->employeeId));
        
        $timeZoneOffset = $this->getUser()->getUserTimeZoneOffset();
        
        $timeStampDiff = $timeZoneOffset * 3600 - date('Z');
        $this->currentDate = date('Y-m-d', time() + $timeStampDiff);
        $this->currentTime = date('H:i', time() + $timeStampDiff);
        $localizationService = new LocalizationService();

        $this->timezone = $timeZoneOffset * 3600;

        $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($this->employeeId, $actionableStatesList);

        if (is_null($attendanceRecord)) {
            $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
            $this->redirect("attendance/punchIn");
        }
        $tempPunchInTime = $attendanceRecord->getPunchInUserTime();
        $this->recordId = $attendanceRecord->getId();
        $this->actionPunchIn = null;
        $this->editmode = null;

        $this->punchInTime = date('Y-m-d H:i', strtotime($tempPunchInTime));

        $this->punchInUtcTime = date('Y-m-d H:i', strtotime($attendanceRecord->getPunchInUtcTime()));
        $this->punchInNote = $attendanceRecord->getPunchInNote();
        $this->form = new AttendanceForm();
        $this->attendanceFormToImplementCsrfToken = new AttendanceFormToImplementCsrfToken();
        $this->actionPunchOut = $this->getActionName();

        $allowedWorkflowItems = $userRoleManager->getAllowedActions(PluginWorkflowStateMachine::FLOW_ATTENDANCE, 
                $attendanceRecord->getState(), array(), array(), array('Employee' => $this->employeeId));
        $this->allowedActions = array_keys($allowedWorkflowItems);
        
        if ($request->isMethod('post')) {
            if (!(in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, $this->allowedActions))) {
                $this->attendanceFormToImplementCsrfToken->bind($request->getParameter('attendance'));

                if ($this->attendanceFormToImplementCsrfToken->isValid()) {

                    $punchOutDate = $localizationService->convertPHPFormatDateToISOFormatDate($inputDatePattern, $this->request->getParameter('date'));
                    $punchOutTime = $this->request->getParameter('time');
                    $punchOutNote = $this->request->getParameter('note');
                    $timeZoneOffset = $this->request->getParameter('timeZone');
                    $punchOutAction = $allowedWorkflowItems[PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT];
                    $nextState = $punchOutAction->getResultingState();
                    $punchOutdateTime = strtotime($punchOutDate . " " . $punchOutTime);

                    $attendanceRecord = $this->setAttendanceRecord($attendanceRecord, $nextState, date('Y-m-d H:i', $punchOutdateTime - $timeZoneOffset), date('Y-m-d H:i', $punchOutdateTime), $timeZoneOffset / 3600, $punchOutNote);

                    $this->getUser()->setFlash('templateMessage', array('success', __(TopLevelMessages::SAVE_SUCCESS)));
                    $this->redirect('attendance/punchIn');

                }
            } else {
                $this->form->bind($request->getParameter('attendance'));
                if ($this->form->isValid()) {

                    $punchOutTime = $this->form->getValue('time');
                    $punchOutNote = $this->form->getValue('note');
                    $punchOutDate = $this->form->getValue('date');
                    $timeZoneOffset = $this->request->getParameter('timeZone');
                    $punchOutEditModeTime = mktime(date('H', strtotime($punchOutTime)), date('i', strtotime($punchOutTime)), 0, date('m', strtotime($punchOutDate)), date('d', strtotime($punchOutDate)), date('Y', strtotime($punchOutDate)));
                    $punchOutAction = $allowedWorkflowItems[PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT];
                    $nextState = $punchOutAction->getResultingState();                    

                    $attendanceRecord = $this->setAttendanceRecord($attendanceRecord, $nextState, date('Y-m-d H:i', $punchOutEditModeTime - $timeZoneOffset), date('Y-m-d H:i', $punchOutEditModeTime), $timeZoneOffset / 3600, $punchOutNote);

                    $this->getUser()->setFlash('templateMessage', array('success', __(TopLevelMessages::SAVE_SUCCESS)));

                    $this->redirect('attendance/punchIn');

                }
            }
        }


        $this->setTemplate("punchTime");
    }

    public function setAttendanceRecord($attendanceRecord, $state, $punchOutUtcTime, $punchOutUserTime, $punchOutTimezoneOffset, $punchOutNote) {

        $attendanceRecord->setState($state);
        $attendanceRecord->setPunchOutUtcTime($punchOutUtcTime);
        $attendanceRecord->setPunchOutUserTime($punchOutUserTime);
        $attendanceRecord->setPunchOutNote($punchOutNote);
        $attendanceRecord->setPunchOutTimeOffset($punchOutTimezoneOffset);
        return $this->getAttendanceService()->savePunchRecord($attendanceRecord);
    }

    protected function _checkAuthentication($empNumber) {
        
        $loggedInEmpNumber = $this->getUser()->getEmployeeNumber();        

        if (empty($loggedInEmpNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
        
    }
    
}
