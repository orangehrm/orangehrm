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

        $this->userObj = $this->getContext()->getUser()->getAttribute('user');
        $accessibleMenus = $this->userObj->getAccessibleAttendanceSubMenus();
        $hasRight = false;

        foreach ($accessibleMenus as $menu) {
            if ($menu->getDisplayName() === __("Employee Records")) {
                $hasRight = true;
                break;
            }
        }

        if (!$hasRight) {
            return $this->renderText(__("You are not allowed to view this page")."!");
        }

        $this->trigger = $request->getParameter('trigger');
        $this->date = $request->getParameter('date');
        $this->employeeId = $request->getParameter('employeeId');
        $this->attendanceService = $this->getAttendanceService();
        $values = array('date' => $this->date, 'employeeId' => $this->employeeId, 'trigger' => $this->trigger);
        $this->form = new AttendanceRecordSearchForm(array(), $values);
        $userObj = $this->getContext()->getUser()->getAttribute("user");
        $employeeList = $userObj->getEmployeeList();
        $this->employeeListAsJson = $this->form->getEmployeeListAsJson($employeeList);
        $this->actionRecorder="viewEmployee";
 

        if (!$this->trigger) {


            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter('attendance'));


                if ($this->form->isValid()) {
                    
                }
            }
        }
    }

}

