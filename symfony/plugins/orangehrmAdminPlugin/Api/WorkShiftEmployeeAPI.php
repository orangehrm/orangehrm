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

namespace OrangeHRM\Admin\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Api\Model\EmployeeDetailedModel;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Service\Model\EmployeeModel;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class WorkShiftEmployeeAPI extends EndPoint implements CollectionEndpoint
{
    use EmployeeServiceTrait;
    use UserRoleManagerTrait;

    public const FILTER_NAME = 'name';
    public const FILTER_EMP_NUMBER = 'empNumber';
    public const FILTER_NAME_OR_ID = 'nameOrId';
    public const FILTER_EMPLOYEE_ID = 'employeeId';
    public const FILTER_INCLUDE_EMPLOYEES = 'includeEmployees';
    public const FILTER_EMP_STATUS_ID = 'empStatusId';
    public const FILTER_JOB_TITLE_ID = 'jobTitleId';
    public const FILTER_SUBUNIT_ID = 'subunitId';
    public const FILTER_SUPERVISOR_EMP_NUMBERS = 'supervisorEmpNumbers';
    public const FILTER_MODEL = 'model';
    public const PARAM_RULE_EMPLOYEE_ID_MAX_LENGTH = 50;

    public const PARAM_RULE_FILTER_NAME_MAX_LENGTH = 100;
    public const PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH = 100;

    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';
    public const MODEL_MAP = [
        self::MODEL_DEFAULT => EmployeeModel::class,
        self::MODEL_DETAILED => EmployeeDetailedModel::class,
    ];

    public function getAll(): EndpointResult
    {
        $employeeParamHolder = new EmployeeSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeParamHolder);

        $empNumber = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_EMP_NUMBER
        );
        if (!is_null($empNumber)) {
            $employeeParamHolder->setEmployeeNumbers([$empNumber]);
        } else {
            $accessibleEmpNumbers = $this->getUserRoleManager()->getAccessibleEntityIds(Employee::class);
            $employeeParamHolder->setEmployeeNumbers($accessibleEmpNumbers);
        }

        $employeeParamHolder->setIncludeEmployees(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_INCLUDE_EMPLOYEES
            )
        );
        $employeeParamHolder->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $employeeParamHolder->setNameOrId(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME_OR_ID
            )
        );
        $employeeParamHolder->setEmployeeId(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_EMPLOYEE_ID
            )
        );
        $employeeParamHolder->setEmpStatusId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_EMP_STATUS_ID
            )
        );
        $employeeParamHolder->setJobTitleId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_JOB_TITLE_ID
            )
        );
        $employeeParamHolder->setSubunitId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_SUBUNIT_ID
            )
        );
        $employeeParamHolder->setSupervisorEmpNumbers(
            $this->getRequestParams()->getArrayOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_SUPERVISOR_EMP_NUMBERS
            )
        );
        $count = $this->getEmployeeService()->getEmployeeCount($employeeParamHolder);
        $employee = $this->getEmployeeService()->getAvailableEmployeesForWorkShift($employeeParamHolder);
        return new EndpointCollectionResult(
            $this->getModelClass(),
            $employee,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
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

    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_INCLUDE_EMPLOYEES,
                    new Rule(
                        Rules::IN,
                        [
                            array_merge(
                                array_keys(EmployeeSearchFilterParams::INCLUDE_EMPLOYEES_MAP),
                                array_values(EmployeeSearchFilterParams::INCLUDE_EMPLOYEES_MAP)
                            )
                        ]
                    )
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_FILTER_NAME_MAX_LENGTH]),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_EMP_NUMBER,
                    new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
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
                    self::FILTER_EMPLOYEE_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_EMPLOYEE_ID_MAX_LENGTH]),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_EMP_STATUS_ID,
                    new Rule(Rules::POSITIVE),
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
                    self::FILTER_SUBUNIT_ID,
                    new Rule(Rules::POSITIVE),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_SUPERVISOR_EMP_NUMBERS,
                    new Rule(Rules::ARRAY_TYPE),
                )
            ),
            $this->getModelParamRule(),
            ...$this->getSortingAndPaginationParamsRules(EmployeeSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    protected function getModelParamRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                self::FILTER_MODEL,
                new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
            )
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
}
