<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccessFlowStateMachineDaoTest
 *
 * @group Core
 */
class AccessFlowStateMachineDaoTest extends PHPUnit_Framework_TestCase {

    private $accessFlowStateMachineDao;

    /**
     * Set up method
     */
    protected function setUp() {

        $this->accessFlowStateMachineDao = new AccessFlowStateMachineDao();

        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/AccessFlowStateMachineDao.yml');
    }

    public function testGetAllowedActionsForExsitingAction() {

        $actionsArray = $this->accessFlowStateMachineDao->getAllowedActions("Time", "SUBMITTED", "ADMIN");
        $this->assertTrue($actionsArray[0] instanceof WorkflowStateMachine);
        $this->assertEquals("APPROVE", $actionsArray[0]->getAction());
    }

    public function testGetAllowedActionsForNonExsitingAction() {

        $actionsArray = $this->accessFlowStateMachineDao->getAllowedActions("Time", "admin", "ADMIN");
        $this->assertNull($actionsArray[0]);
    }

    public function testGetNextStateForExistingState() {
        $nextState = $this->accessFlowStateMachineDao->getNextState("Time", "NOT SUBMITTED", "ESS USER", "SUBMIT");
        $this->assertEquals("SUBMITTED", $nextState->getResultingState());
    }

    public function testGetActionableStates() {



        $actions = array("APPROVE", "REJECT");
        $actionableStates = $this->accessFlowStateMachineDao->getActionableStates("Time", "ADMIN", $actions);

        $this->assertEquals("SUBMITTED", $actionableStates[0]->getState());

        $actions = array("EDIT");
        $actionableStates = $this->accessFlowStateMachineDao->getActionableStates("Time", "ADMIN", $actions);

        $this->assertNull($actionableStates);
    }

