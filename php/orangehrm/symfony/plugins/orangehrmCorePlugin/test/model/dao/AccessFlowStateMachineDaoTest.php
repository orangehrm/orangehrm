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
}

?>  
