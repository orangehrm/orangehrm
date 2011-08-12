<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewMyAttendanceRecordAction
 *
 * @author orangehrm
 */
class viewMyAttendanceRecordAction extends sfAction {

    public function execute($request) {

        $this->attendanceService = $this->getAttendanceService();
        $this->form = new AttendanceRecordSearchForm();
        $userObj = $this->getContext()->getUser()->getAttribute("user");
        $this->employeeId = $userObj->getEmployeeNumber();
        $date = $this->request->getParameter('date');

        if (!($date)) {
            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter('attendance'));


                if ($this->form->isValid()) {
                    
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

    public function setAttendanceService(AttendanceService $attendanceService) {

        $this->attendanceService = $attendanceService;
    }

}

?>
