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

namespace OrangeHRM\Pim\Api;

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
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/tax-exemption",
     *     tags={"PIM/Employee US Tax Exemption"},
     *     summary="Get an Employee's US Tax Exemption Details",
     *     operationId="get-an-employees-us-tax-expemption-details",
     *     description="This endpoint allows you to retrieve an employee's US Tax Exemption details.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the numerical employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmpUsTaxExemptionModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The given numerical employee number of the employee", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
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
     * @OA\Put(
     *     path="/api/v2/pim/employees/{empNumber}/tax-exemption",
     *     tags={"PIM/Employee US Tax Exemption"},
     *     summary="Update an Employee's US Tax Exemption Details",
     *     operationId="update-an-employees-us-tax-exemption-details",
     *     description="This endpoint allows you to update an employee's tax exemption details.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the numerical employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="federalStatus", description="Specify the federal status of the employee", type="string"),
     *             @OA\Property(property="federalExemptions", description="Specify the federal exemption of the employee", type="integer"),
     *             @OA\Property(property="taxStateCode", description="Specify the tax state code", type="string"),
     *             @OA\Property(property="stateStatus", description="Specify the tax state status of the employee", type="string"),
     *             @OA\Property(property="stateExemptions", description="Specify the tax state exemptions of the employee", type="integer"),
     *             @OA\Property(property="unemploymentStateCode", description="Specify the unemployment state", type="string"),
     *             @OA\Property(property="workStateCode", description="Specify the work state", type="string")
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmpUsTaxExemptionModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The given numerical employee number of the employee", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
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
