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

use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Exception\NotImplementedException;
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
use OrangeHRM\Admin\Service\OrganizationService;
use OrangeHRM\Admin\Api\Model\OrganizationModel;
use OrangeHRM\Core\Exception\DaoException;
use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Entity\Organization;
use OrangeHRM\Pim\Service\EmployeeService;

class OrganizationAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_TAX_ID = 'taxId';
    public const PARAMETER_REGISTRATION_NUMBER = 'registrationNumber';
    public const PARAMETER_PHONE = 'phone';
    public const PARAMETER_FAX = 'fax';
    public const PARAMETER_EMAIL = 'email';
    public const PARAMETER_COUNTRY = 'country';
    public const PARAMETER_PROVINCE = 'province';
    public const PARAMETER_CITY = 'city';
    public const PARAMETER_ZIP_CODE = 'zipCode';
    public const PARAMETER_STREET_1 = 'street1';
    public const PARAMETER_STREET_2 = 'street2';
    public const PARAMETER_NOTE = 'note';

    /**
     * @var null|OrganizationService
     */
    protected ?OrganizationService $organizationService = null;

    /**
     * @var EmployeeService|null
     */
    protected ?EmployeeService $employeeService = null;

    /**
     * @return OrganizationService
     */
    public function getOrganizationService(): OrganizationService
    {
        if (is_null($this->organizationService)) {
            $this->organizationService = new OrganizationService();
        }
        return $this->organizationService;
    }

    /**
     * @param OrganizationService $organizationService
     */
    public function setOrganizationService(OrganizationService $organizationService): void
    {
        $this->organizationService = $organizationService;
    }

    /**
     * @return EmployeeService|null
     */
    public function getEmployeeService(): ?EmployeeService
    {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

    /**
     * @param EmployeeService|null $employeeService
     */
    public function setEmployeeService(?EmployeeService $employeeService): void
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @return EndpointGetOneResult
     * @throws RecordNotFoundException
     * @throws Exception
     */
    public function getOne(): EndpointGetOneResult
    {
        // TODO:: Check data group permission
        $orgInfo = $this->getOrganizationService()->getOrganizationGeneralInformation();
        if (!$orgInfo instanceof Organization) {
            throw new RecordNotFoundException();
        }

        return new EndpointGetOneResult(OrganizationModel::class, $orgInfo);
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
     * @throws Exception
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
     * @throws Exception
     */
    public function create(): EndpointCreateResult
    {
        // TODO:: Check data group permission
        $orgInfo = $this->saveOrganizationInfo();

        return new EndpointCreateResult(OrganizationModel::class, $orgInfo);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_NAME),
            new ParamRule(self::PARAMETER_TAX_ID),
            new ParamRule(self::PARAMETER_REGISTRATION_NUMBER),
            new ParamRule(self::PARAMETER_PHONE),
            new ParamRule(self::PARAMETER_FAX),
            new ParamRule(self::PARAMETER_EMAIL),
            new ParamRule(self::PARAMETER_COUNTRY),
            new ParamRule(self::PARAMETER_PROVINCE),
            new ParamRule(self::PARAMETER_CITY),
            new ParamRule(self::PARAMETER_ZIP_CODE),
            new ParamRule(self::PARAMETER_STREET_1),
            new ParamRule(self::PARAMETER_STREET_2),
            new ParamRule(self::PARAMETER_NOTE),
        );
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointUpdateResult
    {
        // TODO:: Check data group permission
        $orgInfo = $this->saveOrganizationInfo();

        return new EndpointUpdateResult(OrganizationModel::class, $orgInfo);
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
            new ParamRule(self::PARAMETER_NAME),
        );
    }

    /**
     * @return Organization
     * @throws DaoException
     * @throws DaoException
     */
    public function saveOrganizationInfo(): Organization
    {
        // TODO:: Check data group permission
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $taxId = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TAX_ID);
        $registrationNumber = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_REGISTRATION_NUMBER
        );
        $phone = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PHONE);
        $fax = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_FAX);
        $email = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EMAIL);
        $country = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_COUNTRY);
        $province = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PROVINCE);
        $city = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CITY);
        $zipCode = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ZIP_CODE);
        $street1 = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STREET_1);
        $street2 = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STREET_2);
        $note = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NOTE);

        $orgInfo = $this->getOrganizationService()->getOrganizationGeneralInformation();
        if ($orgInfo == null) {
            $orgInfo = new Organization();
        }
        $orgInfo->setName($name);
        $orgInfo->setTaxId($taxId);
        $orgInfo->setRegistrationNumber($registrationNumber);
        $orgInfo->setPhone($phone);
        $orgInfo->setFax($fax);
        $orgInfo->setEmail($email);
        $orgInfo->setCountry($country);
        $orgInfo->setProvince($province);
        $orgInfo->setCity($city);
        $orgInfo->setZipCode($zipCode);
        $orgInfo->setStreet1($street1);
        $orgInfo->setStreet2($street2);
        $orgInfo->setNote($note);
        return $this->getOrganizationService()->saveOrganizationGeneralInformation($orgInfo);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function delete(): EndpointDeleteResult
    {
        throw new NotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        throw new NotImplementedException();
    }
}
