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
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\Performance\Api\Model\KpiSummaryModel;
use OrangeHRM\Performance\Dto\ReviewKpiSearchFilterParams;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class ReviewKpiAPI extends Endpoint implements CollectionEndpoint
{
    use PerformanceReviewServiceTrait;
    use UserRoleManagerTrait;

    public const PARAMETER_REVIEW_ID = 'reviewId';

    /**
     * @OA\Get(
     *     path="/api/v2/performance/reviews/{reviewId}/kpis",
     *     tags={"Performance/Review Evaluation"},
     *     summary="List KPIs for a Review",
     *     operationId="list-kpis-for-a-review",
     *     @OA\PathParameter(
     *         name="reviewId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=ReviewKpiSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Performance-KpiSummaryModel")
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
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
        $reviewKpis = $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->getKpisForReview($reviewKpiParamHolder);
        $reviewCount = $this->getPerformanceReviewService()->getPerformanceReviewDao()
            ->getKpisForReviewCount($reviewKpiParamHolder);

        return new EndpointCollectionResult(
            KpiSummaryModel::class,
            $reviewKpis,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $reviewCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_REVIEW_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [PerformanceReview::class])
            ),
            ...$this->getSortingAndPaginationParamsRules(
                ReviewKpiSearchFilterParams::ALLOWED_SORT_FIELDS
            )
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
}
