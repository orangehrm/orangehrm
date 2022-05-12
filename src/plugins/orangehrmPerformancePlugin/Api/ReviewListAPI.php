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
use OrangeHRM\Performance\Api\Model\PerformanceReviewModel;
use OrangeHRM\Performance\Dto\ReviewListSearchFilterParams;
use OrangeHRM\Performance\Service\PerformanceReviewService;

class ReviewListAPI extends Endpoint implements CollectionEndpoint
{
    private ?PerformanceReviewService $performanceReviewService = null;

    public const FILTER_EMP_NUMBER = 'empNumber';
    public const FILTER_JOB_TITLE_ID = 'jobTitleId';
    public const FILTER_SUB_UNIT_ID = 'subUnitId';
    public const FILTER_STATUS_ID = 'statusId';
    public const FILTER_FROM_DATE = 'fromDate';
    public const FILTER_TO_DATE = 'toDate';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $reviewListSearchFilterParams = new ReviewListSearchFilterParams();
        $this->setSortingAndPaginationParams($reviewListSearchFilterParams);

        $reviewListSearchFilterParams->setEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_EMP_NUMBER
            )
        );
        $reviewListSearchFilterParams->setJobTitleId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_JOB_TITLE_ID
            )
        );
        $reviewListSearchFilterParams->setSubunitId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_SUB_UNIT_ID
            )
        );
        $reviewListSearchFilterParams->setStatusId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_STATUS_ID
            )
        );
        $reviewListSearchFilterParams->setFromDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_FROM_DATE
            )
        );
        $reviewListSearchFilterParams->setToDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_TO_DATE
            )
        );

        $reviewList = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getReviewList($reviewListSearchFilterParams);
        $reviewListCount = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getReviewListCount($reviewListSearchFilterParams);

        return new EndpointCollectionResult(
            PerformanceReviewModel::class,
            $reviewList,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $reviewListCount])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_JOB_TITLE_ID, new Rule(Rules::POSITIVE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_SUB_UNIT_ID, new Rule(Rules::POSITIVE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_STATUS_ID, new Rule(Rules::POSITIVE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_FROM_DATE, new Rule(Rules::API_DATE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_TO_DATE, new Rule(Rules::API_DATE))
            ),
            ...$this->getSortingAndPaginationParamsRules(
                ReviewListSearchFilterParams::ALLOWED_SORT_FIELDS
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

    /**
     * @return PerformanceReviewService
     */
    private function getPerformanceReviewService(): PerformanceReviewService
    {
        if (!$this->performanceReviewService instanceof PerformanceReviewService) {
            $this->performanceReviewService = new PerformanceReviewService();
        }
        return $this->performanceReviewService;
    }
}
