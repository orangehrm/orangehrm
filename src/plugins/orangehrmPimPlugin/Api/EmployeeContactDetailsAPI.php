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
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Api\V2\Validator\Rules\EntityUniquePropertyOption;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Api\Model\EmployeeContactDetailsModel;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;

class EmployeeContactDetailsAPI extends Endpoint implements CrudEndpoint
{
    use EmployeeServiceTrait;

    public const PARAMETER_EMP_NUMBER = 'empNumber';
    public const PARAMETER_STREET_1 = 'street1';
    public const PARAMETER_STREET_2 = 'street2';
    public const PARAMETER_CITY = 'city';
    public const PARAMETER_PROVINCE = 'province';
    public const PARAMETER_ZIP_CODE = 'zipCode';
    public const PARAMETER_COUNTRY = 'countryCode';
    public const PARAMETER_HOME_TELEPHONE = 'homeTelephone';
    public const PARAMETER_WORK_TELEPHONE = 'workTelephone';
    public const PARAMETER_MOBILE = 'mobile';
    public const PARAMETER_WORK_EMAIL = 'workEmail';
    public const PARAMETER_OTHER_EMAIL = 'otherEmail';

    public const PARAM_RULE_STREET_1_MAX_LENGTH = 100;
    public const PARAM_RULE_STREET_2_MAX_LENGTH = 100;
    public const PARAM_RULE_CITY_MAX_LENGTH = 100;
    public const PARAM_RULE_PROVINCE_MAX_LENGTH = 100;
    public const PARAM_RULE_ZIP_CODE_MAX_LENGTH = 20;
    public const PARAM_RULE_COUNTRY_MAX_LENGTH = 100;
    public const PARAM_RULE_HOME_TELEPHONE_MAX_LENGTH = 25;
    public const PARAM_RULE_WORK_TELEPHONE_MAX_LENGTH = 25;
    public const PARAM_RULE_MOBILE_MAX_LENGTH = 25;
    public const PARAM_RULE_WORK_EMAIL_MAX_LENGTH = 50;
    public const PARAM_RULE_OTHER_EMAIL_MAX_LENGTH = 50;

    /**
     * @return EndpointResourceResult
     * @throws Exception
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_EMP_NUMBER
        );

        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        return new EndpointResourceResult(EmployeeContactDetailsModel::class, $employee);
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
    public function getAll(): EndpointCollectionResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
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
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        $employee = $this->saveContactDetails();

        return new EndpointResourceResult(EmployeeContactDetailsModel::class, $employee);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STREET_1,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_STREET_1_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STREET_2,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_STREET_2_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_CITY,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CITY_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PROVINCE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_PROVINCE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ZIP_CODE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ZIP_CODE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COUNTRY,
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COUNTRY_MAX_LENGTH]),
                    new Rule(Rules::COUNTRY_CODE),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_HOME_TELEPHONE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::PHONE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_HOME_TELEPHONE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_WORK_TELEPHONE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::PHONE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_WORK_TELEPHONE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MOBILE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::PHONE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MOBILE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_WORK_EMAIL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::EMAIL),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Employee::class, 'workEmail', $this->getEntityPropertiesForEmailRule()]),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Employee::class, 'otherEmail']),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_WORK_EMAIL_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_OTHER_EMAIL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::EMAIL),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Employee::class, 'otherEmail', $this->getEntityPropertiesForEmailRule()]),
                    new Rule(Rules::ENTITY_UNIQUE_PROPERTY, [Employee::class, 'workEmail']),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_OTHER_EMAIL_MAX_LENGTH]),
                ),
                true
            ),
        );
    }

    /**
     * @return EntityUniquePropertyOption
     */
    private function getEntityPropertiesForEmailRule(): EntityUniquePropertyOption
    {
        $entityProperties = new EntityUniquePropertyOption();
        $entityProperties->setIgnoreValues(
            [
                'getEmpNumber' => $this->getRequestParams()->getInt(
                    RequestParams::PARAM_TYPE_ATTRIBUTE,
                    self::PARAMETER_EMP_NUMBER
                )
            ]
        );
        return $entityProperties;
    }

    /**
     * @return Employee
     * @throws DaoException
     * @throws DaoException
     * @throws RecordNotFoundException
     */
    public function saveContactDetails(): Employee
    {
        $empNumber = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_EMP_NUMBER
        );
        $street1 = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STREET_1);
        $street2 = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STREET_2);
        $city = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CITY);
        $province = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_PROVINCE
        );
        $zipcode = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ZIP_CODE);
        $country = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_COUNTRY);
        $homeTelephone = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_HOME_TELEPHONE
        );
        $workTelephone = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_WORK_TELEPHONE
        );
        $mobile = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_MOBILE);
        $workEmail = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_WORK_EMAIL
        );
        $otherEmail = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_OTHER_EMAIL
        );

        if (!empty($workEmail) && !empty($otherEmail) && $workEmail === $otherEmail) {
            throw $this->getBadRequestException('Work Email and Other Email Cannot Be The Same');
        }

        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        $this->throwRecordNotFoundExceptionIfNotExist($employee, Employee::class);

        $employee->setStreet1($street1);
        $employee->setStreet2($street2);
        $employee->setCity($city);
        $employee->setProvince($province);
        $employee->setZipcode($zipcode);
        $employee->setCountry($country);
        $employee->setWorkTelephone($workTelephone);
        $employee->setHomeTelephone($homeTelephone);
        $employee->setMobile($mobile);
        $employee->setWorkEmail($workEmail);
        $employee->setOtherEmail($otherEmail);
        return $this->getEmployeeService()->saveEmployee($employee);
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
