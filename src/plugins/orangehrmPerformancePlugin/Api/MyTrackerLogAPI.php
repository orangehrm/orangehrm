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
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Performance\Api\Model\PerformanceTrackerLogModel;
use OrangeHRM\Performance\Dto\PerformanceTrackerLogSearchFilterParams;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerLogServiceTrait;

class MyTrackerLogAPI extends Endpoint implements CrudEndpoint
{
    use PerformanceTrackerLogServiceTrait;

    public const PARAMETER_TRACKER_ID = 'trackerId';
    public const PARAMETER_NEGATIVE = 'negative';
    public const PARAMETER_POSITIVE = 'positive';

    public function getAll(): EndpointResult
    {
        $performanceTrackerLogParamHolder = new PerformanceTrackerLogSearchFilterParams();
        $performanceTrackerLogParamHolder->setTrackerId($this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_QUERY, self::PARAMETER_TRACKER_ID));

        $this->setSortingAndPaginationParams($performanceTrackerLogParamHolder);

        $performanceTrackerLogs = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogsByTrackerId($performanceTrackerLogParamHolder);

        $performanceTrackerLogCount = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogCountPerTrackerId($performanceTrackerLogParamHolder);
        $performanceTrackerLogsPositiveRatingCount = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogsRateCount(PerformanceTrackerLog::POSITIVE_RATING, $performanceTrackerLogParamHolder->getTrackerId());
        $performanceTrackerLogsNegativeRatingCount = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLogsRateCount(PerformanceTrackerLog::NEGATIVE_RATING, $performanceTrackerLogParamHolder->getTrackerId());
        return new EndpointCollectionResult(
            PerformanceTrackerLogModel::class,
            $performanceTrackerLogs,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_TOTAL => $performanceTrackerLogCount,
                    self::PARAMETER_POSITIVE => $performanceTrackerLogsPositiveRatingCount,
                    self::PARAMETER_NEGATIVE => $performanceTrackerLogsNegativeRatingCount,
                ]
            ),
        );
    }

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_TRACKER_ID,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getSortingAndPaginationParamsRules(PerformanceTrackerLogSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    public function getOne(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $performanceTrackerLogs = $this->getPerformanceTrackerService()->getPerformanceTrackerLogDao()
            ->getPerformanceTrackerLog($id);
        $this->throwRecordNotFoundExceptionIfNotExist($performanceTrackerLogs, PerformanceTrackerLog::class);
        return new EndpointResourceResult(PerformanceTrackerLogModel::class, $performanceTrackerLogs);
    }

    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(new ParamRule(
            CommonParams::PARAMETER_ID,
            new Rule(Rules::POSITIVE)
        ));
    }

    public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
