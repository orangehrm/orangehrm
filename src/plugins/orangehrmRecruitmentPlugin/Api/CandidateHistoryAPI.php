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
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Recruitment\Api\Model\CandidateHistoryDetailedModel;
use OrangeHRM\Recruitment\Api\Model\CandidateHistoryListModel;
use OrangeHRM\Recruitment\Dto\CandidateActionHistory;
use OrangeHRM\Recruitment\Dto\CandidateHistorySearchFilterParams;
use OrangeHRM\Recruitment\Service\CandidateService;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;

class CandidateHistoryAPI extends Endpoint implements CrudEndpoint
{
    use CandidateServiceTrait;
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
        $candidateVacancy = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateVacancyByCandidateId($this->getCandidateId());
        $rolesToExclude = [];
        if ($candidateVacancy instanceof CandidateVacancy) {
            if ($candidateVacancy->getVacancy()->getHiringManager() !== null) {
                $hiringMangerEmpNumber = $candidateVacancy->getVacancy()->getHiringManager()->getEmpNumber();
                if ($hiringMangerEmpNumber !== $this->getAuthUser()->getEmpNumber()) {
                    $rolesToExclude = ['HiringManager'];
                }
            }
        }
        $accessibleActionHistoryIds = $this->getUserRoleManager()
            ->getAccessibleEntityIds(CandidateActionHistory::class, null, null, $rolesToExclude);
        $candidateHistorySearchFilterParams->setActionIds($accessibleActionHistoryIds);

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
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     *
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        $candidateHistoryRecord = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryRecordByCandidateIdAndHistoryId($this->getCandidateId(), $this->getHistoryId());

        $this->throwRecordNotFoundExceptionIfNotExist($candidateHistoryRecord, CandidateHistory::class);
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

        $candidateHistoryRecord = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateHistoryRecordByCandidateIdAndHistoryId($this->getCandidateId(), $this->getHistoryId());

        $this->throwRecordNotFoundExceptionIfNotExist($candidateHistoryRecord, CandidateHistory::class);

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
