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

}

?>
