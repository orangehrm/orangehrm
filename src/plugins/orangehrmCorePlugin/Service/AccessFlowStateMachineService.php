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

namespace OrangeHRM\Core\Service;

use OrangeHRM\Core\Dao\AccessFlowStateMachineDao;
use OrangeHRM\Entity\WorkflowStateMachine;

class AccessFlowStateMachineService
{
    /**
     * @var AccessFlowStateMachineDao|null
     */
    private ?AccessFlowStateMachineDao $accessFlowStateMachineDao = null;

    /**
     * @var array
     */
    private static array $allowedWorkflowItemCache = [];

    /**
     * @return AccessFlowStateMachineDao
     */
    public function getAccessFlowStateMachineDao(): AccessFlowStateMachineDao
    {
        if (is_null($this->accessFlowStateMachineDao)) {
            $this->accessFlowStateMachineDao = new AccessFlowStateMachineDao();
        }

        return $this->accessFlowStateMachineDao;
    }

    /**
     * @param AccessFlowStateMachineDao $accessFlowStateMachineDao
     */
    public function setAccessFlowStateMachineDao(AccessFlowStateMachineDao $accessFlowStateMachineDao): void
    {
        $this->accessFlowStateMachineDao = $accessFlowStateMachineDao;
    }

    /**
     * @param string $workflow
     * @param string|null $state
     * @param string $role
     * @return array|null
     */
    public function getAllowedActions(string $workflow, ?string $state, string $role): ?array
    {
        $results = $this->getAccessFlowStateMachineDao()->getAllowedActions($workflow, $state, $role);

        if (is_null($results)) {
            return null;
        } else {
            $allowedActionArray = [];
            foreach ($results as $allowedAction) {
                $allowedActionArray[] = $allowedAction->getAction();
            }

            return $allowedActionArray;
        }
    }

    /**
     * @param string $workflow
     * @param string|null $state
     * @param string $role
     * @return WorkflowStateMachine[]
     */
    public function getAllowedWorkflowItems(string $workflow, ?string $state, string $role): array
    {
        $key = $workflow . '-' . $state . '-' . $role;
        if (!isset(self::$allowedWorkflowItemCache[$key])) {
            self::$allowedWorkflowItemCache[$key] = $this->getAccessFlowStateMachineDao()->getAllowedWorkflowItems(
                $workflow,
                $state,
                $role
            );
        }
        return self::$allowedWorkflowItemCache[$key];
    }

    /**
     * check State Transition is possible with UserRole
     *
     * @param string $workflow
     * @param string $state
     * @param string $role
     * @param string $action
     * @return bool
     */
    public function isActionAllowed(string $workflow, string $state, string $role, string $action): bool
    {
        return $this->getAccessFlowStateMachineDao()->isActionAllowed($workflow, $state, $role, $action);
    }

    /**
     * @param string $workflow
     * @param string $state
     * @param string $role
     * @param string $action
     * @return string|null
     */
    public function getNextState(string $workflow, string $state, string $role, string $action): ?string
    {
        $result = $this->getAccessFlowStateMachineDao()->getNextState($workflow, $state, $role, $action);
        if (is_null($result)) {
            return null;
        } else {
            return $result->getResultingState();
        }
    }

    /**
     * @param string $workflow
     * @param string $role
     * @return array|null
     */
    public function getAllAlowedRecruitmentApplicationStates(string $workflow, string $role): ?array
    {
        $result = $this->getAccessFlowStateMachineDao()->getAllAlowedRecruitmentApplicationStates($workflow, $role);
        if (is_null($result)) {
            return null;
        } else {
            $resultingStateList = [];
            $stateList = [];
            foreach ($result as $rslt) {
                $stateList[] = $rslt->getState();
                $resultingStateList[] = $rslt->getResultingState();
            }
            return array_merge($stateList, $resultingStateList);
        }
    }

    /**
     * @param string $workflow
     * @param string $role
     * @param array $actions
     * @return array|null
     */
    public function getActionableStates(string $workflow, string $role, array $actions): ?array
    {
        $records = $this->getAccessFlowStateMachineDao()->getActionableStates($workflow, $role, $actions);

        if ($records == null) {
            return null;
        }
        $states = [];
        foreach ($records as $record) {
            $states[] = $record->getState();
        }

        return $states;
    }

