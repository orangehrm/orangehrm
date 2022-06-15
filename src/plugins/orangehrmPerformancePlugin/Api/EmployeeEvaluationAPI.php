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

namespace OrangeHRM\Performance\Api;

use OrangeHRM\Core\Api\V2\Model\WorkflowStateModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Performance\Api\Model\ReviewerModel;
use OrangeHRM\Performance\Dto\SupervisorEvaluationSearchFilterParams;

class EmployeeEvaluationAPI extends SupervisorEvaluationAPI
{
    public const ACTION_SELF_IN_PROGRESS = 'SELF IN PROGRESS';
    public const ACTION_SUPERVISOR_UPDATED = 'SUPERVISOR UPDATED';
    public const ACTION_SELF_COMPLETED = 'SELF COMPLETED';
    public const PARAMETER_COMPLETE = 'complete';

    public const ACTIONABLE_STATES_MAP = [
        WorkflowStateMachine::SELF_REVIEW_SELF_SAVE => 'save',
        WorkflowStateMachine::SELF_REVIEW_SELF_COMPLETE => 'complete',
        WorkflowStateMachine::SELF_REVIEW_SUPERVISOR_ACTION => 'supervisorUpdate',
    ];

    public const WORKFLOW_STATES_MAP = [
        WorkflowStateMachine::SELF_REVIEW_SELF_SAVE => 'INITIAL',
        WorkflowStateMachine::SELF_REVIEW_SELF_COMPLETE => 'SELF IN PROGRESS',
        WorkflowStateMachine::SELF_REVIEW_SUPERVISOR_ACTION => 'SELF COMPLETED',
    ];

    /**
     * @inheritDoc
     */
    protected function getReviewerRatings(SupervisorEvaluationSearchFilterParams $evaluationParamHolder): array
    {
        return $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()->getReviewerRating($evaluationParamHolder, ReviewerGroup::REVIEWER_GROUP_EMPLOYEE);
    }

    /**
     * @inheritDoc
     */
    protected function getReviewerRatingCount(SupervisorEvaluationSearchFilterParams $evaluationParamHolder): int
    {
        return $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()->getReviewerRatingCount($evaluationParamHolder, ReviewerGroup::REVIEWER_GROUP_EMPLOYEE);
    }

    /**
     * @inheritDoc
     */
    protected function getReviewerForReviewRating(): array
    {
        $reviewId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_REVIEW_ID
        );

        return $this->getNormalizerService()->normalize(
            ReviewerModel::class,
            $this->getPerformanceReviewService()->getPerformanceReviewDao()
                ->getReviewerRecord($reviewId, ReviewerGroup::REVIEWER_GROUP_EMPLOYEE)
        );
    }

    /**
     * @inheritDoc
     */
    protected function getAllowedActions(PerformanceReview $review): ?array
    {
        if ($review->getStatusId() == 4) {
            return null;
        }

        $allowedWorkflowItems = $this->getAllowedActionList($review);

        if ($this->getAuthUser()->getEmpNumber() != $review->getEmployee()->getEmpNumber()) {
            if (!$this->checkActionAllowed($review)) {
                throw $this->getForbiddenException();
            }
        }
        foreach ($allowedWorkflowItems as $allowedWorkflowItem) {
            $allowedWorkflowItem->setAction(self::ACTIONABLE_STATES_MAP[$allowedWorkflowItem->getAction()]);
        }
        return $this->getNormalizerService()->normalizeArray(
            WorkflowStateModel::class,
            $allowedWorkflowItems,
        );
    }

    /**
     * @param PerformanceReview $review
     * @return array|null
     */
    private function getAllowedActionList(PerformanceReview $review): ?array
    {
        $selfReviewer = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getPerformanceSelfReviewer($review);

        $currentState = self::WORKFLOW_STATES_MAP[$selfReviewer->getStatus()];

        $excludeRoles = [];
        if ($this->getAuthUser()->getEmpNumber() != $review->getEmployee()->getEmpNumber()) {
            $excludeRoles = ['ESS'];
        }

        return $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_SELF_REVIEW,
            $currentState,
            $excludeRoles,
        );
    }

    /**
     * @inheritDoc
     */
    protected function setReviewRatingsParams(PerformanceReview $review): void
    {
        $ratings = $this->getRequestParams()
            ->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_RATINGS);
        $this->getPerformanceReviewService()->saveAndUpdateReviewRatings(
            $review,
            $ratings,
            ReviewerGroup::REVIEWER_GROUP_EMPLOYEE
        );
    }

    /**
     * @inheritDoc
     */
    protected function checkActionAllowed(PerformanceReview $review): bool
    {
        $hasPermission = false;
        if ($review->getStatusId() == 4) {
            return false;
        }

        $allowedWorkflowItems = $this->getAllowedActionList($review);

        foreach ($allowedWorkflowItems as $allowedWorkflowItem) {
            if (
                $allowedWorkflowItem->getResultingState() == self::ACTION_SELF_IN_PROGRESS ||
                $allowedWorkflowItem->getResultingState() == self::ACTION_SELF_COMPLETED ||
                $allowedWorkflowItem->getResultingState() == self::ACTION_SUPERVISOR_UPDATED
            ) {
                $hasPermission = true;
                break;
            }
        }
        return $hasPermission;
    }

    /**
     * @inheritDoc
     */
    protected function updateReviewerStatus(PerformanceReview $review): void
    {
        $action = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_COMPLETE
        );
        if (!$action) {
            $status = Reviewer::STATUS_IN_PROGRESS;
        } else {
            $status = Reviewer::STATUS_COMPLETED;
        }
        $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->updateReviewerStatus(
                $review,
                ReviewerGroup::REVIEWER_GROUP_EMPLOYEE,
                $status
            );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramRuleCollection = parent::getValidationRuleForUpdate();
        $paramRuleCollection->addParamValidation(
            new ParamRule(
                self::PARAMETER_COMPLETE,
                new Rule(Rules::BOOL_TYPE)
            )
        );
        return $paramRuleCollection;
    }
}
