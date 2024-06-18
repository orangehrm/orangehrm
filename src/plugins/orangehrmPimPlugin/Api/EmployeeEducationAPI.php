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

use Exception;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Entity\EmployeeEducation;
use OrangeHRM\Pim\Api\Model\EmployeeEducationModel;
use OrangeHRM\Pim\Dto\EmployeeEducationSearchFilterParams;
use OrangeHRM\Pim\Service\EmployeeEducationService;

class EmployeeEducationAPI extends Endpoint implements CrudEndpoint
{
    public const PARAMETER_EDUCATION_ID = 'educationId';
    public const PARAMETER_INSTITUTE = 'institute';
    public const PARAMETER_MAJOR = 'major';
    public const PARAMETER_YEAR = 'year';
    public const PARAMETER_SCORE = 'score';
    public const PARAMETER_START_DATE = 'startDate';
    public const PARAMETER_END_DATE = 'endDate';

    public const PARAM_RULE_INSTITUTE_MAX_LENGTH = 100;
    public const PARAM_RULE_MAJOR_MAX_LENGTH = 100;
    public const PARAM_RULE_YEAR_MAX_LENGTH = 4;
    public const PARAM_RULE_SCORE_MAX_LENGTH = 25;

    /**
     * @var null|EmployeeEducationService
     */
    protected ?EmployeeEducationService $employeeEducationService = null;

    /**
     * @return EmployeeEducationService
     */
    public function getEmployeeEducationService(): EmployeeEducationService
    {
        if (is_null($this->employeeEducationService)) {
            $this->employeeEducationService = new EmployeeEducationService();
        }
        return $this->employeeEducationService;
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/educations/{id}",
     *     tags={"PIM/Employee Education"},
     *     summary="Get an Employee's Educational Qualification",
     *     operationId="get-an-employees-educational-qualification",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeEducationModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="empNumber", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        list($empNumber, $id) = $this->getUrlAttributes();
        $employeeEducation = $this->getEmployeeEducationService()
            ->getEmployeeEducationDao()
            ->getEmployeeEducationById($empNumber, $id);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeEducation, EmployeeEducation::class);

