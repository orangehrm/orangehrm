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
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Entity\EmpUsTaxExemption;
use OrangeHRM\Pim\Api\Model\EmpUsTaxExemptionModel;
use OrangeHRM\Pim\Service\EmpUsTaxExemptionService;

class EmpUsTaxExemptionAPI extends Endpoint implements ResourceEndpoint
{
    use ConfigServiceTrait;

    public const PARAMETER_FEDERAL_STATUS = 'federalStatus';
    public const PARAMETER_FEDERAL_EXEMPTIONS = 'federalExemptions';
    public const PARAMETER_TAX_STATE_CODE = 'taxStateCode';
    public const PARAMETER_STATE_STATUS = 'stateStatus';
    public const PARAMETER_STATE_EXEMPTIONS = 'stateExemptions';
    public const PARAMETER_UNEMPLOYMENT_STATE_CODE = 'unemploymentStateCode';
    public const PARAMETER_WORK_STATE_CODE = 'workStateCode';

    /**
     * @var EmpUsTaxExemptionService|null
     */
    protected ?EmpUsTaxExemptionService $empUsTaxExemptionService = null;

    /**
     * @return EmpUsTaxExemptionService
     */
    public function getEmpUsTaxExemptionService(): EmpUsTaxExemptionService
    {
        if (!$this->empUsTaxExemptionService instanceof EmpUsTaxExemptionService) {
            $this->empUsTaxExemptionService = new EmpUsTaxExemptionService();
        }
        return $this->empUsTaxExemptionService;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $empUsTaxExemption = $this->getEmpUsTaxExemptionService()->getEmpUsTaxExemptionDao()->getEmployeeTaxExemption($empNumber);
        if (!$empUsTaxExemption instanceof EmpUsTaxExemption) {
            $empUsTaxExemption = new EmpUsTaxExemption();
            $empUsTaxExemption->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }
        return new EndpointResourceResult(
            EmpUsTaxExemptionModel::class,
            $empUsTaxExemption,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        $this->throwIfDisabled();
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::EQUALS, [0])
            ),
        );
    }

    private function throwIfDisabled(): void
    {
        if (!($this->getConfigService()->showPimTaxExemptions())) {
            throw $this->getForbiddenException();
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResult
    {
        $saveUsTaxExemption = $this->saveTaxExemption();
        return new EndpointResourceResult(
            EmpUsTaxExemptionModel::class,
            $saveUsTaxExemption,
            new ParameterBag(
                [CommonParams::PARAMETER_EMP_NUMBER => $saveUsTaxExemption->getEmployee()->getEmpNumber()]
            )
        );
    }

    /**
     * @return EmpUsTaxExemption
     */
    private function saveTaxExemption(): EmpUsTaxExemption
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $federalStatus = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_FEDERAL_STATUS
        );
        $federalExemptions = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_FEDERAL_EXEMPTIONS
        );
        $taxStateCode = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_TAX_STATE_CODE
        );
        $stateStatus = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_STATE_STATUS
        );
        $stateExemptions = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_STATE_EXEMPTIONS
        );
        $unemploymentStateCode = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_UNEMPLOYMENT_STATE_CODE
        );
        $workStateCode = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_WORK_STATE_CODE
        );
        $empUsTaxExemption = $this->getEmpUsTaxExemptionService()->getEmpUsTaxExemptionDao()->getEmployeeTaxExemption($empNumber);
        if (!$empUsTaxExemption instanceof EmpUsTaxExemption) {
            $empUsTaxExemption = new EmpUsTaxExemption();
            $empUsTaxExemption->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }
        $empUsTaxExemption->setFederalStatus($federalStatus);
        $empUsTaxExemption->setFederalExemptions($federalExemptions);
        $empUsTaxExemption->setState($taxStateCode);
        $empUsTaxExemption->setStateStatus($stateStatus);
        $empUsTaxExemption->setStateExemptions($stateExemptions);
        $empUsTaxExemption->setUnemploymentState($unemploymentStateCode);
        $empUsTaxExemption->setWorkState($workStateCode);
        return $this->getEmpUsTaxExemptionService()->getEmpUsTaxExemptionDao()->saveEmployeeTaxExemption($empUsTaxExemption);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $this->throwIfDisabled();
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::EQUALS, [0])
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_FEDERAL_STATUS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::IN, [
                        [
                            EmpUsTaxExemption::STATUS_SINGLE,
                            EmpUsTaxExemption::STATUS_MARRIED,
                            EmpUsTaxExemption::STATUS_NON_RESIDENT_ALIEN,
                            EmpUsTaxExemption::STATUS_NOT_APPLICABLE
                        ]
                    ]),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_FEDERAL_EXEMPTIONS,
                    new Rule(Rules::LESS_THAN, [100]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TAX_STATE_CODE,
                    new Rule(Rules::PROVINCE_CODE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STATE_STATUS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::IN, [EmpUsTaxExemption::STATUSES]),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STATE_EXEMPTIONS,
                    new Rule(Rules::LESS_THAN, [100]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_UNEMPLOYMENT_STATE_CODE,
                    new Rule(Rules::PROVINCE_CODE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_WORK_STATE_CODE,
                    new Rule(Rules::PROVINCE_CODE),
                ),
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
