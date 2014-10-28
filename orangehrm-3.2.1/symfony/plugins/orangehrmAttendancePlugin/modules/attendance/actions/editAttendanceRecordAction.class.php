<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class editAttendanceRecordAction extends baseAttendanceAction {

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
        $userRoleManager = $this->getContext()->getUserRoleManager();
                
        $this->editPunchIn = array();
        $this->editPunchOut = array();
        $this->employeeId = $request->getParameter('employeeId');
        $this->messageData = array($request->getParameter('message[0]'), $request->getParameter('message[1]'));

        $this->_checkAuthentication($this->employeeId);

        $this->date = $request->getParameter('date');

        $this->actionRecorder = $request->getParameter('actionRecorder');
        $this->errorRows = $request->getParameter('errorRows');

        $userId = $userRoleManager->getUser()->getId();
        
        $userEmployeeNumber = $this->getUser()->getEmployeeNumber();
        
        if ($this->actionRecorder == "viewMy" && $userEmployeeNumber == $this->employeeId) {
            $initialAction = 'viewMyAttendanceRecord';
            $self = true;
        } else {
            $initialAction = 'viewAttendanceRecord';
            $self = false;
        }
        $this->attendancePermissions = $this->getDataGroupPermissions('attendance_records', $self);

        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', $initialAction);
        
        
        $this->records = $this->getAttendanceService()->getAttendanceRecord($this->employeeId, $this->date);
        $totalRows = sizeOf($this->records);

        $values = array('employeeId' => $this->employeeId, 'date' => $this->date);
        $this->editAttendanceForm = new EditAttendanceRecordForm(array(), $values);
        $formSubmitAction = $request->getParameter('formSubmitAction');

        $rolesToExclude = array();
        $rolesToInclude = array();
        $entities = array('Employee' => $this->employeeId);
        if ($this->actionRecorder == "viewEmployee") {
        }
        if ($this->actionRecorder == "viewMy") {
            $rolesToInclude = array('ESS');
        }
        $i = 1;
        foreach ($this->records as $record) {

            $allowedWorkflowItems = $userRoleManager->getAllowedActions(WorkflowStateMachine::FLOW_ATTENDANCE, $record->getState(), $rolesToExclude, $rolesToInclude, $entities);
            $allowedActionsForCurrentRecord = array_keys($allowedWorkflowItems);

            if (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME, $allowedActionsForCurrentRecord)) {

                $this->editPunchIn[$i] = true;
            } else {
                $this->editPunchIn[$i] = false;
            }

            if (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME, $allowedActionsForCurrentRecord)) {

                $this->editPunchOut[$i] = true;
            } else {
                $this->editPunchOut[$i] = false;
            }
            $i++;
        }


        if ($formSubmitAction) {
            if ($request->isMethod('post')) {
                $this->editAttendanceForm->bind($request->getParameter('attendance'));

                if ($this->editAttendanceForm->isValid()) {


                    $errorArray = $this->editAttendanceForm->save($totalRows, $this->editAttendanceForm);
                    if (!empty($errorArray)) {
                        $errorStr = json_encode($errorArray);
                        $this->redirect('attendance/editAttendanceRecord?employeeId=' . $this->employeeId . '&date=' . $this->date . '&actionRecorder=' . $this->actionRecorder . '&errorRows=' . $errorStr);
                    } else {
                        $messageData = array('SUCCESS', __(TopLevelMessages::SAVE_SUCCESS));
                        if ($this->actionRecorder == "viewMy") {
                            $this->redirect('attendance/viewMyAttendanceRecord' . '?' . http_build_query(array('message' => $messageData, 'actionRecorder' => $this->actionRecorder, 'employeeId' => $this->employeeId, 'date' => $this->date, 'trigger' => true)));
                        }
                        if ($this->actionRecorder == "viewEmployee") {
                            $this->redirect('attendance/viewAttendanceRecord' . '?' . http_build_query(array('message' => $messageData, 'actionRecorder' => $this->actionRecorder, 'employeeId' => $this->employeeId, 'date' => $this->date, 'trigger' => true)));
                        }
                    }
                }
            }
        }
    }

    public function getAttendanceService() {

        if (is_null($this->attendanceService)) {
            $this->attendanceService = new AttendanceService();
        }

        return $this->attendanceService;
    }

    public function setTimesheetDao(AttendanceService $attendanceService) {

        $this->attendanceService = $attendanceService;
    }

    protected function _checkAuthentication($empNumber) {

        $loggedInEmpNumber = $this->getUser()->getEmployeeNumber();

        if ($loggedInEmpNumber == $empNumber) {
            return;
        }

        $userRoleManager = $this->getContext()->getUserRoleManager();
        
        if (!$userRoleManager->isEntityAccessible('Employee', $empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
    }

}
