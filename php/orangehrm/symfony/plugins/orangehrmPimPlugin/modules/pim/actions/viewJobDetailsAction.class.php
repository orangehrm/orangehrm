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
 * Actions class for PIM module
 *
 * TODO: Supervisor, admin mode
 * TODO: Edit mode in all screens (click edit to make editable)
 * TODO: Ajax loading icon
 * TODO: Contracts - do not allow empty from/to dates
 * TODO: Replace LocaleUtil::getInstance() with helper
 * remove die:
 *
 * Saving "Owner", "Individual" as translated strings in EmployeeMemberDetail
 * Improve server side validation
 * Standard success message in green at top
 * In trunk: only employement statuses assigned to employee are listed (In Job Tab) - in this branch All statuses are listed.
 * fix reset btn
 * # form change detection by storing original value in form input element (.data)
 * # keep form values (and keep pane open) if validation error on server side
 * # Reset buttons - consistant behaviour
 * # remove yahoo calendar
 * # validate server side validations are done for each screen
 */
class viewJobDetailsAction extends basePimAction {

    public function execute($request) {
        
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();

        $job = $request->getParameter('job');
        $empNumber = (isset($job['emp_number'])) ? $job['emp_number']: $request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
        $supervisorMode = $this->isSupervisor($loggedInEmpNum, $empNumber);

        $this->essMode = false;
        if (!$supervisorMode && !$adminMode) {
            if ($empNumber == $loggedInEmpNum) {
                $this->essMode = true;
            } else {
                $this->getUser()->setFlash('templateMessage', array('warning', __('Access Denied!')));
                $this->redirect($this->getRequest()->getReferer());
                return;
            }
        }
                       
        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }
        
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $param = array('empNumber' => $empNumber, 'ESS' => $this->essMode,
                       'employee' => $employee);
        
        $this->form = new EmployeeJobDetailsForm(array(), $param, true);

        if ($this->getRequest()->isMethod('post')) {

            // Handle the form submission           
            $this->form->bind($request->getParameter($this->form->getName()), 
                    $request->getFiles($this->form->getName()));

            if ($this->form->isValid()) {

                // validate either ADMIN, supervisor for employee or employee himself
                // save data

                $service = new EmployeeService();
                $service->saveJobDetails($this->form->getEmployee(), false);
                
                $this->getUser()->setFlash('templateMessage', array('success', __('Job Details Updated Successfully')));  
            } else {
                $this->getUser()->setFlash('templateMessage', array('warning', __('Form validation failed')));   
            }
            
            $this->redirect('pim/viewJobDetails?empNumber=' . $empNumber);
        }

    }

    /**
     * Assign given location to given employee
     *
     * @param int $empNumber Employee number
     * @param string $locationCode Location code to assign
     *
     * @return boolean true if successfully assigned, false otherwise
     */
