<?php

abstract class AbstractLeaveAllocationService extends BaseService {

    protected $leaveRequestService;
    protected $leaveTypeService;
    protected $leavePeriodService;
    protected $employeeService;
    protected $workWeekService;
    protected $holidayService;
    protected $overlapLeave;
    private $workScheduleService;
    protected $workflowService;
    protected $userRoleManager;
        
    public function getWorkflowService() {
        if (empty($this->workflowService)) {
            $this->workflowService = new AccessFlowStateMachineService();
        }
        return $this->workflowService;
    }

    public function setWorkflowService(AccessFlowStateMachineService $workflowService) {
        $this->workflowService = $workflowService;
    }        
    /**
     * Get work schedule service
     * @return WorkScheduleService
     */
    public function getWorkScheduleService() {
        if (!($this->workScheduleService instanceof WorkScheduleService)) {
            $this->workScheduleService = new WorkScheduleService();
        }
        return $this->workScheduleService;
    }

    /**
     *
     * @param WorkScheduleService $service 
     */
    public function setWorkScheduleService(WorkScheduleService $service) {
        $this->workScheduleService = $service;
    }  
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
    protected abstract function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate, LeaveParameterObject $leaveAssignmentData);

    protected abstract function allowToExceedLeaveBalance();
    
    /**
     * Get Logger instance. Creates if not already created.
     *
     * @return Logger
     */
    protected abstract function getLogger();
    
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
     * Get User role manager instance
     * @return AbstractUserRoleManager
     */
    public function getUserRoleManager() {
        if (!($this->userRoleManager instanceof AbstractUserRoleManager)) {
            $this->userRoleManager = UserRoleManagerFactory::getUserRoleManager();
        }
        return $this->userRoleManager;
    }

    /**
     * Set user role manager instance
     * @param AbstractUserRoleManager $userRoleManager
     */
    public function setUserRoleManager(AbstractUserRoleManager $userRoleManager) {
        $this->userRoleManager = $userRoleManager;
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

        $startDayStartTime = null;
        $startDayEndTime = null;
        $endDayStartTime = null;
        $endDayEndTime = null;
        
        $startDuration = null;
        $endDuration = null;        
        
        if ($leaveAssignmentData->isMultiDayLeave()) {
            $partialDayOption = $leaveAssignmentData->getMultiDayPartialOption();
        
            if ($partialDayOption == 'all') {
                $startDuration = $leaveAssignmentData->getFirstMultiDayDuration();
            } else if ($partialDayOption == 'start') {
                $startDuration = $leaveAssignmentData->getFirstMultiDayDuration();
            } else if ($partialDayOption == 'end') {
                $endDuration = $leaveAssignmentData->getSecondMultiDayDuration();
            } else if ($partialDayOption == 'start_end') {
                $startDuration = $leaveAssignmentData->getFirstMultiDayDuration();
                $endDuration = $leaveAssignmentData->getSecondMultiDayDuration();
            }
            
            $allPartialDays = ($partialDayOption == 'all');
        } else {
            $allPartialDays = false;
            
            $startDuration = $leaveAssignmentData->getSingleDayDuration();           
        }
        
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($leaveAssignmentData->getEmployeeNumber());
        $workScheduleStartEndTime = $workSchedule->getWorkShiftStartEndTime();
        $workScheduleDuration = $workSchedule->getWorkShiftLength();
        $midDay = $this->addHoursDuration($workScheduleStartEndTime['start_time'], $workScheduleDuration / 2);        
        
        // set start times
        if (!is_null($startDuration)) {
            if ($startDuration->getType() == LeaveDuration::HALF_DAY) {
                if ($startDuration->getAmPm() == LeaveDuration::HALF_DAY_AM) {
                    $startDayStartTime = $workScheduleStartEndTime['start_time'];
                    $startDayEndTime = $midDay;                   
                } else {
                    $startDayStartTime = $midDay;                                         
                    $startDayEndTime = $workScheduleStartEndTime['end_time'];
                }
            } else if ($startDuration->getType() == LeaveDuration::SPECIFY_TIME) {
                $startDayStartTime = $startDuration->getFromTime();
                $startDayEndTime = $startDuration->getToTime();                 
            }
        }
        
        // set end times
        if (!is_null($endDuration)) {
            if ($endDuration->getType() == LeaveDuration::HALF_DAY) {               
                if ($endDuration->getAmPm() == LeaveDuration::HALF_DAY_AM) {
                    $endDayStartTime = $workScheduleStartEndTime['start_time'];
                    $endDayEndTime = $midDay;                   
                } else {
                    $endDayStartTime = $midDay;                                         
                    $endDayEndTime = $workScheduleStartEndTime['end_time'];
                }
                
            } else if ($endDuration->getType() == LeaveDuration::SPECIFY_TIME) {
                $endDayStartTime = $endDuration->getFromTime();
                $endDayEndTime = $endDuration->getToTime();                
            }
        }        

        $overlapLeave = $this->getLeaveRequestService()->getOverlappingLeave($leaveAssignmentData->getFromDate(), 
                $leaveAssignmentData->getToDate(), $leaveAssignmentData->getEmployeeNumber(), 
                $startDayStartTime, $startDayEndTime, $allPartialDays, 
                $endDayStartTime, $endDayEndTime);
                
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
     * Check if user has exceeded the allowed hours per day in existing and current leave request.
     * 
     * @param LeaveParameterObject $leaveAssignmentData Leave Parameters
     * @return boolean True if user has exceeded limit, false if not
     */
    public function applyMoreThanAllowedForADay(LeaveParameterObject $leaveAssignmentData) {

        $logger = $this->getLogger();
        
        $workshiftExceeded = false;

        $overlapLeave = array();  
                    
        $empNumber = $leaveAssignmentData->getEmployeeNumber();
        $fromDate = $leaveAssignmentData->getFromDate();
        $toDate = $leaveAssignmentData->getToDate();
        
        $workShiftLength = $this->getWorkShiftDurationForEmployee($empNumber);
            
        $from = strtotime($fromDate);
        $to = strtotime($toDate);
        
        $firstDay = true;

        for ($timeStamp = $from; $timeStamp <= $to; $timeStamp = $this->incDate($timeStamp)) {
            $date = date('Y-m-d', $timeStamp);
            
            $existingDuration = $this->getLeaveRequestService()->getTotalLeaveDuration($empNumber, $date);
            
            $lastDay = ($timeStamp == $to);
            $duration = $this->getApplicableLeaveDuration($leaveAssignmentData, $firstDay, $lastDay);            
            $firstDay = false;  
            
            $workingDayLength = $workShiftLength;
            
            if ($this->isHoliday($date, $leaveAssignmentData) || $this->isWeekend($date, $leaveAssignmentData)) {
                if ($logger->isDebugEnabled()) {
                    $logger->debug("Skipping $date since it is a weekend/holiday");
                }
                continue;
            }
            
            // Reduce workshiftLength for half days
            $halfDay = $this->isHalfDay($date, $leaveAssignmentData);
            if ($halfDay) {
                $workingDayLength = $workShiftLength / 2;
            }
            
            if ($duration->getType() == LeaveDuration::FULL_DAY) {
                $leaveHours = $workingDayLength;
            } else if ($duration->getType() == LeaveDuration::HALF_DAY) {
                $leaveHours = $workShiftLength / 2;
            } else if ($duration->getType() == LeaveDuration::SPECIFY_TIME) {
                $leaveHours = $this->getDurationInHours($duration->getFromTime(), $duration->getToTime());
            } else {
                $logger->error("Unexpected duration type in applyMoreThanAllowedForADay(): " . print_r($duration->getType(), true));
                $leaveHours = 0;
            }                     

            if ($logger->isDebugEnabled()) {
                $logger->debug("date=$date, existing leave duration=$existingDuration, " . 
                        "workShiftLength=$workShiftLength, totalLeaveTime=$leaveHours,workDayLength=$workingDayLength");                            
            }
            
            // We only show workshift exceeded warning for partial leave days (length < workshift)            
            if (($existingDuration + $leaveHours) > $workingDayLength) {

                if ($logger->isDebugEnabled()) {
                    $logger->debug('Workshift length exceeded!');
                }
                
                $parameter = new ParameterObject(array('dateRange' => new DateRange($date, $date), 'employeeFilter' => $empNumber));
                $leaveRequests = $this->getLeaveRequestService()->searchLeaveRequests($parameter);

                if (count($leaveRequests['list']) > 0) {
                    
                    foreach ($leaveRequests['list'] as $leaveRequest) {
                        $leaveList = $leaveRequest->getLeave();
                        foreach ($leaveList as $leave) {
                            if ($leave->getDate() == $date) {
                                $overlapLeave[] = $leave;
                            }                        
                        }
                    }
                }
                
                $workshiftExceeded = true;
            }                        
        }
        
        if (!empty($overlapLeave)) {
            $this->setOverlapLeave($overlapLeave);        
        }
        
        return $workshiftExceeded;
    }

    /**
     * 
     * Checks overlapping leave request
     * @param LeaveParameterObject $leaveAssignmentData
     * @return bool
     */
    public function isOverlapLeaveRequest(LeaveParameterObject $leaveAssignmentData) {

//        $leavePeriod = $this->getLeavePeriodService()->getLeavePeriod(strtotime($leaveAssignmentData->getFromDate()));
//
//        if (!is_null($leavePeriod) && ($leavePeriod instanceof LeavePeriod)) {
//            if ($leaveAssignmentData->getToDate() > $leavePeriod->getEndDate()) {
//                return true;
//            }
//        }

        return false;
    }

    /**
     *
     * @param LeaveParameterObject $leaveAssignmentData
     * @return LeaveRequest 
     */
    protected function generateLeaveRequest(LeaveParameterObject $leaveAssignmentData) {

//        $leavePeriodId = null;
//
//        $leavePeriod = $this->getLeavePeriodService()->getLeavePeriod(strtotime($leaveAssignmentData->getFromDate()));
//        if (!is_null($leavePeriod) && ($leavePeriod instanceof LeavePeriod)) {
//            $leavePeriodId = $leavePeriod->getLeavePeriodId();
//        }

        $leaveRequest = new LeaveRequest();

        $leaveRequest->setLeaveTypeId($leaveAssignmentData->getLeaveType());
        $leaveRequest->setDateApplied($leaveAssignmentData->getFromDate());
//        $leaveRequest->setLeavePeriodId($leavePeriodId);
        $leaveRequest->setEmpNumber($leaveAssignmentData->getEmployeeNumber());
        $leaveRequest->setComments($leaveAssignmentData->getComment());

        return $leaveRequest;
    }

    /**
     *
     * @param int $employeeNumber
     * @return int 
     */
    protected function getWorkShiftDurationForEmployee($empNumber) {
        
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);
        return $workSchedule->getWorkShiftLength();
    }

    protected function getApplicableLeaveDuration($leaveAssignmentData, $firstDay, $lastDay) {
        
        // Default to full day
        $duration = new LeaveDuration();
        $duration->setType(LeaveDuration::FULL_DAY);
        
        if ($leaveAssignmentData->isMultiDayLeave()) {
            $partialDayOption = $leaveAssignmentData->getMultiDayPartialOption();

            if (($partialDayOption == 'all') || 
                    ($firstDay && ($partialDayOption == 'start' || $partialDayOption == 'start_end'))) {
                $duration = $leaveAssignmentData->getFirstMultiDayDuration();
            } else if ($lastDay && ($partialDayOption == 'end' || $partialDayOption == 'start_end')) {
                $duration = $leaveAssignmentData->getSecondMultiDayDuration();
            }

        } else {
            // Single day leave:
            $duration = $leaveAssignmentData->getSingleDayDuration();
        }
        
        return $duration;
    }
    
    protected function updateLeaveDurationParameters(&$leave, $empNumber, LeaveDuration $duration, 
            $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday) {
        
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);
        $workScheduleStartEndTime = $workSchedule->getWorkShiftStartEndTime();
        $workScheduleDuration = $workSchedule->getWorkShiftLength();
        
        $midDay = $this->addHoursDuration($workScheduleStartEndTime['start_time'], $workScheduleDuration / 2);

        // set status

        switch ($duration->getType()) {
            case LeaveDuration::FULL_DAY:
                $leave->setDurationType(Leave::DURATION_TYPE_FULL_DAY);   
                
                // For backwards compatibility, set to 00:00
                $leave->setStartTime('00:00');
                $leave->setEndTime('00:00');                                 
                break;
            case LeaveDuration::HALF_DAY:
                
                if ($duration->getAmPm() == LeaveDuration::HALF_DAY_AM) {
                    $leave->setDurationType(Leave::DURATION_TYPE_HALF_DAY_AM);
                    $leave->setStartTime($workScheduleStartEndTime['start_time']);
                    $leave->setEndTime($midDay);                     
                } else {
                    $leave->setDurationType(Leave::DURATION_TYPE_HALF_DAY_PM);
                    $leave->setStartTime($midDay);
                    $leave->setEndTime($workScheduleStartEndTime['end_time']);                         
                }
                break;
            case LeaveDuration::SPECIFY_TIME:
                $leave->setDurationType(Leave::DURATION_TYPE_SPECIFY_TIME);
                $leave->setStartTime($duration->getFromTime());
                $leave->setEndTime($duration->getToTime());                    
                break;
        }
        
        if ($isWeekend || $isHoliday) {
            // Full Day Off
            $durationInHours = 0;
        } else if ($isHalfday || $isHalfDayHoliday) {
            
            if ($duration->getType() == LeaveDuration::FULL_DAY) {
                $durationInHours = $workScheduleDuration;
            } else if ($duration->getType() == LeaveDuration::HALF_DAY) {
                $durationInHours = $workScheduleDuration / 2;
            } else {
                $durationInHours = $this->getDurationInHours($duration->getFromTime(), $duration->getToTime());
            }   
            
            $halfDayHours = ($workScheduleDuration / 2);
            if ($durationInHours > $halfDayHours) {
                $durationInHours = $halfDayHours;
            }
            // Half Day Off
        } else {
            // Full Working Day
            if ($duration->getType() == LeaveDuration::FULL_DAY) {
                $durationInHours = $workScheduleDuration;
            } else if ($duration->getType() == LeaveDuration::HALF_DAY) {
                $durationInHours = $workScheduleDuration / 2;
            } else {
                $durationInHours = $this->getDurationInHours($duration->getFromTime(), $duration->getToTime());
            }            
        }
        
        $leave->setLengthHours(number_format($durationInHours, 2));
        $leave->setLengthDays(number_format($durationInHours / $workScheduleDuration, 3));
    }
    
    protected function addHoursDuration($time, $hoursToAdd) {
        list($hours, $minutes) = explode(':', $time);
        $timeInMinutes = (intVal($hours) * 60) + intval($minutes);
        $minutesToAdd = 60 * floatval($hoursToAdd);
        
        $newMinutes = $timeInMinutes + $minutesToAdd;
        $hoursPart = intval(floor($newMinutes / 60));
        $minutesPart = round($newMinutes) % 60;

        return sprintf("%02d:%02d", $hoursPart, $minutesPart);
    }    
    
    protected function getDurationInHours($fromTime, $toTime) {
        list($startHour, $startMin) = explode(':', $fromTime);
        list($endHour, $endMin) = explode(':', $toTime);

        $durationMinutes = (intVal($endHour) - intVal($startHour)) * 60 + (intVal($endMin) - intVal($startMin));
        
        $hours = $durationMinutes / 60;

        return $hours;
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
        
        $firstDay = true;

        for ($timeStamp = $from; $timeStamp <= $to; $timeStamp = $this->incDate($timeStamp)) {
            $leave = new Leave();

            $leaveDate = date('Y-m-d', $timeStamp);
            
            $isWeekend = $this->isWeekend($leaveDate, $leaveAssignmentData);
            $isHoliday = $this->isHoliday($leaveDate, $leaveAssignmentData);
            $isHalfday = $this->isHalfDay($leaveDate, $leaveAssignmentData);
            $isHalfDayHoliday = $this->isHalfdayHoliday($leaveDate, $leaveAssignmentData);
            
            $leave->setDate($leaveDate);
            
            $lastDay = ($timeStamp == $to);
            $leaveDuration = $this->getApplicableLeaveDuration($leaveAssignmentData, $firstDay, $lastDay);
            
            $firstDay = false;
            
            $this->updateLeaveDurationParameters($leave, $leaveAssignmentData->getEmployeeNumber(), $leaveDuration, 
                    $isWeekend, $isHoliday, $isHalfday, $isHalfDayHoliday);
            $leave->setStatus($this->getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate, $leaveAssignmentData));

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
        
        $empNumber = $leaveAssignmentData->getEmployeeNumber();
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);

        /* This is to check weekday half days */
        $isHalfDay = $workSchedule->isHalfDay($day);

        if (!$isHalfDay) {
            /* This checks for weekend half day */
            $isHalfDay = $workSchedule->isWeekend($day, false);
        }

        return $isHalfDay;
    }
    
    protected function isWeekend($day, LeaveParameterObject $leaveAssignmentData) { 
        $empNumber = $leaveAssignmentData->getEmployeeNumber();
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);        
        $isWeekend = $workSchedule->isWeekend($day, true);
        return $isWeekend;
    }
    
    protected function isHoliday($day, LeaveParameterObject $leaveAssignmentData) {
        $empNumber = $leaveAssignmentData->getEmployeeNumber();
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);        
        $isHoliday = $workSchedule->isHoliday($day);
        return $isHoliday;
    }
    
    protected function isHalfdayHoliday($day, LeaveParameterObject $leaveAssignmentData) {
        $empNumber = $leaveAssignmentData->getEmployeeNumber();
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);        
        $isHalfDayHoliday = $workSchedule->isHalfdayHoliday($day);
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
