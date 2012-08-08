<?php

/**
 * Leave Application Service
 * 
 * Functionalities related to leave applying.
 * 
 * @package leave
 * @todo Add license 
 */

class LeaveApplicationService extends AbstractLeaveAllocationService {

    protected $leaveEntitlementService;

    /**
     * Get LeaveEntitlementService
     * @return LeaveEntitlementService
     * 
     */
    public function getLeaveEntitlementService() {
        if (!($this->leaveEntitlementService instanceof LeaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }
    
    /**
     * Set LeaveEntitlementService
     * @param LeaveEntitlementService $service 
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $service) {
        $this->leaveEntitlementService = $service;
    }

    /**
     * Creates a new leave application
     * 
     * @param LeaveParameterObject $leaveAssignmentData
     * @return boolean True if leave request is saved else false
     * @throws LeaveAllocationServiceException When leave request length exceeds work shift length. 
     * 
     * @todo Add LeaveParameterObject to the API
     */
    public function applyLeave(LeaveParameterObject $leaveAssignmentData) {

        if ($this->applyMoreThanAllowedForADay($leaveAssignmentData)) {
            throw new LeaveAllocationServiceException('Failed to Submit: Work Shift Length Exceeded');
        }

        if (!$this->hasOverlapLeave($leaveAssignmentData)) {
            return $this->saveLeaveRequest($leaveAssignmentData);
        }
        
        return false;
        
    }

    /**
     * Saves Leave Request and Sends Email Notification
     * 
     * @param LeaveParameterObject $leaveAssignmentData 
     * @return boolean True if leave request is saved else false
     * @throws LeaveAllocationServiceException
     * 
     * @todo Don't catch general Exception. Catch specific one.
     */
    protected function saveLeaveRequest(LeaveParameterObject $leaveAssignmentData) {

        $leaveRequest = $this->generateLeaveRequest($leaveAssignmentData);

        $leaveType = $this->getLeaveTypeService()->readLeaveType($leaveAssignmentData->getLeaveType());
        $leaveRequest->setLeaveTypeName($leaveType->getLeaveTypeName());

        if (is_null($leaveRequest->getLeavePeriodId())) {
            if ($this->getLeavePeriodService()->isWithinNextLeavePeriod(strtotime($leaveRequest->getDateApplied()))) {
                $nextLeavePeriod = $this->getLeavePeriodService()->createNextLeavePeriod($leaveRequest->getDateApplied());
                $leaveRequest->setLeavePeriodId($nextLeavePeriod->getLeavePeriodId());
            }
        }

        $leaves = $this->createLeaveObjectListForAppliedRange($leaveAssignmentData);
        if ($this->isEmployeeAllowedToApply($leaveType)) {
            if ($this->isValidLeaveRequest($leaveRequest, $leaves)) {
                try {
                    $this->getLeaveRequestService()->saveLeaveRequest($leaveRequest, $leaves);

                    if ($this->isOverlapLeaveRequest($leaveAssignmentData)) {
                        $this->getLeaveRequestService()->modifyOverlapLeaveRequest($leaveRequest, $leaves);
                    }

                    //sending leave apply notification
                    $this->sendEmail($this->getLoggedInEmployee(), $leaveRequest, $leaves);

                    return $leaveRequest;
                } catch (Exception $e) {
                    throw new LeaveAllocationServiceException('Leave Quota will Exceed');
                }
            }
        }
        
        return false;
        
    }

    protected function sendEmail($loggedInEmployee, $leaveRequest, $leaves) {
        
        $leaveApplicationMailer = new LeaveApplicationMailer($loggedInEmployee, $leaveRequest, $leaves);
        $leaveApplicationMailer->send();
    }

    /**
     * Returns leave status based on weekend and holiday
     * 
     * If weekend, returns Leave::LEAVE_STATUS_LEAVE_WEEKEND
     * If holiday, returns Leave::LEAVE_STATUS_LEAVE_HOLIDAY
     * Else, returns LEAVE_STATUS_LEAVE_PENDING_APPROVAL
     * 
     * @param $isWeekend boolean
     * @param $isHoliday boolean
     * @param $leaveDate string 
     * @return status
     * 
     * @todo Check usage of $leaveDate
     * 
     */
    public function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate) {
        $status = Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;

        if ($isWeekend) {
            $status = Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        }

        if ($isHoliday) {
            $status = Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
        }

        return $status;
    }

    /**
     * Is Valid leave request
     * @param LeaveType $leaveType
     * @param array $leaveRecords
     * @returns boolean
     */
    protected function isValidLeaveRequest($leaveRequest, $leaveRecords) {
        $holidayCount = 0;
        $requestedLeaveDays = array();
        $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
        foreach ($leaveRecords as $k => $leave) {
            if (in_array($leave->getLeaveStatus(), $holidays)) {
                $holidayCount++;
            }
            $leavePeriod = $this->getLeavePeriodService()->getLeavePeriod(strtotime($leave->getLeaveDate()));
            if($leavePeriod instanceof LeavePeriod) {
                $leavePeriodId = $leavePeriod->getLeavePeriodId();
            } else {
                $leavePeriodId = null; //todo create leave period?
            }

            if(key_exists($leavePeriodId, $requestedLeaveDays)) {
                $requestedLeaveDays[$leavePeriodId] += $leave->getLeaveLengthDays();
            } else {
                $requestedLeaveDays[$leavePeriodId] = $leave->getLeaveLengthDays();
            }
        }

        if ($this->isLeaveRequestNotExceededLeaveBalance($requestedLeaveDays, $leaveRequest) && $this->hasWorkingDays($holidayCount, $leaveRecords)) {
            return true;
        }
    }
    
    /**
     * isLeaveRequestNotExceededLeaveBalance
     * @param array $requestedLeaveDays key => leave period id
     * @param LeaveRequest $leaveRequest
     * @returns boolean
     */
    protected function isLeaveRequestNotExceededLeaveBalance($requestedLeaveDays, $leaveRequest) {

        if (!$this->getLeaveEntitlementService()->isLeaveRequestNotExceededLeaveBalance($requestedLeaveDays, $leaveRequest)) {
            throw new LeaveAllocationServiceException('Failed to Submit: Leave Balance Exceeded');
            return false;
        }
        return true;
    }
    
    /**
     * hasWorkingDays
     * @param LeaveType $leaveType
     * @returns boolean
     */
    protected function hasWorkingDays($holidayCount, $leaves) {

        if ($holidayCount == count($leaves)) {
            throw new LeaveAllocationServiceException('Failed to Submit: No Working Days Selected');
        }

        return true;
    }
    
    /**
     *
     * @return Employee
     * @todo Remove the use of session
     */
    public function getLoggedInEmployee() {
        $employee = $this->getEmployeeService()->getEmployee($_SESSION['empNumber']);
        return $employee;
    }

}
