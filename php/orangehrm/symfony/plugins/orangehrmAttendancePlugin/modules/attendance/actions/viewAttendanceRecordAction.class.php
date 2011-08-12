<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewAttendanceRecordAction
 *
 * @author orangehrm
 */
class viewAttendanceRecordAction extends sfAction {

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


        $this->trigger = $request->getParameter('trigger');
        $this->date = $request->getParameter('date');
        $this->employeeId = $request->getParameter('employeeId');
        $this->attendanceService = $this->getAttendanceService();
        $values = array('date' => $this->date, 'employeeId' => $this->employeeId, 'trigger' => $this->trigger);
        $this->form = new AttendanceRecordSearchForm(array(), $values);
        $userObj = $this->getContext()->getUser()->getAttribute("user");
        $employeeList = $userObj->getEmployeeList();
        $this->employeeListAsJson = $this->form->getEmployeeListAsJson($employeeList);



        if (!$this->trigger) {


            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter('attendance'));


                if ($this->form->isValid()) {
                    
                }
            }
        }
    }

}

?>
