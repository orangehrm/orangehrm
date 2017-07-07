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
namespace Orangehrm\Rest\Api\Leave\Service;

class APILeaveAssignmentService extends \LeaveAssignmentService
{

    protected $action;

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     *
     * @param array $leaveAssignmentData
     * @return bool
     */
    public function assignLeave(\LeaveParameterObject $leaveAssignmentData)
    {

        $employeeId = $leaveAssignmentData->getEmployeeNumber();

        /* Check whether employee exists */
        if (empty($employeeId)) {
            throw new \LeaveAllocationServiceException('Invalid Employee');
        }

        if ($this->hasOverlapLeave($leaveAssignmentData)) {
            throw new \LeaveAllocationServiceException('Failed to Assign : Has Overlapping Leave');
        }

        if ($this->applyMoreThanAllowedForADay($leaveAssignmentData)) {
            throw new \LeaveAllocationServiceException('Failed to Assign: Work Shift Length Exceeded');
        }

        return $this->saveLeaveRequest($leaveAssignmentData);
    }


    protected function getWorkflowItemForAssignAction(\LeaveParameterObject $leaveAssignmentData)
    {

        if (is_null($this->assignWorkflowItem)) {

            switch ($this->getAction()) {
                case "SCHEDULED":
                    $this->assignWorkflowItem = $this->getWorkflowService()
                        ->getWorkflowItemByStateActionAndRole(\WorkflowStateMachine::FLOW_LEAVE, 'INITIAL', 'ASSIGN',
                            'ADMIN');
                    break;

                case "PENDING":
                    $this->assignWorkflowItem = $this->getWorkflowService()
                        ->getWorkflowItemByStateActionAndRole(\WorkflowStateMachine::FLOW_LEAVE, 'INITIAL', 'APPLY',
                            'ESS');
                    break;
                case "REJECTED":
                    $this->assignWorkflowItem = $this->getWorkflowService()
                        ->getWorkflowItemByStateActionAndRole(\WorkflowStateMachine::FLOW_LEAVE, 'PENDING APPROVAL', 'REJECT',
                            'ADMIN');
                    break;
                case "CANCELLED":
                    $this->assignWorkflowItem = $this->getWorkflowService()
                        ->getWorkflowItemByStateActionAndRole(\WorkflowStateMachine::FLOW_LEAVE, 'SCHEDULED', 'CANCEL',
                            'ADMIN');
                    break;
                default:
                    $this->assignWorkflowItem = $this->getWorkflowService()
                        ->getWorkflowItemByStateActionAndRole(\WorkflowStateMachine::FLOW_LEAVE, 'INITIAL', 'ASSIGN',
                            'ADMIN');
                    break;

            }

            if (is_null($this->assignWorkflowItem)) {
                $this->getLogger()->error("No workflow item found for ASSIGN leave action!");
            }

            return $this->assignWorkflowItem;
        }
        return $this->assignWorkflowItem;
    }


    public function getLeaveRequestStatus(
        $isWeekend,
        $isHoliday,
        $leaveDate,
        \LeaveParameterObject $leaveAssignmentData
    ) {

        // TODO: Change here for leave workflow

        $status = null;

        if ($isWeekend) {
            return \Leave::LEAVE_STATUS_LEAVE_WEEKEND;
        }

        if ($isHoliday) {
            return \Leave::LEAVE_STATUS_LEAVE_HOLIDAY;
        }

        if (is_null($status)) {

            $workFlowItem = $this->getWorkflowItemForAssignAction($leaveAssignmentData);

            if (!is_null($workFlowItem)) {

                $status = \Leave::getLeaveStatusForText($workFlowItem->getResultingState());

            } else {
                throw new \LeaveAllocationServiceException('Not Allowed to Assign Leave to Selected Employee!');
            }

            if (($status == \Leave::LEAVE_STATUS_LEAVE_APPROVED) && (strtotime($leaveDate) < strtotime(date('Y-m-d')))) {
                $status = \Leave::LEAVE_STATUS_LEAVE_TAKEN;
            }
        }

        return $status;
    }

}