<?php

class LeaveAssignmentService extends AbstractLeaveAllocationService {

    protected $leaveEntitlementService;
    protected $dispatcher;
    protected $assignWorkflowItem;

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
     * Set dispatcher.
     * 
     * @param $dispatcher
     */
    public function setDispatcher($dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    public function getDispatcher() {
        if(is_null($this->dispatcher)) {
            $this->dispatcher = sfContext::getInstance()->getEventDispatcher();
        }
        return $this->dispatcher;
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
            throw new LeaveAllocationServiceException('Invalid Employee');
        }

        if ($this->hasOverlapLeave($leaveAssignmentData)) {
            return false;
        }
        
        if ($this->applyMoreThanAllowedForADay($leaveAssignmentData)) {
            throw new LeaveAllocationServiceException('Failed to Assign: Work Shift Length Exceeded');
        }
                
        return $this->saveLeaveRequest($leaveAssignmentData);
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
//        $leaveRequest->setLeaveTypeName($leaveType->getLeaveTypeName());

//        if (is_null($leaveRequest->getLeavePeriodId())) {
//            if ($this->getLeavePeriodService()->isWithinNextLeavePeriod(strtotime($leaveRequest->getDateApplied()))) {
//                $nextLeavePeriod = $this->getLeavePeriodService()->createNextLeavePeriod($leaveRequest->getDateApplied());
//                $leaveRequest->setLeavePeriodId($nextLeavePeriod->getLeavePeriodId());
//            }
//        }

        $leaveDays = $this->createLeaveObjectListForAppliedRange($leaveAssignmentData);        
        
        $empNumber = $leaveAssignmentData->getEmployeeNumber();
        
        $nonHolidayLeaveDays = array();
        
        $holidayCount = 0;
        $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);
        foreach ($leaveDays as $k => $leave) {
            if (in_array($leave->getStatus(), $holidays)) {
                $holidayCount++;
            } else {
                $nonHolidayLeaveDays[] = $leave;
            }
        }        
                
        if (count($nonHolidayLeaveDays) > 0) {
            $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();            
            $entitlements = $strategy->handleLeaveCreate($empNumber, $leaveType->getId(), $nonHolidayLeaveDays, true);

            if ($entitlements == false) {
                throw new LeaveAllocationServiceException('Leave Balance Exceeded');
            }
        }

        /* This is to see whether employee applies leave only during weekends or standard holidays */
        if ($holidayCount != count($leaveDays)) {
            if ($this->isEmployeeAllowedToApply($leaveType)) { // TODO: Should this be checked on Assign??
                try {
                    
                    $user = sfContext::getInstance()->getUser();
                    $loggedInUserId = $user->getAttribute('auth.userId');
                    $loggedInEmpNumber = $user->getAttribute('auth.empNumber');
        
                    $leaveRequest = $this->getLeaveRequestService()->saveLeaveRequest($leaveRequest, $leaveDays, $entitlements);
                    $leaveComment = trim($leaveRequest->getComments());
                                   
                    if (!empty($leaveComment)) {
                        if (!empty($loggedInEmpNumber)) {
                            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
                            $createdBy = $employee->getFullName();
                        } else {
                            $createdBy = $user->getAttribute('auth.firstName');
                        }
                        $this->getLeaveRequestService()->saveLeaveRequestComment($leaveRequest->getId(), 
                                $leaveComment, $createdBy, $loggedInUserId, $loggedInEmpNumber);
                    }
//                    if ($this->isOverlapLeaveRequest($leaveAssignmentData)) {
//                        $this->getLeaveRequestService()->modifyOverlapLeaveRequest($leaveRequest, $leaveDays);
//                    }

                    /* Send notification to the when leave is assigned; TODO: Move to action? */
                    
                    /** TODO: Don't hard code 'ADMIN' here: Use method to get allowed actions ordered by priority and choose the assign action */
                    
                    $workFlow = $this->getWorkflowService()
                             ->getWorkflowItemByStateActionAndRole(WorkflowStateMachine::FLOW_LEAVE, 'INITIAL', 'ASSIGN', 'ADMIN');
                                        
                    $eventData = array('request' => $leaveRequest, 'days' => $leaveDays, 'empNumber' => $_SESSION['empNumber'],
                        'workFlow' => $workFlow);
                    $this->getDispatcher()->notify(new sfEvent($this, LeaveEvents::LEAVE_CHANGE, $eventData));

//                    return true;
                    return $leaveRequest;
                } catch (Exception $e) {
                    throw new LeaveAllocationServiceException('Error saving leave request');
                }
            }
        } else {
            throw new LeaveAllocationServiceException('Failed to Submit: No Working Days Selected');
        }
    }

    /**
     *
     * @param type $isWeekend
     * @param type $isHoliday
     * @param type $leaveDate
     * @return type 
     */
    public function getLeaveRequestStatus($isWeekend, $isHoliday, $leaveDate, LeaveParameterObject $leaveAssignmentData) {
        
        // TODO: Change here for leave workflow
        
        $status = null;

        if ($isWeekend) {
            return Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        }

        if ($isHoliday) {
            return Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
        }

        if (is_null($status)) {
            
            $workFlowItem = $this->getWorkflowItemForAssignAction($leaveAssignmentData);
            if (!is_null($workFlowItem)) {
                $status = Leave::getLeaveStatusForText($workFlowItem->getResultingState());
            } else {                
                throw new LeaveAllocationServiceException('Not Allowed to Assign Leave to Selected Employee!');
            }                        
            
            if (($status == Leave::LEAVE_STATUS_LEAVE_APPROVED) && (strtotime($leaveDate) < strtotime(date('Y-m-d')))) {
                $status = Leave::LEAVE_STATUS_LEAVE_TAKEN;
            }
        }
        
        return $status;
    }
    
    protected function allowToExceedLeaveBalance() {
        return true;
    }
    
    protected function getWorkflowItemForAssignAction(LeaveParameterObject $leaveAssignmentData) {
        
        if (is_null($this->assignWorkflowItem)) {

            $empNumber = $leaveAssignmentData->getEmployeeNumber();            
            $workFlowItems = $this->getUserRoleManager()->getAllowedActions(WorkflowStateMachine::FLOW_LEAVE, 
                    'INITIAL', array(), array(), array('Employee' => $empNumber));

            // get apply action
            foreach ($workFlowItems as $item) {
                if ($item->getAction() == 'ASSIGN') {
                    $this->assignWorkflowItem = $item;
                    break;
                }
            }        
        }
        
        if (is_null($this->assignWorkflowItem)) {
            $this->getLogger()->error("No workflow item found for ASSIGN leave action!");
        }
        
        return $this->assignWorkflowItem;
    }    
    
    /**
     * Get Logger instance. Creates if not already created.
     *
     * @return Logger
     */
    protected function getLogger() {
        if (is_null($this->logger)) {
            $this->logger = Logger::getLogger('leave.LeaveAssignmentService');
        }

        return($this->logger);
    }     

}