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

use Exception;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\InAccessibleEntityIdOption;
use OrangeHRM\Core\Authorization\UserRole\EssUserRole;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Performance\Api\Model\CompletedPerformanceReviewModel;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class PerformanceReviewFinalEvaluationAPI extends Endpoint implements ResourceEndpoint
{
    use EntityManagerHelperTrait;
    use UserRoleManagerTrait;
    use PerformanceReviewServiceTrait;

    public const PARAMETER_REVIEW_ID = 'reviewId';
    public const PARAMETER_FINAL_RATING = 'finalRating';
    public const PARAMETER_FINAL_COMMENT = 'finalComment';
    public const PARAMETER_COMPLETED_DATE = 'completedDate';
    public const PARAMETER_COMPLETE = 'complete';

    public const WORKFLOW_STATES_MAP = [
        PerformanceReview::STATUS_INACTIVE => 'SAVED',
        PerformanceReview::STATUS_ACTIVATED => 'ACTIVATED',
        PerformanceReview::STATUS_IN_PROGRESS => 'IN PROGRESS',
        PerformanceReview::STATUS_COMPLETED => 'COMPLETED'
    ];

    public const PARAM_RULE_FINAL_RATING_MIN_VALUE = 0;
    public const PARAM_RULE_FINAL_RATING_MAX_VALUE = 100;

    /**
     * @OA\Put(
     *     path="/api/v2/performance/reviews/{reviewId}/evaluation/final",
     *     tags={"Performance/Review Evaluation"},
     *     summary="Finalize Performance Review",
     *     operationId="finalize-performance-review",
     *     @OA\PathParameter(
     *         name="reviewId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="finalRating", type="string"),
     *             @OA\Property(property="completedDate", type="number"),
     *             @OA\Property(property="finalComment", type="string"),
     *             @OA\Property(property="complete", type="string"),
     *             required={"finalRating", "completedDate", "finalComment", "complete"}
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
        $reviewId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_REVIEW_ID
        );

        $complete = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_COMPLETE
        );

        $review = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getPerformanceReviewById($reviewId);

        $this->throwRecordNotFoundExceptionIfNotExist($review, PerformanceReview::class);

        if (!$this->getUserRoleManager()->isActionAllowed(
            WorkflowStateMachine::FLOW_REVIEW,
            self::WORKFLOW_STATES_MAP[$review->getStatusId()],
            $complete ? WorkflowStateMachine::REVIEW_COMPLETE : WorkflowStateMachine::REVIEW_IN_PROGRESS_SAVE
        )) {
            throw $this->getForbiddenException();
        }

        $this->setFinalEvaluation($review);

        $this->beginTransaction();
        try {
            if ($complete) {
                $this->getPerformanceReviewService()
                    ->getPerformanceReviewDao()
                    ->setReviewerStatusToCompleted($reviewId);
                $review->setStatusId(PerformanceReview::STATUS_COMPLETED);
            }
            $review = $this->getPerformanceReviewService()
                ->getPerformanceReviewDao()
                ->savePerformanceReview($review);
            $this->commitTransaction();
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }

        return new EndpointResourceResult(CompletedPerformanceReviewModel::class, $review);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_REVIEW_ID,
                new Rule(Rules::POSITIVE),
                new Rule(
                    Rules::IN_ACCESSIBLE_ENTITY_ID,
                    [
                        PerformanceReview::class,
                        (new InAccessibleEntityIdOption())
                            ->setRolesToExclude(['ESS'])
                    ]
                )
            ),
            new ParamRule(
                self::PARAMETER_COMPLETE,
                new Rule(Rules::BOOL_TYPE)
            ),
            ...$this->getFinalEvaluationBodyParamRules()
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getFinalEvaluationBodyParamRules(): array
    {
        $paramRules = [
            new ParamRule(
                self::PARAMETER_FINAL_RATING,
                new Rule(Rules::NUMBER),
                new Rule(
                    Rules::BETWEEN,
                    [self::PARAM_RULE_FINAL_RATING_MIN_VALUE, self::PARAM_RULE_FINAL_RATING_MAX_VALUE]
                )
            ),
            new ParamRule(
                self::PARAMETER_FINAL_COMMENT,
                new Rule(Rules::STRING_TYPE)
            ),
            new ParamRule(
                self::PARAMETER_COMPLETED_DATE,
                new Rule(Rules::API_DATE)
            ),
        ];

        $complete = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_COMPLETE
        );

        if (!$complete) {
            return array_map(function ($rule) {
                return $this->getValidationDecorator()
                    ->notRequiredParamRule($rule);
            }, $paramRules);
        }
        return $paramRules;
    }

    /**
     * @param PerformanceReview $performanceReview
     */
    private function setFinalEvaluation(PerformanceReview $performanceReview): void
    {
        $performanceReview->setFinalRate(
            $this->getRequestParams()->getFloatOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_FINAL_RATING,
            )
        );
        $performanceReview->setFinalComment(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_FINAL_COMMENT
            )
        );
        $performanceReview->setCompletedDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_COMPLETED_DATE
            )
        );
        $performanceReview->setStatusId(
            PerformanceReview::STATUS_IN_PROGRESS
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/performance/reviews/{reviewId}/evaluation/final",
     *     tags={"Performance/Review Evaluation"},
     *     summary="Get a Finalized Performance Review",
     *     operationId="get-a-finalized-performance-revew",
     *     @OA\PathParameter(
     *         name="reviewId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Performance-CompletedPerformanceReviewModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $reviewId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_REVIEW_ID
        );

        $review = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getPerformanceReviewById($reviewId);

        $this->throwRecordNotFoundExceptionIfNotExist($review, PerformanceReview::class);

        if ($review->getStatusId() === PerformanceReview::STATUS_INACTIVE) {
            throw $this->getForbiddenException();
        }

        return new EndpointResourceResult(CompletedPerformanceReviewModel::class, $review);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_REVIEW_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [
                    PerformanceReview::class,
                    (new InAccessibleEntityIdOption())
                        ->setRequiredPermissions(
                            [EssUserRole::ALLOWED_REVIEW_STATUSES => [PerformanceReview::STATUS_COMPLETED]]
                        )
                ])
            )
        );
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
