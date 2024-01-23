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
use OrangeHRM\Entity\EmployeeSkill;
use OrangeHRM\Pim\Api\Model\EmployeeSkillModel;
use OrangeHRM\Pim\Dto\EmployeeSkillSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeSkillService;

class EmployeeSkillAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_SKILL_ID = 'skillId';
    public const PARAMETER_YEARS_OF_EXP = 'yearsOfExperience';
    public const PARAMETER_COMMENTS = 'comments';

    public const PARAM_RULE_YEARS_OF_EXP_MAX_LENGTH = 2;
    public const PARAM_RULE_COMMENTS_MAX_LENGTH = 100;

    /**
     * @var null|EmployeeSkillService
     */
    protected ?EmployeeSkillService $employeeSkillService = null;

    /**
     * @return EmployeeSkillService
     */
    public function getEmployeeSkillService(): EmployeeSkillService
    {
        if (is_null($this->employeeSkillService)) {
            $this->employeeSkillService = new EmployeeSkillService();
        }
        return $this->employeeSkillService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/skills/{id}",
     *     tags={"PIM/Employee Skill"},
     *     summary="Get an Employee's Skill",
     *     operationId="get-an-employees-skill",
     *     description="This endpoint allows you to get one of an employee's skills.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         description="Specify the numerical ID of the desired employee skill",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeSkillModel"
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @return EndpointResourceResult
     * @throws RecordNotFoundException
     */
    public function getOne(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $skillId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $employeeSkill = $this->getEmployeeSkillService()->getEmployeeSkillDao()->getEmployeeSkillById(
            $empNumber,
            $skillId
        );
        $this->throwRecordNotFoundExceptionIfNotExist($employeeSkill, EmployeeSkill::class);

        return new EndpointResourceResult(
            EmployeeSkillModel::class,
            $employeeSkill,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID),
            $this->getEmpNumberRule(),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/skills",
     *     tags={"PIM/Employee Skill"},
     *     summary="List an Employee's Skills",
     *     operationId="list-an-employees-skills",
     *     description="This endpoint allows you to list an employee's skills.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         description="Sort the employee's skill by name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeSkillSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 @OA\Items(ref="#/components/schemas/Pim-EmployeeSkillModel")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", description="The total number of employee skills", type="integer"),
     *                 @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @return EndpointCollectionResult
     */
    public function getAll(): EndpointCollectionResult
    {
        $employeeSkillSearchParams = new EmployeeSkillSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeSkillSearchParams);

        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $employeeSkillSearchParams->setEmpNumber($empNumber);

        $employeeSkills = $this->getEmployeeSkillService()->getEmployeeSkillDao()->searchEmployeeSkill(
            $employeeSkillSearchParams
        );

        return new EndpointCollectionResult(
            EmployeeSkillModel::class,
            $employeeSkills,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmployeeSkillService()->getEmployeeSkillDao(
                    )->getSearchEmployeeSkillsCount(
                        $employeeSkillSearchParams
                    )
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
            $this->getEmpNumberRule(),
            ...$this->getSortingAndPaginationParamsRules(EmployeeSkillSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/pim/employees/{empNumber}/skills",
     *     tags={"PIM/Employee Skill"},
     *     summary="Add a Skill to an Employee",
     *     operationId="add-a-skill-to-an-employee",
     *     description="This endpoint allows you to create a skill for a particular employee.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="yearsOfExperience",
     *                 description="Specify the years of experience in the skill",
     *                 type="integer",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeSkillAPI::PARAM_RULE_YEARS_OF_EXP_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="comments",
     *                 description="Specify the comment regarding the skill",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeSkillAPI::PARAM_RULE_COMMENTS_MAX_LENGTH
     *             ),
     *             @OA\Property(property="skillId", description="Specify the numerical ID of the skill", type="integer"),
     *             required={"skillId"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeSkillModel"
     *             ),
     *             @OA\Property(property="empNumber", description="The employee number given in the request", type="integer")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResourceResult
    {
        $employeeSkill = $this->saveEmployeeSkill();
        return new EndpointResourceResult(
            EmployeeSkillModel::class,
            $employeeSkill,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $employeeSkill->getEmployee()->getEmpNumber(),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_SKILL_ID, new Rule(Rules::REQUIRED)),
            $this->getEmpNumberRule(),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    private function getCommonBodyValidationRules(): array
    {
        return [
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_COMMENTS,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_COMMENTS_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_YEARS_OF_EXP,
                    new Rule(Rules::INT_TYPE),
                    new Rule((Rules::ZERO_OR_POSITIVE)),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_YEARS_OF_EXP_MAX_LENGTH]),
                ),
                true
            ),
        ];
    }

    /**
     * @OA\Put(
     *     path="/api/v2/pim/employees/{empNumber}/skills/{id}",
     *     tags={"PIM/Employee Skill"},
     *     summary="Update an Employee's Skill",
     *     operationId="update-an-employees-skill",
     *     description="This endpoint allows you to update an employee's skill.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number for the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         description="Specify the numerical ID of the desired employee skill",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="yearsOfExperience",
     *                 description="Specify the years of experience in the skill",
     *                 type="integer",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeSkillAPI::PARAM_RULE_YEARS_OF_EXP_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="comments",
     *                 description="Specify the comment regarding the skill",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeSkillAPI::PARAM_RULE_COMMENTS_MAX_LENGTH
     *             ),
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeSkillModel"
     *             ),
     *             @OA\Property(property="empNumber", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResourceResult
    {
        $employeeSkill = $this->saveEmployeeSkill();

        return new EndpointResourceResult(
            EmployeeSkillModel::class,
            $employeeSkill,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $employeeSkill->getEmployee()->getEmpNumber(),
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::REQUIRED)),
            $this->getEmpNumberRule(),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/pim/employees/{empNumber}/skills",
     *     tags={"PIM/Employee Skill"},
     *     summary="Delete an Employee's Skills",
     *     operationId="delete-an-employees-skills",
     *     description="This endpoint allows you to delete an employee's skills.",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         description="Specify the employee number of the desired employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function delete(): EndpointResourceResult
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $ids = $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS);
        $this->getEmployeeSkillService()->getEmployeeSkillDao()->deleteEmployeeSkills($empNumber, $ids);
        return new EndpointResourceResult(
            ArrayModel::class,
            $ids,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                ]
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getEmpNumberRule(),
            new ParamRule(CommonParams::PARAMETER_IDS),
        );
    }

    /**
     * @return EmployeeSkill
     */
    public function saveEmployeeSkill(): EmployeeSkill
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $skillId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_SKILL_ID);
        $comments = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_COMMENTS
        );
        $yrsOfExp = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_YEARS_OF_EXP
        );
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        if (!empty($skillId)) { // create operation
            $id = $skillId;
        }
        $employeeSkill = $this->getEmployeeSkillService()->getEmployeeSkillDao()->getEmployeeSkillById(
            $empNumber,
            $id
        );
        if ($employeeSkill == null) {
            $employeeSkill = new EmployeeSkill();
            $employeeSkill->getDecorator()->setEmployeeByEmpNumber($empNumber);
            $employeeSkill->getDecorator()->setSkillBySkillId($id);
        }
        $employeeSkill->setYearsOfExp($yrsOfExp);
        $employeeSkill->setComments($comments);

        return $this->getEmployeeSkillService()->getEmployeeSkillDao()->saveEmployeeSkill($employeeSkill);
    }

    /**
     * @return ParamRule
     */
    private function getEmpNumberRule(): ParamRule
    {
        return new ParamRule(
            CommonParams::PARAMETER_EMP_NUMBER,
            new Rule(Rules::IN_ACCESSIBLE_EMP_NUMBERS)
        );
    }
}
