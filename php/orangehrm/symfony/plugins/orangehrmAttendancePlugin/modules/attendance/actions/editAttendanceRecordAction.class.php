<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class editAttendanceRecordAction extends sfAction {

    public function execute($request) {
        $this->editPunchIn = array();
        $this->editPunchOut = array();
        $this->employeeId = $request->getParameter('employeeId');
        $this->date = $request->getParameter('date');
        $userObj = sfContext::getInstance()->getUser()->getAttribute('user');

        $this->records = $this->getAttendanceService()->getAttendanceRecord($this->employeeId, $this->date);
        $totalRows = sizeOf($this->records);
        $values = array('employeeId' => $this->employeeId, 'date' => $this->date);
        $this->editAttendanceForm = new EditAttendanceRecordForm(array(), $values);
        $action = $request->getParameter('actionName');
        $i = 1;
        foreach ($this->records as $record) {


            $allowedActionsForCurrentRecord = $userObj->getAllowedActions(WorkflowStateMachine::FLOW_ATTENDANCE, $record->getState());

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

        if (!$action) {
            if ($request->isMethod('post')) {
                $this->editAttendanceForm->bind($request->getParameter('attendance'));


                if ($this->editAttendanceForm->isValid()) {


                    $this->editAttendanceForm->save($totalRows, $this->editAttendanceForm);
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

}
