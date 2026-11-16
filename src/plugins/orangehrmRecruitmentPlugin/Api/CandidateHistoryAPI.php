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
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Recruitment\Api\Model\CandidateHistoryDetailedModel;
use OrangeHRM\Recruitment\Api\Model\CandidateHistoryListModel;
use OrangeHRM\Recruitment\Dto\CandidateActionHistory;
use OrangeHRM\Recruitment\Dto\CandidateHistorySearchFilterParams;
use OrangeHRM\Recruitment\Service\CandidateService;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;
use OrangeHRM\Recruitment\Traits\Service\VacancyServiceTrait;

class CandidateHistoryAPI extends Endpoint implements CrudEndpoint
{
    use CandidateServiceTrait;
    use VacancyServiceTrait;
    use UserRoleManagerTrait;
    use AuthUserTrait;

    public const PARAMETER_CANDIDATE_ID = 'candidateId';
    public const PARAMETER_HISTORY_ID = 'historyId';
    public const PARAMETER_NOTE = 'note';

    public const PARAMETER_RULE_NOTE_MAX_LENGTH = 2000;

    /**
     * @OA\Get(
     *     path="/api/v2/recruitment/candidates/{candidateId}/history",
     *     tags={"Recruitment/Candidate History"},
     *     summary="List a Candidate's History",
     *     operationId="list-a-candidates-history",
     *     @OA\PathParameter(
     *         name="candidateId",
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
     *                 type="object",
     *                 @OA\Schema(ref="#/components/schemas/Recruitment-CandidateHistoryListModel"),
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
    public function getAll(): EndpointResult
    {
        $candidateHistorySearchFilterParams = new CandidateHistorySearchFilterParams();
        $this->setSortingAndPaginationParams($candidateHistorySearchFilterParams);
        $candidateHistorySearchFilterParams->setCandidateId($this->getCandidateId());
        $candidateHistorySearchFilterParams->setActionIds($this->getAccessibleActionIds());

        $candidateHistoryRecords = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryRecords($candidateHistorySearchFilterParams);

        $count = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryRecordsCount($candidateHistorySearchFilterParams);

        return new EndpointCollectionResult(
            CandidateHistoryListModel::class,
            $candidateHistoryRecords,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => $count])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetAll(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CANDIDATE_ID,
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Candidate::class])
            ),
            ...$this->getSortingAndPaginationParamsRules(CandidateHistorySearchFilterParams::ALLOWED_SORT_FIELDS)
        );
    }

    /**
     * @inheritDoc
     */
    public function create(): EndpointResult
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
    public function delete(): EndpointResult
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

