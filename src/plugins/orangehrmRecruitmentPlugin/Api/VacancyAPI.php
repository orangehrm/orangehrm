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

namespace OrangeHRM\Recruitment\Api;

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\CrudEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\BadRequestException;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\JobTitle;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Recruitment\Api\Model\VacancyDetailedModel;
use OrangeHRM\Recruitment\Api\Model\VacancyModel;
use OrangeHRM\Recruitment\Api\Model\VacancySummaryModel;
use OrangeHRM\Recruitment\Dto\VacancySearchFilterParams;
use OrangeHRM\Recruitment\Traits\Service\VacancyServiceTrait;

class VacancyAPI extends Endpoint implements CrudEndpoint
{
    use VacancyServiceTrait;
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;

    public const FILTER_JOB_TITLE_ID = 'jobTitleId';
    public const FILTER_VACANCY_ID = 'vacancyId';
    public const FILTER_HIRING_MANAGER_ID = 'hiringManagerId';
    public const FILTER_STATUS = 'status';
    public const FILTER_NAME = 'name';
    public const FILTER_EXCLUDE_INTERVIEWERS = 'excludeInterviewers';
    public const FILTER_MODEL = 'model';

    public const PARAMETER_NAME = 'name';
    public const PARAMETER_DESCRIPTION = 'description';
    public const PARAMETER_NUM_OF_POSITIONS = 'numOfPositions';
    public const PARAMETER_STATUS = 'status';
    public const PARAMETER_IS_PUBLISHED = 'isPublished';
    public const PARAMETER_JOB_TITLE_ID = 'jobTitleId';
    public const PARAMETER_EMPLOYEE_ID = 'employeeId';

    public const PARAMETER_RULE_NAME_MAX_LENGTH = 100;
    public const PARAMETER_RULE_NO_OF_POSITIONS_MAX_LENGTH = 13;

    public const MODEL_DEFAULT = 'default';
    public const MODEL_SUMMARY = 'summary';
    public const MODEL_DETAILED = 'detailed';

    public const MODEL_MAP = [
        self::MODEL_DEFAULT => VacancyModel::class,
        self::MODEL_SUMMARY => VacancySummaryModel::class,
        self::MODEL_DETAILED => VacancyDetailedModel::class,
    ];

