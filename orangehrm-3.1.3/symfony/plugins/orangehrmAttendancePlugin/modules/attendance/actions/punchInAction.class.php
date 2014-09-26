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
class punchInAction extends sfAction {

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

        $userRoleManager = $this->getContext()->getUserRoleManager();
        
        $this->editmode = null;
        $this->employeeId = $this->getUser()->getEmployeeNumber();
        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_OUT);
                
        $actionableStatesList = $userRoleManager->getActionableStates(WorkflowStateMachine::FLOW_ATTENDANCE, 
                            $actions, array(), array(), array('Employee' => $this->employeeId));
        
        $timeZoneOffset = $this->getUser()->getUserTimeZoneOffset();
        $timeStampDiff = $timeZoneOffset * 3600 - date('Z');
        $this->currentDate = date('Y-m-d', time() + $timeStampDiff);
        $this->currentTime = date('H:i', time() + $timeStampDiff);
        $this->timezone = $timeZoneOffset * 3600;
        $localizationService = new LocalizationService();
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();


        if ($this->getUser()->hasFlash('templateMessage')) {
            list($messageType, $message) = $this->getUser()->getFlash('templateMessage');
            $this->getUser()->setFlash($messageType, $message);
        }
        $attendanceRecord = $this->getAttendanceService()->getLastPunchRecord($this->employeeId, $actionableStatesList);


        if (is_null($attendanceRecord)) {
           
            $allowedWorkflowItems = $userRoleManager->getAllowedActions(PluginWorkflowStateMachine::FLOW_ATTENDANCE, 
                    AttendanceRecord::STATE_INITIAL, array(), array(), array('Employee' => $this->employeeId));
            $this->allowedActions = array_keys($allowedWorkflowItems);            
        } else {

            $this->redirect("attendance/punchOut");
        }


        $this->punchInTime = null;
        $this->punchInUtcTime = null;
        $this->punchInNote = null;
        $this->actionPunchOut = null;

        $this->form = new AttendanceForm();
        $this->actionPunchIn = $this->getActionName();
        $this->attendanceFormToImplementCsrfToken = new AttendanceFormToImplementCsrfToken();


        if ($request->isMethod('post')) {

            $attendanceRecord = new AttendanceRecord();
            $attendanceRecord->setEmployeeId($this->employeeId);


            if (!(in_array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME, $this->allowedActions)) ) {
                $this->attendanceFormToImplementCsrfToken->bind($request->getParameter('attendance'));
           
                if ($this->attendanceFormToImplementCsrfToken->isValid()) {

  
                    $punchInDate = $localizationService->convertPHPFormatDateToISOFormatDate($inputDatePattern, $this->request->getParameter('date'));
                    $punchIntime = $this->request->getParameter('time');
                    $punchInNote = $this->request->getParameter('note');
                    $timeZoneOffset = $this->request->getParameter('timeZone');

                    $punchInAction = $allowedWorkflowItems[PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_IN];
                    $nextState = $punchInAction->getResultingState();                    

                    $punchIndateTime = strtotime($punchInDate . " " . $punchIntime);

                    $attendanceRecord = $this->setAttendanceRecord($attendanceRecord, $nextState, date('Y-m-d H:i', $punchIndateTime - $timeZoneOffset), date('Y-m-d H:i', $punchIndateTime), $timeZoneOffset / 3600, $punchInNote);

                    $this->redirect("attendance/punchOut");

                }
            } else {

                $this->form->bind($request->getParameter('attendance'));

                if ($this->form->isValid()) {

                    $punchInDate = $this->form->getValue('date');
                    $punchIntime = $this->form->getValue('time');
                    $punchInNote = $this->form->getValue('note');
                    $timeZoneOffset = $this->request->getParameter('timeZone');

                    $punchInEditModeTime = mktime(date('H', strtotime($punchIntime)), date('i', strtotime($punchIntime)), 0, date('m', strtotime($punchInDate)), date('d', strtotime($punchInDate)), date('Y', strtotime($punchInDate)));

                    $punchInAction = $allowedWorkflowItems[PluginWorkflowStateMachine::ATTENDANCE_ACTION_PUNCH_IN];
                    $nextState = $punchInAction->getResultingState();       
                    
                    $attendanceRecord = $this->setAttendanceRecord($attendanceRecord, $nextState, date('Y-m-d H:i', $punchInEditModeTime - $timeZoneOffset), date('Y-m-d H:i', $punchInEditModeTime), $timeZoneOffset / 3600, $punchInNote);
                    $this->redirect("attendance/punchOut");

                }
            }
        }

        $this->setTemplate("punchTime");
    }

    public function setAttendanceRecord($attendanceRecord, $state, $punchInUtcTime, $punchInUserTime, $punchInTimezoneOffset, $punchInNote) {

        $attendanceRecord->setState($state);
        $attendanceRecord->setPunchInUtcTime($punchInUtcTime);
        $attendanceRecord->setPunchInUserTime($punchInUserTime);
        $attendanceRecord->setPunchInNote($punchInNote);
        $attendanceRecord->setPunchInTimeOffset($punchInTimezoneOffset);
        return $this->getAttendanceService()->savePunchRecord($attendanceRecord);
    }
    
    protected function _checkAuthentication($empNumber) {
        
        $loggedInEmpNumber = $this->getUser()->getEmployeeNumber();        

        if (empty($loggedInEmpNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
        
    }

}