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
 *
 * @author sujith
 */
class applyLeaveAction extends sfAction {
    private $employeeService;
    private $leaveRequestService;
    private $leaveTypeService;
    private $leavePeriodService;
    private $leaveNotificationService;

    /**
     * Get Employee number
     * @return int
     */
    public function getEmployeeNumber() {
        return $_SESSION['empNumber'];
    }

    public function getLoggedInEmployee() {

        $employeeService = new EmployeeService(new EmployeeDao());
        $employee = $employeeService->getEmployee($this->getEmployeeNumber());

        return $employee;

    }

    /**
     * Set Employee number
     * @param int
     */
    public function setEmployeeNumber($empId) {
        $_SESSION['empNumber'] = $empId;
    }

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if(is_null($this->form)) {
            $this->form	= $form;
        }
    }

    /**
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {

        if (is_null($this->leaveRequestService)) {
            $leaveRequestService = new LeaveRequestService();
            $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
            $this->leaveRequestService = $leaveRequestService;
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
        if(is_null($this->leavePeriodService)) {
            $leavePeriodService	= new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService	=	$leavePeriodService;
        }
        return $this->leavePeriodService;
    }

    /**
     *
     * @param LeavePeriodService $leavePeriodService
     */
    public function setLeavePeriodService(LeavePeriodService $leavePeriodService) {
        $this->leavePeriodService	=	$leavePeriodService;
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

        $form = $this->getApplyLeaveForm();
        $this->setForm($form);
        $this->overlapLeaves = 0;

        //this section is to save leave request
        if($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if($this->form->isValid()) {
                if(!$this->hasOverlapLeave($this->form)) {
                    $this->saveLeaveRequest($this->form);
                }
            }
        }
    }

    /**
     * Retrieve Eligible Leave Type
     */
    protected function getElegibleLeaveTypes() {
        $leaveTypeChoices	=	array();
        $empId				=	$this->getEmployeeNumber() ;
        $employeeService	= 	$this->getEmployeeService();
        $employee			=	$employeeService->getEmployee($empId);

        $leaveRequestService	=	$this->getLeaveRequestService();
        $leaveTypeList			=	$leaveRequestService->getEmployeeAllowedToApplyLeaveTypes($employee);

        $leaveTypeChoices['']	=	"Select a Leave Type";
        foreach( $leaveTypeList as $leaveType) {
            $leaveTypeChoices[$leaveType->getLeaveTypeId()]	=	$leaveType->getLeaveTypeName();
        }
        return $leaveTypeChoices;
    }

    /**
     * Creating user forms
     */
    protected function getApplyLeaveForm() {
        //Check for available leave types
        $leaveTypes = $this->getElegibleLeaveTypes();
        if(count($leaveTypes) == 1) {
            $this->templateMessage = array('WARNING', 'No Eligible Leave types to apply for leave');
            return;
        }
        $form = new ApplyLeaveForm(array(), array('leaveTypes' => $leaveTypes), true);

        return $form;
    }

    /**
     * Checking for leave overlaps
     */
    protected function hasOverlapLeave(sfForm $form) {
        $post   =	$form->getValues();

        //find duplicate leaves
        $overlapLeaves  = $this->getLeaveRequestService()->getOverlappingLeave($post['txtFromDate'],$post['txtToDate'],
                $post['txtEmpID']);
        $this->overlapLeaves    = $overlapLeaves;
        if(count($overlapLeaves) == 0) {
            $this->overlapLeaves = null;
            return false;
        }
        return true;
    }

    /**
     * @return LeaveTypeService
     */
    public function getLeaveTypeService() {
        if(is_null($this->leaveTypeService)) {
            $leaevTypeservice	= new LeaveTypeService();
            $leaevTypeservice->setLeaveTypeDao(new LeaveTypeDao());
            $this->leaveTypeService	=	$leaevTypeservice ;
        }
        return $this->leaveTypeService;
    }

    /**
     * @param LeaveTypeService $leaveTypeService
     */
    public function setLeaveTypeService( LeaveTypeService $leaveTypeService) {
        $this->leaveTypeService	=	$leaveTypeService ;
    }

    /**
     * Saves Leave Request and Sends Notification
     */
    protected function saveLeaveRequest(sfForm $form) {
        $post           =	$form->getValues();
        $leaveRequest	=	$form->getLeaveRequest();
        $leaveType		=	$this->getLeaveTypeService()->readLeaveType($post['txtLeaveType']);
        $leaveRequest->setLeaveTypeName($leaveType->getLeaveTypeName());
        $leaveRequest->setDateApplied(date('Y-m-d'));

        if(is_null($leaveRequest->getLeavePeriodId())) {
            if($this->getLeavePeriodService()->isWithinNextLeavePeriod(strtotime($leaveRequest->getDateApplied()))) {
                $nextLeavePeriod	=	$this->getLeavePeriodService()->createNextLeavePeriod($leaveRequest->getDateApplied());
                $leaveRequest->setLeavePeriodId($nextLeavePeriod->getLeavePeriodId());
            }
        }
        $leaves	=	$form->createLeaveObjectListForAppliedRange();
        $holidayCount = 0;
        $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
        foreach($leaves as $k => $leave) {
            if(in_array($leave->getLeaveStatus(), $holidays)) {
                $holidayCount++;
            }
        }

        //this is to see whether employee applies leave only during weekends or standard holidays
        if($holidayCount != count($leaves)) {
            try {
                $this->getLeaveRequestService()->saveLeaveRequest($leaveRequest,$leaves);

                //sending leave apply notification

                $leaveApplicationMailer = new LeaveApplicationMailer($this->getLoggedInEmployee(), $leaveRequest, $leaves);
                $leaveApplicationMailer->send();

                $this->templateMessage = array('SUCCESS', 'Leave Request Successfully Submitted');
            } catch(Exception $e) {
                $this->templateMessage = array('WARNING', "Leave Period Does Not Exists");
            }
        } else {
            $this->templateMessage = array('WARNING', "Leave Request Made on Holiday(s), Submission Failed");
        }
    }
}
?>
