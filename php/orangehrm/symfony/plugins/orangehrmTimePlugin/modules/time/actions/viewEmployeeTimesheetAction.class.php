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
class viewEmployeeTimesheetAction extends sfAction {

    private $employeeNumber;
    private $timesheetService;

    public function getTimesheetService() {

        if (is_null($this->timesheetService)) {

            $this->timesheetService = new TimesheetService();
        }

        return $this->timesheetService;
    }

    public function execute($request) {

        $this->form = new viewEmployeeTimesheetForm();


        if ($request->isMethod("post")) {


            $this->form->bind($request->getParameter('time'));

            if ($this->form->isValid()) {

                $this->employeeId = $this->form->getValue('employeeId');
                $startDaysListForm = new startDaysListForm(array(), array('employeeId' => $this->employeeId));
                $dateOptions = $startDaysListForm->getDateOptions();

                if ($dateOptions == null) {

                    $this->getContext()->getUser()->setFlash('errorMessage', __("No Timesheets Found"));
                    $this->redirect('time/createTimesheetForSubourdinate?' . http_build_query(array('employeeId' => $this->employeeId)));
                }

                $this->redirect('time/viewTimesheet?' . http_build_query(array('employeeId' => $this->employeeId)));
            }
        }

        $userObj = $this->getContext()->getUser()->getAttribute("user");
        $this->form->employeeList = $userObj->getEmployeeNameList();

        $this->pendingApprovelTimesheets = $userObj->getActionableTimesheets();
    }

}

