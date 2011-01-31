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
 * Displaying AssignLeave UI and saving data
 *
 * @author sujith
 */
class assignLeaveAction extends sfAction {
    private $employeeService;
    private $leaveRequestService;
    private $leaveTypeService;
    private $leavePeriodService;
    private $leaveNotificationService;

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
     * @return LeaveTypeService
     */
    public function getLeaveTypeService() {
        if(is_null($this->leaveTypeService)) {
            $leaveTypeService	= new LeaveTypeService();
            $leaveTypeService->setLeaveTypeDao(new LeaveTypeDao());
            $this->leaveTypeService	=	$leaveTypeService ;
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

        //authentication
        $userDetails = $this->getLoggedInUserDetails();
        if($userDetails['userType'] == 'ESS') {
            $this->forward('leave', 'viewMyLeaveList');
        }
        
        //this section is to save leave request
        if($request->isMethod('post')) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if($this->form->isValid()) {
                $post   =	$form->getValues();
                //check whether employee exists
                if(empty($post['txtEmpID'])) {
                    $this->templateMessage = array('WARNING', "Employee Does Not Exist");
                }
                
                if(!empty($post['txtEmpID']) && !$this->hasOverlapLeave($this->form)) {
                    $this->saveLeaveRequest($this->form);
                }
            }
        }
    }

    /**
     * Retrieve Leave Type List
     */
    protected function getElegibleLeaveTypes() {
        $leaveTypeChoices =	array();
        $leaveTypeList =	$this->getLeaveTypeService()->getLeaveTypeList();

        $leaveTypeChoices[''] =	"Select a Leave Type";
        foreach( $leaveTypeList as $leaveType) {
            $leaveTypeChoices[$leaveType->getLeaveTypeId()]	=	$leaveType->getLeaveTypeName();
        }
        return $leaveTypeChoices;
    }

    /**
     * Creating Assign Leave Form
     */
    protected function getAssignLeaveForm() {
        //making the optional parameters to create the form
        $leaveTypes = $this->getElegibleLeaveTypes();
        $userDetails = $this->getLoggedInUserDetails();
        if(count($leaveTypes) == 1) {
            $this->templateMessage = array('WARNING', 'No Eligible Leave Types to Assign Leave, Please contact HR Admin');
        }
        $leaveFormOptions = array('leaveTypes' => $leaveTypes, 'userType' => $userDetails['userType'],
                'loggedUserId' => $userDetails['loggedUserId']);
        $form = new AssignLeaveForm(array(), $leaveFormOptions, true);

        return $form;
    }

    /**
     * Returns Logged in user details
     */
    protected function getLoggedInUserDetails() {
        $userDetails['userType'] = 'ESS';

        if (!empty($_SESSION['empNumber'])) {
            $userDetails['loggedUserId'] = $_SESSION['empNumber'];
        } else {
            $userDetails['loggedUserId'] = 0; // Means default admin
        }

        if ($_SESSION['isSupervisor']) {
            $userDetails['userType'] = 'Supervisor';
        }

        if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=='Yes') {
            $userDetails['userType'] = 'Admin';
        }
        return $userDetails;
    }

    /**
     * Checking for leave overlaps
     */
    protected function hasOverlapLeave(sfForm $form) {
        $post   =	$form->getValues();
        
        //find duplicate leaves
        $overlapLeaves  = $this->getLeaveRequestService()->getOverlappingLeave($post['txtFromDate'],$post['txtToDate'],
                $post['txtEmpID']);
        $this->overlapLeaves = $overlapLeaves;
        if(count($overlapLeaves) == 0) {
            $this->overlapLeaves = null;
            return false;
        }
        return true;
    }

    /**
     * Saving Leave Request
     */
    protected function saveLeaveRequest(sfForm $form) {
        $post           =	$form->getValues();
        $leaveRequest	=	$form->getLeaveRequest();
        $leaveType		=	$this->getLeaveTypeService()->readLeaveType($post['txtLeaveType']);
        $leaveRequest->setLeaveTypeName($leaveType->getLeaveTypeName());
        //$leaveRequest->setDateApplied(date('Y-m-d'));

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
            if($this->isEmployeeAllowedToApply($leaveType)) {
                try {
                    $this->getLeaveRequestService()->saveLeaveRequest($leaveRequest,$leaves);

                    if($this->form->isOverlapLeaveRequest()){
                        $this->getLeaveRequestService()->modifyOverlapLeaveRequest($leaveRequest, $leaves);
                    }

                    //send notification to the when leave is assigned
                    $leaveAssignmentMailer = new LeaveAssignmentMailer($leaveRequest, $leaves, $_SESSION['empNumber']);
                    $leaveAssignmentMailer->send();

                    $this->templateMessage = array('SUCCESS', 'Leave Successfully Assigned');
                } catch(Exception $e) {
                    $this->templateMessage = array('WARNING', "Leave Period Does Not Exist");
                }
            }
        } else {
            $this->templateMessage = array('WARNING', "Make Sure Leave Request Contain Work Days");
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
