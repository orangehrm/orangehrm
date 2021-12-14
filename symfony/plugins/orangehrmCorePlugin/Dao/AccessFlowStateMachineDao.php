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

namespace OrangeHRM\Core\Dao;

use Exception;
use OrangeHRM\Core\Exception\DaoException;
use OrangeHRM\Entity\WorkflowStateMachine;

class AccessFlowStateMachineDao extends BaseDao
{
    /**
     * @param string $workflow
     * @param string|null $state
     * @param string $role
     * @return WorkflowStateMachine[]|null
     * @throws DaoException
     */
    public function getAllowedActions(string $workflow, ?string $state, string $role): ?array
    {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->andWhere('sm.workflow = :workflow')
                ->setParameter('workflow', $workflow);
            $q->andWhere('sm.role = :role')
                ->setParameter('role', $role);

            if ($state != null) {
                $q->andWhere('sm.state = :state')
                    ->setParameter('state', $state);
            }

            $results = $q->getQuery()->execute();
            return empty($results) ? null : $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * @param string $workflow
     * @param string|null $state
     * @param string $role
     * @return WorkflowStateMachine[]
     * @throws DaoException
     */
    public function getAllowedWorkflowItems(string $workflow, ?string $state, string $role): array
    {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->andWhere('sm.workflow = :workflow')
                ->setParameter('workflow', $workflow);
            $q->andWhere('sm.role = :role')
                ->setParameter('role', $role);

            if ($state != null) {
                $q->andWhere('sm.state = :state')
                    ->setParameter('state', $state);
            }

            return $q->getQuery()->execute();
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * @param string $workflow
     * @param string $state
     * @param string $role
     * @param string $action
     * @return WorkflowStateMachine|null
     * @throws DaoException
     */
    public function getNextState(string $workflow, string $state, string $role, string $action): ?WorkflowStateMachine
    {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->andWhere('sm.workflow = :workflow')
                ->setParameter('workflow', $workflow);
            $q->andWhere('sm.state = :state')
                ->setParameter('state', $state);
            $q->andWhere('sm.action = :action')
                ->setParameter('action', $action);
            $q->andWhere('sm.role = :role')
                ->setParameter('role', $role);

            $results = $q->getQuery()->execute();

            return $results[0] ?? null;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * @param string $workflow
     * @param string $role
     * @param array $actions
     * @return WorkflowStateMachine[]|null
     * @throws DaoException
     */
    public function getActionableStates(string $workflow, string $role, array $actions): ?array
    {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->andWhere('sm.workflow = :workflow')
                ->setParameter('workflow', $workflow);
            $q->andWhere('sm.role = :role')
                ->setParameter('role', $role);
            $q->andWhere($q->expr()->in('sm.action', ':actions'))
                ->setParameter('actions', $actions);

            $results = $q->getQuery()->execute();
            return empty($results) ? null : $results;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * @param WorkflowStateMachine $workflowStateMachine
     * @return WorkflowStateMachine
     * @throws DaoException
     */
    public function saveWorkflowStateMachineRecord(WorkflowStateMachine $workflowStateMachine): WorkflowStateMachine
    {
        try {
            $this->persist($workflowStateMachine);
            return $workflowStateMachine;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * @param WorkflowStateMachine[] $workflowStateMachineRecordArray
     * @return WorkflowStateMachine[]
     * @throws DaoException
     */
    public function saveWorkflowStateMachineRecordAsArray(array $workflowStateMachineRecordArray): array
    {
        try {
            foreach ($workflowStateMachineRecordArray as $workflowStateMachineRecord) {
                $this->getEntityManager()->persist($workflowStateMachineRecord);
            }
            $this->getEntityManager()->flush();
            return $workflowStateMachineRecordArray;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * @param string $workflow
     * @param string $state
     * @param string $role
     * @param string $action
     * @param string $resultingState
     * @return bool
     * @throws DaoException
     */
    public function deleteWorkflowStateMachineRecord(
        string $workflow,
        string $state,
        string $role,
        string $action,
        string $resultingState
    ): bool {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->delete();
            $q->andWhere('sm.workflow = :workflow')
                ->setParameter('workflow', $workflow);
            $q->andWhere('sm.state = :state')
                ->setParameter('state', $state);
            $q->andWhere('sm.action = :action')
                ->setParameter('action', $action);
            $q->andWhere('sm.role = :role')
                ->setParameter('role', $role);
            $q->andWhere('sm.resultingState = :resultingState')
                ->setParameter('resultingState', $resultingState);

            $result = $q->getQuery()->execute();
            return !empty($result);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param string|null $workflow
     * @param string $role
     * @return int
     * @throws DaoException
     */
    public function deleteWorkflowRecordsForUserRole(?string $workflow, string $role): int
    {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->delete();
            $q->andWhere('sm.role = :role')
                ->setParameter('role', $role);

            if (!is_null($workflow)) {
                $q->andWhere('sm.workflow = :workflow')
                    ->setParameter('workflow', $workflow);
            }

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $oldRoleName
     * @param string $newRoleName
     * @return int
     * @throws DaoException
     */
    public function handleUserRoleRename(string $oldRoleName, string $newRoleName): int
    {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->update();
            $q->set('sm.role', ':role')
                ->setParameter('role', $newRoleName);
            $q->andWhere('sm.role = :oldRoleName')
                ->setParameter('oldRoleName', $oldRoleName);

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $workflow
     * @param string $role
     * @return WorkflowStateMachine[]|null
     * @throws DaoException
     */
    public function getAllAlowedRecruitmentApplicationStates(string $workflow, string $role): ?array
    {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->andWhere('sm.workflow = :workflow')
                ->setParameter('workflow', $workflow);
            $q->andWhere('sm.role = :role')
                ->setParameter('role', $role);

            $results = $q->getQuery()->execute();
            return empty($results) ? null : $results;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param string $workflow
     * @param string|null $role
     * @return WorkflowStateMachine[]
     * @throws DaoException
     */
    public function getWorkFlowStateMachineRecords(string $workflow, ?string $role = null): array
    {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->andWhere('sm.workflow = :workflow')
                ->setParameter('workflow', $workflow);

            if ($role != null) {
                $q->andWhere('sm.role = :role')
                    ->setParameter('role', $role);
            }

            return $q->getQuery()->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param string $workflow
     * @param string $state
     * @param string $role
     * @param string $action
     * @return bool
     * @throws DaoException
     */
    public function isActionAllowed(string $workflow, string $state, string $role, string $action): bool
    {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->andWhere('sm.workflow = :workflow')
                ->setParameter('workflow', $workflow);
            $q->andWhere('sm.state = :state')
                ->setParameter('state', $state);
            $q->andWhere('sm.role = :role')
                ->setParameter('role', $role);
            $q->andWhere('sm.action = :action')
                ->setParameter('action', $action);

            return $this->count($q) > 0;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * @param int $id
     * @return WorkflowStateMachine|null
     * @throws DaoException
     */
    public function getWorkflowItem(int $id): ?WorkflowStateMachine
    {
        try {
            $workflowStateMachine = $this->getRepository(WorkflowStateMachine::class)->find($id);
            if ($workflowStateMachine instanceof WorkflowStateMachine) {
                return $workflowStateMachine;
            }

            return null;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * @param string $workflow
     * @param string $state
     * @param string $action
     * @param string $role
     * @return WorkflowStateMachine|null
     * @throws DaoException
     */
    public function getWorkflowItemByStateActionAndRole(
        string $workflow,
        string $state,
        string $action,
        string $role
    ): ?WorkflowStateMachine {
        try {
            $q = $this->createQueryBuilder(WorkflowStateMachine::class, 'sm');
            $q->andWhere('sm.workflow = :workflow')
                ->setParameter('workflow', $workflow);
            $q->andWhere('sm.state = :state')
                ->setParameter('state', $state);
            $q->andWhere('sm.role = :role')
                ->setParameter('role', $role);
            $q->andWhere('sm.action = :action')
                ->setParameter('action', $action);

            return $this->fetchOne($q);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
