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
use OrangeHRM\Core\Api\V2\CollectionEndpoint;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointCollectionResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Model\ArrayModel;
use OrangeHRM\Core\Api\V2\ParameterBag;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;

class CandidateAllowedActionAPI extends Endpoint implements CollectionEndpoint
{
    use CandidateServiceTrait;
    use UserRoleManagerTrait;
    use AuthUserTrait;

    public const PARAMETER_CANDIDATE_ID = 'candidateId';

    public const MAX_ALLOWED_INTERVIEW_COUNT = 2;

    public const STATE_INITIAL = 'INITIAL';

    public const ACTIONABLE_STATES_MAP = [
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY => 'Initiate Application',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHORTLIST => 'Shortlist',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_REJECT => 'Reject',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW => 'Schedule Interview',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED => 'Mark Interview Passed',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED => 'Mark Interview Failed',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_OFFER_JOB => 'Offer Job',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_DECLINE_OFFER => 'Decline Offer',
        WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HIRE => 'Hire',
    ];

    /**
     * @OA\Get(
     *     path="/api/v2/recruitment/candidates/{candidateId}/actions/allowed",
     *     tags={"Recruitment/Candidate Workflow"},
     *     summary="Get Allowed Actions for Candidate",
     *     operationId="get-allowed-actions-for-candidate",
     *     @OA\PathParameter(
     *         name="candidateId",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="label", type="string"),
     *                 ),
     *             ),
     *             @OA\Property(property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="404", ref="#/components/responses/RecordNotFound")
     * )
     * @inheritDoc
     */
    public function getAll(): EndpointResult
    {
        $candidateId = $this->getRequestParams()->getInt(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_CANDIDATE_ID
        );

        $candidate = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateById($candidateId);
        $this->throwRecordNotFoundExceptionIfNotExist($candidate, Candidate::class);

        $candidateVacancy = $this->getCandidateService()
            ->getCandidateDao()
            ->getCandidateVacancyByCandidateId($candidateId);

        if (!is_null($candidateVacancy)) {
            /**
             * if vacancy is closed, no action is allowed to perform on candidates, assigned to the vacancy
             */
            if (!$candidateVacancy->getVacancy()->getStatus()) {
                return new EndpointCollectionResult(
                    ArrayModel::class,
                    [],
                    new ParameterBag([CommonParams::PARAMETER_TOTAL => 0])
                );
            }
        }

        $currentState = is_null($candidateVacancy) ? self::STATE_INITIAL : $candidateVacancy->getStatus();

        $rolesToExclude = [];
        if (!is_null($candidateVacancy) && !$this->isHiringManager($candidateVacancy)) {
            $rolesToExclude = ['HiringManager'];
        }

        $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
            WorkflowStateMachine::FLOW_RECRUITMENT,
            $currentState,
            $rolesToExclude
        );
        if (!is_null($candidateVacancy)) {
            $interviewCount = $this->getCandidateService()
                ->getCandidateDao()
                ->getInterviewCountByCandidateIdAndVacancyId($candidateId, $candidateVacancy->getVacancy()->getId());
            if ($interviewCount >= self::MAX_ALLOWED_INTERVIEW_COUNT &&
                in_array(
                    WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW,
                    array_keys($allowedWorkflowItems)
                )) {
                unset($allowedWorkflowItems[WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW]);
            }
        }
        ksort($allowedWorkflowItems);

        $actionableStates = array_map(function ($item) {
            $actionableState['id'] = $item->getAction();
            $actionableState['label'] = self::ACTIONABLE_STATES_MAP[$item->getAction()];
            return $actionableState;
        }, $allowedWorkflowItems);

        return new EndpointCollectionResult(
            ArrayModel::class,
            $actionableStates,
            new ParameterBag([CommonParams::PARAMETER_TOTAL => count($actionableStates)])
        );
    }

    /**
     * @param CandidateVacancy $candidateVacancy
     * @return bool
     */
    private function isHiringManager(CandidateVacancy $candidateVacancy): bool
    {
        $hiringMangerEmpNumber = null;
        if ($candidateVacancy->getVacancy()->getHiringManager()) {
            $hiringMangerEmpNumber = $candidateVacancy->getVacancy()
                ->getHiringManager()
                ->getEmpNumber();
        }
        return $hiringMangerEmpNumber === $this->getAuthUser()->getEmpNumber();
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
            )
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
}
