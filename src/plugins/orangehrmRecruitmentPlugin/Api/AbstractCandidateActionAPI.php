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

use Exception;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\EndpointResourceResult;
use OrangeHRM\Core\Api\V2\EndpointResult;
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Api\V2\Exception\RecordNotFoundException;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Api\V2\ResourceEndpoint;
use OrangeHRM\Core\Api\V2\Validator\ParamRule;
use OrangeHRM\Core\Api\V2\Validator\ParamRuleCollection;
use OrangeHRM\Core\Api\V2\Validator\Rule;
use OrangeHRM\Core\Api\V2\Validator\Rules;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\Candidate;
use OrangeHRM\Entity\CandidateHistory;
use OrangeHRM\Entity\CandidateVacancy;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Recruitment\Api\Model\CandidateHistoryDefaultModel;
use OrangeHRM\Recruitment\Service\CandidateService;
use OrangeHRM\Recruitment\Traits\Service\CandidateServiceTrait;

abstract class AbstractCandidateActionAPI extends Endpoint implements ResourceEndpoint
{
    use CandidateServiceTrait;
    use EntityManagerHelperTrait;
    use AuthUserTrait;
    use DateTimeHelperTrait;
    use UserRoleManagerTrait;
    use EmployeeServiceTrait;

    public const PARAMETER_CANDIDATE_ID = 'candidateId';
    public const PARAMETER_INTERVIEW_ID = 'interviewId';
    public const PARAMETER_NOTE = 'note';

    /**
     * @inheritDoc
     */
    public function getOne(): EndpointResult
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     */
    public function getValidationRuleForGetOne(): ParamRuleCollection
    {
        throw $this->getNotImplementedException();
    }

    /**
     * @inheritDoc
     * @throws TransactionException|ForbiddenException | RecordNotFoundException
     */
    public function update(): EndpointResult
    {
        $this->beginTransaction();
        try {
            $candidateId = $this->getRequestParams()->getInt(
                RequestParams::PARAM_TYPE_ATTRIBUTE,
                self::PARAMETER_CANDIDATE_ID
            );

            $candidateVacancy = $this->getCandidateService()
                ->getCandidateDao()
                ->getCandidateVacancyByCandidateId($candidateId);
            $this->throwRecordNotFoundExceptionIfNotExist($candidateVacancy, CandidateVacancy::class);

            $vacancy = $candidateVacancy->getVacancy();

            if (!$vacancy->getStatus()) {
                throw $this->getForbiddenException();
            }

            $rolesToExclude = [];
            if (!$this->isAuthUserHiringManager($vacancy)) {
                $rolesToExclude = ['HiringManager'];
            }

            $allowedWorkflowItems = $this->getUserRoleManager()->getAllowedActions(
                WorkflowStateMachine::FLOW_RECRUITMENT,
                $candidateVacancy->getStatus(),
                $rolesToExclude
            );
            if (
                !in_array($this->getResultingState(), array_keys($allowedWorkflowItems))
            ) {
                throw $this->getForbiddenException();
            }

            $candidateVacancy->setStatus(
                CandidateService::STATUS_MAP[$this->getResultingState()]
            );

            $this->getCandidateService()->getCandidateDao()->saveCandidateVacancy($candidateVacancy);

            if ($this->getResultingState() === WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HIRE) {
                $employee = new Employee();
                $this->setCandidateAsEmployee($candidateVacancy, $employee);
                $this->getEmployeeService()->getEmployeeDao()->saveEmployee($employee);
            }

            $candidateHistory = new CandidateHistory();
            $this->setCandidateHistory($candidateHistory, $candidateVacancy);
            $result = $this->getCandidateService()->getCandidateDao()->saveCandidateHistory($candidateHistory);

            $this->commitTransaction();
            return new EndpointResourceResult(CandidateHistoryDefaultModel::class, $result);
        } catch (RecordNotFoundException|ForbiddenException $e) {
            $this->rollBackTransaction();
            throw $e;
        } catch (Exception $e) {
            $this->rollBackTransaction();
            throw new TransactionException($e);
        }
    }

    /**
     * @param CandidateHistory $candidateHistory
     * @param CandidateVacancy $candidateVacancy
     */
    protected function setCandidateHistory(CandidateHistory $candidateHistory, CandidateVacancy $candidateVacancy): void
    {
        $candidateHistory->getDecorator()->setCandidateById($candidateVacancy->getCandidate()->getId());
        $candidateHistory->getDecorator()->setVacancyById($candidateVacancy->getVacancy()->getId());
        $candidateHistory->setCandidateVacancyName($candidateVacancy->getVacancy()->getName());
        $candidateHistory->setAction($this->getResultingState());
        $candidateHistory->getDecorator()->setPerformedBy($this->getAuthUser()->getEmpNumber());
        $candidateHistory->setPerformedDate($this->getDateTimeHelper()->getNow());

        $candidateHistory->setNote(
            $this->getRequestParams()->getStringOrNull(
                RequestParams::PARAM_TYPE_BODY,
                self::PARAMETER_NOTE
            )
        );
        if (!is_null($this->getInterviewId())) {
            $candidateHistory->getDecorator()->setInterviewByInterviewId($this->getInterviewId());
        }
    }

    /**
     * @param Vacancy $vacancy
     * @return bool
     */
    private function isAuthUserHiringManager(Vacancy $vacancy): bool
    {
        $hiringMangerEmpNumber = $vacancy->getHiringManager()->getEmpNumber();
        return $hiringMangerEmpNumber === $this->getAuthUser()->getEmpNumber();
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
            $this->getValidationDecorator()->notRequiredParamRule(
                new ParamRule(
                    self::PARAMETER_NOTE,
                    new Rule(Rules::STRING_TYPE)
                ),
                true
            )
        );
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
     * @return int|null
     */
    protected function getInterviewId(): ?int
    {
        return $this->getRequestParams()->getIntOrNull(
            RequestParams::PARAM_TYPE_ATTRIBUTE,
            self::PARAMETER_INTERVIEW_ID
        );
    }


    /**
     * @param CandidateVacancy $candidateVacancy
     * @param Employee $employee
     */
    protected function setCandidateAsEmployee(CandidateVacancy $candidateVacancy, Employee $employee)
    {
        $employee->setFirstName($candidateVacancy->getCandidate()->getFirstName());
        $employee->setMiddleName($candidateVacancy->getCandidate()->getMiddleName() ?? '');
        $employee->setLastName($candidateVacancy->getCandidate()->getLastName());
        $employee->setOtherEmail($candidateVacancy->getCandidate()->getEmail());
        $employee->getDecorator()->setJobTitleById(
            $candidateVacancy->getVacancy()->getJobTitle()->getId()
        );
    }

    /**
     * @return int
     */
    abstract public function getResultingState(): int;
}
