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
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Performance\Api\Model\PerformanceReviewModel;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;
use OrangeHRM\Performance\Traits\Service\PerformanceReviewServiceTrait;

class PerformanceReviewAPI extends Endpoint implements CrudEndpoint
{
    use PerformanceReviewServiceTrait;

    public const FILTER_EMP_NUMBER = 'empNumber';
    public const FILTER_REVIEWER_EMP_NUMBER = 'reviewerEmpNumber';
    public const FILTER_JOB_TITLE_ID = 'jobTitleId';
    public const FILTER_STATUS_ID = 'statusId';
    public const FILTER_FROM_DATE = 'fromDate';
    public const FILTER_TO_DATE = 'toDate';
    public const FILTER_INCLUDE_EMPLOYEES = 'includeEmployees';

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $performanceReviewSearchFilterParams = new PerformanceReviewSearchFilterParams();
        $this->setSortingAndPaginationParams($performanceReviewSearchFilterParams);

        $performanceReviewSearchFilterParams->setSupervisorId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_REVIEWER_EMP_NUMBER
            )
        );
        $this->getCommonBodyFilterParams($performanceReviewSearchFilterParams);

        $performanceReviewList = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getPerformanceReviewList($performanceReviewSearchFilterParams);
        $performanceReviewCount = $this->getPerformanceReviewService()
            ->getPerformanceReviewDao()
            ->getPerformanceReviewCount($performanceReviewSearchFilterParams);

        return new EndpointCollectionResult(
            PerformanceReviewModel::class,
            $performanceReviewList,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $performanceReviewCount])
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
                    self::FILTER_REVIEWER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_STATUS_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN, [PerformanceReviewSearchFilterParams::PERFORMANCE_REVIEW_STATUSES])
                )
            ),
            ...$this->getCommonBodyFilterParamRules(),
            ...$this->getSortingAndPaginationParamsRules(
                PerformanceReviewSearchFilterParams::PERFORMANCE_REVIEW_ALLOWED_SORT_FIELDS
            )
        );
    }

    /**
     * @param PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams
     */
    protected function getCommonBodyFilterParams(PerformanceReviewSearchFilterParams $performanceReviewSearchFilterParams): void
    {
        $performanceReviewSearchFilterParams->setEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_EMP_NUMBER
            )
        );
        $performanceReviewSearchFilterParams->setJobTitleId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_JOB_TITLE_ID
            )
        );
        $performanceReviewSearchFilterParams->setStatusId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_STATUS_ID
            )
        );
        $performanceReviewSearchFilterParams->setFromDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_FROM_DATE
            )
        );
        $performanceReviewSearchFilterParams->setToDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_TO_DATE
            )
        );
        $performanceReviewSearchFilterParams->setIncludeEmployees(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_INCLUDE_EMPLOYEES,
                PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES_ONLY_CURRENT
            )
        );
    }

    /**
     * @return ParamRule[]
     */
    protected function getCommonBodyFilterParamRules(): array
    {
        return [
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
                new ParamRule(self::FILTER_FROM_DATE, new Rule(Rules::API_DATE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(self::FILTER_TO_DATE, new Rule(Rules::API_DATE))
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_INCLUDE_EMPLOYEES,
                    new Rule(Rules::IN, [PerformanceReviewSearchFilterParams::INCLUDE_EMPLOYEES])
                )
            )
        ];
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
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getPerformanceReviewService()->getPerformanceReviewDao()->deletePerformanceReviews($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::INT_ARRAY)
            )
        );
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
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
