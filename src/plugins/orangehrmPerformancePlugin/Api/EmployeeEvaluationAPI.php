<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Performance\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\WorkflowStateModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
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
use OrangeHRM\Performance\Api\Model\ReviewerRatingModel;
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
     * @OA\Get(
     *     path="/api/v2/performance/reviews/{reviewId}/evaluation/employee",
     *     tags={"Performance/Review Evaluation by Employee"},
     *     summary="Get the Employee's Evaluation in a Review",
     *     operationId="get-the-employees-evaluation-in-a-review",
     *     @OA\PathParameter(
     *         name="reviewId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=SupervisorEvaluationSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Performance-ReviewerRatingModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="generalComment", type="string"),
     *                 @OA\Property(
     *                     property="kpis",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Performance-KpiSummaryModel")
     *                 ),
     *                 @OA\Property(
     *                     property="reviewer",
     *                     ref="#/components/schemas/Performance-KpiSummaryModel"
     *                 ),
     *                 @OA\Property(
     *                     property="allowedActions",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Core-WorkflowStateModel")
     *                 ),
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $supervisorParamHolder = new SupervisorEvaluationSearchFilterParams();
        $supervisorParamHolder->setReviewId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_REVIEW_ID
            )
        );
        $this->setSortingAndPaginationParams($supervisorParamHolder);
        $review = $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->getPerformanceReviewById($supervisorParamHolder->getReviewId());
        $allowedActions = $this->getAllowedActions($review);

        $sendRatings = true;
        $employeeReviewer = $review->getDecorator()->getEmployeeReviewer();
        // Check if supervisor/admin is accessing API
        if ($this->getAuthUser()->getEmpNumber() !== $review->getEmployee()->getEmpNumber()) {
            // Don't send ratings if employee status is activated / in progress
            if (
                $employeeReviewer->getStatus() === Reviewer::STATUS_ACTIVATED ||
                $employeeReviewer->getStatus() === Reviewer::STATUS_IN_PROGRESS
            ) {
                $sendRatings = false;
            }
        }

        $ratings = $sendRatings ? $this->getReviewerRatings($supervisorParamHolder) : [];
        $ratingCount = $sendRatings ? $this->getReviewerRatingCount($supervisorParamHolder) : 0;

        return new EndpointCollectionResult(
            ReviewerRatingModel::class,
            $ratings,
            new ParameterBag([
                CommonParams::PARAMETER_TOTAL => $ratingCount,
                self::PARAMETER_GENERAL_COMMENT => $sendRatings ? $employeeReviewer->getComment() : null,
                self::PARAMETER_KPIS => $this->getKpisForReview(),
                self::PARAMETER_REVIEWERS => $this->getReviewerForReviewRating(),
                self::PARAMETER_ALLOWED_ACTIONS => $allowedActions,
            ])
        );
    }

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
        if ($review->getStatusId() == PerformanceReview::STATUS_COMPLETED) {
            return null;
        }

        $allowedWorkflowItems = $this->getAllowedActionList($review);

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
        if ($review->getStatusId() == PerformanceReview::STATUS_COMPLETED) {
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
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_COMPLETE
        );
        $status = !$action ? Reviewer::STATUS_IN_PROGRESS : Reviewer::STATUS_COMPLETED;
        $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->updateReviewerStatus(
                $review,
                ReviewerGroup::REVIEWER_GROUP_EMPLOYEE,
                $status
            );
    }

    /**
     * @param PerformanceReview $review
     * @param string|null $comment
     */
    protected function updateReviewerComment(PerformanceReview $review, ?string $comment): void
    {
        if (!is_null($comment)) {
            $this->getPerformanceReviewService()
                ->getPerformanceReviewDao()
                ->updateReviewerComment($review, ReviewerGroup::REVIEWER_GROUP_EMPLOYEE, $comment);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v2/performance/reviews/{reviewId}/evaluation/employee",
     *     tags={"Performance/Review Evaluation by Employee"},
     *     summary="Update the Employee's Evaluation in a Review",
     *     operationId="update-the-employees-evaluation-in-a-review",
     *     @OA\PathParameter(
     *         name="reviewId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="reviewers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="rating", type="number"),
     *                     @OA\Property(property="comment", type="string"),
     *                     required={"id", "rating", "comment"}
     *                 ),
     *             ),
     *             @OA\Property(property="generalComment", type="string"),
     *             @OA\Property(property="complete", type="boolean"),
     *             required={"reviewers", "complete"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Performance-ReviewerRatingModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
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