    /**
     * @OA\Get(
     *     path="/api/v2/recruitment/candidates/{candidateId}/history/{historyId}",
     *     tags={"Recruitment/Candidate History"},
     *     summary="Get a Candidate's History Record",
     *     operationId="get-a-candidates-history-record",
     *     @OA\PathParameter(
     *         name="candidateId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="historyId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-CandidateHistoryDetailedModel"
     *             ),
     *             @OA\Property(property="meta", type="object", @OA\Property(property="disabled", type="boolean"))
     *         )
     *     ),
     *     @OA\Response(response="403", ref="#/components/responses/ForbiddenResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $candidateHistoryRecord = $this->getAccessibleCandidateHistoryRecord();
        $vacancy = $candidateHistoryRecord->getVacancy();
        $disabled = false;
        if ($vacancy->getHiringManager()->getEmpNumber() !== null) {
            if ($candidateHistoryRecord->getAction() === WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHORTLIST &&
                (
                    !is_null($vacancy) &&
                    $vacancy->getHiringManager()->getEmpNumber() !== $this->getAuthUser()->getEmpNumber()
                )
            ) {
                $rolesToExclude = ['HiringManager'];
                $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
                    WorkflowStateMachine::FLOW_RECRUITMENT,
                    CandidateService::STATUS_MAP[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY],
                    $rolesToExclude
                );
                $disabled = !in_array(
                    WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHORTLIST,
                    array_keys($allowedWorkflowItems)
                );
            }
        }
        return new EndpointResourceResult(
            CandidateHistoryDetailedModel::class,
            $candidateHistoryRecord,
            new ParameterBag(['disabled' => $disabled])
        );
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CANDIDATE_ID,
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Candidate::class])
            ),
            new ParamRule(
                self::PARAMETER_HISTORY_ID,
                new Rule(Rules::POSITIVE)
            ),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v2/recruitment/candidates/{candidateId}/history/{historyId}",
     *     tags={"Recruitment/Candidate History"},
     *     summary="Update a Candidate's History Record",
     *     operationId="update-a-candidates-history-record",
     *     @OA\PathParameter(
     *         name="candidateId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\PathParameter(
     *         name="historyId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="note", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 ref="#/components/schemas/Recruitment-CandidateHistoryDetailedModel"
     *             ),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response="403", ref="#/components/responses/ForbiddenResponse"),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function update(): EndpointResult
    {
        $candidateVacancy = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateVacancyByCandidateId($this->getCandidateId());

        if (is_null($candidateVacancy)) {
            throw $this->getForbiddenException();
        }

        $candidateHistoryRecord = $this->getAccessibleCandidateHistoryRecord();
        $candidateHistoryRecord->setNote(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NOTE
            )
        );
        $this->getCandidateService()->getCandidateDao()->saveCandidateHistory($candidateHistoryRecord);
        return new EndpointResourceResult(CandidateHistoryDetailedModel::class, $candidateHistoryRecord);
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForUpdate(): ParamRuleCollection
    {
        return new ParamRuleCollection(
            new ParamRule(
                self::PARAMETER_CANDIDATE_ID,
                new Rule(Rules::IN_ACCESSIBLE_ENTITY_ID, [Candidate::class])
            ),
            new ParamRule(
                self::PARAMETER_HISTORY_ID,
                new Rule(Rules::POSITIVE)
            ),
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE),
                    new Rule(Rules::LENGTH, [null, self::PARAMETER_RULE_NOTE_MAX_LENGTH]),
                ),
                true
            )
        );
    }

    /**
     * The requested history record of the requested candidate, for the single read and the update.
     * 404 when it does not exist for this candidate; 403 when its action is outside the set the list shows
     * the auth user.
     *
     * @return CandidateHistory
     */
    private function getAccessibleCandidateHistoryRecord(): CandidateHistory
    {
        $candidateHistoryRecord = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryRecordByCandidateIdAndHistoryId($this->getCandidateId(), $this->getHistoryId());
        $this->throwRecordNotFoundExceptionIfNotExist($candidateHistoryRecord, CandidateHistory::class);
        if (!in_array($candidateHistoryRecord->getAction(), $this->getAccessibleActionIds())) {
            throw $this->getForbiddenException();
        }
        return $candidateHistoryRecord;
    }

    /**
     * Candidate history actions the auth user may see for this candidate. The list, the single read and the
     * update all use this set, so a record left out of the list cannot be read or changed by its id.
     * The Hiring Manager role is only counted when the user manages the candidate's current vacancy (a vacancy
     * without a hiring manager, or no current vacancy, gives it nothing). The current vacancy is looked up as
     * the candidate history list model and page do, so a candidate with more than one vacancy row is not an error.
     *
     * @return int[]
     */
    private function getAccessibleActionIds(): array
    {
        $vacancyId = $this->getCandidateService()
            ->getCandidateDao()
            ->getCurrentVacancyIdByCandidateId($this->getCandidateId());
        $vacancy = is_null($vacancyId)
            ? null
            : $this->getVacancyService()->getVacancyDao()->getVacancyById((int)$vacancyId);
        $hiringManager = $vacancy instanceof Vacancy ? $vacancy->getHiringManager() : null;
        $rolesToExclude = [];
        if (!$hiringManager instanceof Employee
            || $hiringManager->getEmpNumber() !== $this->getAuthUser()->getEmpNumber()) {
            $rolesToExclude = ['HiringManager'];
        }
        return $this->getUserRoleManager()
            ->getAccessibleEntityIds(CandidateActionHistory::class, null, null, $rolesToExclude);
    }

    /**
     * @return int
     */
    private function getCandidateId(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_CANDIDATE_ID
        );
    }

    /**
     * @return int
     */
    private function getHistoryId(): int
    {
        return $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_HISTORY_ID
        );
    }
}
