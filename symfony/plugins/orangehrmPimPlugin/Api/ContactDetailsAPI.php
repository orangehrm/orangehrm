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

use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\EndpointCreateResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointDeleteResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetAllResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetOneResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointUpdateResult;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Pim\Api\Model\ContactDetailsModel;
use OrangeHRM\Core\Exception\DaoException;
use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Entity\Employee;

class ContactDetailsAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_STREET_1 = 'street1';
    public const PARAMETER_STREET_2 = 'street2';
    public const PARAMETER_CITY = 'city';
    public const PARAMETER_PROVINCE = 'province';
    public const PARAMETER_ZIP_CODE = 'zipcode';
    public const PARAMETER_COUNTRY = 'employeeCountry';
    public const PARAMETER_HOME_TELEPHONE = 'employeeHomeTelephone';
    public const PARAMETER_WORK_TELEPHONE = 'employeeWorkTelephone';
    public const PARAMETER_MOBILE = 'employeeMobile';
    public const PARAMETER_WORK_EMAIL = 'empWorkEmail';
    public const PARAMETER_OTHER_EMAIL = 'empOtherEmail';

    public const PARAM_RULE_STREET_1_MAX_LENGTH = 100;
    public const PARAM_RULE_STREET_2_MAX_LENGTH = 100;
    public const PARAM_RULE_CITY_MAX_LENGTH = 100;
    public const PARAM_RULE_PROVINCE_MAX_LENGTH = 100;
    public const PARAM_RULE_ZIP_CODE_MAX_LENGTH = 20;
    public const PARAM_RULE_COUNTRY_MAX_LENGTH = 100;
    public const PARAM_RULE_HOME_TELEPHONE_MAX_LENGTH = 50;
    public const PARAM_RULE_WORK_TELEPHONE_MAX_LENGTH = 50;
    public const PARAM_RULE_MOBILE_MAX_LENGTH = 50;
    public const PARAM_RULE_WORK_EMAIL_MAX_LENGTH = 50;
    public const PARAM_RULE_OTHER_EMAIL_MAX_LENGTH = 50;

    /**
     * @var null|EmployeeService
     */
    protected ?EmployeeService $employeeService = null;

    /**
     * @return EmployeeService
     */
    public function getEmployeeService(): EmployeeService
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService): void
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @return EndpointGetOneResult
     * @throws Exception
     */
    public function getOne(): EndpointGetOneResult
    {
        // TODO:: Check data group permission
        $empNumber = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);

        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        if (!$employee instanceof Employee) {
            throw new RecordNotFoundException();
        }

        return new EndpointGetOneResult(ContactDetailsModel::class, $employee);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @return EndpointGetAllResult
     * @throws NotImplementedException
     */
    public function getAll(): EndpointGetAllResult
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws NotImplementedException
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws NotImplementedException
     */
    public function create(): EndpointCreateResult
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws NotImplementedException
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointUpdateResult
    {
        // TODO:: Check data group permission
        $employee = $this->saveContactDetails();

        return new EndpointUpdateResult(ContactDetailsModel::class, $employee);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::POSITIVE)

            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STREET_1,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_STREET_1_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_STREET_2,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_STREET_2_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_CITY,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CITY_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PROVINCE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_PROVINCE_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_ZIP_CODE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_ZIP_CODE_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COUNTRY,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COUNTRY_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_HOME_TELEPHONE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_HOME_TELEPHONE_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_WORK_TELEPHONE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_WORK_TELEPHONE_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MOBILE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MOBILE_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_WORK_EMAIL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_WORK_EMAIL_MAX_LENGTH]),
                ), true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_OTHER_EMAIL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_OTHER_EMAIL_MAX_LENGTH]),
                ), true
            ),
        );
    }

    /**
     * @return Employee
     * @throws DaoException
     * @throws DaoException
     * @throws RecordNotFoundException
     */
    public function saveContactDetails(): Employee
    {
        // TODO:: Check data group permission
        $empNumber = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $street1 = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STREET_1);
        $street2 = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STREET_2);
        $city = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CITY);
        $province = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PROVINCE);
        $zipcode = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ZIP_CODE);
        $country = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_COUNTRY);
        $homeTelephone = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_HOME_TELEPHONE);
        $workTelephone = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_WORK_TELEPHONE);
        $mobile = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_MOBILE);
        $workEmail = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_WORK_EMAIL);
        $otherEmail = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_OTHER_EMAIL);

        $employee = $this->getEmployeeService()->getEmployeeByEmpNumber($empNumber);
        if ($employee == null) {
            throw new RecordNotFoundException();
        }
        $employee->setStreet1($street1);
        $employee->setStreet2($street2);
        $employee->setCity($city);
        $employee->setProvince($province);
        $employee->setZipcode($zipcode);
        $employee->setEmployeeCountry($country);
        $employee->setEmployeeWorkTelephone($workTelephone);
        $employee->setEmployeeHomeTelephone($homeTelephone);
        $employee->setEmployeeMobile($mobile);
        $employee->setEmpWorkEmail($workEmail);
        $employee->setEmpOtherEmail($otherEmail);
        return $this->getEmployeeService()->saveEmployee($employee);
    }

    /**
     * @inheritDoc
     * @throws NotImplementedException
     */
    public function delete(): EndpointDeleteResult
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws NotImplementedException
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }
}
