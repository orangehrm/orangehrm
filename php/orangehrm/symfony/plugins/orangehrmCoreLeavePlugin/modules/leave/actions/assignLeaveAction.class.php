<?php

/*
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
 * Displaying AssignLeave UI and saving data
 */
class assignLeaveAction extends baseLeaveAction {

    protected $leaveAssignmentService;

    /**
     * @param sfForm $form
     */
    public function setForm(sfForm $form) {
        if (is_null($this->assignLeaveForm)) {
            $this->assignLeaveForm = $form;
        }
    }

    /**
     *
     * @return LeaveAssignmentService
     */
    public function getLeaveAssignmentService() {
        if (!($this->leaveAssignmentService instanceof LeaveAssignmentService)) {
            $this->leaveAssignmentService = new LeaveAssignmentService();
        }
        return $this->leaveAssignmentService;
    }

    /**
     *
     * @param LeaveAssignmentService $service 
     */
    public function setLeaveAssignmentService(LeaveAssignmentService $service) {
        $this->leaveAssignmentService = $service;
    }
    
    public function execute($request) {

        $form = $this->getAssignLeaveForm();
        $this->setForm($form);
        $this->overlapLeave = 0;

        /* This section is to save leave request */
        if ($request->isMethod('post')) {
            $this->assignLeaveForm->bind($request->getParameter($this->assignLeaveForm->getName()));
            if ($this->assignLeaveForm->isValid()) {
                try {
                    $leaveParameters = $this->getLeaveParameterObject($form->getValues());
                    
                    $success = $this->getLeaveAssignmentService()->assignLeave($leaveParameters);
                    
                    if ($success) {
                        $this->templateMessage = array('SUCCESS', __('Successfully Assigned'));
                    } else {
                        $this->overlapLeave = $this->getLeaveAssignmentService()->getOverlapLeave();
                        $this->templateMessage = array('WARNING', __('Failed to Assign'));
                    }
                } catch (LeaveAllocationServiceException $e) {
                    $this->templateMessage = array('WARNING', __($e->getMessage()));
                }
            }
        }
    }
    
    protected function getLeaveParameterObject(array $formValues) {
        
        $empData = $formValues['txtEmployee'];
        $formValues['txtEmpID'] = $empData['empId'];
        
        return new LeaveParameterObject($formValues);
    }
    
    /**
     * Retrieve Leave Type List
     */
    protected function getElegibleLeaveTypes() {
        $leaveTypeList = $this->getLeaveTypeService()->getLeaveTypeList();
        return $leaveTypeList;
    }

    /**
     * Creating Assign Leave Form
     */
    protected function getAssignLeaveForm() {
        /* Making the optional parameters to create the form */
        $leaveTypes = $this->getElegibleLeaveTypes();

        if (count($leaveTypes) == 0) {
            $this->templateMessage = array('WARNING', __('No Leave Types with Leave Balance'));
        }
        $leaveFormOptions = array('leaveTypes' => $leaveTypes);
        $form = new AssignLeaveForm(array(), $leaveFormOptions, true);

        return $form;
    }

}
