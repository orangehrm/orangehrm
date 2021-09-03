<?php
/**
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
 */

namespace OrangeHRM\Leave\Service;

use InvalidArgumentException;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Leave\Dao\LeaveRequestDao;
use OrangeHRM\Leave\Dto\LeaveRequest\DetailedLeaveRequest;

class LeaveRequestService
{
    use UserRoleManagerTrait;

    public const WORKFLOW_LEAVE_TYPE_DELETED_STATUS_PREFIX = 'LEAVE TYPE DELETED';

    /**
     * @var LeaveRequestDao|null
     */
    private ?LeaveRequestDao $leaveRequestDao = null;
    private $leaveTypeService;
    private $leaveEntitlementService;
    private $leavePeriodService;
    private $holidayService;
    private $accessFlowStateMachineService;

    /**
     * @var array|null
     * array(
     *     -1 => 'REJECTED',
     *     0 => 'CANCELLED',
     *     1 => 'PENDING APPROVAL',
     *     2 => 'SCHEDULED',
     *     3 => 'TAKEN',
     *     4 => 'WEEKEND',
     *     5 => 'HOLIDAY'
     * )
     */
    private ?array $leaveStatuses = null;

    private $dispatcher;

    const LEAVE_CHANGE_TYPE_LEAVE = 'change_leave';
    const LEAVE_CHANGE_TYPE_LEAVE_REQUEST = 'change_leave_request';

    /**
     * @return LeaveRequestDao
     */
    public function getLeaveRequestDao(): LeaveRequestDao
    {
        if (!($this->leaveRequestDao instanceof LeaveRequestDao)) {
            $this->leaveRequestDao = new LeaveRequestDao();
        }
        return $this->leaveRequestDao;
    }

