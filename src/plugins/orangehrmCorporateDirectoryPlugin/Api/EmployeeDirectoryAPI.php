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

namespace OrangeHRM\CorporateDirectory\Api;

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
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\CorporateDirectory\Api\Model\EmployeeDirectoryDetailedModel;
use OrangeHRM\CorporateDirectory\Api\Model\EmployeeDirectoryModel;
use OrangeHRM\CorporateDirectory\Dto\EmployeeDirectorySearchFilterParams;
use OrangeHRM\CorporateDirectory\Service\EmployeeDirectoryService;
use OrangeHRM\Entity\Employee;

class EmployeeDirectoryAPI extends Endpoint implements CrudEndpoint
{
    use UserRoleManagerTrait;

    public const FILTER_EMP_NUMBER = 'empNumber';
    public const FILTER_NAME_OR_ID = 'nameOrId';
    public const FILTER_JOB_TITLE_ID = 'jobTitleId';
    public const FILTER_LOCATION_ID = 'locationId';
    public const FILTER_MODEL = 'model';
    public const PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH = 100;
    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';
    public const MODEL_MAP = [
        self::MODEL_DEFAULT => EmployeeDirectoryModel::class,
        self::MODEL_DETAILED => EmployeeDirectoryDetailedModel::class,
    ];

    /**
     * @OA\Get(
     *     path="/api/v2/directory/employees/{empNumber}",
     *     tags={"Directory/Employees"},
     *     summary="Get an Employee Directory Listing",
     *     operationId="get-an-employee-directory-listing",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={OrangeHRM\CorporateDirectory\Api\EmployeeDirectoryAPI::MODEL_DEFAULT, OrangeHRM\CorporateDirectory\Api\EmployeeDirectoryAPI::MODEL_DETAILED, OrangeHRM\CorporateDirectory\Api\EmployeeDirectoryAPI::MODEL_DETAILED},
     *             default=OrangeHRM\CorporateDirectory\Api\EmployeeDirectoryAPI::MODEL_DEFAULT
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     oneOf={
     *                         @OA\Schema(ref="#/components/schemas/CorporateDirectory-EmployeeDirectoryModel"),
     *                         @OA\Schema(ref="#/components/schemas/CorporateDirectory-EmployeeDirectoryDetailedModel"),
     *                     }
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employee = $this->getEmployeeDirectoryService()->getEmployeeDirectoryDao()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        return new EndpointResourceResult($this->getModelClass(), $employee);
    }

    /**
     * @return EmployeeDirectoryService
     */
    public function getEmployeeDirectoryService(): EmployeeDirectoryService
    {
        return new EmployeeDirectoryService();
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        $model = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODEL,
            self::MODEL_DEFAULT
        );
        return self::MODEL_MAP[$model];
    }


    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::ENTITY_ID_EXISTS, [Employee::class])
            ),
            $this->getModelParamRule(),
        );
    }

    /**
     * @return ParamRule
     */
    protected function getModelParamRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                self::FILTER_MODEL,
                new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
            )
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/directory/employees",
     *     tags={"Directory/Employees"},
     *     summary="Get the Employee Directory",
     *     operationId="get-the-employee-directory",
     *     @OA\Parameter(
     *         name="empNumber",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="nameOrId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="jobTitleId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="locationId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={
     *                 OrangeHRM\CorporateDirectory\Api\EmployeeDirectoryAPI::MODEL_DEFAULT,
     *                 OrangeHRM\CorporateDirectory\Api\EmployeeDirectoryAPI::MODEL_DETAILED
     *             },
     *             default=OrangeHRM\CorporateDirectory\Api\EmployeeDirectoryAPI::MODEL_DEFAULT
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     oneOf={
     *                         @OA\Schema(ref="#/components/schemas/CorporateDirectory-EmployeeDirectoryModel"),
     *                         @OA\Schema(ref="#/components/schemas/CorporateDirectory-EmployeeDirectoryDetailedModel"),
     *                     }
     *                 )
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $employeeDirectoryParamHolder = new EmployeeDirectorySearchFilterParams();
        $this->setSortingAndPaginationParams($employeeDirectoryParamHolder);

        $empNumber = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_EMP_NUMBER
        );
        if (!is_null($empNumber)) {
            $employeeDirectoryParamHolder->setEmpNumbers([$empNumber]);
        }
        $employeeDirectoryParamHolder->setNameOrId(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME_OR_ID
            )
        );
        $employeeDirectoryParamHolder->setJobTitleId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_JOB_TITLE_ID
            )
        );
        $employeeDirectoryParamHolder->setLocationId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_LOCATION_ID
            )
        );

        $employees = $this->getEmployeeDirectoryService()->getEmployeeDirectoryDao()->getEmployeeList(
            $employeeDirectoryParamHolder
        );
        $count = $this->getEmployeeDirectoryService()->getEmployeeDirectoryDao()->getEmployeeCount(
            $employeeDirectoryParamHolder
        );
        return new EndpointCollectionResult(
            $this->getModelClass(),
            $employees,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
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
                    new Rule(Rules::ENTITY_ID_EXISTS, [Employee::class])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_NAME_OR_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH]),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_JOB_TITLE_ID,
                    new Rule(Rules::POSITIVE),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_LOCATION_ID,
                    new Rule(Rules::POSITIVE),
                )
            ),
            $this->getModelParamRule(),
            ...$this->getSortingAndPaginationParamsRules(EmployeeDirectorySearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
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
