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

namespace OrangeHRM\Dashboard\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Dashboard\Service\EmployeeOnLeaveService;

class EmployeeOnLeaveTodayConfigAPI extends Endpoint implements ResourceEndpoint
{
    use ConfigServiceTrait;

    public const PARAMETER_CONFIG_ENABLED = 'isEnabled';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $isEnabled = $this->getConfigService()
            ->getConfigDao()
            ->getValue(EmployeeOnLeaveService::CONFIG_ONLY_SHOW_EMPLOYEES_REPORTING_TO_ME);
        return new EndpointResourceResult(ArrayModel::class, ['isEnabled' => boolval($isEnabled)]);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $paramsRules = new ParamRuleCollection();
        $paramsRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramsRules;
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $isEnabled = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_CONFIG_ENABLED
        );

        $config = $this->getConfigService()
            ->getConfigDao()
            ->setValue(EmployeeOnLeaveService::CONFIG_ONLY_SHOW_EMPLOYEES_REPORTING_TO_ME, (int)$isEnabled);

        return new EndpointResourceResult(ArrayModel::class, ['isEnabled' => (boolval($config->getValue()))]);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $paramsRules = new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CONFIG_ENABLED,
                new Rule(Rules::BOOL_TYPE)
            )
        );
        $paramsRules->addExcludedParamKey(CommonParams::PARAMETER_ID);
        return $paramsRules;
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