    /**
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService() {
        // TODO
        if(is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     * @return LeaveTypeService
     */
    public function getLeaveTypeService() {
        // TODO
        if(is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    /**
     * Returns LeavePeriodService
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {
        // TODO
        if(is_null($this->leavePeriodService)) {
            $this->leavePeriodService = new LeavePeriodService();
            $this->leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
        }
        return $this->leavePeriodService;
    }

    /**
     * Returns HolidayService
     * @return HolidayService
     */
    public function getHolidayService() {
        // TODO
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
        // TODO
        $this->holidayService = $holidayService;
    }

    /**
     * Set dispatcher.
     *
     * @param $dispatcher
     */
    public function setDispatcher($dispatcher) {
        // TODO
        $this->dispatcher = $dispatcher;
    }

    public function getDispatcher() {
        // TODO
        if(is_null($this->dispatcher)) {
            $this->dispatcher = sfContext::getInstance()->getEventDispatcher();
        }
        return $this->dispatcher;
    }

    public function getAccessFlowStateMachineService() {
        // TODO
        if (is_null($this->accessFlowStateMachineService)) {
            $this->accessFlowStateMachineService = new AccessFlowStateMachineService();
        }
        return $this->accessFlowStateMachineService;
    }

    public function setAccessFlowStateMachineService($accessFlowStateMachineService) {
        // TODO
        $this->accessFlowStateMachineService = $accessFlowStateMachineService;
    }

    /**
     *
     * @param Employee $employee
     * @return LeaveType Collection
     */
    public function getEmployeeAllowedToApplyLeaveTypes(Employee $employee) {
        // TODO
        try {
            $leaveEntitlementService    = $this->getLeaveEntitlementService();                $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();

            $leaveTypeService           = $this->getLeaveTypeService();

            $leaveTypes     = $leaveTypeService->getLeaveTypeList();
            $leaveTypeList  = array();

            foreach($leaveTypes as $leaveType) {
                $balance = $leaveEntitlementService->getLeaveBalance($employee->getEmpNumber(), $leaveType->getId());

                if($balance->getEntitled() > 0) {
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
     * @param ParameterObject $searchParameters
     * @param array $statuses
     * @return array
     */
    public function searchLeaveRequests($searchParameters, $page = 1, $isCSVPDFExport = false, $isMyLeaveList = false,
        // TODO
            $prefetchLeave = false, $prefetchComments = false, $includePurgeEmployee = false) {
        $result = $this->getLeaveRequestDao()->searchLeaveRequests($searchParameters, $page, $isCSVPDFExport,
                $isMyLeaveList, $prefetchLeave, $prefetchComments, $includePurgeEmployee);
        return $result;

    }

    /**
     * Get Leave Request Status
     * @param $day
     * @return unknown_type
     */
    public function getLeaveRequestStatus( $day ) {
        // TODO
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
        // TODO
        return $this->getLeaveRequestDao()->fetchLeave($leaveRequestId);

    }

    /**
     *
     * @param int $leaveId
     * @return array
     */
    public function readLeave($leaveId) {
        // TODO
        return $this->getLeaveRequestDao()->readLeave($leaveId);

    }

    public function saveLeave(Leave $leave) {
        // TODO
        return $this->getLeaveRequestDao()->saveLeave($leave);
    }

    /**
     * @param int $leaveRequestId
     */
    public function fetchLeaveRequest($leaveRequestId) {
        // TODO
        return $this->getLeaveRequestDao()->fetchLeaveRequest($leaveRequestId);

    }

    function groupChanges($changes) {
        // TODO
        $groupedChanges = array();

        foreach ($changes as $id => $value) {
            if (strpos($value, 'WF') === 0) {
                $workFlowId = substr($value, 2);
                if (isset($groupedChanges[$workFlowId])) {
                    $groupedChanges[$workFlowId][] = $id;
                } else {
                    $groupedChanges[$workFlowId] = array($id);
                }
            }
        }

        return $groupedChanges;
    }

    /**
     *
     * @param array $changes
     * @param string $changeType
     * @return boolean
     */
    public function changeLeaveStatus($changes, $changeType, $changeComments = null, $changedByUserType = null, $changedUserId = null) {
        // TODO
        if (is_array($changes)) {
            $groupedChanges = $this->groupChanges($changes);

            $workflowService = $this->getAccessFlowStateMachineService();

            if ($changeType == 'change_leave_request') {

                foreach ($groupedChanges as $workFlowId => $changedItems) {
                    $workFlow = $workflowService->getWorkflowItem($workFlowId);
                    $nextStateStr = $workFlow->getResultingState();
                    $nextState = Leave::getLeaveStatusForText($nextStateStr);

                    $event = LeaveEvents::LEAVE_CHANGE;
                    //LeaveEvents::LEAVE_REJECT
                    //LeaveEvents::LEAVE_CANCEL

                    foreach ($changedItems as $leaveRequestId) {
                        $changedLeaveRequest = $this->fetchLeaveRequest($leaveRequestId);
                        $changedLeave = $changedLeaveRequest->getLeave();
                        $allowedActions = $this->getLeaveRequestActions($changedLeaveRequest, $changedUserId);

                        if (isset($allowedActions[$workFlowId])) {
                            $this->_changeLeaveStatus($changedLeave, $nextState, $changeComments[$leaveRequestId]);
                            $this->_notifyLeaveStatusChange($event, $workFlow, $changedLeave,
                                $changedByUserType, $changedUserId, 'request');
                        } else {
                            throw new Exception('Action Not allowed');
                        }
                    }
                }

            } elseif ($changeType == 'change_leave') {

                $actionTypes = count($groupedChanges);

                $workFlowItems = array();
                $changes = array();
                $allDays = array();

                foreach ($groupedChanges as $workFlowId => $changedItems) {
                    $workFlow = $workflowService->getWorkflowItem($workFlowId);
                    $workFlowItems[$workFlow->getId()] = $workFlow;

                    $nextStateStr = $workFlow->getResultingState();
                    $nextState = Leave::getLeaveStatusForText($nextStateStr);

                    $event = LeaveEvents::LEAVE_CHANGE;
                    //LeaveEvents::LEAVE_REJECT
                    //LeaveEvents::LEAVE_CANCEL
                    $changedLeave = array();
                    foreach ($changedItems as $leaveId) {
                        $leaveObj = $this->getLeaveRequestDao()->getLeaveById($leaveId);
                        if (array_key_exists($workFlowId, $this->getLeaveActions($leaveObj, $changedUserId))) {
                            $changedLeave[] = $leaveObj;
                        }
                    }

                    if (count($changedLeave) === 0) {
                        throw new Exception('Action Not allowed');
                    }

                    $this->_changeLeaveStatus($changedLeave, $nextState, $changeComments);

                    if ($actionTypes == 1) {
                        $this->_notifyLeaveStatusChange($event, $workFlow, $changedLeave,
                                $changedByUserType, $changedUserId, 'multiple');
                    } else {

                        $changes[$workFlow->getId()] = $changedLeave;
                        $allDays = array_merge($allDays, $changedLeave);
                    }
                }

                if ($actionTypes > 1) {
                    $this->_notifyLeaveMultiStatusChange($allDays, $changes, $workFlowItems,
                                $changedByUserType, $changedUserId, 'multiple');
                }
            } else {
                throw new LeaveServiceException('Wrong change type passed');
            }
        }else {
            throw new LeaveServiceException('Empty changes list');
        }

    }

    protected function _changeLeaveStatus($leaveList, $newState, $comments = null) {
        // TODO
        $dao = $this->getLeaveRequestDao();

        foreach ($leaveList as $leave) {

            $currentState = $leave->getStatus();
            if (($currentState != Leave::LEAVE_STATUS_LEAVE_WEEKEND) &&
                    ($currentState != Leave::LEAVE_STATUS_LEAVE_HOLIDAY)) {
                $entitlementChanges = array();

                $removeLinkedEntitlements = (($newState == Leave::LEAVE_STATUS_LEAVE_CANCELLED) ||
                        ($newState == Leave::LEAVE_STATUS_LEAVE_REJECTED));


                $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();

                if ($removeLinkedEntitlements) {
                    $entitlementChanges = $strategy->handleLeaveCancel($leave);
                }

                $leave->setStatus($newState);

                if (!is_null($comments)) {
                    if (is_array($comments)) {
                        $comment = isset($comments[$leave->getId()]) ? $comments[$leave->getId()] : '';
                    } else {
                        $comment = $comments;
                    }
                    $leave->setComments($comment);
                }

                $dao->changeLeaveStatus($leave, $entitlementChanges, $removeLinkedEntitlements);
            }
        }
    }

    private function _notifyLeaveStatusChange($eventType, $workflow, $leaveList, $performerType, $performerId, $requestType) {
        // TODO
        $request = $leaveList[0]->getLeaveRequest();

        $eventData = array('days' => $leaveList,
                           'performerType' => $performerType,
                           'empNumber' => $performerId,
                           'requestType' => $requestType,
                           'request' => $request,
                           'workFlow' => $workflow);
        $this->getDispatcher()->notify(new sfEvent($this, $eventType, $eventData));
    }

    private function _notifyLeaveMultiStatusChange($allDays, $leaveList, $workFlows, $performerType, $performerId, $requestType) {
        // TODO
        $request = $allDays[0]->getLeaveRequest();

        $eventData = array('days' => $allDays,
                           'changes' => $leaveList,
                           'performerType' => $performerType,
                           'empNumber' => $performerId,
                           'requestType' => $requestType,
                           'request' => $request,
                           'workFlow' => $workFlows);
        $this->getDispatcher()->notify(new sfEvent($this, LeaveEvents::LEAVE_CHANGE, $eventData));
    }

    public function getScheduledLeavesSum($employeeId, $leaveTypeId, $leavePeriodId) {
        // TODO
        return $this->getLeaveRequestDao()->getScheduledLeavesSum($employeeId, $leaveTypeId, $leavePeriodId);

    }

    public function getTakenLeaveSum($employeeId, $leaveTypeId, $leavePeriodId) {
        // TODO
        return $this->getLeaveRequestDao()->getTakenLeaveSum($employeeId, $leaveTypeId, $leavePeriodId);

    }

    /**
     * @param Employee $employee
     * @param LeaveType $leaveType
     * @param string $leaveStatus e.g. ['PENDING APPROVAL', 'SCHEDULED', 'TAKEN']
     * @param int $loggedInEmpNumber
     * @return WorkflowStateMachine[]
     */
    public function getLeaveRequestAllowedWorkflows(
        Employee $employee,
        LeaveType $leaveType,
        string $leaveStatus,
        int $loggedInEmpNumber
    ): array {
        $includeRoles = [];
        $empNumber = $employee->getEmpNumber();

        // If looking at own leave request, only consider ESS role
        if ($empNumber == $loggedInEmpNumber && ($this->getUserRoleManager()->essRightsToOwnWorkflow()
                || !$this->getUserRoleManager()->isEntityAccessible(Employee::class, $empNumber))) {
            $includeRoles = ['ESS'];
        }

        $leaveTypeDeleted = $leaveType->isDeleted();
        if ($leaveTypeDeleted) {
            $leaveStatus = self::WORKFLOW_LEAVE_TYPE_DELETED_STATUS_PREFIX . ' ' . $leaveStatus;
        }

        return $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_LEAVE,
            $leaveStatus,
            [],
            $includeRoles,
            [Employee::class => $empNumber]
        );
    }

    /**
     * @param LeaveRequest $leaveRequest
     * @param string $leaveStatus e.g. ['PENDING APPROVAL', 'SCHEDULED', 'TAKEN']
     * @param int $loggedInEmpNumber
     * @return array
     */
    public function getLeaveRequestActions(LeaveRequest $leaveRequest, string $leaveStatus, int $loggedInEmpNumber): array
    {
        // TODO
        $workFlowItems = $this->getLeaveRequestAllowedWorkflows(
            $leaveRequest->getEmployee(),
            $leaveRequest->getLeaveType(),
            $leaveStatus,
            $loggedInEmpNumber
        );

        $actions = [];
        foreach ($workFlowItems as $item) {
            $name = $item->getAction();
            $actions[$item->getId()] = ucfirst(strtolower($name));
        }

        return $actions;
    }

    public function getLeaveActions($leave, $loggedInEmpNumber) {
        // TODO
        $actions = array();

        $includeRoles = array();
        $excludeRoles = array();

        $userRoleManager = $this->getUserRoleManager();

        // If looking at own leave, only consider ESS role
        if ($leave->getEmpNumber() == $loggedInEmpNumber && ($userRoleManager->essRightsToOwnWorkflow() || !$userRoleManager->isEntityAccessible('Employee', $leave->getEmpNumber()))) {
            $includeRoles = array('ESS');
        }

        $status = $leave->getTextLeaveStatus();

        $leaveTypeDeleted = $leave->getLeaveType()->getDeleted();

        if ($leaveTypeDeleted) {
            $status = Leave::LEAVE_STATUS_LEAVE_TYPE_DELETED_TEXT . ' ' . $status;
        }

        $workFlowItems = $userRoleManager->getAllowedActions(WorkflowStateMachine::FLOW_LEAVE,
                $status, $excludeRoles, $includeRoles, array('Employee' => $leave->getEmpNumber()));

        foreach ($workFlowItems as $item) {
            $name = $item->getAction();
            $actions[$item->getId()] = ucfirst(strtolower($name));
        }

        return $actions;
    }

    public function getLeaveById($leaveId) {
        // TODO
        return $this->getLeaveRequestDao()->getLeaveById($leaveId);
    }
     /**
     *
     * @param ParameterObject $searchParameters
     * @param array $statuses
     * @return array
     */
    public function getLeaveRequestSearchResultAsArray($searchParameters) {
        // TODO
        return $this->getLeaveRequestDao()->getLeaveRequestSearchResultAsArray($searchParameters);
    }

     /**
     *
     * @param ParameterObject $searchParameters
     * @param array $statuses
     * @return array
     */
    public function getDetailedLeaveRequestSearchResultAsArray($searchParameters) {
        // TODO
        return $this->getLeaveRequestDao()->getDetailedLeaveRequestSearchResultAsArray($searchParameters);
    }

    public function markApprovedLeaveAsTaken() {
        // TODO
        return $this->getLeaveRequestDao()->markApprovedLeaveAsTaken();
    }

    /**
     * Update leave request status (required prior access right validation)
     * @param LeaveRequest $leaveRequest
     * @param string $action 'Approve', 'Cancel', 'Reject'
     * @param null|string $actionPerformerUserType
     * @param null|string $actionPerformerEmpNumber
     */
    public function changeLeaveRequestStatus($leaveRequest, $action, $actionPerformerUserType = null, $actionPerformerEmpNumber = null) {
        // TODO
        $changedLeave = $leaveRequest->getLeave();
        $allowedActions = $this->getLeaveRequestActions($leaveRequest, $actionPerformerEmpNumber);

        $workFlowId = array_flip($allowedActions)[$action];
        $workFlow = $this->getAccessFlowStateMachineService()->getWorkflowItem($workFlowId);
        $nextState = Leave::getLeaveStatusForText($workFlow->getResultingState());

        $this->_changeLeaveStatus($changedLeave, $nextState);
        $this->_notifyLeaveStatusChange(LeaveEvents::LEAVE_CHANGE, $workFlow, $changedLeave,
            $actionPerformerUserType, $actionPerformerEmpNumber, 'request');
    }

    /**
     * @param string $fromDate
     * @param string $toDate
     * @param int $employeeId
     * @param $statuses
     * @return array|Doctrine_Collection|Doctrine_Collection_OnDemand|int
     * @throws DaoException
     */
    public function getLeaveRecordsBetweenTwoDays(string $fromDate, string $toDate,int $employeeId,$statuses)
    {
        // TODO
        return $this->getLeaveRequestDao()->getLeaveRecordsBetweenTwoDays($fromDate,$toDate,$employeeId,$statuses);
    }

    /**
     * @param LeaveRequest[] $leaveRequests
     * @return DetailedLeaveRequest[]
     */
    public function getDetailedLeaveRequests(array $leaveRequests): array
    {
        $leaveRequestsMap = $this->getLeaveRequestsMap($leaveRequests);
        $leaveRequestsIds = array_keys($leaveRequestsMap);
        $leaves = $this->getLeaveRequestDao()->getLeavesByLeaveRequestIds($leaveRequestsIds);

        $detailedLeaveRequests = [];
        foreach ($leaves as $leave) {
            $leaveRequestId = $leave->getLeaveRequest()->getId();
            if (!isset($detailedLeaveRequests[$leaveRequestId])) {
                $detailedLeaveRequest = new DetailedLeaveRequest();
                $detailedLeaveRequest->setLeaveRequest($leave->getLeaveRequest());
                $detailedLeaveRequests[$leaveRequestId] = $detailedLeaveRequest;
            }
            $detailedLeaveRequests[$leaveRequestId]->addLeave($leave);
        }
        $sortedDetailedLeaveRequests = [];
        foreach ($leaveRequestsIds as $leaveRequestId) {
            if (isset($detailedLeaveRequests[$leaveRequestId])) {
                $sortedDetailedLeaveRequests[] = $detailedLeaveRequests[$leaveRequestId];
            }
        }
        return $sortedDetailedLeaveRequests;
    }

    /**
     * @param LeaveRequest[] $leaveRequests
     * @return array<int, LeaveRequest>
     */
    private function getLeaveRequestsMap(array $leaveRequests): array
    {
        $leaveRequestsMap = [];
        foreach ($leaveRequests as $leaveRequest) {
            $leaveRequestsMap[$leaveRequest->getId()] = $leaveRequest;
        }
        return $leaveRequestsMap;
    }

    /**
     * @return array<int, string>
     */
    public function getAllLeaveStatusesAssoc(): ?array
    {
        if (is_null($this->leaveStatuses)) {
            foreach ($this->getLeaveRequestDao()->getAllLeaveStatuses() as $status) {
                $this->leaveStatuses[$status->getStatus()] = $status->getName();
            }
        }
        return $this->leaveStatuses;
    }

    /**
     * @param int $status
     * @return string
     */
    public function getLeaveStatusNameByStatus(int $status): string
    {
        $leaveStatuses = $this->getAllLeaveStatusesAssoc();
        if (isset($leaveStatuses[$status])) {
            return $leaveStatuses[$status];
        }
        throw new InvalidArgumentException('Invalid status');
    }

    /**
     * @param string $name
     * @return int
     */
    public function getLeaveStatusByName(string $name): int
    {
        $leaveStatuses = array_flip($this->getAllLeaveStatusesAssoc());
        if (isset($leaveStatuses[$name])) {
            return $leaveStatuses[$name];
        }
        throw new InvalidArgumentException('Invalid status name');
    }

    /**
     * @param string[] $names e.g. ['REJECTED', 'PENDING APPROVAL', 'TAKEN']
     * @return int[] e.g. [-1, 1, 3]
     */
    public function getLeaveStatusesByNames(array $names): array
    {
        $leaveStatuses = array_flip($this->getAllLeaveStatusesAssoc());
        return array_map(function (string $name) use ($leaveStatuses) {
            if (isset($leaveStatuses[$name])) {
                return $leaveStatuses[$name];
            }
            throw new InvalidArgumentException("Invalid status name $name");
        }, $names);
    }
}
