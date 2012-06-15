<?php

abstract class AbstractLeaveAllocationService extends BaseService {

    protected $leaveRequestService;
    protected $leaveTypeService;
    protected $leavePeriodService;
    protected $employeeService;
    protected $workWeekService;
    protected $holidayService;
    protected $overlapLeave;

    /**
     * 
     * Saves Leave Request and Sends Notification
     * @param LeaveParameterObject $leaveAssignmentData 
     */
    protected abstract function saveLeaveRequest(LeaveParameterObject $leaveAssignmentData);
    
    /**
     *
     * @param bool $isWeekend
     * @return int
     */
    protected abstract function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate);

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
     * @return mixed 
     */
    public function getOverlapLeave() {
        return $this->overlapLeave;
    }
    
    /**
     *
     * @param mixed $overlapLeaveRecords 
     */
    public function setOverlapLeave($overlapLeaveRecords) {
        $this->overlapLeave = $overlapLeaveRecords;
    }

    /**
     * Checking for leave overlaps
     * @return bool
     */
    public function hasOverlapLeave(LeaveParameterObject $leaveAssignmentData) {

        $fromTime = null;
        if (strlen($leaveAssignmentData->getFromTime()) > 0) {
            $fromTime = date('H:i:s', strtotime($leaveAssignmentData->getFromTime()));
        }

        
        $toTime = null;
        if (strlen($leaveAssignmentData->getToTime()) > 0) {
            $toTime = date('H:i:s', strtotime($leaveAssignmentData->getToTime()));
        }

        /* Find duplicate leaves */
        $overlapLeave = $this->getLeaveRequestService()->getOverlappingLeave(
                $leaveAssignmentData->getFromDate(), $leaveAssignmentData->getToDate(), $leaveAssignmentData->getEmployeeNumber(), $fromTime, $toTime
        );

        $this->setOverlapLeave($overlapLeave);

        return (count($overlapLeave) !== 0);
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
        $workshift = $this->getEmployeeService()->getEmployeeWorkShift($employeeNumber);

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
            
            $isWeekend = $this->isWeekend($leaveDate, $leaveAssignmentData);
            $isHoliday = $this->isHoliday($leaveDate, $leaveAssignmentData);
            $isHalfday = $this->isHalfDay($leaveDate, $leaveAssignmentData);
            $isHalfDayHoliday = $this->isHalfdayHoliday($leaveDate, $leaveAssignmentData);
            
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
    public function isHalfDay($day, LeaveParameterObject $leaveAssignmentData) {

        /* This is to check weekday half days */
        $flag = $this->getHolidayService()->isHalfDay($day);

        if (!$flag) {
            /* This checks for weekend half day */
            return $this->getWorkWeekService()->isWeekend($day, false);
        }

        return $flag;
    }
    
    protected function isWeekend($day, LeaveParameterObject $leaveAssignmentData) {        
        $isWeekend = $this->getWorkWeekService()->isWeekend($day, true);
        return $isWeekend;
    }
    
    protected function isHoliday($day, LeaveParameterObject $leaveAssignmentData) {
        $isHoliday = $this->getHolidayService()->isHoliday($day);
        return $isHoliday;
    }
    
    protected function isHalfdayHoliday($day, LeaveParameterObject $leaveAssignmentData) {
        $isHalfDayHoliday = $this->getHolidayService()->isHalfdayHoliday($leaveDate);
        return $isHalfDayHoliday;
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
     * Get work shift length
     * @return int
     */
    protected function getWorkShiftLengthOfEmployee($employeeNumber) {

        $employeeWorkShift = $this->getEmployeeService()->getEmployeeWorkShift($employeeNumber);

        if (!is_null($employeeWorkShift) && ($employeeWorkShift instanceof EmployeeWorkShift)) {
            return $employeeWorkShift->getWorkShift()->getHoursPerDay();
        } else {
            return WorkShift::DEFAULT_WORK_SHIFT_LENGTH;
        }
    }

}
