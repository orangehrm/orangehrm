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

use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayCollectionModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Report\Api\EndpointAwareReport;
use OrangeHRM\Core\Report\Api\EndpointProxy;
use OrangeHRM\Core\Traits\ValidatorTrait;

abstract class ReportDataAPI extends EndpointProxy implements CollectionEndpoint
{
    use ValidatorTrait;

    private ?ParamRuleCollection $paramRules = null;

    /**
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $report = $this->getReport();
        $report->checkReportAccessibility($this);

        $validationRule = $report->getValidationRule($this);
        $excludedParamKeys = array_keys($this->getValidationRuleForGetAll()->getMap());
        foreach ($excludedParamKeys as $excludedParamKey) {
            $validationRule->addExcludedParamKey($excludedParamKey);
        }
        $this->validate($this->getRequest()->getAllParameters(), $validationRule);

        $filterParams = $report->prepareFilterParams($this);
        $data = $report->getData($filterParams);

        return new EndpointCollectionResult(
            ArrayCollectionModel::class,
            $data->normalize(),
            $data->getMeta()
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
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        if (!$this->paramRules instanceof ParamRuleCollection) {
            $this->paramRules = new ParamRuleCollection($this->getReportNameParamRule());
            // Not validation additional parameter, let it validate within getAll
            $this->paramRules->setStrict(false);
        }
        return $this->paramRules;
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
    final public function create(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    final public function getValidationRuleForCreate(): ParamRuleCollection
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
