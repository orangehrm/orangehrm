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
 *
 * @author sujith
 */
class assignLeaveAction extends baseLeaveAction {

    protected $employeeService;
    protected $leaveRequestService;
    protected $leavePeriodService;
    protected $leaveNotificationService;

    /**
     * @param sfForm $form
     */
    public function setForm(sfForm $form) {
        if (is_null($this->assignLeaveForm)) {
            $this->assignLeaveForm = $form;
        }
    }

    /**
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {

        if (!($this->leaveRequestService instanceof LeaveRequestService)) {
            $this->leaveRequestServic = new LeaveRequestService();
        }

        return $this->leaveRequestService;
    }

    /**
     * @param LeaveRequestService $leaveRequestService
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService) {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     * @return EmployeeService
     */
    public function getEmployeeService() {

        if (is_null($this->employeeService)) {
            $employeeService = new EmployeeService();
            $employeeService->setEmployeeDao(new EmployeeDao());
            $this->employeeService = $employeeService;
        }

        return $this->employeeService;
    }

    /**
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    /**
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {
        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }
        return $this->leavePeriodService;
    }

    /**
     *
     * @param LeavePeriodService $leavePeriodService
     */
    public function setLeavePeriodService(LeavePeriodService $leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * @return LeaveNotificationService
     */
    public function getLeaveNotificationService() {

        if (is_null($this->leaveNotificationService)) {
            $this->leaveNotificationService = new LeaveNotificationService();
        }

        return $this->leaveNotificationService;
    }

    /**
     * @param LeaveRequestService $leaveRequestService
     */
    public function setLeaveNotificationService(LeaveNotificationService $leaveNotificationService) {
        $this->leaveNotificationService = $leaveNotificationService;
    }

    public function execute($request) {

        $form = $this->getAssignLeaveForm();
        $this->setForm($form);
        $this->overlapLeaves = 0;

        /* Authentication */
        $userDetails = $this->getLoggedInUserDetails();
        if ($userDetails['userType'] == 'ESS') {
            $this->forward('leave', 'viewMyLeaveList');
        }

        /* This section is to save leave request */
        if ($request->isMethod('post')) {
            $this->assignLeaveForm->bind($request->getParameter($this->assignLeaveForm->getName()));
            if ($this->assignLeaveForm->isValid()) {
                $post = $form->getValues();
                /* Check whether employee exists */
                if (empty($post['txtEmpID'])) {
                    $this->templateMessage = array('WARNING', __("Invalid Employee"));
                }

                if (!empty($post['txtEmpID']) && !$this->applyMoreThanAllowedForAday($this->assignLeaveForm)) {
                    if (!$this->hasOverlapLeave($this->assignLeaveForm)) {
                        $this->saveLeaveRequest($this->assignLeaveForm);
                    }
                } elseif (!empty($post['txtEmpID']) && $this->applyMoreThanAllowedForAday($this->assignLeaveForm)) {
                    $this->templateMessage = array('WARNING', __("Failed to Assign: Work Shift Length Exceeded"));
                    $this->overlapLeaves = 0;
                }
            }
        }
    }

    protected function getWorkShiftDurationForEmployee($employeeId) {
        $workshift = $this->getEmployeeService()->getWorkShift($employeeId);

        if ($workshift == null) {
            $definedDuration = sfConfig::get('app_orangehrm_core_leave_plugin_default_work_shift_length_hours');
        } else {
            $definedDuration = $workshift->getWorkShift()->getHoursPerDay();
        }
        return $definedDuration;
    }

    /**
     *
     * @param sfForm $form
     * @return boolean 
     */
    public function applyMoreThanAllowedForAday($form) {

        $post = $form->getValues();

        $fromTime = date("H:i:s", strtotime($post['txtFromTime']));
        $toTime = date("H:i:s", strtotime($post['txtToTime']));


        if ($post['txtFromDate'] == $post['txtToDate']) {
            $totalDuration = $this->getLeaveRequestService()->getTotalLeaveDuration($post['txtEmpID'], $post['txtFromDate']);
        }

        if (($totalDuration + $post['txtLeaveTotalTime']) > $this->getWorkShiftDurationForEmployee($post['txtEmpID'])) {

            $dateRange = new DateRange();
            $dateRange->setFromDate($post['txtFromDate']);
            $dateRange->setToDate($post['txtFromDate']);

            $searchParameters['dateRange'] = $dateRange;
            $searchParameters['employeeFilter'] = $post['txtEmpID'];

            $parameter = new ParameterObject($searchParameters);
            $leaveRequests = $this->getLeaveRequestService()->searchLeaveRequests($parameter);

            if (count($leaveRequests['list']) > 0) {
                foreach ($leaveRequests['list'] as $leaveRequest) {
                    $this->overlapLeaves [] = $leaveRequest->getLeave();
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retrieve Leave Type List
     */
    protected function getElegibleLeaveTypes() {
        $leaveTypeChoices = array();
        $leaveTypeList = $this->getLeaveTypeService()->getLeaveTypeList();

        $leaveTypeChoices[''] = '--' . __('Select') . '--';
        foreach ($leaveTypeList as $leaveType) {
            $leaveTypeChoices[$leaveType->getLeaveTypeId()] = $leaveType->getLeaveTypeName();
        }
        return $leaveTypeChoices;
    }

    /**
     * Creating Assign Leave Form
     */
    protected function getAssignLeaveForm() {
        /* Making the optional parameters to create the form */
        $leaveTypes = $this->getElegibleLeaveTypes();
        $userDetails = $this->getLoggedInUserDetails();
        if (count($leaveTypes) == 1) {
            $this->templateMessage = array('WARNING', __('No Leave Types with Leave Balance'));
        }
        $leaveFormOptions = array('leaveTypes' => $leaveTypes, 'userType' => $userDetails['userType'],
            'loggedUserId' => $userDetails['loggedUserId']);
        $form = new AssignLeaveForm(array(), $leaveFormOptions, true);

        return $form;
    }

    /**
     * Checking for leave overlaps
     */
    protected function hasOverlapLeave(sfForm $form) {
        $post = $form->getValues();
        $fromTime = '';
        $toTime = '';
        if (strlen($post['txtFromTime']) > 0) {
            $fromTime = date("H:i:s", strtotime($post['txtFromTime']));
        }
        if (strlen($post['txtToTime']) > 0) {
            $toTime = date("H:i:s", strtotime($post['txtToTime']));
        }
        /* Find duplicate leaves */
        $overlapLeaves = $this->getLeaveRequestService()->getOverlappingLeave($post['txtFromDate'], $post['txtToDate'], $post['txtEmpID'], $fromTime, $toTime);

        $this->overlapLeaves = $overlapLeaves;
        if (count($overlapLeaves) == 0) {
            $this->overlapLeaves = null;
            return false;
        }
        return true;
    }

    /**
     * Saving Leave Request
     */
    protected function saveLeaveRequest(sfForm $form) {
        $post = $form->getValues();
        $leaveRequest = $form->getLeaveRequest();
        $leaveType = $this->getLeaveTypeService()->readLeaveType($post['txtLeaveType']);
        $leaveRequest->setLeaveTypeName($leaveType->getLeaveTypeName());

        if (is_null($leaveRequest->getLeavePeriodId())) {
            if ($this->getLeavePeriodService()->isWithinNextLeavePeriod(strtotime($leaveRequest->getDateApplied()))) {
                $nextLeavePeriod = $this->getLeavePeriodService()->createNextLeavePeriod($leaveRequest->getDateApplied());
                $leaveRequest->setLeavePeriodId($nextLeavePeriod->getLeavePeriodId());
            }
        }
        $leaves = $form->createLeaveObjectListForAppliedRange();
        $holidayCount = 0;
        $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
        foreach ($leaves as $k => $leave) {
            if (in_array($leave->getLeaveStatus(), $holidays)) {
                $holidayCount++;
            }
        }

        /* This is to see whether employee applies leave only during weekends or standard holidays */
        if ($holidayCount != count($leaves)) {
            if ($this->isEmployeeAllowedToApply($leaveType)) {
                try {
                    $this->getLeaveRequestService()->saveLeaveRequest($leaveRequest, $leaves);

                    if ($this->assignLeaveForm->isOverlapLeaveRequest()) {
                        $this->getLeaveRequestService()->modifyOverlapLeaveRequest($leaveRequest, $leaves);
                    }

                    /* Send notification to the when leave is assigned */
                    $leaveAssignmentMailer = new LeaveAssignmentMailer($leaveRequest, $leaves, $_SESSION['empNumber']);
                    $leaveAssignmentMailer->send();

                    $this->templateMessage = array('SUCCESS', __('Successfully Assigned'));
                } catch (Exception $e) {
                    $this->templateMessage = array('WARNING', __("Leave Period Does Not Exist"));
                }
            }
        } else {
            $this->templateMessage = array('WARNING', __("Leave Request Should Contain Work Days"));
        }
    }

    /**
     * isEmployeeAllowedToApply
     * @param LeaveType $leaveType
     * @returns boolean
     */
    protected function isEmployeeAllowedToApply(LeaveType $leaveType) {
        return true;
    }

}

?>
