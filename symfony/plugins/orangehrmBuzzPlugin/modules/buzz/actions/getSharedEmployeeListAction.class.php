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

/**
 * Description of getSharedEmployeeListAction
 *
 * @author aruna
 */
class getSharedEmployeeListAction extends BaseBuzzAction {

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
                    $this->event = $formValues['event'];
                    $this->loggedInUser = $this->getLogedInEmployeeNumber();
                    $this->buzzService = $this->getBuzzService();
                    $this->loggedInEmployeeId = $this->getLogedInEmployeeNumber();
                    $this->post = $this->buzzService->getShareById($this->id)->getPostShared();
                    $this->sharedEmployeeDetailsList = $this->getPostSharedEmployeeNameList($this->post);

                    if ($this->event === "click") {
                        
                    } else {
                        foreach ($this->sharedEmployeeDetailsList as $employee) {
                            if ($employee['empNumber'] !== $this->loggedInEmployeeId) {
                                echo $employee['empName'];
                                echo "\n";
                            }
                        }
                        sfView::NONE;
                        die;
                    }
                }
            }
        } catch (Exception $ex) {
            $this->redirect('auth/login');
        }
    }

    /**
     * get post shared employee list
     * @param type $post
     * @return array EployeeList
     */
    private function getPostSharedEmployeeList($post) {
        $sharedEmployeeList = array();
        $isAdminShare = false;
        foreach ($post->getShare() as $share) {
            if ($share->getEmployeeNumber() == null) {
                $isAdminShare = true;
            } else {
                $employee = $share->getEmployeePostShared();
                array_push($sharedEmployeeList, $employee);
            }
        }
        $sharedUniqueEmployeeList = $this->generateUniqueEmployeeList($sharedEmployeeList);
        if ($isAdminShare) {
            array_push($sharedUniqueEmployeeList, new Employee());
        }
        return $sharedUniqueEmployeeList;
    }

    /**
     * Returns a unique list of employees from the $employeeList
     * @param array $employeeList
     * @return array
     */
    private function generateUniqueEmployeeList($employeeList) {
        $uniqueEmployeeList = array();
        foreach ($employeeList as $employee) {
            $id = $employee->getEmpNumber();
            isset($uniqueEmployeeList[$id]) or $uniqueEmployeeList[$id] = $employee;
        }
        return $uniqueEmployeeList;
    }

    /**
     * get post shared employee name list;
     * @param type $post
     * @return array employee name list
     */
    private function getPostSharedEmployeeNameList($post) {
        $sharedEmployeeDetailsList = array();
        foreach ($post->getShare() as $share) {

            if ($share->getEmployeeNumber() == null) {
                $empName = 'Admin';
            } else {
                $employee = $share->getEmployeePostShared();
                if ($employee instanceof Employee) {
                    $empName = $employee->getFirstAndLastNames();
                    $jobTitle = $employee->getJobTitleName();
                } else {
                    $empName = '(' . __(self::LABEL_EMPLOYEE_DELETED) . ')';
                    $jobTitle = '';
                    $employeeDeleted = true;
                }
            }

            $employeeDetails = array(self::EMP_DELETED => $employeeDeleted, self::EMP_NUMBER => $share->getEmployeeNumber(), self::EMP_NAME => $empName, self::EMP_JOB_TITLE => $jobTitle);
            $sharedEmployeeDetailsList[$share->getEmployeeNumber()] = $employeeDetails;
        }
        return $sharedEmployeeDetailsList;
    }

}
