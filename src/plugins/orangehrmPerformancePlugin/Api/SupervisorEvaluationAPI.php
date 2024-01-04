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
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Model\WorkflowStateModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Performance\Api\Model\KpiSummaryModel;
use OrangeHRM\Performance\Api\Model\ReviewerRatingModel;
use OrangeHRM\Performance\Api\Model\SupervisorReviewerModel;
use OrangeHRM\Performance\Api\ValidationRules\ReviewReviewerRatingParamRule;
use OrangeHRM\Performance\Dto\ReviewKpiSearchFilterParams;
use OrangeHRM\Performance\Dto\SupervisorEvaluationSearchFilterParams;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;
use PHPUnit\Exception;

class SupervisorEvaluationAPI extends Endpoint implements CrudEndpoint
{
    use PerformanceReviewServiceTrait;
    use UserRoleManagerTrait;
    use NormalizerServiceTrait;
    use EntityManagerHelperTrait;
    use AuthUserTrait;

    public const PARAMETER_REVIEW_ID = 'reviewId';
    public const PARAMETER_KPIS = 'kpis';
    public const PARAMETER_RATINGS = 'ratings';
    public const PARAMETER_RATING = 'rating';
    public const PARAMETER_COMMENT = 'comment';
    public const PARAMETER_GENERAL_COMMENT = 'generalComment';
    public const PARAMETER_KPI_ID = 'kpiId';
    public const PARAMETER_REVIEWERS = 'reviewer';
    public const PARAMETER_ALLOWED_ACTIONS = 'allowedActions';
    public const STATE_INITIAL = 'INITIAL';
    public const ACTION_IN_PROGRESS = 'IN PROGRESS';
    public const ACTION_COMPLETE = 'COMPLETE';

    public const ACTIONABLE_STATES_MAP = [
        WorkflowStateMachine::REVIEW_INACTIVE_SAVE => 'saveDraft',
        WorkflowStateMachine::REVIEW_ACTIVATE => 'activate',
        WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE => 'save',
        WorkflowStateMachine::REVIEW_COMPLETE => 'complete'
    ];

    public const WORKFLOW_STATES_MAP = [
        WorkflowStateMachine::REVIEW_INACTIVE_SAVE => 'SAVED',
        WorkflowStateMachine::REVIEW_ACTIVATE => 'ACTIVATED',
        WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE => 'IN PROGRESS',
        WorkflowStateMachine::REVIEW_COMPLETE => 'COMPLETED'
    ];

    /**
     * @OA\Get(
     *     path="/api/v2/performance/reviews/{reviewId}/evaluation/supervisor",
     *     tags={"Performance/Review Evaluation by Supervisor"},
     *     summary="Get the Supervisor's Evaluation in a Review",
     *     operationId="get-the-supervisors-evaluation-in-a-review",
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
        $supervisorReviewer = $review->getDecorator()->getSupervisorReviewer();
        // Check if ESS is accessing API
        if (!$this->isAdminAccessingApi() && $this->getAuthUser()->getEmpNumber() === $review->getEmployee()->getEmpNumber()) {
            // Don't send ratings if supervisor status is activated / in progress
            if (
                $supervisorReviewer->getStatus() === Reviewer::STATUS_ACTIVATED ||
                $supervisorReviewer->getStatus() === Reviewer::STATUS_IN_PROGRESS
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
                self::PARAMETER_GENERAL_COMMENT => $sendRatings ? $supervisorReviewer->getComment() : null,
                self::PARAMETER_KPIS => $this->getKpisForReview(),
                self::PARAMETER_REVIEWERS => $this->getReviewerForReviewRating(),
                self::PARAMETER_ALLOWED_ACTIONS => $allowedActions,
            ])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getReviewIdParamRule(),
            ...$this->getSortingAndPaginationParamsRules(
                ReviewKpiSearchFilterParams::ALLOWED_SORT_FIELDS
            )
        );
    }

    /**
     * @param PerformanceReview $review
     * @return array|null
     */
    protected function getAllowedActions(PerformanceReview $review): ?array
    {
        $currentState = self::WORKFLOW_STATES_MAP[$this->getPerformanceReviewStatus($review)];

        $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_REVIEW,
            $currentState
        );

