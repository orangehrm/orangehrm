<?php

class LeaveAssignmentService extends AbstractLeaveAllocationService {

    /**
     *
     * @param array $leaveAssignmentData
     * @return bool
     */
    public function assignLeave(LeaveParameterObject $leaveAssignmentData) {

        $employeeId = $leaveAssignmentData->getEmployeeNumber();

        /* Check whether employee exists */
        if (empty($employeeId)) {
            throw new LeaveAllocationServiceException('Invalid Employee');
        }

        if ($this->applyMoreThanAllowedForADay($leaveAssignmentData)) {
            throw new LeaveAllocationServiceException('Failed to Assign: Work Shift Length Exceeded');
        } else {
            if (!$this->hasOverlapLeave($leaveAssignmentData)) {
                return $this->saveLeaveRequest($leaveAssignmentData);
//                return true;
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

        $leaveDays = $this->createLeaveObjectListForAppliedRange($leaveAssignmentData);
        $holidayCount = 0;
        $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
        foreach ($leaveDays as $k => $leave) {
            if (in_array($leave->getLeaveStatus(), $holidays)) {
                $holidayCount++;
            }
        }

        /* This is to see whether employee applies leave only during weekends or standard holidays */
        if ($holidayCount != count($leaveDays)) {
            if ($this->isEmployeeAllowedToApply($leaveType)) {
                try {
                    $this->getLeaveRequestService()->saveLeaveRequest($leaveRequest, $leaveDays);

                    if ($this->isOverlapLeaveRequest($leaveAssignmentData)) {
                        $this->getLeaveRequestService()->modifyOverlapLeaveRequest($leaveRequest, $leaveDays);
                    }

                    /* Send notification to the when leave is assigned; TODO: Move to action? */
                    $this->sendEmail($leaveRequest, $leaveDays, $_SESSION['empNumber']);

//                    return true;
                    return $leaveRequest;
                } catch (Exception $e) {
                    throw new LeaveAllocationServiceException('Leave Period Does Not Exist');
                }
            }
        } else {
            throw new LeaveAllocationServiceException('Failed to Submit: No Working Days Selected');
        }
    }

    protected function sendEmail($leaveRequest, $leaveDays, $employeeNumber) {

        $leaveAssignmentMailer = new LeaveAssignmentMailer($leaveRequest, $leaveDays, $employeeNumber);
        $leaveAssignmentMailer->send();
    }

    /**
     *
     * @param type $isWeekend
     * @param type $isHoliday
     * @param type $leaveDate
     * @return type 
     */
    public function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate) {
        $status = null;

        if ($isWeekend) {
            return Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        }

        if ($isHoliday) {
            return Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
        }

        if (strtotime($leaveDate) < strtotime(date('Y-m-d'))) {
            $status = Leave::LEAVE_STATUS_LEAVE_TAKEN;
        } else {
            $status = Leave::LEAVE_STATUS_LEAVE_APPROVED;
        }

        return $status;
    }

}