    public function testSaveWorkflowStateMachineRecord() {

        $workflowStateMachineRecord = new WorkflowStateMachine();

        $workflowStateMachineRecord->setAction(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME);
        $workflowStateMachineRecord->setState(PluginAttendanceRecord::STATE_CREATED);
        $workflowStateMachineRecord->setResultingState(PluginAttendanceRecord::STATE_CREATED);
        $workflowStateMachineRecord->setWorkflow(PluginWorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachineRecord->setRole("ESS USER");

        $this->accessFlowStateMachineDao->saveWorkflowStateMachineRecord($workflowStateMachineRecord);

        $this->assertNotNull($workflowStateMachineRecord->getId());
        $this->assertEquals($workflowStateMachineRecord->getAction(), PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME);
        $this->assertEquals($workflowStateMachineRecord->getState(), PluginAttendanceRecord::STATE_CREATED);
    }

    public function testDeleteWorkflowStateMachineRecord() {

        $isSuccess = $this->accessFlowStateMachineDao->deleteWorkflowStateMachineRecord("Time", "NOT SUBMITTED", "ESS USER", "VIEW TIMESHEET", "NOT SUBMITTED");

        $this->assertTrue($isSuccess);

    }
    
    public function testGetWorkFlowStateMachineRecords() {
        $workFlow = $this->accessFlowStateMachineDao->getWorkFlowStateMachineRecords(PluginWorkflowStateMachine::FLOW_EMPLOYEE);
        $this->assertTrue($workFlow[0] instanceof WorkflowStateMachine);
        $this->assertEquals("NOT EXIST", $workFlow[0]->getState());
        $this->assertEquals(PluginWorkflowStateMachine::EMPLOYEE_ACTION_ADD, $workFlow[0]->getAction());
        $this->assertEquals("ACTIVE", $workFlow[0]->getResultingState());
    }
    
    public function testGetWorkFlowStateMachineRecordsNullWorkflow() {
        $workFlow = $this->accessFlowStateMachineDao->getWorkFlowStateMachineRecords('10');
//        $this->assertEquals(0, $workFlow->getCount());
//        $this->assertNull($workFlow[0]);
    }
    
    public function testIsActionAllowed() {
        $isAllowed = $this->accessFlowStateMachineDao->isActionAllowed(PluginWorkflowStateMachine::FLOW_EMPLOYEE, 
                'NOT EXIST', 'ADMIN', PluginWorkflowStateMachine::EMPLOYEE_ACTION_ADD);
        $this->assertTrue($isAllowed);        
    }
    
    public function testIsActionAllowedForNonEntry() {
        $isAllowed = $this->accessFlowStateMachineDao->isActionAllowed(PluginWorkflowStateMachine::FLOW_EMPLOYEE, 
                'ACTIVE', 'ADMIN', PluginWorkflowStateMachine::EMPLOYEE_ACTION_ADD);
        $this->assertTrue(!$isAllowed); 
    }
    
    public function testGetAllowedWorkflowItemsExisting() {
        $items = $this->accessFlowStateMachineDao->getAllowedWorkflowItems('Time', 'SUBMITTED', 'ADMIN');
        $this->assertEquals(1, count($items));
        $this->assertEquals(1, $items[0]->getId());        
    }
    public function testGetAllowedWorkflowItemsAllStates() {
        $items = $this->accessFlowStateMachineDao->getAllowedWorkflowItems('Time', NULL, 'ADMIN');
        $this->assertEquals(3, count($items));
    }  
    public function testGetAllowedWorkflowItemsNotExisting() {
        $items = $this->accessFlowStateMachineDao->getAllowedWorkflowItems('Time', 'SUBMITTED', 'XYZ');
        $this->assertEquals(0, count($items));    
    }    
    public function testGetWorkflowItemsByStateActionAndRole() {
        $item = $this->accessFlowStateMachineDao->getWorkflowItemByStateActionAndRole('Time', 'SUBMITTED', 'APPROVE', 'ADMIN');
        $this->assertTrue($item instanceof WorkflowStateMachine);
        $this->assertEquals(1, $item->getId());
        
        $item = $this->accessFlowStateMachineDao->getWorkflowItemByStateActionAndRole('Time', 'NOT SUBMITTED', 'SAVE', 'ESS USER');
        $this->assertTrue($item instanceof WorkflowStateMachine);
        $this->assertEquals(4, $item->getId());
        
        $item = $this->accessFlowStateMachineDao->getWorkflowItemByStateActionAndRole('Time', 'NOT SUBMITTED', 'SAVE', 'XYZ');
        $this->assertTrue(!$item); 
    }
    
    public function testDeleteWorkflowRecordsForUserRole() {
        $ids = $this->getWorkflowItemIdsForRole('Time', 'ADMIN');
        $this->assertTrue(count($ids) > 0);
        
        $supervisorIds = $this->getWorkflowItemIdsForRole('Time', 'SUPERVISOR');
        $this->accessFlowStateMachineDao->deleteWorkflowRecordsForUserRole('Time', 'ADMIN');
        $ids = $this->getWorkflowItemIdsForRole('Time', 'ADMIN');
        $this->assertEquals(0, count($ids));   
        
        // verify other items not deleted
        $supervisorIdsAfter = $this->getWorkflowItemIdsForRole('Time', 'SUPERVISOR');
        $this->assertEquals(count($supervisorIds), count($supervisorIdsAfter));
    }
    
    public function testDeleteWorkflowRecordsForUserRoleAllWorkflows() {
        $ids = $this->getWorkflowItemIdsForRole('Time', 'ADMIN');
        $this->assertTrue(count($ids) > 0);
        
        $employeeWorkflowItems = $this->getWorkflowItemIdsForRole('3', 'ADMIN');
        $this->assertTrue(count($employeeWorkflowItems) > 0);
         
        $this->accessFlowStateMachineDao->deleteWorkflowRecordsForUserRole(null, 'ADMIN');
        
        $ids = $this->getWorkflowItemIdsForRole('Time', 'ADMIN');
        $this->assertEquals(0, count($ids));   
        
        $employeeWorkflowItems = $this->getWorkflowItemIdsForRole('3', 'ADMIN');
        $this->assertEquals(0, count($employeeWorkflowItems));
    }    
    
    public function testHandleUserRoleRename() {
        $oldName = 'ADMIN';
        $newName = 'SECRETARY';
        
        $oldIds = $this->getWorkflowItemIdsForRole('Time', $oldName);
        $this->assertTrue(count($oldIds) > 0);
        $newIds = $this->getWorkflowItemIdsForRole('Time', $newName);
        $this->assertEquals(0, count($newIds));
        
        $this->accessFlowStateMachineDao->handleUserRoleRename($oldName, $newName);
        
        $oldIdsAfterRename = $this->getWorkflowItemIdsForRole('Time', $oldName);
        $this->assertEquals(0, count($oldIdsAfterRename));
        
        $newIdsAfterRename = $this->getWorkflowItemIdsForRole('Time', $newName);
        $this->assertEquals(count($oldIds), count($newIdsAfterRename));        
    }
    
    protected function getWorkflowItemIdsForRole($flow, $role) {
        $conn = Doctrine_Manager::connection()->getDbh();
        
        $query = "SELECT id from ohrm_workflow_state_machine WHERE workflow = ? AND role = ?";
        $statement = $conn->prepare($query);
        $this->assertTrue($statement->execute(array($flow, $role)));
        $ids = $statement->fetchAll(PDO::FETCH_COLUMN, 0);
        return $ids;             
    }
}


