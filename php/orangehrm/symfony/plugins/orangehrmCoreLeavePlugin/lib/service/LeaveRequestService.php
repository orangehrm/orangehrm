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
class LeaveRequestService extends BaseService {

    private $leaveRequestDao ;
    private $leaveTypeService;
    private $leaveEntitlementService;
    private $leavePeriodService;
    private $holidayService;

    private $leaveNotificationService;
    private $leaveStateManager;

    const LEAVE_CHANGE_TYPE_LEAVE = 'change_leave';
    const LEAVE_CHANGE_TYPE_LEAVE_REQUEST = 'change_leave_request';

    /**
     *
     * @return LeaveRequestDao
     */
    public function getLeaveRequestDao() {
        return $this->leaveRequestDao;
    }

    /**
     *
     * @param LeaveRequestDao $leaveRequestDao
     * @return void
     */
    public function setLeaveRequestDao( LeaveRequestDao $leaveRequestDao) {
        $this->leaveRequestDao = $leaveRequestDao;
    }

    /**
     *
     * @return <type>
     */
    public function setLeaveNotificationService(LeaveNotificationService $leaveNotificationService) {
        $this->leaveNotificationService = $leaveNotificationService;
    }

    /**
     *
     * @return LeaveNotificationService
     */
    public function getLeaveNotificationService() {
        if(is_null($this->leaveNotificationService)) {
            $this->leaveNotificationService = new LeaveNotificationService();
        }
        return $this->leaveNotificationService;
    }

    /**
     *
     * @param LeaveRequest $leaveRequest
     * @param Leave $leave
     * @return boolean
     */
    public function saveLeaveRequest( LeaveRequest $leaveRequest , $leaveList) {

        $this->getLeaveRequestDao()->saveLeaveRequest( $leaveRequest, $leaveList);

        return true ;

    }

    /**
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService() {
        if(is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
            $this->leaveEntitlementService->setLeaveEntitlementDao(new LeaveEntitlementDao());
        }
        return $this->leaveEntitlementService;
    }

    /**
     * @return LeaveTypeService
     */
    public function getLeaveTypeService() {
        if(is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
            $this->leaveTypeService->setLeaveTypeDao(new LeaveTypeDao());
        }
        return $this->leaveTypeService;
    }

    /**
     * Sets LeaveEntitlementService
     * @param LeaveEntitlementService $leaveEntitlementService
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $leaveEntitlementService) {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }

    /**
     * Sets LeaveTypeService
     * @param LeaveTypeService $leaveTypeService
     */
    public function setLeaveTypeService(LeaveTypeService $leaveTypeService) {
        $this->leaveTypeService = $leaveTypeService;
    }

    /**
     * Returns LeavePeriodService
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {
        if(is_null($this->leavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
            $this->leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
        }
        return $this->leavePeriodService;
    }

    /**
     * Sets LeavePeriodService
     * @param LeavePeriodService $leavePeriodService
     */
    public function setLeavePeriodService(LeavePeriodService $leavePeriodService) {
        $this->leavePeriodService = $leavePeriodService;
    }

    /**
     * Returns HolidayService
     * @return HolidayService
     */
    public function getHolidayService() {
        if(is_null($this->holidayService)) {
            $this->holidayService = new HolidayService();
        }
        return $this->holidayService;
    }

    /**
     * Sets HolidayService
     * @param HolidayService $holidayService
     */
    public function setHolidayService(HolidayService $holidayService) {
        $this->holidayService = $holidayService;
    }

    /**
     * Set leave state manager. Only use for unit testing.
     * 
     * @param LeaveStateManager $leaveStateManager
     */
    public function setLeaveStateManager(LeaveStateManager $leaveStateManager) {
        $this->leaveStateManager = $leaveStateManager;
    }

    public function getLeaveStateManager() {
        if(is_null($this->leaveStateManager)) {
            $this->leaveStateManager = LeaveStateManager::instance();
        }
        return $this->leaveStateManager;
    }

