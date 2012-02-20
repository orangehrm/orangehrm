<?php

class LeaveAssignmentService extends BaseService {

    protected $leaveRequestService;
    protected $leaveTypeService;
    protected $leavePeriodService;
    protected $employeeService;
    protected $workWeekService;
    protected $holidayService;

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

    /**
     *
     * @return LeaveTypeService
     */
    public function getLeaveTypeService() {
        if (!($this->leaveTypeService instanceof LeaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    /**
     *
     * @param LeaveTypeService $service 
     */
    public function setLeaveTypeService(LeaveTypeService $service) {
        $this->leaveTypeService = $service;
    }

    /**
     *
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {
        if (!($this->leavePeriodService instanceof LeavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
        }
        return $this->leavePeriodService;
    }

    /**
     *
     * @param LeavePeriodService $service 
     */
    public function setLeavePeriodService(LeavePeriodService $service) {
        $this->leavePeriodService = $service;
    }

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
     * @return WorkWeekService
     */
    public function getWorkWeekService() {
        if (!($this->workWeekService instanceof WorkWeekService)) {
            $this->workWeekService = new WorkWeekService();
        }
        return $this->workWeekService;
    }

    /**
     *
     * @param WorkWeekService $service 
     */
    public function setWorkWeekService(WorkWeekService $service) {
        $this->workWeekService = $service;
    }

    /**
     *
     * @return HolidayService
     */
    public function getHolidayService() {
        if (!($this->holidayService instanceof HolidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     *
     * @param HolidayService $service 
     */
    public function setHolidayService(HolidayService $service) {
        $this->holidayService = $service;
    }

    /**
     *
     * @param array $leaveAssignmentData
     * @return bool
     */
    public function assignLeave(LeaveParameterObject $leaveAssignmentData) {

        $employeeId = $leaveAssignmentData->getEmployeeNumber();

        /* Check whether employee exists */
        if (empty($employeeId)) {
            throw new LeaveAssignmentServiceException('Invalid Employee');
        }

        if ($this->applyMoreThanAllowedForADay($leaveAssignmentData)) {
            throw new LeaveAssignmentServiceException('Failed to Assign: Work Shift Length Exceeded');
        } else {
            if (!$this->hasOverlapLeave($leaveAssignmentData)) {
                $this->saveLeaveRequest($leaveAssignmentData);
                return true;
            }
        }
    }

    /**
     * Saving Leave Request
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
                    $leaveAssignmentMailer = new LeaveAssignmentMailer($leaveRequest, $leaveDays, $_SESSION['empNumber']);
                    $leaveAssignmentMailer->send();

                    return true;
                } catch (Exception $e) {
                    throw new LeaveAssignmentServiceException('Leave Period Does Not Exist');
                }
            }
        } else {
            throw new LeaveAssignmentServiceException('Leave Request Should Contain Work Days');
        }
    }

    /**
     * Checking for leave overlaps
     * @return bool
     */
    public function hasOverlapLeave(LeaveParameterObject $leaveAssignmentData) {

        if (strlen($leaveAssignmentData->getFromTime()) > 0) {
            $fromTime = date('H:i:s', strtotime($leaveAssignmentData->getFromTime()));
        }

        if (strlen($leaveAssignmentData->getToTime()) > 0) {
            $toTime = date('H:i:s', strtotime($leaveAssignmentData->getToTime()));
        }

        /* Find duplicate leaves */
        $overlapLeaves = $this->getLeaveRequestService()->getOverlappingLeave(
                $leaveAssignmentData->getFromDate(), $leaveAssignmentData->getToDate(), $leaveAssignmentData->getEmployeeNumber(), $leaveAssignmentData->getFromTime(), $leaveAssignmentData->getToTime()
        );

        return (count($overlapLeaves) !== 0);
    }

    /**
     * isEmployeeAllowedToApply
     * @param LeaveType $leaveType
     * @returns boolean
     */
    public function isEmployeeAllowedToApply(LeaveType $leaveType) {
        return true;
    }

    /**
     *
     * @param sfForm $form
     * @return boolean 
     */
    public function applyMoreThanAllowedForADay(LeaveParameterObject $leaveAssignmentData) {

        $fromTime = date('H:i:s', strtotime($leaveAssignmentData->getFromTime()));
        $toTime = date('H:i:s', strtotime($leaveAssignmentData->getToTime()));

        $totalDuration = 0;
        if ($leaveAssignmentData->getFromDate() == $leaveAssignmentData->getToDate()) {
            $totalDuration = $this->getLeaveRequestService()->getTotalLeaveDuration($leaveAssignmentData->getEmployeeNumber(), $leaveAssignmentData->getFromDate());
        }

        if (($totalDuration + $leaveAssignmentData->getLeaveTotalTime()) > $this->getWorkShiftDurationForEmployee($leaveAssignmentData->getEmployeeNumber())) {

            $dateRange = new DateRange();
            $dateRange->setFromDate($leaveAssignmentData->getFromDate());
            $dateRange->setToDate($leaveAssignmentData->getToDate());

            $searchParameters['dateRange'] = $dateRange;
            $searchParameters['employeeFilter'] = $leaveAssignmentData->getEmployeeNumber();

            $parameter = new ParameterObject($searchParameters);
            $leaveRequests = $this->getLeaveRequestService()->searchLeaveRequests($parameter);

            if (count($leaveRequests['list']) > 0) {
                foreach ($leaveRequests['list'] as $leaveRequest) {
                    $this->overlapLeaves[] = $leaveRequest->getLeave();
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * Checks overlapping leave request
     * @param LeaveParameterObject $leaveAssignmentData
     * @return bool
     */
    public function isOverlapLeaveRequest(LeaveParameterObject $leaveAssignmentData) {

        $leavePeriod = $this->getLeavePeriodService()->getLeavePeriod(strtotime($leaveAssignmentData->getFromDate()));

        if (!is_null($leavePeriod) && ($leavePeriod instanceof LeavePeriod)) {
            if ($leaveAssignmentData->getToDate() > $leavePeriod->getEndDate()) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param LeaveParameterObject $leaveAssignmentData
     * @return LeaveRequest 
     */
    protected function generateLeaveRequest(LeaveParameterObject $leaveAssignmentData) {

        $leavePeriodId = null;

        $leavePeriod = $this->getLeavePeriodService()->getLeavePeriod(strtotime($leaveAssignmentData->getFromDate()));
        if (!is_null($leavePeriod) && ($leavePeriod instanceof LeavePeriod)) {
            $leavePeriodId = $leavePeriod->getLeavePeriodId();
        }

        $leaveRequest = new LeaveRequest();

        $leaveRequest->setLeaveTypeId($leaveAssignmentData->getLeaveType());
        $leaveRequest->setDateApplied($leaveAssignmentData->getFromDate());
        $leaveRequest->setLeavePeriodId($leavePeriodId);
        $leaveRequest->setEmpNumber($leaveAssignmentData->getEmployeeNumber());
        $leaveRequest->setLeaveComments($leaveAssignmentData->getComment());

        return $leaveRequest;
    }

    /**
     *
     * @param int $employeeNumber
     * @return int 
     */
    protected function getWorkShiftDurationForEmployee($employeeNumber) {
        $workshift = $this->getEmployeeService()->getWorkShift($employeeNumber);

        if ($workshift == null) {
            $definedDuration = sfConfig::get('app_orangehrm_core_leave_plugin_default_work_shift_length_hours');
        } else {
            $definedDuration = $workshift->getWorkShift()->getHoursPerDay();
        }
        return $definedDuration;
    }

    /**
     * 
     * Get Leave array
     * @param LeaveParameterObject $leaveAssignmentData
     * @return array
     */
    public function createLeaveObjectListForAppliedRange(LeaveParameterObject $leaveAssignmentData) {

        $leaveList = array();
        $from = strtotime($leaveAssignmentData->getFromDate());
        $to = strtotime($leaveAssignmentData->getToDate());

        for ($timeStamp = $from; $timeStamp <= $to; $timeStamp = $this->incDate($timeStamp)) {
            $leave = new Leave();

            $leaveDate = date('Y-m-d', $timeStamp);
            $isWeekend = $this->getWorkWeekService()->isWeekend($leaveDate, true);
            $isHoliday = $this->getHolidayService()->isHoliday($leaveDate);
            $isHalfday = $this->isHalfDay($leaveDate);
            $isHalfDayHoliday = $this->getHolidayService()->isHalfdayHoliday($leaveDate);

            $leave->setLeaveDate($leaveDate);
            $leave->setLeaveComments($leaveAssignmentData->getComment());
            $leave->setLeaveLengthDays($this->calculateDateDeference($leaveAssignmentData, $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday));
            $leave->setStartTime(($leaveAssignmentData->getFromTime() != '') ? $leaveAssignmentData->getFromTime() : '00:00');
            $leave->setEndTime(($leaveAssignmentData->getToTime() != '') ? $leaveAssignmentData->getToTime() : '00:00');
            $leave->setLeaveLengthHours($this->calculateTimeDeference($leaveAssignmentData, $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday));
            $leave->setLeaveStatus($this->getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate));

            array_push($leaveList, $leave);
        }
        return $leaveList;
    }

    /**
     * Date increment
     * @param int $timestamp
     */
    protected final function incDate($timestamp) {
        return strtotime("+1 day", $timestamp);
    }

    /**
     *
     * @param $day
     * @return boolean
     */
    public function isHalfDay($day) {

        /* This is to check weekday half days */
        $flag = $this->getHolidayService()->isHalfDay($day);

        if (!$flag) {
            /* This checks for weekend half day */
            return $this->getWorkWeekService()->isWeekend($day, false);
        }

        return $flag;
    }

    /**
     * Calculate Date deference
     * 
     * @param LeaveParameterObject $leaveAssignmentData
     * @param bool $isWeekend
     * @param bool $isHoliday
     * @param bool $isHalfday
     * @param bool $isHalfDayHoliday
     * @return int 
     */
    public function calculateDateDeference(LeaveParameterObject $leaveAssignmentData, $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday) {

        if ($isWeekend) {
            $dayDeference = 0;
        } elseif ($isHoliday) {
            if ($isHalfDayHoliday) {
                if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                    if ($leaveAssignmentData->getWorkShiftLength() / 2 <= $leaveAssignmentData->getLeaveTotalTime()) {
                        $dayDeference = 0.5;
                    } else {
                        $dayDeference = number_format($leaveAssignmentData->getLeaveTotalTime() / $leaveAssignmentData->getWorkShiftLength(), 3);
                    }
                } else {
                    $dayDeference = 0.5;
                }
            } else {
                $dayDeference = 0;
            }
        } elseif ($isHalfday) {

            if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                if ($leaveAssignmentData->getWorkShiftLength() / 2 <= $leaveAssignmentData->getLeaveTotalTime()) {
                    $dayDeference = 0.5;
                } else {
                    $dayDeference = number_format($leaveAssignmentData->getLeaveTotalTime() / $leaveAssignmentData->getWorkShiftLength(), 3);
                }
            } else {
                $dayDeference = 0.5;
            }
        } else {
            if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                $dayDeference = number_format($leaveAssignmentData->getLeaveTotalTime() / $leaveAssignmentData->getWorkShiftLength(), 3);
            } else {
                //$dayDeference	=	floor((strtotime($posts['txtToDate'])-strtotime($posts['txtFromDate']))/86400)+1;
                $dayDeference = 1;
            }
        }

        return $dayDeference;
    }

    /**
     *
     * @param LeaveParameterObject $leaveAssignmentData
     * @param bool $isWeekend
     * @param bool $isHoliday
     * @param bool $isHalfday
     * @param bool $isHalfDayHoliday
     * @return int 
     */
    public function calculateTimeDeference(LeaveParameterObject $leaveAssignmentData, $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday) {

        if ($isWeekend) {
            $timeDeference = 0;
        } elseif ($isHoliday) {
            if ($isHalfDayHoliday) {
                if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                    if ($leaveAssignmentData->getWorkShiftLength() / 2 <= $leaveAssignmentData->getLeaveTotalTime()) {
                        $timeDeference = number_format($leaveAssignmentData->getWorkShiftLength() / 2, 3);
                    } else {
                        $timeDeference = $leaveAssignmentData->getLeaveTotalTime();
                    }
                } else {
                    $timeDeference = number_format($leaveAssignmentData->getWorkShiftLength() / 2, 3);
                }
            } else {
                $timeDeference = 0;
            }
        } elseif ($isHalfday) {
            if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate() && $leaveAssignmentData->getLeaveTotalTime() > 0) {
                if ($leaveAssignmentData->getWorkShiftLength() / 2 <= $leaveAssignmentData->getLeaveTotalTime()) {
                    $timeDeference = number_format($leaveAssignmentData->getWorkShiftLength() / 2, 3);
                } else {
                    $timeDeference = $leaveAssignmentData->getLeaveTotalTime();
                }
            } else {
                $timeDeference = number_format($leaveAssignmentData->getWorkShiftLength() / 2, 3);
            }
        } else {
            if ($leaveAssignmentData->getToDate() == $leaveAssignmentData->getFromDate()) {
                $timeDeference = $leaveAssignmentData->getLeaveTotalTime();
            } else {
                $timeDeference = $this->getWorkShiftLengthOfEmployee($leaveAssignmentData->getEmployeeNumber());
            }
        }

        return $timeDeference;
    }

    /**
     *
     * @param bool $isWeekend
     * @return int
     */
    public function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate) {
        $status = null;

        if ($isWeekend) {
            return Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        }

        if ($isHoliday) {
            return Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
        }

        if (strtotime($leaveDate) <= strtotime(date('Y-m-d')))
            $status = Leave::LEAVE_STATUS_LEAVE_TAKEN;
        else
            $status = Leave::LEAVE_STATUS_LEAVE_APPROVED;

        return $status;
    }

    /**
     * Get work shift length
     * @return int
     */
    protected function getWorkShiftLengthOfEmployee($employeeNumber) {

        $employeeWorkShift = $this->getEmployeeService()->getWorkShift($employeeNumber);

        if (!is_null($employeeWorkShift) && ($employeeWorkShift instanceof WorkShift)) {
            return $employeeWorkShift->getWorkShift()->getHoursPerDay();
        } else {
            return WorkShift::DEFAULT_WORK_SHIFT_LENGTH;
        }
    }

}
