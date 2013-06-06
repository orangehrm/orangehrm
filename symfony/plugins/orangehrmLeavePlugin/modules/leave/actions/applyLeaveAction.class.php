<?php

/*
 *
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
 *
 */

/**
 * Displaying ApplyLeave UI and saving data
 */
class applyLeaveAction extends baseLeaveAction {

    protected $employeeService;
    protected $leaveApplicationService;
    protected $leaveRequestService;

    /**
     *
     * @return EmployeeService
     */
    public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     *
     * @param EmployeeService $service 
     */
    public function setEmployeeService(EmployeeService $service) {
        $this->employeeService = $service;
    }

    /**
     *
     * @return LeaveApplicationService
     */
    public function getLeaveApplicationService() {
        if (!($this->leaveApplicationService instanceof LeaveApplicationService)) {
            $this->leaveApplicationService = new LeaveApplicationService();
        }
        return $this->leaveApplicationService;
    }

    /**
     *
     * @param LeaveApplicationService $service 
     */
    public function setLeaveApplicationService(LeaveApplicationService $service) {
        $this->leaveApplicationService = $service;
    }

    /**
     *
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {
        if (!($this->leaveRequestService instanceof LeaveRequestService)) {
            $this->leaveRequestService = new LeaveRequestService();
        }
        return $this->leaveRequestService;
    }

    /**
     *
     * @param LeaveRequestService $service 
     */
    public function setLeaveRequestService(LeaveRequestService $service) {
        $this->leaveRequestService = $service;
    }
    
    public function execute($request) {
        
        $this->getLeaveRequestService()->markApprovedLeaveAsTaken();

        $this->leaveTypes = $this->getElegibleLeaveTypes();
        
        if (count($this->leaveTypes) <= 1) {
            $this->getUser()->setFlash('warning.nofade', __('No Leave Types with Leave Balance'));
        }
        
        $this->applyLeaveForm = $this->getApplyLeaveForm($this->leaveTypes);

        $this->overlapLeaves = 0;
        $this->workshiftLengthExceeded = true;

        //this section is to save leave request
        if ($request->isMethod('post')) {
            $this->applyLeaveForm->bind($request->getParameter($this->applyLeaveForm->getName()));
            if ($this->applyLeaveForm->isValid()) {
                try {
                    $leaveParameters = $this->getLeaveParameterObject($this->applyLeaveForm->getValues());
                    $success = $this->getLeaveApplicationService()->applyLeave($leaveParameters);

                    if ($success) {
                        $this->getUser()->setFlash('success', __('Successfully Submitted'));
                    } else {
                        $this->overlapLeave = $this->getLeaveApplicationService()->getOverlapLeave();
                        $this->getUser()->setFlash('warning', __('Failed to Submit'));
                    }
                } catch (LeaveAllocationServiceException $e) {
                    $this->getUser()->setFlash('warning', __($e->getMessage()));
                    $this->overlapLeave = $this->getLeaveApplicationService()->getOverlapLeave();
                    $this->workshiftLengthExceeded = true;
                }
            }
        }
    }
    
    protected function getLeaveParameterObject(array $formValues) {

        $time = $formValues['time'];
        $formValues['txtFromTime'] = $time['from'];
        $formValues['txtToTime'] = $time['to'];
        
        return new LeaveParameterObject($formValues);
    } 

    /**
     * Retrieve Eligible Leave Type
     */
    protected function getElegibleLeaveTypes() {
        $leaveTypeChoices = array();
        $empId = $_SESSION['empNumber']; // TODO: Use a session manager
        $employeeService = $this->getEmployeeService();
        $employee = $employeeService->getEmployee($empId);

        $leaveRequestService = $this->getLeaveRequestService();
        $leaveTypeList = $leaveRequestService->getEmployeeAllowedToApplyLeaveTypes($employee);

        $leaveTypeChoices[''] = '--' . __('Select') . '--';
        foreach ($leaveTypeList as $leaveType) {
            $leaveTypeChoices[$leaveType->getId()] = $leaveType->getName();
        }
        
        return $leaveTypeChoices;
    }

    /**
     * Creating user forms
     */
    protected function getApplyLeaveForm($leaveTypes) {
        $form = new ApplyLeaveForm(array(), array('leaveTypes' => $leaveTypes), true);
        return $form;
    }

}
