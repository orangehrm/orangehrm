<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccessFlowStateMachine
 *
 * @author orangehrm
 */
class AccessFlowStateMachineDao {

    public function getAllowedActions($flow, $state, $role) {
        try {
            if ($state != null) {
                $query = Doctrine_Query::create()
                                ->select("action")
                                ->from("WorkflowStateMachine")
                                ->where("workflow = ?", $flow)
                                ->andWhere("state = ?", $state)
                                ->andWhere("role = ?", $role);
                $results = $query->execute();
            } else {
                $query = Doctrine_Query::create()
                                ->select("action")
                                ->from("WorkflowStateMachine")
                                ->where("workflow = ?", $flow)
                                ->andWhere("role = ?", $role);
                $results = $query->execute();
            }

            if ($results[0]->getId() == null) {

                return null;
            } else {
                return $results;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function getNextState($flow, $state, $role, $action) {

        try {

            $query = Doctrine_Query::create()
                            ->select("resultingState")
                            ->from("WorkflowStateMachine")
                            ->where("workflow = ?", $flow)
                            ->andWhere("state = ?", $state)
                            ->andWhere("action = ?", $action)
                            ->andWhere("role = ?", $role);
            $results = $query->execute();

            if ($results[0]->getId() == null) {

                return null;
            } else {
                return $results[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function getActionableStates($flow, $role, $actions) {

        try {

            $query = Doctrine_Query::create()
                            ->select("state")
                            ->where("workflow = ?", $flow)
                            ->from("WorkflowStateMachine")
                            ->andWhere("role = ?", $role)
                            ->andWhereIn('action', $actions);
            $results = $query->execute();

            if ($results[0]->getId() == null) {

                return null;
            } else {

                return $results;
            }
        } catch (Exception $ex) {

            throw new DaoException($ex->getMessage());
        }
    }

    public function saveWorkflowStateMachineRecord(WorkflowStateMachine $workflowStateMachine) {

        try {

            if ($workflowStateMachine->getId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($workflowStateMachine);
                $workflowStateMachine->setId($idGenService->getNextID());
            }
            $workflowStateMachine->save();

            return $workflowStateMachine;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    public function deleteWorkflowStateMachineRecord($flow, $state, $role, $action, $resultingState) {

        try {
            $q = Doctrine_Query:: create()
                            ->delete('WorkflowStateMachine')
                            ->where("workflow = ?", $flow)
                            ->andWhere("role = ?", $role)
                            ->andWhere("state = ?", $state)
                            ->andWhere("action = ?", $action)
                            ->andWhere("resultingState = ?", $resultingState);

            $result = $q->execute();

            if (count($result) > 0) {
                return true;

            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getAllAlowedRecruitmentApplicationStates($flow, $role) {

        try {
            $q = Doctrine_Query:: create()
                            ->select("state")
                            ->from("WorkflowStateMachine")
                            ->where("workflow = ?", $flow)
                            ->andWhere("role = ?", $role);

            $results = $q->execute();

            if ($results[0]->getId() == null) {

                return null;
            } else {
                return $results;
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }


}