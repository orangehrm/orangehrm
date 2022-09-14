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

namespace OrangeHRM\Dashboard\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Dashboard\Dto\ActionSummary\PendingAction;
use OrangeHRM\Dashboard\Dto\ActionSummary\PendingAppraisalReviewSummary;
use OrangeHRM\Dashboard\Dto\ActionSummary\PendingLeaveRequestSummary;
use OrangeHRM\Dashboard\Dto\ActionSummary\PendingTimesheetSummary;
use OrangeHRM\Dashboard\Dto\ActionSummary\ScheduledInterviewSummary;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Leave\Dto\LeaveRequestSearchFilterParams;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;
use OrangeHRM\Time\Dto\EmployeeTimesheetListSearchFilterParams;

class EmployeeActionSummaryAPI extends Endpoint implements ResourceEndpoint
{
    use UserRoleManagerTrait;
    use AuthUserTrait;

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $empNumber = $this->getAuthUser()->getEmpNumber();

        $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
        $authUserIndex = array_search($empNumber, $accessibleEmpNumbers);
        unset($accessibleEmpNumbers[$authUserIndex]);

        /**
         * Pending Leave Requests
         */

        $leaveRequestSearchFilterParams = new LeaveRequestSearchFilterParams();
        $leaveRequestSearchFilterParams->setEmpNumbers(array_values($accessibleEmpNumbers));
        $leaveRequestSearchFilterParams->setIncludeEmployees(
            LeaveRequestSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
        );
        $leaveRequestSearchFilterParams->setStatuses([Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL]);
        $pendingLeaveRequestSummary = new PendingLeaveRequestSummary($leaveRequestSearchFilterParams);

        /**
         * Pending Time-sheets
         */

        $employeeTimesheetFilterParams = new EmployeeTimesheetListSearchFilterParams();
        $employeeTimesheetFilterParams->setEmployeeNumbers(array_values($accessibleEmpNumbers));
        $actionableStatesList = $this->getUserRoleManager()
            ->getActionableStates(
                WorkflowStateMachine::FLOW_TIME_TIMESHEET,
                [
                    WorkflowStateMachine::TIMESHEET_ACTION_APPROVE,
                    WorkflowStateMachine::TIMESHEET_ACTION_REJECT
                ]
            );
        $employeeTimesheetFilterParams->setActionableStatesList($actionableStatesList);
        $pendingTimesheetSummary = new PendingTimesheetSummary($employeeTimesheetFilterParams);

        /**
         * Pending Appraisal Reviews
         */

        $performanceReviewSearchFilterParams = new PerformanceReviewSearchFilterParams();
        $performanceReviewSearchFilterParams->setExcludeInactiveReviews(true);
        $performanceReviewSearchFilterParams->setIsForPendingReviewActionWidget(true);
        $performanceReviewSearchFilterParams->setEmpNumber($empNumber);
        $performanceReviewSearchFilterParams->setReviewerEmpNumber($empNumber);
        $performanceReviewSearchFilterParams->setActionableStatuses(
            [
                WorkflowStateMachine::REVIEW_ACTIVATE,
                WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE
            ]
        );
        $pendingAppraisalReviewSummary = new PendingAppraisalReviewSummary($performanceReviewSearchFilterParams);

        /**
         * Candidates to Interview
         */

        $accessibleCandidateIds = $this->getUserRoleManager()->getAccessibleEntityIds(Candidate::class);

        $scheduledInterviewSummary = new ScheduledInterviewSummary($accessibleCandidateIds);

        $actionsSummary = [];
        $availableActionGroups = [
            $pendingLeaveRequestSummary,
            $pendingTimesheetSummary,
            $pendingAppraisalReviewSummary,
            $scheduledInterviewSummary
        ];
        foreach ($availableActionGroups as $actionGroup) {
            $pendingAction = new PendingAction($actionGroup);
            $actionSummary = $pendingAction->generateActionSummary();
            if (!is_null($actionSummary)) {
                $actionsSummary[] = $actionSummary;
            }
        }

        return new EndpointResourceResult(ArrayModel::class, $actionsSummary);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramRules = new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    CommonParams::PARAMETER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                ),
            ),
        );
        $paramRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramRules;
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
