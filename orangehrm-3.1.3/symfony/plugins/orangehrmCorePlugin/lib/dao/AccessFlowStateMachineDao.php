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
    
    public function getAllowedWorkflowItems($flow, $state, $role) {
        try {
            $query = Doctrine_Query::create()
                    ->from("WorkflowStateMachine")
                    ->where("workflow = ?", $flow)
                    ->andWhere("role = ?", $role);

            if ($state != null) {
                $query->andWhere("state = ?", $state);
            }

            $results = $query->execute();

            return $results;
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
    
    public function deleteWorkflowRecordsForUserRole($flow, $role) {
        try {
            $q = Doctrine_Query:: create()
                    ->delete('WorkflowStateMachine')
                    ->where("role = ?", $role);
            
            if (!is_null($flow)) {
                $q->andWhere("workflow = ?", $flow);
            }

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
    }
    
    public function handleUserRoleRename($oldName, $newName) {
        try {
            $q = Doctrine_Query::create()
                    ->update('WorkflowStateMachine w')
                    ->set('w.role', '?', $newName)
                    ->where("w.role = ?", $oldName);
            
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
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

    /**
     * 
     * @param type $workFlowId
     * @param type $role
     * @return Doctrine_Collection
     * @throws DaoException 
     */
    public function getWorkFlowStateMachineRecords($workFlowId, $role = NULL) {
        try {
            if ($role != NULL) {
                $q = Doctrine_Query:: create()
                    ->from("WorkflowStateMachine")
                    ->where("workflow = ?", $workFlowId)
                    ->andWhere("role = ?", $role);
             
                return $results = $q->execute();
            } else {
             $q = Doctrine_Query:: create()
                    ->from("WorkflowStateMachine")
                    ->where("workflow = ?", $workFlowId);
             
                return $results = $q->execute();
            }
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     * 
     * @param type $workflow
     * @param type $state
     * @param type $role
     * @param type $action
     * @return boolean
     * @throws DaoException 
     */
    public function isActionAllowed($workflow, $state, $role, $action) {
        try {
            $query = Doctrine_Query::create()
                        ->from("WorkflowStateMachine")
                        ->where("workflow = ?", $workflow)
                        ->andWhere("state = ?", $state)
                        ->andWhere("role = ?", $role)
                        ->andWhere("action = ?", $action);
            $result = $query->fetchOne();
            
            if (!empty($result)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }
    
    public function getWorkflowItem($id) {
        try {
            $q = Doctrine_Query:: create()
                    ->from("WorkflowStateMachine")
                    ->where("id = ?", $id);

            return $results = $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    public function getWorkflowItemByStateActionAndRole($workFlow, $state, $action, $role) {
        try {
            $q = Doctrine_Query:: create()
                    ->from("WorkflowStateMachine")
                    ->where("workflow = ?", $workFlow)
                    ->andWhere("state = ?", $state)
                    ->andWhere("action = ?", $action)
                    ->andWhere("role = ?", $role);

            return $results = $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }        
    }

}