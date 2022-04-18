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

use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Api\Model\EmployeePersonalDetailModel;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeePersonalDetailAPI extends Endpoint implements ResourceEndpoint
{
    use EmployeeServiceTrait;
    use ConfigServiceTrait;

    public const PARAMETER_EMP_NUMBER = 'empNumber';
    public const PARAMETER_FIRST_NAME = 'firstName';
    public const PARAMETER_MIDDLE_NAME = 'middleName';
    public const PARAMETER_LAST_NAME = 'lastName';
    public const PARAMETER_EMPLOYEE_ID = 'employeeId';
    public const PARAMETER_OTHER_ID = 'otherId';
    public const PARAMETER_DRIVING_LICENSE_NO = 'drivingLicenseNo';
    public const PARAMETER_DRIVING_LICENSE_EXPIRED_DATE = 'drivingLicenseExpiredDate';
    public const PARAMETER_GENDER = 'gender';
    public const PARAMETER_MARTIAL_STATUS = 'maritalStatus';
    public const PARAMETER_BIRTHDAY = 'birthday';
    public const PARAMETER_NATIONALITY_ID = 'nationalityId';

    // Deprecated Fields
    public const PARAMETER_NICKNAME = 'nickname';
    public const PARAMETER_SMOKER = 'smoker';
    public const PARAMETER_MILITARY_SERVICE = 'militaryService';

    // Country Specific
    public const PARAMETER_SSN_NUMBER = 'ssnNumber';
    public const PARAMETER_SIN_NUMBER = 'sinNumber';

    public const PARAM_RULE_FIRST_NAME_MAX_LENGTH = 30;
    public const PARAM_RULE_MIDDLE_NAME_MAX_LENGTH = 30;
    public const PARAM_RULE_LAST_NAME_MAX_LENGTH = 30;
    public const PARAM_RULE_EMPLOYEE_ID_MAX_LENGTH = 50;
    public const PARAM_RULE_OTHER_ID_MAX_LENGTH = 100;
    public const PARAM_RULE_DRIVING_LICENSE_NO_MAX_LENGTH = 100;
    public const PARAM_RULE_MARTIAL_STATUS_MAX_LENGTH = 20;
    public const PARAM_RULE_NICKNAME_MAX_LENGTH = 100;
    public const PARAM_RULE_MILITARY_SERVICE_MAX_LENGTH = 100;
    public const PARAM_RULE_SSN_NUMBER_MAX_LENGTH = 100;
    public const PARAM_RULE_SIN_NUMBER_MAX_LENGTH = 100;

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_EMP_NUMBER);
        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        return new EndpointResourceResult(EmployeePersonalDetailModel::class, $employee);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_EMP_NUMBER);
        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        $employee->setFirstName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_FIRST_NAME)
        );
        $employee->setMiddleName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_MIDDLE_NAME)
        );
        $employee->setLastName(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_LAST_NAME)
        );
        $employee->setEmployeeId(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EMPLOYEE_ID)
        );
        $employee->setOtherId(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_OTHER_ID)
        );
        $employee->setDrivingLicenseNo(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DRIVING_LICENSE_NO
            )
        );
        $employee->setDrivingLicenseExpiredDate(
            $this->getRequestParams()->getDateTimeOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DRIVING_LICENSE_EXPIRED_DATE
            )
        );
        $employee->setGender(
            $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_GENDER)
        );
        $employee->setMaritalStatus(
            $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_MARTIAL_STATUS)
        );
        $employee->setBirthday(
            $this->getRequestParams()->getDateTimeOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_BIRTHDAY)
        );
        $employee->getDecorator()->setNationality(
            $this->getRequestParams()->getIntOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NATIONALITY_ID)
        );

        $showDeprecatedFields = $this->getConfigService()->showPimDeprecatedFields();
        $showSsn = $this->getConfigService()->showPimSSN();
        $showSin = $this->getConfigService()->showPimSIN();

        // Deprecated Fields
        if ($showDeprecatedFields) {
            $employee->setNickName(
                $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NICKNAME)
            );
            $employee->getDecorator()->setSmoker(
                $this->getRequestParams()->getBooleanOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_SMOKER)
            );
            $employee->setMilitaryService(
                $this->getRequestParams()->getStringOrNull(
                    RequestParams::PARAM_TYPE_BODY,
                    self::PARAMETER_MILITARY_SERVICE
                )
            );
        }

        // Country Specific
        if ($showSsn) {
            $employee->setSsnNumber(
                $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_SSN_NUMBER)
            );
        }
        if ($showSin) {
            $employee->setSinNumber(
                $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_SIN_NUMBER)
            );
        }

        $this->getEmployeeService()->updateEmployeePersonalDetails($employee);

        return new EndpointResourceResult(EmployeePersonalDetailModel::class, $employee);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        $showDeprecatedFields = $this->getConfigService()->showPimDeprecatedFields();
        $showSsn = $this->getConfigService()->showPimSSN();
        $showSin = $this->getConfigService()->showPimSIN();
        $paramRules = [
            new ParamRule(
                self::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
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
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_OTHER_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_OTHER_ID_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DRIVING_LICENSE_NO,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_DRIVING_LICENSE_NO_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DRIVING_LICENSE_EXPIRED_DATE,
                    new Rule(Rules::API_DATE),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_GENDER,
                    new Rule(Rules::IN, [[Employee::GENDER_MALE, Employee::GENDER_FEMALE]])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MARTIAL_STATUS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MARTIAL_STATUS_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_BIRTHDAY,
                    new Rule(Rules::API_DATE),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NATIONALITY_ID,
                    new Rule(Rules::POSITIVE),
                )
            ),
        ];

        if ($showDeprecatedFields) {
            $paramRules[] = $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NICKNAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NICKNAME_MAX_LENGTH]),
                ),
                true
            );
            $paramRules[] = $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SMOKER,
                    new Rule(Rules::BOOL_TYPE),
                ),
                true
            );
            $paramRules[] = $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MILITARY_SERVICE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MILITARY_SERVICE_MAX_LENGTH]),
                ),
                true
            );
        }

        if ($showSsn) {
            $paramRules[] = $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SSN_NUMBER,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SSN_NUMBER_MAX_LENGTH]),
                ),
                true
            );
        }

        if ($showSin) {
            $paramRules[] = $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SIN_NUMBER,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SIN_NUMBER_MAX_LENGTH]),
                ),
                true
            );
        }
        return new ParamRuleCollection(...$paramRules);
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
