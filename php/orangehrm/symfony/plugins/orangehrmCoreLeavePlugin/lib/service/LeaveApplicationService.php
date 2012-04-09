<?php

class LeaveApplicationService extends AbstractLeaveAllocationService {

    protected $leaveEntitlementService;

    /**
     *
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService() {
        if (!($this->leaveEntitlementService instanceof LeaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }
    
    /**
     *
     * @param LeaveEntitlementService $service 
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $service) {
        $this->leaveEntitlementService = $service;
    }

    /**
     *
     * @param LeaveParameterObject $leaveAssignmentData
     * @return type 
     */
    public function applyLeave(LeaveParameterObject $leaveAssignmentData) {

        if ($this->applyMoreThanAllowedForADay($leaveAssignmentData)) {
            throw new LeaveAllocationServiceException('Failed to Submit: Work Shift Length Exceeded');
        } else {
            if (!$this->hasOverlapLeave($leaveAssignmentData)) {
                $this->saveLeaveRequest($leaveAssignmentData);
                return true;
            }
        }
    }

    /**
     * Saves Leave Request and Sends Notification
     * 
     * @param LeaveParameterObject $leaveAssignmentData 
     * 
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

                    $leaveApplicationMailer = new LeaveApplicationMailer($this->getLoggedInEmployee(), $leaveRequest, $leaves);
                    $leaveApplicationMailer->send();

                    return true;
                } catch (Exception $e) {
                    throw new LeaveAllocationServiceException('Leave Quota will Exceed');
                }
            }
        }
    }

        /**
     *
     * @param $isWeekend
     * @return status
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