    /**
     * @OA\Get(
     *     path="/api/v2/recruitment/vacancies/{id}",
     *     tags={"Recruitment/Vacancy"},
     *     summary="Get a Vacancy",
     *     operationId="get-a-vacancy",
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
     *                 ref="#/components/schemas/Recruitment-VacancyDetailedModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResourceResult
    {
        $id = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            CommonParams::PARAMETER_ID
        );
        $vacancy = $this->getVacancyService()->getVacancyDao()->getVacancyById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($vacancy, Vacancy::class);
        return new EndpointResourceResult(VacancyDetailedModel::class, $vacancy);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_ID,
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Vacancy::class])
            )
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v2/recruitment/vacancies",
     *     tags={"Recruitment/Vacancy"},
     *     summary="List All Vacancies",
     *     operationId="list-all-vacancies",
     *     @OA\Parameter(
     *         name="sortField",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum=VacancySearchFilterParams::ALLOWED_SORT_FIELDS)
     *     ),
     *     @OA\Parameter(
     *         name="jobTitleId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="hiringManagerId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="vacancyId",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
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
     *                 @OA\Items(oneOf={
     *                     @OA\Schema(ref="#/components/schemas/Recruitment-VacancyModel"),
     *                     @OA\Schema(ref="#/components/schemas/Recruitment-VacancySummaryModel"),
     *                     @OA\Schema(ref="#/components/schemas/Recruitment-VacancyDetailedModel")
     *                 })
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function getAll(): EndpointCollectionResult
    {
        $vacancyParamHolder = new VacancySearchFilterParams();
        $this->setSortingAndPaginationParams($vacancyParamHolder);

        $vacancyId = $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_VACANCY_ID
        );

        $excludeInterviewers = $this->getRequestParams()->getBoolean(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_EXCLUDE_INTERVIEWERS,
        );

        if (!is_null($vacancyId)) {
            $vacancyParamHolder->setVacancyIds([$vacancyId]);
        } else {
            $rolesToExclude = [];
            if ($excludeInterviewers) {
                $rolesToExclude = ['Interviewer'];
            }
            $accessibleVacancyIds = $this->getUserRoleManager()
                ->getAccessibleEntityIds(Vacancy::class, null, null, $rolesToExclude);
            $vacancyParamHolder->setVacancyIds($accessibleVacancyIds);
        }

        $vacancyParamHolder->setJobTitleId(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_JOB_TITLE_ID
            )
        );
        $vacancyParamHolder->setEmpNumber(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_HIRING_MANAGER_ID
            )
        );
        $vacancyParamHolder->setStatus(
            $this->getRequestParams()->getBooleanOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_STATUS
            )
        );
        $vacancyParamHolder->setName(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_QUERY,
                self::FILTER_NAME
            )
        );
        $vacancies = $this->getVacancyService()->getVacancyDao()->getVacancies($vacancyParamHolder);
        $count = $this->getVacancyService()->getVacancyDao()->getVacanciesCount($vacancyParamHolder);
        return new EndpointCollectionResult(
            $this->getModelClass(),
            $vacancies,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_VACANCY_ID,
                    new Rule(Rules::POSITIVE),
                    new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Vacancy::class])
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_HIRING_MANAGER_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_JOB_TITLE_ID,
                    new Rule(Rules::POSITIVE)
                )
            ),
            new ParamRule(
                self::FILTER_STATUS,
                new Rule(Rules::BOOL_VAL)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::FILTER_NAME,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [0, self::PARAMETER_RULE_NAME_MAX_LENGTH])
                )
            ),
            new ParamRule(
                self::FILTER_EXCLUDE_INTERVIEWERS,
                new Rule(Rules::BOOL_VAL),
            ),
            $this->getModelClassParamRule(),
            ...$this->getSortingAndPaginationParamsRules(VacancySearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        $model = $this->getRequestParams()->getString(
            RequestParams::PARAM_TYPE_QUERY,
            self::FILTER_MODEL,
            self::MODEL_DEFAULT,
        );
        return self::MODEL_MAP[$model];
    }

    /**
     * @return ParamRule
     */
    protected function getModelClassParamRule(): ParamRule
    {
        return $this->getValidationDecorator()->notRequiredParamRule(
            new ParamRule(
                self::FILTER_MODEL,
                new Rule(Rules::IN, [array_keys(self::MODEL_MAP)])
            ),
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v2/recruitment/vacancies",
     *     tags={"Recruitment/Vacancy"},
     *     summary="Create a Vacancy",
     *     operationId="create-a-vacancy",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="jobTitleId", type="integer"),
     *             @OA\Property(property="isPublished", type="boolean"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(
     *                 property="numOfPositions",
     *                 type="integer",
     *                 maximum=OrangeHRM\Recruitment\Api\VacancyAPI::PARAMETER_RULE_NO_OF_POSITIONS_MAX_LENGTH
     *             ),
     *             @OA\Property(property="employeeId", type="integer"),
     *             required={"name", "status", "jobTitleId", "isPublished", "employeeId"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-VacancyDetailedModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     *
     * @inheritDoc
     */
    public function create(): EndpointResult
    {
        $vacancy = new Vacancy();
        $vacancy->setDefinedTime($this->getDateTimeHelper()->getNow());
        $this->setVacancy($vacancy);
        $vacancy = $this->getVacancyService()->getVacancyDao()->saveJobVacancy($vacancy);

        return new EndpointResourceResult(VacancyDetailedModel::class, $vacancy);
    }

    /**
     * @param Vacancy $vacancy
     * @throws BadRequestException
     */
    private function setVacancy(Vacancy $vacancy): void
    {
        $jobTitleId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_JOB_TITLE_ID
        );
        if (!$this->getVacancyService()->getVacancyDao()->isActiveJobTitle($jobTitleId)) {
            throw $this->getBadRequestException('Please Select An Active Job Title');
        }

        $hiringManagerId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_BODY,
            self::PARAMETER_EMPLOYEE_ID
        );
        if (!$this->getVacancyService()->getVacancyDao()->isActiveHiringManger($hiringManagerId)) {
            throw $this->getBadRequestException('Employee No Longer Exists');
        }

        $vacancy->getDecorator()->setJobTitleById($jobTitleId);
        $vacancy->getDecorator()->setEmployeeById($hiringManagerId);

        $vacancy->getDecorator()->setEmployeeById(
            $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_EMPLOYEE_ID
            )
        );
        $vacancy->setName(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NAME
            )
        );
        $vacancy->setDescription(
            $this->getRequestParams()->getString(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_DESCRIPTION
            )
        );
        $vacancy->setNumOfPositions(
            $this->getRequestParams()->getIntOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NUM_OF_POSITIONS
            )
        );
        $vacancy->setIsPublished(
            $this->getRequestParams()->getBoolean(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_IS_PUBLISHED,
                true
            )
        );
        $vacancy->setUpdatedTime($this->getDateTimeHelper()->getNow());
        $vacancy->setStatus(
            $this->getRequestParams()->getBoolean(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_STATUS,
                true
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForCreate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @return ParamRule[]
     */
    protected function getCommonBodyValidationRules(): array
    {
        return [
            new ParamRule(
                self::PARAMETER_NAME,
                new Rule(Rules::STRING_TYPE),
                new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NAME_MAX_LENGTH])
            ),
            new ParamRule(
                self::PARAMETER_STATUS,
                new Rule(Rules::BOOL_TYPE),
            ),
            new ParamRule(
                self::PARAMETER_JOB_TITLE_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [JobTitle::class]),
            ),
            new ParamRule(
                self::PARAMETER_IS_PUBLISHED,
                new Rule(Rules::BOOL_TYPE),
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_DESCRIPTION,
                    new Rule(Rules::STRING_TYPE),
                ),
                true
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NUM_OF_POSITIONS,
                    new Rule(Rules::INT_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NO_OF_POSITIONS_MAX_LENGTH])
                )
            ),
            new ParamRule(
                self::PARAMETER_EMPLOYEE_ID,
                new Rule(Rules::POSITIVE),
                new Rule(Rules::ENTITY_ID_EXISTS, [Employee::class])
            ),
        ];
    }

    /**
     * @OA\Put(
     *     path="/api/v2/recruitment/vacancies/{id}",
     *     tags={"Recruitment/Vacancy"},
     *     summary="Update a Vacancy",
     *     operationId="update-a-vacancy",
     *     @OA\PathParameter(
     *         name="id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="status", type="boolean"),
     *             @OA\Property(property="jobTitleId", type="integer"),
     *             @OA\Property(property="isPublished", type="boleean"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(
     *                 property="numOfPositions",
     *                 type="integer",
     *                 maximum=OrangeHRM\Recruitment\Api\VacancyAPI::PARAMETER_RULE_NO_OF_POSITIONS_MAX_LENGTH
     *             ),
     *             @OA\Property(property="employeeId", type="integer"),
     *             required={"name", "status", "jobTitleId", "isPublished", "employeeId"}
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-VacancyDetailedModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $id = $this->getRequestParams()->getInt(RequestParams::PARAM_TYPE_ATTRIBUTE, CommonParams::PARAMETER_ID);
        $vacancy = $this->getVacancyService()->getVacancyDao()->getVacancyById($id);
        $this->throwRecordNotFoundExceptionIfNotExist($vacancy, Vacancy::class);
        $this->setVacancy($vacancy);
        $this->getVacancyService()->getVacancyDao()->saveJobVacancy($vacancy);
        return new EndpointResourceResult(VacancyDetailedModel::class, $vacancy);
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
            ...$this->getCommonBodyValidationRules(),
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/v2/recruitment/vacancies",
     *     tags={"Recruitment/Vacancy"},
     *     summary="Delete Vacancies",
     *     operationId="delete-vacancies",
     *     @OA\RequestBody(ref="#/components/requestBodies/DeleteRequestBody"),
     *     @OA\Response(response="200", ref="#/components/responses/DeleteResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function delete(): EndpointResult
    {
        $ids = $this->getVacancyService()->getVacancyDao()->getExistingVacancyIds(
            $this->getRequestParams()->getArray(RequestParams::PARAM_TYPE_BODY, CommonParams::PARAMETER_IDS)
        );
        $this->throwRecordNotFoundExceptionIfEmptyIds($ids);
        $this->getVacancyService()->getVacancyDao()->deleteVacancies($ids);
        return new EndpointResourceResult(ArrayModel::class, $ids);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForDelete(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                CommonParams::PARAMETER_IDS,
                new Rule(Rules::ARRAY_TYPE)
            ),
        );
    }
}
