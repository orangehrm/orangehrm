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

namespace OrangeHRM\Pim\Api;

use Exception;
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
use OrangeHRM\Core\Exception\CoreServiceException;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;

class OptionalFieldAPI extends Endpoint implements ResourceEndpoint
{
    use ConfigServiceTrait;

    public const PARAMETER_SSN = 'showSSN';
    public const PARAMETER_SIN = 'showSIN';
    public const PARAMETER_TAX_EXEMPTIONS = 'showTaxExemptions';
    public const PARAMETER_DEPRECATED_FIELDS = 'pimShowDeprecatedFields';

    /**
     * @return array
     * @throws CoreServiceException
     */
    private function getParameterArray(): array
    {
        $parameters = [
            self::PARAMETER_DEPRECATED_FIELDS => $this->getConfigService()->showPimDeprecatedFields(),
            self::PARAMETER_SIN => $this->getConfigService()->showPimSIN(),
            self::PARAMETER_SSN => $this->getConfigService()->showPimSSN(),
            self::PARAMETER_TAX_EXEMPTIONS => $this->getConfigService()->showPimTaxExemptions(),
        ];
        return $parameters;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $parameters = $this->getParameterArray();
        return new EndpointResourceResult(ArrayModel::class, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID
            ),
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResult
    {
        $saveConfig = $this->saveOptionalFields();
        return new EndpointResourceResult(ArrayModel::class, $saveConfig);
    }

    /**
     * @throws CoreServiceException
     */
    private function saveOptionalFields(): array
    {
        $showSIN = $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_SIN);
        $showSSN = $this->getRequestParams()->getBoolean(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_SSN);
        $showTaxExemptions = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_TAX_EXEMPTIONS
        );
        $showDeprecatedFields = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_DEPRECATED_FIELDS
        );
        $this->getConfigService()->setShowPimSSN($showSSN);
        $this->getConfigService()->setShowPimSIN($showSIN);
        $this->getConfigService()->setShowPimTaxExemptions($showTaxExemptions);
        $this->getConfigService()->setShowPimDeprecatedFields($showDeprecatedFields);
        return $this->getParameterArray();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
            ),
            new ParamRule(
                self::PARAMETER_DEPRECATED_FIELDS,
                new Rule(Rules::BOOL_VAL),
            ),
            new ParamRule(
                self::PARAMETER_SIN,
                new Rule(Rules::BOOL_VAL),
            ),
            new ParamRule(
                self::PARAMETER_SSN,
                new Rule(Rules::BOOL_VAL),
            ),
            new ParamRule(
                self::PARAMETER_TAX_EXEMPTIONS,
                new Rule(Rules::BOOL_VAL),
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
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
