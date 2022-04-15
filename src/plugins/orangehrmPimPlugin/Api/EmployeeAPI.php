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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmpPicture;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Pim\Api\Model\EmployeeDetailedModel;
use OrangeHRM\Pim\Api\Model\EmployeeModel;
use OrangeHRM\Pim\Dto\EmployeeSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeePictureService;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeAPI extends Endpoint implements CrudEndpoint
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

    public const PARAMETER_FIRST_NAME = 'firstName';
    public const PARAMETER_MIDDLE_NAME = 'middleName';
    public const PARAMETER_LAST_NAME = 'lastName';
    public const PARAMETER_EMPLOYEE_ID = 'employeeId';
    public const PARAMETER_EMP_PICTURE = 'empPicture';

    public const PARAM_RULE_FIRST_NAME_MAX_LENGTH = 30;
    public const PARAM_RULE_MIDDLE_NAME_MAX_LENGTH = 30;
    public const PARAM_RULE_LAST_NAME_MAX_LENGTH = 30;
    public const PARAM_RULE_EMPLOYEE_ID_MAX_LENGTH = 50;
    public const PARAM_RULE_EMP_PICTURE_FILE_NAME_MAX_LENGTH = 100;
    public const PARAM_RULE_FILTER_NAME_MAX_LENGTH = 100;
    public const PARAM_RULE_FILTER_NAME_OR_ID_MAX_LENGTH = 100;

    public const MODEL_DEFAULT = 'default';
    public const MODEL_DETAILED = 'detailed';
    public const MODEL_MAP = [
        self::MODEL_DEFAULT => EmployeeModel::class,
        self::MODEL_DETAILED => EmployeeDetailedModel::class,
    ];

    /**
     * @var EmployeePictureService|null
     */
    protected ?EmployeePictureService $employeePictureService = null;

    /**
     * @return EmployeePictureService
     */
    public function getEmployeePictureService(): EmployeePictureService
    {
        if (!$this->employeePictureService instanceof EmployeePictureService) {
            $this->employeePictureService = new EmployeePictureService();
        }
        return $this->employeePictureService;
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
        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        return new EndpointResourceResult($this->getModelClass(), $employee);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            $this->getModelParamRule(),
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
    public function getAll(): EndpointCollectionResult
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

        $employees = $this->getEmployeeService()->getEmployeeList($employeeParamHolder);
        $count = $this->getEmployeeService()->getEmployeeCount($employeeParamHolder);
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

    /**
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $allowedToAddEmployee = $this->getUserRoleManager()->isActionAllowed(
            WorkflowStateMachine::FLOW_EMPLOYEE,
            Employee::STATE_NOT_EXIST,
            WorkflowStateMachine::EMPLOYEE_ACTION_ADD
        );

        if (!$allowedToAddEmployee) {
            throw new BadRequestException('Logged in User Not Allowed to Create an Employee');
        }

        $employee = new Employee();
        $this->setParamsToEmployee($employee);

        $empPictureAttachment = $this->getRequestParams()->getAttachmentOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EMP_PICTURE
        );

        if ($empPictureAttachment) {
            $empPicture = new EmpPicture();
            $empPicture->setFilename($empPictureAttachment->getFilename());
            $empPicture->setFileType($empPictureAttachment->getFileType());
            $empPicture->setSize($empPictureAttachment->getSize());
            $empPicture->setPicture($empPictureAttachment->getContent());

            list($width, $height) = $this->getEmployeePictureService()->pictureSizeAdjust(
                $empPictureAttachment->getContent()
            );
            $empPicture->setWidth($width);
            $empPicture->setHeight($height);
            $empPicture->setEmployee($employee);

            $this->getEmployeePictureService()->saveEmployeePicture($empPicture);
        } else {
            $this->getEmployeeService()->saveNewEmployee($employee);
        }
        $this->getEmployeeService()->dispatchAddEmployeeEvent($employee);

        return new EndpointResourceResult(EmployeeModel::class, $employee);
    }

    /**
     * @param Employee $employee
     * @return void
     */
    private function setParamsToEmployee(Employee $employee): void
    {
        $firstName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_FIRST_NAME);
        $middleName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_MIDDLE_NAME);
        $lastName = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_LAST_NAME);
        $employeeId = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EMPLOYEE_ID
        );

        $employee->setFirstName($firstName);
        $employee->setMiddleName($middleName);
        $employee->setLastName($lastName);
        $employee->setEmployeeId($employeeId);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EMP_PICTURE,
                    new Rule(
                        Rules::BASE_64_ATTACHMENT,
                        [EmpPicture::ALLOWED_IMAGE_TYPES, EmpPicture::ALLOWED_IMAGE_EXTENSIONS, self::PARAM_RULE_EMP_PICTURE_FILE_NAME_MAX_LENGTH]
                    )
                ),
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_FIRST_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_FIRST_NAME_MAX_LENGTH]),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MIDDLE_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MIDDLE_NAME_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_LAST_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_LAST_NAME_MAX_LENGTH]),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EMPLOYEE_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_EMPLOYEE_ID_MAX_LENGTH]),
                ),
                true
            ),
        ];
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
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
    public function delete(): EndpointResourceResult
    {
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        if (!$this->getUserRoleManager()->areEntitiesAccessible(Employee::class, $ids)) {
            throw $this->getBadRequestException('Employees not accessible');
        }
        $this->getEmployeeService()->deleteEmployees($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        $allowedToDeleteActive = $this->getUserRoleManager()->isActionAllowed(
            WorkflowStateMachine::FLOW_EMPLOYEE,
            Employee::STATE_ACTIVE,
            WorkflowStateMachine::EMPLOYEE_ACTION_DELETE_ACTIVE
        );
        $allowedToDeleteTerminated = $this->getUserRoleManager()->isActionAllowed(
            WorkflowStateMachine::FLOW_EMPLOYEE,
            Employee::STATE_TERMINATED,
            WorkflowStateMachine::EMPLOYEE_ACTION_DELETE_TERMINATED
        );
        if (!($allowedToDeleteActive || $allowedToDeleteTerminated)) {
            throw $this->getBadRequestException('Not allowed to delete employees');
        }

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            )
        );
    }
}
