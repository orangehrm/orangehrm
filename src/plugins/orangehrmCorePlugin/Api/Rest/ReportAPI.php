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

namespace OrangeHRM\Core\Api\Rest;

use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;
use OrangeHRM\Core\Report\Api\EndpointProxy;

abstract class ReportAPI extends EndpointProxy implements ResourceEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_HEADERS = 'headers';
    public const PARAMETER_FILTERS = 'filters';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $report = $this->getReport();
        $report->checkReportAccessibility($this);
        $header = $report->getHeaderDefinition();
        $filter = $report->getFilterDefinition();

        return new EndpointResourceResult(
            ArrayModel::class,
            [
                self::PARAMETER_HEADERS => $header->normalize(),
                self::PARAMETER_FILTERS => $filter->normalize(),
            ],
            new ParameterBag(
                [
                    self::PARAMETER_HEADERS => is_null($header->getMeta()) ? null : $header->getMeta()->all(),
                    self::PARAMETER_FILTERS => is_null($filter->getMeta()) ? null : $filter->getMeta()->all(),
                ]
            )
        );
    }

    /**
     * @return EndpointAwareReport
     */
    abstract protected function getReport(): EndpointAwareReport;

    /**
     * @return string
     */
    protected function getReportName(): string
    {
        return $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_QUERY, ReportAPI::PARAMETER_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection($this->getReportNameParamRule());
    }

    /**
     * @return ParamRule
     */
    protected function getReportNameParamRule(): ParamRule
    {
        return new ParamRule(ReportAPI::PARAMETER_NAME, new Rule(Rules::STRING_TYPE));
    }

    /**
     * @inheritDoc
     */
    final public function update(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    final public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    final public function delete(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    final public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }
}