        return new EndpointResourceResult(
            EmployeeEducationModel::class,
            $employeeEducation,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @return array
     */
    private function getUrlAttributes(): array
    {
        $empNumber = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_EMP_NUMBER
        );
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        return [$empNumber, $id];
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
            $this->getEmpNumberRule(),
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/pim/employees/{empNumber}/educations",
     *     tags={"PIM/Employee Education"},
     *     summary="List an Employee's Educational Qualifications",
     *     operationId="list-an-employees-educational-qualifications",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=EmployeeEducationSearchFilterParams::ALLOWED_SORT_FIELDS)
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
     *                 ref="#/components/schemas/Pim-EmployeeEducationModel"
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer"),
     *                 @OA\Property(property="empNumber", type="integer")
     *             )
     *         )
     *     ),
     * )
     *
     * @return EndpointCollectionResult
     * @throws Exception
     */
    public function getAll(): EndpointCollectionResult
    {
        list($empNumber) = $this->getUrlAttributes();
        $employeeEducationSearchParams = new EmployeeEducationSearchFilterParams();
        $this->setSortingAndPaginationParams($employeeEducationSearchParams);
        $employeeEducationSearchParams->setEmpNumber($empNumber);

        $employeeEducations = $this->getEmployeeEducationService()->getEmployeeEducationDao()->searchEmployeeEducation(
            $employeeEducationSearchParams
        );

        return new EndpointCollectionResult(
            EmployeeEducationModel::class,
            $employeeEducations,
            new ParameterBag(
                [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_TOTAL => $this->getEmployeeEducationService()->getEmployeeEducationDao(
                    )->getSearchEmployeeEducationsCount(
                        $employeeEducationSearchParams
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
            ...$this->getSortingAndPaginationParamsRules(EmployeeEducationSearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/pim/employees/{empNumber}/educations",
     *     tags={"PIM/Employee Education"},
     *     summary="Add an Educational Qualification to an Employee",
     *     operationId="add-an-educational-qualification-to-an-employee",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="educationId", type="integer"),
     *             @OA\Property(
     *                 property="institute",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeEducationAPI::PARAM_RULE_INSTITUTE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="major",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeEducationAPI::PARAM_RULE_MAJOR_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="year",
     *                 type="integer",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeEducationAPI::PARAM_RULE_YEAR_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="score",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeEducationAPI::PARAM_RULE_SCORE_MAX_LENGTH
     *             ),
     *             @OA\Property(property="startDate", type="string", format="date"),
     *             @OA\Property(property="endDate", type="string", format="date"),
     *             required={"educationId"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeEducationModel"
     *             ),
     *             @OA\Property(property="meta")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function create(): EndpointResourceResult
    {
        list($empNumber) = $this->getUrlAttributes();
        $educationId = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_EDUCATION_ID);
        $employeeEducation = new EmployeeEducation();
        $employeeEducation->getDecorator()->setEmployeeByEmpNumber($empNumber);
        $employeeEducation->getDecorator()->setEducationByEducationId($educationId);
        $employeeEducation = $this->saveEmployeeEducation($employeeEducation);
        return new EndpointResourceResult(
            EmployeeEducationModel::class,
            $employeeEducation,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(self::PARAMETER_EDUCATION_ID, new Rule(Rules::REQUIRED), new Rule(Rules::POSITIVE)),
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
                    self::PARAMETER_INSTITUTE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_INSTITUTE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_MAJOR,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_MAJOR_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_YEAR,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_YEAR_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_SCORE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAM_RULE_SCORE_MAX_LENGTH]),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_START_DATE,
                    new Rule(Rules::API_DATE),
                ),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_END_DATE,
                    new Rule(Rules::API_DATE),
                    new Rule(
                        Rules::GREATER_THAN,
                        [$this->getRequestParams()->getDateTimeOrNull(RequestParams::PARAM_TYPE_BODY, self::PARAMETER_START_DATE)]
                    )
                ),
            ),
        ];
    }

    /**
     * @OA\Put(
     *     path="/api/v2/pim/employees/{empNumber}/educations/{id}",
     *     tags={"PIM/Employee Education"},
     *     summary="Update an Employee's Educational Qualification",
     *     operationId="update-an-employees-educational-qualification",
     *     @OA\PathParameter(
     *         name="empNumber",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="educationId", type="integer"),
     *             @OA\Property(
     *                 property="institute",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeEducationAPI::PARAM_RULE_INSTITUTE_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="major",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeEducationAPI::PARAM_RULE_MAJOR_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="year",
     *                 type="integer",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeEducationAPI::PARAM_RULE_YEAR_MAX_LENGTH
     *             ),
     *             @OA\Property(
     *                 property="score",
     *                 type="string",
     *                 maxLength=OrangeHRM\Pim\Api\EmployeeEducationAPI::PARAM_RULE_SCORE_MAX_LENGTH
     *             ),
     *             @OA\Property(property="startDate", type="string", format="date"),
     *             @OA\Property(property="endDate", type="string", format="date"),
     *             required={"educationId"}
     *         )
     *     ),
     *     @OA\Response(response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Pim-EmployeeEducationModel"
     *             ),
     *             @OA\Property(property="empNumber", type="integer")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     * @throws Exception
     */
    public function update(): EndpointResourceResult
    {
        list($empNumber, $id) = $this->getUrlAttributes();
        $employeeEducation = $this->getEmployeeEducationService()
            ->getEmployeeEducationDao()
            ->getEmployeeEducationById($empNumber, $id);
        $this->throwRecordNotFoundExceptionIfNotExist($employeeEducation, EmployeeEducation::class);
        $this->saveEmployeeEducation($employeeEducation);

        return new EndpointResourceResult(
            EmployeeEducationModel::class,
            $employeeEducation,
            new ParameterBag([CommonParams::PARAMETER_EMP_NUMBER => $empNumber])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(CommonParams::PARAMETER_ID, new Rule(Rules::REQUIRED), new Rule(Rules::POSITIVE)),
            $this->getEmpNumberRule(),
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/pim/employees/{empNumber}/educations",
     *     tags={"PIM/Employee Education"},
     *     summary="Delete an Employee's Educational Qualifications",
     *     operationId="delete-an-employees-educational-qualifications",
     *     @OA\PathParameter(
     *         name="empNumber",
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
        $ids = $this->getEmployeeEducationService()->getEmployeeEducationDao()->getExistingEmpEducationIdsByEmpNumber(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS),
            $empNumber
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getEmployeeEducationService()->getEmployeeEducationDao()->deleteEmployeeEducations($empNumber, $ids);
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
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            )
        );
    }

    /**
     * @param EmployeeEducation $employeeEducation
     * @return EmployeeEducation
     */
    public function saveEmployeeEducation(EmployeeEducation $employeeEducation): EmployeeEducation
    {
        $year = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_YEAR
        );
        $score = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_SCORE
        );
        $institute = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_INSTITUTE
        );
        $major = $this->getRequestParams()->getStringOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_MAJOR
        );
        $startDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_START_DATE
        );
        $endDate = $this->getRequestParams()->getDateTimeOrNull(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_END_DATE
        );

        $employeeEducation->setYear($year);
        $employeeEducation->setScore($score);
        $employeeEducation->setInstitute($institute);
        $employeeEducation->setMajor($major);
        $employeeEducation->setStartDate($startDate);
        $employeeEducation->setEndDate($endDate);

        return $this->getEmployeeEducationService()
            ->getEmployeeEducationDao()
            ->saveEmployeeEducation($employeeEducation);
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