//    public function executeAssignLocation(sfWebRequest $request) {
//
//        $this->setLayout(false);
//        sfConfig::set('sf_web_debug', false);
//        sfConfig::set('sf_debug', false);
//
//        $result = false;
//
//        //$auth = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
//
//        $empNumber = $request->getParameter('empNumber');
//        $locationCode = $request->getParameter('location');
//
//        /* TODO: Only allow admins and supervisors of the given employee to assign locations */
//        //if ($auth->isAdmin() || ($auth->isSupervisor() && $auth->isTheSupervisor($empNumber))) {
//
//        $service = new EmployeeService();
//        $result = $service->assignLocation($empNumber, $locationCode);
//        //}
//
//        return $this->renderText(json_encode($result));
//    }
//
//    /**
//     * Remove given location from given employee
//     *
//     * @param int $empNumber Employee number
//     * @param string $locationCode Location code to assign
//     *
//     * @return boolean true if successfully assigned, false otherwise
//     */
//    public function executeRemoveLocation(sfWebRequest $request) {
//
//        $this->setLayout(false);
//        sfConfig::set('sf_web_debug', false);
//        sfConfig::set('sf_debug', false);
//
//        $result = false;
//
//        //$auth = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
//
//        $empNumber = $request->getParameter('empNumber');
//        $locationCode = $request->getParameter('location');
//
//        /* TODO: Only allow admins and supervisors of the given employee to assign locations */
//        //if ($auth->isAdmin() || ($auth->isSupervisor() && $auth->isTheSupervisor($empNumber))) {
//
//        $service = new EmployeeService();
//        $result = $service->removeLocation($empNumber, $locationCode);
//        //}
//
//        return $this->renderText(json_encode($result));
//    }
//
//    /**
//     * Add job history item to employeee
//     *
//     * @param int $empNumber Employee number
//     *
//     * @return boolean true if successfully assigned, false otherwise
//     */
//    public function executeDeleteJobHistory(sfWebRequest $request) {
//
//        $this->setLayout(false);
//        sfConfig::set('sf_web_debug', false);
//        sfConfig::set('sf_debug', false);
//
//        $result = false;
//
//        //$auth = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
//
//        /* TODO: Only allow admins and supervisors of the given employee to assign locations */
//        //if ($auth->isAdmin() || ($auth->isSupervisor() && $auth->isTheSupervisor($empNumber))) {
//        $service = new EmployeeService();
//
//        // Job title history
//        $empId = $request->getParameter('EmpID', false);
//        if (!$empId) {
//            throw new PIMServiceException("No Employee ID given");
//        }
//        $jobTitlesToDelete = $request->getParameter('chkjobtitHistory', false);
//        $subDivisionsToDelete = $request->getParameter('chksubdivisionHistory', false);
//        $locationsToDelete = $request->getParameter('chklocationHistory', false);
//
//        if ($jobTitlesToDelete) {
//            $service->deleteJobTitleHistory($empId, $jobTitlesToDelete);
//        }
//        if ($subDivisionsToDelete) {
//            $service->deleteSubDivisionHistory($empId, $subDivisionsToDelete);
//        }
//        if ($locationsToDelete) {
//            $service->deleteLocationHistory($empId, $locationsToDelete);
//        }
//        //}
//        $this->redirect('pim/viewEmployee?empNumber=' . $empId . '&pane=' . self::JOB_PANE);
//    }
//
//    /**
//     * Delete employee contracts
//     *
//     * @param int $empNumber Employee number
//     *
//     * @return boolean true if successfully deleted, false otherwise
//     */
//    public function executeDeleteContracts(sfWebRequest $request) {
//
//        $this->form = new EmployeeContractsDeleteForm(array(), array(), true);
//        $this->form->bind($request->getParameter($this->form->getName()));
//        if ($this->form->isValid()) {
//            $result = false;
//
//            //$auth = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
//
//            /* TODO: Only allow admins and supervisors of the given employee to assign locations */
//            //if ($auth->isAdmin() || ($auth->isSupervisor() && $auth->isTheSupervisor($empNumber))) {
//            // Job title history
//            $empId = $request->getParameter('EmpID', false);
//            if (!$empId) {
//                throw new PIMServiceException("No Employee ID given");
//            }
//            $contractsToDelete = $request->getParameter('chkconextdel', array());
//            if ($contractsToDelete) {
//                $service = new EmployeeService();
//                $service->deleteContracts($empId, $contractsToDelete);
//            }
//            //}
//            $this->redirect('pim/viewEmployee?empNumber=' . $empId . '&pane=' . self::JOB_PANE);
//        }
//    }
//
//    /**
//     * Add / update employee contract
//     *
//     * @param int $empNumber Employee number
//     *
//     * @return boolean true if successfully assigned, false otherwise
//     */
//    public function executeUpdateContract(sfWebRequest $request) {
//        // TODO: Set ESS mode, enable csrf protection
//        $this->form = new EmployeeContractForm(array(), array(), true);
//
//        if ($this->getRequest()->isMethod('post')) {
//
//
//            // Handle the form submission
//            $this->form->bind($request->getPostParameters());
//
//            if ($this->form->isValid()) {
//
//                // validate either ADMIN, supervisor for employee or employee himself
//                // save data
//
//                $this->form->save();
//            } else {
//                $this->getUser()->setFlash('errorForm', $this->form);
//            }
//        }
//
//        $empNumber = $request->getParameter('empNumber');
//
//        $this->redirect('pim/viewEmployee?empNumber=' . $empNumber . '&pane=' . self::JOB_PANE);
//    }
//
//    /**
//     * Add job history item to employeee
//     *
//     * @param int $empNumber Employee number
//     *
//     * @return boolean true if successfully assigned, false otherwise
//     */
//    public function executeAddJobHistory(sfWebRequest $request) {
//
//
//        $result = false;
//
//        //$auth = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
//
//        /* TODO: Only allow admins and supervisors of the given employee to assign locations */
//        //if ($auth->isAdmin() || ($auth->isSupervisor() && $auth->isTheSupervisor($empNumber))) {
//        $this->form = new JobHistoryForm(array(), array(), true);
//        $empId = $request->getParameter('EmpID', false);
//
//        if ($this->getRequest()->isMethod('post')) {
//
//            // Handle the form submission
//            $this->form->bind($request->getPostParameters());
//
//            if ($this->form->isValid()) {
//
//                // validate either ADMIN, supervisor for employee or employee himself
//                // save data
//                $this->form->save();
//            } else {
//                $this->getUser()->setFlash('errorForm', $this->form);
//            }
//        }
//
//        $this->redirect('pim/viewEmployee?empNumber=' . $empId . '&pane=' . self::JOB_PANE);
//    }
//
//    /**
//     * Add job history item to employeee
//     *
//     * @param int $empNumber Employee number
//     *
//     * @return boolean true if successfully assigned, false otherwise
//     */
//    public function executeUpdateJobHistory(sfWebRequest $request) {
//
//        $this->form = new JobHistoryForm(array(), array(), true);
//        $result = false;
//
//        //$auth = new authorize($_SESSION['empID'], $_SESSION['isAdmin']);
//
//        /* TODO: Only allow admins and supervisors of the given employee to assign locations */
//        //if ($auth->isAdmin() || ($auth->isSupervisor() && $auth->isTheSupervisor($empNumber))) {
//        $this->form->bind($request->getPostParameters());
//        if ($this->form->isValid()) {
//            $empId = $request->getParameter('EmpID', false);
//            if ($this->getRequest()->isMethod('post')) {
//                $service = new EmployeeService();
//                //var_dump($request->getPostParameters());die;
//                $service->updateJobHistory($empId, $request->getPostParameters());
//            }
//        }
//
//        $this->redirect('pim/viewEmployee?empNumber=' . $empId . '&pane=' . self::JOB_PANE);
//    }

}
