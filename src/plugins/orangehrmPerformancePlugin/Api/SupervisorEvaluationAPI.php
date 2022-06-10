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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\NormalizerServiceTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Performance\Api\Model\KpiModel;
use OrangeHRM\Performance\Api\Model\ReviewerModel;
use OrangeHRM\Performance\Api\Model\ReviewerRatingModel;
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

    public const PARAMETER_REVIEW_ID = 'reviewId';
    public const PARAMETER_IS_SELF_EVALUATION = 'selfEvaluation';
    public const PARAMETER_KPIS = 'kpis';
    public const PARAMETER_RATINGS = 'ratings';
    public const PARAMETER_RATING = 'rating';
    public const PARAMETER_COMMENT = 'comment';
    public const PARAMETER_KPI_ID = 'kpiId';
    public const PARAMETER_REVIEWERS = 'reviewer';

    /**
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
        $ratings = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()->getSupervisorRating($supervisorParamHolder);
        $ratingCount = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()->getSupervisorRatingCount($supervisorParamHolder);

        return new EndpointCollectionResult(
            ReviewerRatingModel::class,
            $ratings,
            new ParameterBag([
                CommonParams::PARAMETER_TOTAL => $ratingCount,
                self::PARAMETER_KPIS => $this->getKpisForReview(),
                self::PARAMETER_REVIEWERS => $this->getSupervisorReviewerForReviewRating(),
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
    private function getKpisForReview(): array
    {
        $reviewKpiParamHolder = new ReviewKpiSearchFilterParams();
        $reviewKpiParamHolder->setReviewId(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_REVIEW_ID
            )
        );
        $this->setSortingAndPaginationParams($reviewKpiParamHolder);
        return $this->getNormalizerService()->normalizeArray(
            KpiModel::class,
            $this->getPerformanceReviewService()->getPerformanceReviewDao()
                ->getKpisForReview($reviewKpiParamHolder)
        );
    }

    /**
     * @return array
     */
    private function getSupervisorReviewerForReviewRating(): array
    {
        $reviewId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_REVIEW_ID
        );

        return $this->getNormalizerService()->normalizeArray(
            ReviewerModel::class,
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
                ->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE,self::PARAMETER_REVIEW_ID);
            $review = $this->getPerformanceReviewService()->getPerformanceReviewDao()
                ->getReviewById($reviewId);
            $ratings = $this->getRequestParams()
                ->getArray(RequestParams::PARAM_TYPE_BODY,self::PARAMETER_RATINGS);

            $kpisForReview = $this->getKpisForReview();
            $this->getPerformanceReviewService()->saveAndUpdateReviewRatings($review,$ratings,$kpisForReview);

            $reviewRatings = $this->getPerformanceReviewService()->getPerformanceReviewDao()
                ->getSupervisorRating($supervisorParamHolder);
            $this->commitTransaction();
            return new EndpointCollectionResult(
                ReviewerRatingModel::class,
                $reviewRatings
            );
        } catch (RecordNotFoundException|ForbiddenException|BadRequestException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
        $this->rollBackTransaction();
        throw new TransactionException($e);

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
            )
        );
    }
}
