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


use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Exception\DaoException;
use Exception;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\EndpointCreateResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointDeleteResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetAllResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetOneResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointUpdateResult;
use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\Pim\Api\Model\EmpEmergencyContactModel;
use OrangeHRM\Pim\Dto\EmpEmergencyContactSearchFilterParams;
use OrangeHRM\Pim\Service\EmpEmergencyContactService;
use phpDocumentor\Reflection\Types\Self_;

class EmpEmergencyContactAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_EMP_NUMBER = 'emp_number' ;
    public const PARAMETER_EMP_NUMBERS = 'emp_numbers' ;
    public const PARAMETER_EEC_SEQNO = 'seqno' ;
    public const PARAMETER_NAME = 'name' ;
    public const PARAMETER_RELATIONSHIP = 'relationship' ;
    public const PARAMETER_HOME_PHONE = 'home_phone' ;
    public const PARAMETER_OFFICE_PHONE = 'office_phone' ;
    public const PARAMETER_MOBILE_PHONE = 'mobile_phone' ;

    public const FILTER_EMP_NUMBER = 'emp_number' ;
    public const FILTER_EEC_SEQNO = 'eec_seqno' ;
    public const FILTER_NAME = 'name' ;
    public const FILTER_RELATIONSHIP = 'relationship' ;
    public const FILTER_HOME_PHONE = 'home_phone' ;
    public const FILTER_OFFICE_PHONE = 'office_phone' ;
    public const FILTER_MOBILE_PHONE = 'mobile_phone' ;

    /**
     * @var EmpEmergencyContactService|null
     */
    protected ?EmpEmergencyContactService $empEmergencyContactService = null;

    /**
     * @return EmpEmergencyContactService|null
     */
    public function getEmpEmergencyContactService(): ?EmpEmergencyContactService
    {
        return $this->empEmergencyContactService;
    }

    /**
     * @param EmpEmergencyContactService|null $empEmergencyContactService
     */
    public function setEmpEmergencyContactService(?EmpEmergencyContactService $empEmergencyContactService): void
    {
        $this->empEmergencyContactService = $empEmergencyContactService;
    }




    /**
     * @inheritDoc
     */
    public function getAll(): EndpointGetAllResult
    {
        // TODO: Implement getAll() method.
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForGetAll() method.
        return new ParamRuleCollection(
            //new ParamRule(self::FILTER_EMP_NUMBER),
            //new ParamRule(self::FILTER_EEC_SEQNO),
            new ParamRule(self::FILTER_NAME),
            new ParamRule(self::FILTER_RELATIONSHIP),
            new ParamRule(self::FILTER_HOME_PHONE),
            new ParamRule(self::FILTER_OFFICE_PHONE),
            new ParamRule(self::FILTER_MOBILE_PHONE),
            ...$this->getSortingAndPaginationParamsRules(EmpEmergencyContactSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointCreateResult
    {
        // TODO: Implement create() method.
        $empEmergencyContact = $this->saveEmployeeEmergencyContacts();

        return new EndpointCreateResult(EmpEmergencyContactModel::class, $empEmergencyContact);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForCreate() method.
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    private function getCommonBodyValidationRules(): array
    {
        return [
            //new ParamRule(self::PARAMETER_EMP_NUMBER),
            //new ParamRule(self::PARAMETER_EEC_SEQNO),
            new ParamRule(self::PARAMETER_NAME),
            new ParamRule(self::PARAMETER_RELATIONSHIP),
            new ParamRule(self::PARAMETER_HOME_PHONE),
            new ParamRule(self::PARAMETER_OFFICE_PHONE),
            new ParamRule(self::PARAMETER_MOBILE_PHONE),
        ];
    }

    /**
     * @inheritDoc
     */
    public function delete(): EndpointDeleteResult
    {
        // TODO: Implement delete() method.
        $empNumbers = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EMP_NUMBERS);
        $this->getEmpEmergencyContactService()->deleteEmployeeEmergencyContacts($empNumbers);
        return new EndpointDeleteResult(ArrayModel::class, $empNumbers);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForDelete() method.
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_EMP_NUMBERS),
        );
    }

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointGetOneResult
    {
        // TODO:: Check data group permission
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_EMP_NUMBER);
        $emergencyContact = $this->getEmpEmergencyContactService()->getEmployeeEmergencyContacts($empNumber);
        if (!$emergencyContact instanceof EmpEmergencyContact) {
            throw new RecordNotFoundException();
        }

        return new EndpointGetOneResult(EmpEmergencyContactModel::class, $emergencyContact);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_EMP_NUMBER),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointUpdateResult
    {
        // TODO: Implement update() method.
        $empEmergencyContact = $this->saveEmployeeEmergencyContacts();

        return new EndpointUpdateResult(EmpEmergencyContactModel::class, $empEmergencyContact);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        // TODO: Implement getValidationRuleForUpdate() method.
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_EMP_NUMBER,
                new Rule(Rules::POSITIVE)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    public function saveEmployeeEmergencyContacts(): EmpEmergencyContact
    { //fix this
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, self::PARAMETER_EMP_NUMBER);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $relationship = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_RELATIONSHIP
        );  // add other parameters as well
        if (!empty($empNumber)) {
            $empEmergencyContact = $this->getEmpEmergencyContactService()->getEmployeeEmergencyContacts($empNumber);
            if ($empEmergencyContact == null) {
                throw new RecordNotFoundException();
            }
        } else {
            $skill = new Skill();
        }

        $empEmergencyContact->setName($name);
        $empEmergencyContact->setRelationship($relationship);
        return $this->getSkillService()->saveSkill($empEmergencyContact);

    }
}