        foreach ($allowedWorkflowItems as $allowedWorkflowItem) {
            $allowedWorkflowItem->setAction(self::ACTIONABLE_STATES_MAP[$allowedWorkflowItem->getAction()]);
        }
        return $this->getNormalizerService()->normalizeArray(
            WorkflowStateModel::class,
            $allowedWorkflowItems,
        );
    }

    /**
     * @param SupervisorEvaluationSearchFilterParams $evaluationParamHolder
     * @return ReviewerRating[]
     */
    protected function getReviewerRatings(SupervisorEvaluationSearchFilterParams $evaluationParamHolder): array
    {
        return $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()->getReviewerRating($evaluationParamHolder, ReviewerGroup::REVIEWER_GROUP_SUPERVISOR);
    }

    /**
     * @param SupervisorEvaluationSearchFilterParams $evaluationParamHolder
     * @return int
     */
    protected function getReviewerRatingCount(SupervisorEvaluationSearchFilterParams $evaluationParamHolder): int
    {
        return $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()->getReviewerRatingCount($evaluationParamHolder, ReviewerGroup::REVIEWER_GROUP_SUPERVISOR);
    }

    /**
     * @return ParamRule
     */
    private function getReviewIdParamRule(): ParamRule
    {
        return new ParamRule(
            self::PARAMETER_REVIEW_ID,
            new Rule(Rules::POSITIVE),
            new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceReview::class])
        );
    }

    /**
     * @return array
     */
    protected function getKpisForReview(): array
    {
        $reviewKpiParamHolder = new ReviewKpiSearchFilterParams();
        $reviewKpiParamHolder->setReviewId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_REVIEW_ID
            )
        );
        $reviewKpiParamHolder->setReviewerGroupName(ReviewerGroup::REVIEWER_GROUP_SUPERVISOR);
        $this->setSortingAndPaginationParams($reviewKpiParamHolder);
        return $this->getNormalizerService()->normalizeArray(
            KpiSummaryModel::class,
            $this->getPerformanceReviewService()->getPerformanceReviewDao()
                ->getKpisForReview($reviewKpiParamHolder)
        );
    }

    /**
     * @return array
     */
    protected function getReviewerForReviewRating(): array
    {
        $reviewId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_REVIEW_ID
        );

        return $this->getNormalizerService()->normalize(
            SupervisorReviewerModel::class,
            $this->getPerformanceReviewService()->getPerformanceReviewDao()
                ->getReviewerRecord($reviewId, ReviewerGroup::REVIEWER_GROUP_SUPERVISOR)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
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

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @OA\Put(
     *     path="/api/v2/performance/reviews/{reviewId}/evaluation/supervisor",
     *     tags={"Performance/Review Evaluation by Supervisor"},
     *     summary="Update the Supervisor's Evaluation in a Review",
     *     operationId="update-the-supervisors-evaluation-in-a-review",
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
     *             required={"reviewers"}
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
    public function update(): EndpointResult
    {
        $supervisorParamHolder = new SupervisorEvaluationSearchFilterParams();
        $supervisorParamHolder->setReviewId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_REVIEW_ID
            )
        );
        $this->setSortingAndPaginationParams($supervisorParamHolder);

        $this->beginTransaction();
        try {
            $reviewId = $this->getRequestParams()
                ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_REVIEW_ID);
            $review = $this->getPerformanceReviewService()->getPerformanceReviewDao()
                ->getReviewById($reviewId);

            $actionAllowed = $this->checkActionAllowed($review);
            if (!$actionAllowed) {
                throw $this->getForbiddenException();
            }

            $comment = $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_GENERAL_COMMENT,
            );

            $this->setReviewRatingsParams($review);
            $this->updateReviewerStatus($review);
            $this->updateReviewerComment($review, $comment);
            $this->updateReviewStatus($review);

            $reviewRatings = $this->getReviewerRatings($supervisorParamHolder);
            $this->commitTransaction();
            return new EndpointCollectionResult(
                ReviewerRatingModel::class,
                $reviewRatings
            );
        } catch (ForbiddenException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @param PerformanceReview $review
     */
    protected function setReviewRatingsParams(PerformanceReview $review): void
    {
        $ratings = $this->getRequestParams()
            ->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_RATINGS);
        $this->getPerformanceReviewService()->saveAndUpdateReviewRatings(
            $review,
            $ratings,
            ReviewerGroup::REVIEWER_GROUP_SUPERVISOR
        );
    }

    /**
     * @param PerformanceReview $performanceReview
     * @return int
     */
    private function getPerformanceReviewStatus(PerformanceReview $performanceReview): int
    {
        if (!$this->isAdminAccessingApi() && $this->getAuthUser()->getEmpNumber() === $performanceReview->getEmployee()->getEmpNumber()) {
            $selfReviewer = $this->getPerformanceReviewService()
                ->getPerformanceReviewDao()
                ->getPerformanceSelfReviewer($performanceReview);
            // Self status => 1 (activated), 2 (in progress), 3 (completed)
            // Add 1 and return to match the overall status id
            return $selfReviewer->getStatus() + 1;
        }
        return $performanceReview->getStatusId();
    }

    /**
     * @param PerformanceReview $review
     * @return bool
     */
    protected function checkActionAllowed(PerformanceReview $review): bool
    {
        $currentState = self::WORKFLOW_STATES_MAP[$this->getPerformanceReviewStatus($review)];

        $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_REVIEW,
            $currentState
        );
        $hasPermission = false;
        foreach ($allowedWorkflowItems as $allowedWorkflowItem) {
            if ($allowedWorkflowItem->getResultingState() == self::ACTION_COMPLETE || $allowedWorkflowItem->getResultingState() == self::ACTION_IN_PROGRESS) {
                $hasPermission = true;
                break;
            }
        }
        return $hasPermission;
    }

    /**
     * @param PerformanceReview $review
     */
    protected function updateReviewerStatus(PerformanceReview $review): void
    {
        $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->updateReviewerStatus(
                $review,
                ReviewerGroup::REVIEWER_GROUP_SUPERVISOR,
                Reviewer::STATUS_IN_PROGRESS
            );
    }

    /**
     * @param PerformanceReview $review
     */
    protected function updateReviewStatus(PerformanceReview $review): void
    {
        if ($review->getStatusId() === PerformanceReview::STATUS_ACTIVATED) {
            $review->setStatusId(PerformanceReview::STATUS_IN_PROGRESS);
            $this->getPerformanceReviewService()
                ->getPerformanceReviewDao()
                ->savePerformanceReview($review);
        }
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
                ->updateReviewerComment($review, ReviewerGroup::REVIEWER_GROUP_SUPERVISOR, $comment);
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getReviewIdParamRule(),
            new ParamRule(
                self::PARAMETER_RATINGS,
                new Rule(
                    ReviewReviewerRatingParamRule::class,
                    [$this->getRequest()->getAttributes()->get(self::PARAMETER_REVIEW_ID)]
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_GENERAL_COMMENT,
                    new Rule(Rules::STRING_TYPE),
                ),
                true
            )
        );
    }

    private function isAdminAccessingApi(): bool
    {
        $permission = $this->getUserRoleManager()->getDataGroupPermissions(
            'apiv2_performance_review_supervisor_evaluation',
            ['ESS', 'Supervisor']
        );

        return $permission->canRead() || $permission->canUpdate();
    }
}
