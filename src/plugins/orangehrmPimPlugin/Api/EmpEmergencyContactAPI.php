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

namespace OrangeHRM\Pim\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\Pim\Api\Model\EmpEmergencyContactModel;
use OrangeHRM\Pim\Dto\EmpEmergencyContactSearchFilterParams;
use OrangeHRM\Pim\Service\EmpEmergencyContactService;

class EmpEmergencyContactAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_NAME = 'name';
    public const PARAMETER_RELATIONSHIP = 'relationship';
    public const PARAMETER_HOME_PHONE = 'homePhone';
    public const PARAMETER_OFFICE_PHONE = 'officePhone';
    public const PARAMETER_MOBILE_PHONE = 'mobilePhone';

    public const FILTER_NAME = 'name';
    public const PARAM_RULE_MAX_LENGTH = 100;
    public const PARAM_RULE_HOME_PHONE_MAX_LENGTH = 30;
    public const PARAM_RULE_OFFICE_PHONE_MAX_LENGTH = 30;
    public const PARAM_RULE_MOBILE_PHONE_MAX_LENGTH = 30;

    /**
     * @var EmpEmergencyContactService|null
     */
    protected ?EmpEmergencyContactService $empEmergencyContactService = null;

    /**
     * @return EmpEmergencyContactService
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
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/emergency-contacts",
     *     tags={"PIM/Employee Emergency Contact"},
     *     summary="List an Employee's Emergency Contacts",
     *     operationId="list-an-employees-emergency-contacts",
     *     description="This endpoint allows you to list an employee's emergency contacts.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="The numerical employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         description="The name of the desired emergency contact",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_MAX_LENGTH)
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         description="Sort the emergency contacts by their names",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmpEmergencyContactSearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(ref="#/components/parameters/sortOrder"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/offset"),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Pim-EmpEmergencyContactModel")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The given numerical employee number of the employee", type="integer"),
     *                 @OA\Property(property="total", description="The total number of emergency contacts", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $emergencyContactSearchParams = new EmpEmergencyContactSearchFilterParams();
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );

        $this->setSortingAndPaginationParams($emergencyContactSearchParams);
        $emergencyContactSearchParams->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $emergencyContactSearchParams->setEmpNumber($empNumber);
        $emergencyContact = $this->getEmpEmergencyContactService()->searchEmployeeEmergencyContacts(
            $emergencyContactSearchParams
        );
        return new EndpointCollectionResult(
            EmpEmergencyContactModel::class,
            $emergencyContact,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmpEmergencyContactService(
                    )->getSearchEmployeeEmergencyContactsCount($emergencyContactSearchParams)
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MAX_LENGTH]),
                ),
            ),
            ...$this->getSortingAndPaginationParamsRules(EmpEmergencyContactSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/pim/employees/{empNumber}/emergency-contacts",
     *     tags={"PIM/Employee Emergency Contact"},
     *     summary="Add an Emergency Contact to an Employee",
     *     operationId="add-an-emergency-contact-to-an-employee",
     *     description="The endpoint allows you to add an emergency contact for a particular employee.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="The numerical employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 description="Specify the name of the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="relationship",
     *                 description="Specify the relationship between the employee and the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="homePhone",
     *                 description="Specify the home phone number of the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_HOME_PHONE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="officePhone",
     *                 description="Specfiy the office phone number of the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_OFFICE_PHONE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="mobilePhone",
     *                 description="Specify the mobile phone number of the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_MOBILE_PHONE_MAX_LENGTH
     *             ),
     *             required={"name", "relationship", "homePhone", "officePhone", "mobilePhone"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmpEmergencyContactModel"
     *             ),
     *             @OA\Property(property="meta", type="object", additionalProperties=false)
     *         )
     *     ),
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $emergencyContact = $this->saveEmployeeEmergencyContacts();

        return new EndpointResourceResult(
            EmpEmergencyContactModel::class,
            $emergencyContact,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $emergencyContact->getEmployee()->getEmpNumber()])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    private function getCommonBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MAX_LENGTH])
                )
            ),
            $this->getValidationDecorator()->requiredParamRule(
                new ParamRule(
                    self::PARAMETER_RELATIONSHIP,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MAX_LENGTH])
                )
            ),
            new ParamRule(
                self::PARAMETER_HOME_PHONE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::PHONE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_HOME_PHONE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_OFFICE_PHONE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::PHONE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_OFFICE_PHONE_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_MOBILE_PHONE,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::PHONE),
                new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MOBILE_PHONE_MAX_LENGTH])
            ),
        ];
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/pim/employees/{empNumber}/emergency-contacts",
     *     tags={"PIM/Employee Emergency Contact"},
     *     summary="Delete an Employee's Emergency Contacts",
     *     operationId="delete-an-employees-emergency-contacts",
     *     description="This endpoint allows you to delete an employee's emergency contacts.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="The numerical employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $sequenceNumbers = $this->getEmpEmergencyContactService()->getEmpEmergencyContactDao()->getExistingSeqNosForEmpNumber(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS),
            $empNumber
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($sequenceNumbers);
        $this->getEmpEmergencyContactService()->deleteEmployeeEmergencyContacts($empNumber, $sequenceNumbers);
        return new EndpointResourceResult(
            ArrayModel::class,
            $sequenceNumbers,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_EMP_NUMBER,
                new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
            ),
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            ),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/emergency-contacts/{id}",
     *     tags={"PIM/Employee Emergency Contact"},
     *     summary="Get an Employee's Emergency Contact",
     *     operationId="get-an-employees-emergency-contact",
     *     description="This endpoint allows you to get one of an employee's emergency contacts.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="The numerical employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         description="The numerical ID of the desired emergency contact",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmpEmergencyContactModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *             @OA\Property(property="empNumber", description="The given numerical employee number of the employee", type="integer"))
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws RecordNotFoundException
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $seqNo = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $emergencyContact = $this->getEmpEmergencyContactService()->getEmployeeEmergencyContact($empNumber, $seqNo);
        $this->throwRecordNotFoundExceptionIfNotExist($emergencyContact, EmpEmergencyContact::class);

        return new EndpointResourceResult(
            EmpEmergencyContactModel::class,
            $emergencyContact,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
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
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::BETWEEN, [0, 100])
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/pim/employees/{empNumber}/emergency-contacts/{id}",
     *     tags={"PIM/Employee Emergency Contact"},
     *     summary="Update an Employee's Emergency Contact",
     *     operationId="update-an-employees-emergency-contact",
     *     description="This endpoint allows you to update one of an employee's emergency contacts.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="The numerical employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         description="The numerical ID of the desired emergency contact",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 description="Specify the name of the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="relationship",
     *                 description="Specify the relationship between the employee and the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_HOME_PHONE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="homePhone",
     *                 description="Specify the home phone number of the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_HOME_PHONE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="officePhone",
     *                 description="Specify the office phone number of the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_OFFICE_PHONE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="mobilePhone",
     *                 description="Specify the mobile phone number of the emergency contact",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmpEmergencyContactAPI::PARAM_RULE_MOBILE_PHONE_MAX_LENGTH
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmpEmergencyContactModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The given numerical employee number of the employee", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $empEmergencyContact = $this->saveEmployeeEmergencyContacts();
        return new EndpointResourceResult(
            EmpEmergencyContactModel::class,
            $empEmergencyContact,
            new ParameterBag(
                [CommonParams::PARAMETER_EMP_NUMBER => $empEmergencyContact->getEmployee()->getEmpNumber()]
            )
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
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::BETWEEN, [0, 100])
            ),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return EmpEmergencyContact
     * @throws RecordNotFoundException
     */
    public function saveEmployeeEmergencyContacts(): EmpEmergencyContact
    {
        $seqNo = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $name = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_NAME);
        $relationship = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_RELATIONSHIP
        );
        $homePhone = $this->getRequestParams()->getString(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_HOME_PHONE);
        $mobilePhone = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_MOBILE_PHONE
        );
        $officePhone = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_OFFICE_PHONE
        );
        if ($seqNo) {
            $empEmergencyContact = $this->getEmpEmergencyContactService()->getEmployeeEmergencyContact(
                $empNumber,
                $seqNo
            );
            $this->throwRecordNotFoundExceptionIfNotExist($empEmergencyContact, EmpEmergencyContact::class);
        } else {
            $empEmergencyContact = new EmpEmergencyContact();
            $empEmergencyContact->getDecorator()->setEmployeeByEmpNumber($empNumber);
        }

        $empEmergencyContact->setName($name);
        $empEmergencyContact->setRelationship($relationship);
        $empEmergencyContact->setHomePhone($homePhone);
        $empEmergencyContact->setMobilePhone($mobilePhone);
        $empEmergencyContact->setOfficePhone($officePhone);
        return $this->getEmpEmergencyContactService()->saveEmpEmergencyContact($empEmergencyContact);
    }
}
