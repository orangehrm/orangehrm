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
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class ValidationEmployeeOtherEmailAPI extends Endpoint implements ResourceEndpoint
{
    use EmployeeServiceTrait;

    public const PARAMETER_OTHER_EMAIL = 'otherEmail';
    public const PARAMETER_IS_UNIQUE_OTHER_EMAIL = 'valid';

    public const PARAM_RULE_OTHER_EMAIL_MAX_LENGTH = 50;

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/contact-details/validation/other-emails",
     *     tags={"PIM/Employee Email Validation"},
     *     summary="Validate an Employee's Other Email",
     *     operationId="validate-an-employee-other-email",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="otherEmail",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             maxLength=OrangeHRM\Pim\Api\ValidationEmployeeOtherEmailAPI::PARAM_RULE_OTHER_EMAIL_MAX_LENGTH
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 @OA\Property(property="valid", type="boolean")
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $workEmail = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::PARAMETER_OTHER_EMAIL
        );
        $employee = $this->getEmployeeService()->getEmployeeDao()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);
        $isChangeableWorkEmail = $this->getEmployeeService()
            ->isUniqueEmail(
                $workEmail,
                $employee->getOtherEmail()
            );
        return new EndpointResourceResult(
            ArrayModel::class,
            [
                self::PARAMETER_IS_UNIQUE_OTHER_EMAIL => $isChangeableWorkEmail,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_OTHER_EMAIL,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_OTHER_EMAIL_MAX_LENGTH]),
            ),
            new ParamRule(CommonParams::PARAMETER_EMP_NUMBER),
        );
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
