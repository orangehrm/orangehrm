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

use Exception;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\InAccessibleEntityIdOption;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Performance\Api\Model\CompletedPerformanceReviewModel;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class PerformanceReviewFinalEvaluationAPI extends Endpoint implements ResourceEndpoint
{
    use EntityManagerHelperTrait;
    use PerformanceReviewServiceTrait;

    public const PARAMETER_REVIEW_ID = 'reviewId';
    public const PARAMETER_FINAL_RATING = 'finalRating';
    public const PARAMETER_FINAL_COMMENT = 'finalComment';
    public const PARAMETER_COMPLETED_DATE = 'completedDate';
    public const PARAMETER_COMPLETE = 'complete';

    public const PARAM_RULE_FINAL_RATING_MIN_VALUE = 0;
    public const PARAM_RULE_FINAL_RATING_MAX_VALUE = 100;

    /**
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
        $this->throwBadRequestExceptionIfStatusInArray(
            $review->getStatusId(),
            [PerformanceReview::STATUS_INACTIVE, PerformanceReview::STATUS_COMPLETED]
        );

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
                new Rule(Rules::FLOAT_TYPE),
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
        $this->throwBadRequestExceptionIfStatusInArray(
            $review->getStatusId(),
            [PerformanceReview::STATUS_INACTIVE]
        );

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
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceReview::class])
            )
        );
    }

    /**
     * @param int $statusId
     * @param int[] $statuses
     * @throws BadRequestException
     */
    private function throwBadRequestExceptionIfStatusInArray(int $statusId, array $statuses): void
    {
        if (in_array($statusId, $statuses)) {
            throw $this->getBadRequestException();
        }
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
