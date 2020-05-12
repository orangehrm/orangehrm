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

class viewLikedEmployeesAction extends BaseBuzzAction {

    /**
     * @param sfForm $form
     * @return
     */
    protected function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {
        try {

            $this->setForm(new LikedOrSharedEmployeeForm());

            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    
                    $formValues = $this->form->getValues();
                    $this->id = $formValues['id'];
                    $this->actions = $formValues['type'];
                    $this->loggedInUser = $this->getLogedInEmployeeNumber();
                    $this->error = 'no';
                    if ($this->actions == 'post') {
                        $share = $this->getBuzzService()->getShareById($this->id);
                        $likes = $share->getLike();
                        $this->employeeList = $this->extractEmployeeInformation($likes);
                    } else {
                        $comment = $this->getBuzzService()->getCommentById($this->id);
                        $likes = $comment->getLike();
                        $this->employeeList = $this->extractEmployeeInformation($likes, false);
                    }
                }
            }
        } catch (Exception $ex) {
            $this->error = 'yes';
        }
    }

    public function extractEmployeeInformation($likes, $isPost = true) {
        $likedEmployeeDetailsList = array();

        foreach ($likes as $like) {
            if ($like->getEmployeeNumber() == null) {
                $empName = 'Admin';
            } else {
                if ($isPost) {
                    $employee = $like->getEmployeeLike();
                } else {
                    $employee = $like->getEmployeeLike()->getFirst();
                }
                if($employee instanceof Employee) {
                    $empName = $employee->getFirstAndLastNames();
                    $jobTitle = $employee->getJobTitleName();
                } else {
                    $empName = '(' . __(self::LABEL_EMPLOYEE_DELETED) . ')';
                    $employeeDeleted = true;
                    $jobTitle = '';
                }
            }
            $employeeDetails = array(self::EMP_DELETED => $employeeDeleted, self::EMP_NUMBER => $like->getEmployeeNumber(), self::EMP_NAME => $empName, self::EMP_JOB_TITLE => $jobTitle);
            $likedEmployeeDetailsList[$like->getEmployeeNumber()] = $employeeDetails;
        }
        return $likedEmployeeDetailsList;
    }

}
