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
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Serializer\EndpointCreateResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointDeleteResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetAllResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointGetOneResult;
use OrangeHRM\Core\Api\V2\Serializer\EndpointUpdateResult;
use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Core\Exception\SearchParamException;
use OrangeHRM\Core\Exception\ServiceException;
use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Pim\Api\Model\EmpEmergencyContactModel;
use OrangeHRM\Pim\Dto\EmpEmergencyContactSearchFilterParams;
use OrangeHRM\Pim\Service\EmpEmergencyContactService;
use OrangeHRM\Core\Traits\UserRoleManagerTrait ;


class EmpEmergencyContactAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_EMP_NUMBER = 'empNumber';
    public const PARAMETER_EEC_SEQNOS = 'seqNos';
    public const PARAMETER_EEC_SEQNO = 'seqNo';
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_RELATIONSHIP = 'relationship';
    public const PARAMETER_HOME_PHONE = 'homePhone';
    public const PARAMETER_OFFICE_PHONE = 'officePhone';
    public const PARAMETER_MOBILE_PHONE = 'mobilePhone';

    public const FILTER_EMP_NUMBER = 'empNumber';
    public const FILTER_EEC_SEQNO = 'seqNo';
    public const FILTER_NAME = 'name';
    public const FILTER_RELATIONSHIP = 'relationship';
    public const FILTER_HOME_PHONE = 'homePhone';
    public const FILTER_OFFICE_PHONE = 'officePhone';
    public const FILTER_MOBILE_PHONE = 'mobilePhone';


    /**
     * @var EmpEmergencyContactService|null
     */
    protected ?EmpEmergencyContactService $empEmergencyContactService = null;

    /**
     * @return EmpEmergencyContactService|null
     */
    public function getEmpEmergencyContactService(): EmpEmergencyContactService
    {
        if (!$this->empEmergencyContactService instanceof EmpEmergencyContactService) {
            $this->empEmergencyContactService = new EmpEmergencyContactService();
        }
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
     * @return EndpointGetAllResult
     * @throws DaoException
     * @throws NormalizeException
     */
    public function getAll(): EndpointGetAllResult //done
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $emergencyContact = $this->getEmpEmergencyContactService()->getEmployeeEmergencyContactList($empNumber);
        return new EndpointGetAllResult(
            EmpEmergencyContactModel::class, $emergencyContact,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => count($emergencyContact)
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection //done
    {
        // TODO: Implement getValidationRuleForGetAll() method.
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),

        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointCreateResult //done
    {
        // TODO: Implement create() method.

        $emergencyContact = $this->saveEmployeeEmergencyContacts();

        return new EndpointCreateResult(EmpEmergencyContactModel::class, $emergencyContact,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $emergencyContact->getEmployee()->getEmpNumber()])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection //done
    {

        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    private function getCommonBodyValidationRules(): array //done
    {
        return [
            new ParamRule(self::PARAMETER_NAME),
            new ParamRule(self::PARAMETER_RELATIONSHIP),
            new ParamRule(self::PARAMETER_HOME_PHONE),
            new ParamRule(self::PARAMETER_OFFICE_PHONE),
            new ParamRule(self::PARAMETER_MOBILE_PHONE),
        ];
    }

    /**
     * @inheritDoc
     * @throws DaoException
     */
    public function delete(): EndpointDeleteResult
    {

        $sequenceNumbers = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_EMP_NUMBER);
        $this->getEmpEmergencyContactService()->deleteEmployeeEmergencyContacts($empNumber,$sequenceNumbers );
        return new EndpointDeleteResult(ArrayModel::class, $sequenceNumbers);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection //done
    {
        // TODO: Implement getValidationRuleForDelete() method.
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_EMP_NUMBER),
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    /**
     * @inheritDoc
     * @throws DaoException
     * @throws RecordNotFoundException
     */
    public function getOne(): EndpointGetOneResult
    {
        // TODO:: Check data group permission
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_EMP_NUMBER);
        $seqNo = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $emergencyContact = $this->getEmpEmergencyContactService()->getEmployeeEmergencyContact( $empNumber, $seqNo);
        $this->throwRecordNotFoundExceptionIfNotExist($emergencyContact, EmpEmergencyContact::class);

        if (!$emergencyContact instanceof EmpEmergencyContact) {
            throw new RecordNotFoundException();
        }
        return new EndpointGetOneResult(EmpEmergencyContactModel::class, $emergencyContact,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection //done
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::BETWEEN, [0, 100])
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function update(): EndpointUpdateResult
    {
        $empEmergencyContact = $this->saveEmployeeEmergencyContacts();
        return new EndpointUpdateResult(EmpEmergencyContactModel::class, $empEmergencyContact,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empEmergencyContact->getEmployee()->getEmpNumber()])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(CommonParams::PARAMETER_ID,
            new Rule(Rules::BETWEEN, [0, 100])
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    public function saveEmployeeEmergencyContacts(): EmpEmergencyContact
    {
        $seqNo = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $empNumber = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_EMP_NUMBER);
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $relationship = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_RELATIONSHIP);
        $homePhone = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_OFFICE_PHONE);
        $mobilePhone = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_OFFICE_PHONE);
        $officePhone = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_OFFICE_PHONE);
        if ($seqNo) {
            $empEmergencyContact = $this->getEmpEmergencyContactService()->getEmployeeEmergencyContact( $empNumber, $seqNo);
            $this->throwRecordNotFoundExceptionIfNotExist($empEmergencyContact, EmpEmergencyContact::class );

        } else {
            $empEmergencyContact = new EmpEmergencyContact();
            $empEmergencyContact->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }

        $empEmergencyContact->setName($name);
        $empEmergencyContact->setRelationship($relationship);
        $empEmergencyContact->setHomePhone($homePhone);
        $empEmergencyContact->setMobilePhone($mobilePhone);
        $empEmergencyContact->setOfficePhone($officePhone);
        return $this->getEmpEmergencyContactService()->saveEmpEmergencyContact($empEmergencyContact );
    }
    
}