    /**
     *
     * @param Employee $employee
     * @return LeaveType Collection
     */
    public function getEmployeeAllowedToApplyLeaveTypes(Employee $employee) {

        try {
            $leavePeriodService = $this->getLeavePeriodService();
            $leavePeriod = $leavePeriodService->getCurrentLeavePeriod();

            $leaveEntitlementService    = $this->getLeaveEntitlementService();
            $leaveTypeService           = $this->getLeaveTypeService();

            $leaveTypes     = $leaveTypeService->getLeaveTypeList();
            $leaveTypeList  = array();

            foreach($leaveTypes as $leaveType) {
                $entitlementDays = $leaveEntitlementService->getLeaveBalance($employee->getEmpNumber(), $leaveType->getLeaveTypeId(),$leavePeriod->getLeavePeriodId());

                if($entitlementDays > 0) {
                    array_push($leaveTypeList, $leaveType);
                }
            }
            return $leaveTypeList;
        } catch(Exception $e) {
            throw new LeaveServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param date $leaveStartDate
     * @param date $leaveEndDate
     * @param int $empId
     * @return Leave List
     */
    public function getOverlappingLeave($leaveStartDate, $leaveEndDate ,$empId) {

        return $this->leaveRequestDao->getOverlappingLeave($leaveStartDate, $leaveEndDate ,$empId);

    }

    /**
     *
     * @param LeaveType $leaveType
     * @return boolean
     */
    public function isApplyToMoreThanCurrent(LeaveType $leaveType){
		try{
			$leaveRuleEligibilityProcessor	=	new LeaveRuleEligibilityProcessor();
			return $leaveRuleEligibilityProcessor->allowApplyToMoreThanCurrent($leaveType);

		}catch( Exception $e){
			throw new LeaveServiceException($e->getMessage());
		}
	}

    /**
     *
     * @param $empId
     * @param $leaveTypeId
     * @return int
     */
    public function getNumOfLeave($empId, $leaveTypeId) {

        return $this->leaveRequestDao->getNumOfLeave($empId, $leaveTypeId);

    }

    /**
     *
     * @param $empId
     * @param $leaveTypeId
     * @return int
     */
    public function getNumOfAvaliableLeave($empId, $leaveTypeId) {

        return $this->leaveRequestDao->getNumOfAvaliableLeave($empId, $leaveTypeId);

    }

    /**
     *
     * @param $empId
     * @param $leaveTypeId
     * @return bool
     */
    public function isEmployeeHavingLeaveBalance( $empId, $leaveTypeId ,$leaveRequest, $applyDays, $leaveList = null) {
        try {
            $leaveEntitlementService = $this->getLeaveEntitlementService();
            $entitledDays	=	$leaveEntitlementService->getEmployeeLeaveEntitlementDays($empId, $leaveTypeId,$leaveRequest->getLeavePeriodId());
            $leaveDays		=	$this->leaveRequestDao->getNumOfAvaliableLeave($empId, $leaveTypeId);

            $leaveEntitlement = $leaveEntitlementService->readEmployeeLeaveEntitlement($empId, $leaveTypeId, $leaveRequest->getLeavePeriodId());
            $leaveBoughtForward = 0;
            if($leaveEntitlement instanceof EmployeeLeaveEntitlement) {
                $leaveBoughtForward = $leaveEntitlement->getLeaveBroughtForward();
            }

            $leaveBalance = $leaveEntitlementService->getLeaveBalance(
                    $empId, $leaveTypeId,
                    $leaveRequest->getLeavePeriodId());

            $entitledDays += $leaveBoughtForward;

            if($entitledDays == 0)
                throw new Exception('Leave Entitlements Not Allocated',102);

            //this is for border period leave apply - days splitting
            $leavePeriodService = $this->getLeavePeriodService();

            //this would either create or returns the next leave period
            $currentLeavePeriod     = $leavePeriodService->getLeavePeriod(strtotime($leaveRequest->getDateApplied()));
            $leaveAppliedEndDateTimeStamp = strtotime("+" . $applyDays . " day", strtotime($leaveRequest->getDateApplied()));
            $nextLeavePeriod        = $leavePeriodService->createNextLeavePeriod(date("Y-m-d", $leaveAppliedEndDateTimeStamp));
            $currentPeriodStartDate = explode("-", $currentLeavePeriod->getStartDate());
            $nextYearLeaveBalance   = 0;

            if($nextLeavePeriod instanceof LeavePeriod) {
                $nextYearLeaveBalance = $leaveEntitlementService->getLeaveBalance(
                        $empId, $leaveTypeId,
                        $nextLeavePeriod->getLeavePeriodId());
                //this is to notify users are applying to the same leave period
                $nextPeriodStartDate    = explode("-", $nextLeavePeriod->getStartDate());
                if($nextPeriodStartDate[0] == $currentPeriodStartDate[0]) {
                    $nextLeavePeriod        = null;
                    $nextYearLeaveBalance   = 0;
                }
            }

            //this is only applicable if user applies leave during current leave period
            if(strtotime($currentLeavePeriod->getStartDate()) < strtotime($leaveRequest->getDateApplied()) &&
                    strtotime($currentLeavePeriod->getEndDate()) > $leaveAppliedEndDateTimeStamp) {
                if($leaveBalance < $applyDays) {
                    throw new Exception('Leave Balance Exceeded',102);
                }
            }

            /* This is for leave request that span on two leave periods
             *  Ex: 2011-12-30 to 2012-01-02
             */
            if (!$this->_isAllowedToApplyForNextLeavePeriod($empId, $leaveTypeId, $leaveList)) {
                throw new Exception("Leave Balance Exceeded", 102);
            }

            return true ;

        }catch( Exception $e) {
            throw new LeaveServiceException($e->getMessage());
        }
    }

    private function _isAllowedToApplyForNextLeavePeriod($employeeId, $leaveTypeId, $leaveList) {

        $currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriod();
        $currentLeavePeriodEndDate = $currentLeavePeriod->getEndDate();
        $currentLeavePeriodEndDateTimeStamp = strtotime($currentLeavePeriodEndDate);
        $lastLeave = end($leaveList);
        $lastLeaveTimeStamp = strtotime($lastLeave->getLeaveDate());

        /* Proceed only if there is leave on next leave period */
        if ($lastLeaveTimeStamp > $currentLeavePeriodEndDateTimeStamp) {

            $nextLeavePeriod = $this->getLeavePeriodService()->getNextLeavePeriodByCurrentEndDate($currentLeavePeriodEndDate);

            if (is_null($nextLeavePeriod)) {

                return false;
                
            } else {

                /* Generating leave length on next leave period */

                $leaveLengthOnNextLeavePeriod = 0;

                foreach ($leaveList as $leave) {

                    if (strtotime($leave->getLeaveDate()) > $currentLeavePeriodEndDateTimeStamp) {

                        $leaveLengthOnNextLeavePeriod += $leave->getLeaveLengthDays();

                    }

                }

                $leaveEntitlementService = $this->getLeaveEntitlementService();
                $nextLeavePeriodBalance = $leaveEntitlementService->getLeaveBalance($employeeId, $leaveTypeId, $nextLeavePeriod->getLeavePeriodId());

                if ($leaveLengthOnNextLeavePeriod < $nextLeavePeriodBalance) {
                    return true;
                }

                return false;

            }

        } else {

            return true;

        }


    }

    /**
     *
     * @param ParameterObject $searchParameters
     * @param array $statuses
     * @return array
     */
    public function searchLeaveRequests($searchParameters, $page = 1) {

        return $this->leaveRequestDao->searchLeaveRequests($searchParameters, $page);

    }

    /**
     * Get Leave Request Status
     * @param $day
     * @return unknown_type
     */
    public function getLeaveRequestStatus( $day ) {
        try {
            $holidayService = $this->getHolidayService();
            $holiday = $holidayService->readHolidayByDate($day);
            if ($holiday != null) {
                return Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
            }

            return Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;

        } catch (Exception $e) {
            throw new LeaveServiceException($e->getMessage());
        }
    }

    /**
     *
     * @param int $leaveRequestId
     * @return array
     */
    public function searchLeave($leaveRequestId) {

        return $this->leaveRequestDao->fetchLeave($leaveRequestId);

    }

    /**
     *
     * @param int $leaveId
     * @return array
     */
    public function readLeave($leaveId) {

        return $this->leaveRequestDao->readLeave($leaveId);

    }

    public function saveLeave(Leave $leave) {
        return $this->leaveRequestDao->saveLeave($leave);
    }

    /**
     * @param int $leaveRequestId
     */
    public function fetchLeaveRequest($leaveRequestId) {

        return $this->leaveRequestDao->fetchLeaveRequest($leaveRequestId);

    }

    /**
     * Modify Over lap leaves
     * @param LeaveRequest $leaveRequest
     * @param $leaveList
     * @return unknown_type
     */
    public function modifyOverlapLeaveRequest(LeaveRequest $leaveRequest , $leaveList ) {

        return $this->leaveRequestDao->modifyOverlapLeaveRequest($leaveRequest , $leaveList);

    }

    /**
     *
     * @param LeavePeriod $leavePeriod
     * @return boolean
     */
    public function adjustLeavePeriodOverlapLeaves(LeavePeriod $leavePeriod) {

        $overlapleaveList =	$this->leaveRequestDao->getLeavePeriodOverlapLeaves($leavePeriod);

        if (count($overlapleaveList) > 0) {

            foreach($overlapleaveList as $leave) {

                $leaveRequest	=	$leave->getLeaveRequest();
                $leaveList		=	$this->leaveRequestDao->fetchLeave($leaveRequest->getLeaveRequestId());
                $this->leaveRequestDao->modifyOverlapLeaveRequest($leaveRequest,$leaveList,$leavePeriod);

            }

        }

    }

    /**
     *
     * @param array $changes
     * @param string $changeType
     * @return boolean
     */
    public function changeLeaveStatus($changes, $changeType, $changeComments = null, $changedByUserType = null, $changedUserId = null) {
        if(is_array($changes)) {
            $approvalIds = array_keys(array_filter($changes, array($this, '_filterApprovals')));
            $rejectionIds = array_keys(array_filter($changes, array($this, '_filterRejections')));
            $cancellationIds = array_keys(array_filter($changes, array($this, '_filterCancellations')));

            $leaveNotificationService = $this->getLeaveNotificationService();

            if ($changeType == 'change_leave_request') {
                foreach ($approvalIds as $leaveRequestId) {
                    $approvals = $this->searchLeave($leaveRequestId);
                    $this->_approveLeave($approvals, $changeComments[$leaveRequestId]);

                    $leaveNotificationService->approve($approvals, $changedByUserType, $changedUserId, 'request');
                }

                foreach ($rejectionIds as $leaveRequestId) {
                    $rejections = $this->searchLeave($leaveRequestId);
                    $this->_rejectLeave($rejections, $changeComments[$leaveRequestId]);
                    $leaveNotificationService->reject($rejections, $changedByUserType, $changedUserId, 'request');
                }

                foreach ($cancellationIds as $leaveRequestId) {
                    $cancellations = $this->searchLeave($leaveRequestId);
                    $this->_cancelLeave($cancellations, $changedByUserType);
                    
                    if ($changedByUserType == Users::USER_TYPE_EMPLOYEE) {
                        $leaveNotificationService->cancelEmployee($cancellations, $changedByUserType, $changedUserId, 'request');
                    } else {
                        $leaveNotificationService->cancel($cancellations, $changedByUserType, $changedUserId, 'request');
                    }                    
                }

            } elseif ($changeType == 'change_leave') {

                $approvals = array();
                foreach ($approvalIds as $leaveId) {
                    $approvals[] = $this->getLeaveRequestDao()->getLeaveById($leaveId);
                }
                $this->_approveLeave($approvals, $changeComments);

                foreach ($approvals as $approval) {
                    $leaveNotificationService->approve(array($approval), $changedByUserType, $changedUserId, 'single');
                }

                $rejections = array();
                foreach ($rejectionIds as $leaveId) {
                    $rejections[] = $this->getLeaveRequestDao()->getLeaveById($leaveId);
                }
                $this->_rejectLeave($rejections, $changeComments);

                foreach ($rejections as $rejection) {
                    $leaveNotificationService->reject(array($rejection), $changedByUserType, $changedUserId, 'single');
                }

                $cancellations = array();
                foreach ($cancellationIds as $leaveId) {
                    $cancellations[] = $this->getLeaveRequestDao()->getLeaveById($leaveId);
                }
                $this->_cancelLeave($cancellations, $changedByUserType);

                foreach ($cancellations as $cancellation) {

                    if ($changedByUserType == Users::USER_TYPE_EMPLOYEE) {
                        $leaveNotificationService->cancelEmployee(array($cancellation), $changedByUserType, $changedUserId, 'single');
                    } else {
                        $leaveNotificationService->cancel(array($cancellation), $changedByUserType, $changedUserId, 'single');
                    }
                }

            } else {
                throw new LeaveServiceException('Wrong change type passed');
            }
        }else {
            throw new LeaveServiceException('Empty changes list');
        }

    }

    private function _approveLeave($leave, $comments, $changeType = null) {
        $leaveStateManager = $this->getLeaveStateManager();

        $leaveRequests = array();
        foreach ($leave as $approval) {
            $leaveRequestId = $approval->getLeaveRequest()->getLeaveRequestId();
            $leaveRequests[$leaveRequestId]['requestObj'] = $approval->getLeaveRequest();
            $leaveRequests[$leaveRequestId]['leaves'][] = $approval;

            $comment = is_array($comments) ? $comments[$approval->getLeaveId()] : $comments;

            $leaveStateManager->setLeave($approval);
            $leaveStateManager->setChangeComments($comment);
            $leaveStateManager->approve();
        }

    }

    private function _rejectLeave($leave, $comments, $changeType = null) {
        $leaveStateManager = $this->getLeaveStateManager();

        $leaveRequests = array();
        foreach ($leave as $rejection) {
            $leaveRequestId = $rejection->getLeaveRequest()->getLeaveRequestId();
            $leaveRequests[$leaveRequestId]['requestObj'] = $rejection->getLeaveRequest();
            $leaveRequests[$leaveRequestId]['leaves'][] = $rejection;

            $comment = is_array($comments) ? $comments[$rejection->getLeaveId()] : $comments;

            $leaveStateManager->setLeave($rejection);
            $leaveStateManager->setChangeComments($comment);
            $leaveStateManager->reject();
        }

    }

    private function _cancelLeave($leave, $changeType = null) {
        $leaveStateManager = $this->getLeaveStateManager();

        $leaveRequests = array();
        foreach ($leave as $cancellation) {
            $leaveRequestId = $cancellation->getLeaveRequest()->getLeaveRequestId();
            $leaveRequests[$leaveRequestId]['requestObj'] = $cancellation->getLeaveRequest();
            $leaveRequests[$leaveRequestId]['leaves'][] = $cancellation;

            $leaveStateManager->setLeave($cancellation);
            $leaveStateManager->cancel();
        }

    }

    public function getScheduledLeavesSum($employeeId, $leaveTypeId, $leavePeriodId) {

        return $this->leaveRequestDao->getScheduledLeavesSum($employeeId, $leaveTypeId, $leavePeriodId);

    }

    public function getTakenLeaveSum($employeeId, $leaveTypeId, $leavePeriodId) {

        return $this->leaveRequestDao->getTakenLeaveSum($employeeId, $leaveTypeId, $leavePeriodId);

    }

    /**
     *
     * @param string $element
     * @return boolean
     */
    private function _filterApprovals($element) {
        return ($element == 'markedForApproval');
    }

    /**
     *
     * @param unknown_type $element
     * @return boolean
     */
    private function _filterRejections($element) {
        return ($element == 'markedForRejection');
    }

    /**
     *
     * @param unknown_type $element
     * @return boolean
     */
    private function _filterCancellations($element) {
        return ($element == 'markedForCancellation');
    }


}
