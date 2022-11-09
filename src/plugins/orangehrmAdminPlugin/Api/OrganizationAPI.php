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

use Exception;
use OrangeHRM\Admin\Api\Model\OrganizationModel;
use OrangeHRM\Admin\Service\OrganizationService;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\Organization;

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

    public const PARAM_RULE_NAME_MAX_LENGTH = 100;
    public const PARAM_RULE_TAX_ID_MAX_LENGTH = 30;
    public const PARAM_RULE_REGISTRATION_NUMBER_MAX_LENGTH = 30;
    public const PARAM_RULE_PHONE_MAX_LENGTH = 30;
    public const PARAM_RULE_FAX_MAX_LENGTH = 30;
    public const PARAM_RULE_EMAIL_MAX_LENGTH = 30;
    public const PARAM_RULE_COUNTRY_MAX_LENGTH = 30;
    public const PARAM_RULE_PROVINCE_MAX_LENGTH = 30;
    public const PARAM_RULE_CITY_MAX_LENGTH = 30;
    public const PARAM_RULE_ZIP_CODE_MAX_LENGTH = 30;
    public const PARAM_RULE_STREET_1_MAX_LENGTH = 100;
    public const PARAM_RULE_STREET_2_MAX_LENGTH = 100;
    public const PARAM_RULE_NOTE_MAX_LENGTH = 255;
    /**
     * @var null|OrganizationService
     */
    protected ?OrganizationService $organizationService = null;

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
     * @return EndpointResourceResult
     * @throws Exception
     */
    public function getOne(): EndpointResourceResult
    {
        $orgInfo = $this->getOrganizationService()->getOrganizationGeneralInformation();
        if (!$orgInfo instanceof Organization) {
            $orgInfo = new Organization();
            $orgInfo->setName("");
            $orgInfo->setTaxId("");
            $orgInfo->setRegistrationNumber("");
            $orgInfo->setPhone(null);
            $orgInfo->setFax(null);
            $orgInfo->setEmail(null);
            $orgInfo->setCountry("");
            $orgInfo->setProvince("");
            $orgInfo->setCity("");
            $orgInfo->setZipCode("");
            $orgInfo->setStreet1("");
            $orgInfo->setStreet2("");
            $orgInfo->setNote("");
        }

        return new EndpointResourceResult(OrganizationModel::class, $orgInfo);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID
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
     */
    public function update(): EndpointResourceResult
    {
        $orgInfo = $this->saveOrganizationInfo();

        return new EndpointResourceResult(OrganizationModel::class, $orgInfo);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NAME_MAX_LENGTH]),
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_TAX_ID,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_TAX_ID_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_REGISTRATION_NUMBER,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_REGISTRATION_NUMBER_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COUNTRY,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COUNTRY_MAX_LENGTH]),
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
                    self::PARAMETER_CITY,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_CITY_MAX_LENGTH]),
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
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_NOTE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_EMAIL,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::EMAIL),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_EMAIL_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_PHONE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::PHONE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_PHONE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_FAX,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_FAX_MAX_LENGTH]),
                ),
                true
            ),
        );
    }

    /**
     * @return Organization
     */
    public function saveOrganizationInfo(): Organization
    {
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $taxId = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_TAX_ID);
        $registrationNumber = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_REGISTRATION_NUMBER
        );
        $phone = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_PHONE);
        $fax = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_FAX);
        $email = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EMAIL);
        $country = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_COUNTRY);
        $province = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_PROVINCE
        );
        $city = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_CITY);
        $zipCode = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_ZIP_CODE);
        $street1 = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STREET_1);
        $street2 = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_STREET_2);
        $note = $this->getRequestParams()->getStringOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NOTE);

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