    /**
     * @param string $workflow
     * @param string|null $role
     * @return WorkflowStateMachine[]
     */
    public function getWorkFlowStateMachineRecords(string $workflow, ?string $role = null): array
    {
        return $this->accessFlowStateMachineDao->getWorkFlowStateMachineRecords($workflow, $role);
    }

    /**
     * @param WorkflowStateMachine $workflowStateMachineRecord
     * @return WorkflowStateMachine
     */
    public function saveWorkflowStateMachineRecord(WorkflowStateMachine $workflowStateMachineRecord): WorkflowStateMachine
    {
        return $this->getAccessFlowStateMachineDao()->saveWorkflowStateMachineRecord($workflowStateMachineRecord);
    }

    /**
     * set workflow records form array
     * @param WorkflowStateMachine[] $workflowStateMachineRecordArray
     * @return WorkflowStateMachine[]
     */
    public function saveWorkflowStateMachineRecordAsArray(array $workflowStateMachineRecordArray): array
    {
        return $this->getAccessFlowStateMachineDao()->saveWorkflowStateMachineRecordAsArray($workflowStateMachineRecordArray);
    }

    /**
     * @param string $workflow
     * @param string $state
     * @param string $role
     * @param string $action
     * @param string $resultingState
     * @return bool
     */
    public function deleteWorkflowStateMachineRecord(string $workflow, string $state, string $role, string $action, string $resultingState): bool
    {
        return $this->getAccessFlowStateMachineDao()->deleteWorkflowStateMachinerecord(
            $workflow,
            $state,
            $role,
            $action,
            $resultingState
        );
    }

    public function getAllowedCandidateList($role, $empNumber)
    {
        // TODO
        $candidateService = new CandidateService();
        return $candidateService->getCandidateListForUserRole($role, $empNumber);
    }

    public function getAllowedProjectList($role, $empNumber)
    {
        // TODO
        $projetService = new ProjectService();
        return $projetService->getProjectListForUserRole($role, $empNumber);
    }

    public function getAllowedVacancyList($role, $empNumber)
    {
        // TODO
        $vacancyService = new VacancyService();
        return $vacancyService->getVacancyListForUserRole($role, $empNumber);
    }

    public function getAllowedCandidateHistoryList($role, $empNumber, $candidateId)
    {
        // TODO
        $candidateService = new CandidateService();
        return $candidateService->getCanidateHistoryForUserRole($role, $empNumber, $candidateId);
    }

    /**
     * @param int $id
     * @return WorkflowStateMachine|null
     */
    public function getWorkflowItem(int $id): ?WorkflowStateMachine
    {
        return $this->getAccessFlowStateMachineDao()->getWorkflowItem($id);
    }

    /**
     * @param string $workflow
     * @param string $state
     * @param string $action
     * @param string $role
     * @return WorkflowStateMachine|null
     */
    public function getWorkflowItemByStateActionAndRole(
        string $workflow,
        string $state,
        string $action,
        string $role
    ): ?WorkflowStateMachine {
        return $this->getAccessFlowStateMachineDao()->getWorkflowItemByStateActionAndRole(
            $workflow,
            $state,
            $action,
            $role
        );
    }

    /**
     * @param string|null $workflow
     * @param string $role
     * @return int
     */
    public function deleteWorkflowRecordsForUserRole(?string $workflow, string $role): int
    {
        return $this->getAccessFlowStateMachineDao()->deleteWorkflowRecordsForUserRole($workflow, $role);
    }

    /**
     * @param string $oldRoleName
     * @param string $newRoleName
     * @return int
     */
    public function handleUserRoleRename(string $oldRoleName, string $newRoleName): int
    {
        return $this->getAccessFlowStateMachineDao()->handleUserRoleRename($oldRoleName, $newRoleName);
    }

    public static function resetWorkflowCache(): void
    {
        self::$allowedWorkflowItemCache = [];
    }
}
