<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AccessFlowStateMachineServiceTest
 *
 * @group Core
 */
class AccessFlowStateMachineServiceTest extends PHPUnit_Framework_TestCase {

    private $accessFlowStateMachineService;

    protected function setUp() {

        $this->accessFlowStateMachineService = new AccessFlowStateMachineService();

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/AccessFlowStateMachineService.yml';
        
        TestDataService::populate($this->fixture);        
    }
    
    public function testGetAccessFlowStateMachineDao(){
        
       $accessFlowStateMachineDao= $this->accessFlowStateMachineService->getAccessFlowStateMachineDao();
       
       $this->assertTrue($accessFlowStateMachineDao instanceof AccessFlowStateMachineDao);
        
        
    }
    
    public function testSetAccessFlowStateMachineDao(){
        
        $accessFlowStateMachineDao= new AccessFlowStateMachineDao();
        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDao);
        
        $this->assertTrue($this->accessFlowStateMachineService->getAccessFlowStateMachineDao() instanceof AccessFlowStateMachineDao);
        
        
    }
    public function testGetAllowedActions() {
        $flow = "Time";
        $state = "SUBMITTED";
        $role = "ESS USER";
        $fetchedRecord1 = TestDataService::fetchObject('WorkflowStateMachine', 10);
        $fetchedRecord2 = TestDataService::fetchObject('WorkflowStateMachine', 12);
        $recordsArray = array($fetchedRecord1, $fetchedRecord2);

        $acessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('getAllowedActions'));
        $acessFlowStateMachineDaoMock->expects($this->once())
                ->method('getAllowedActions')
                ->with($flow, $state, $role)
                ->will($this->returnValue($recordsArray));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($acessFlowStateMachineDaoMock);
        $retrievedActionsArray = $this->accessFlowStateMachineService->getAllowedActions($flow, $state, $role);

        $this->assertEquals($retrievedActionsArray[0], $recordsArray[0]->getAction());
        $this->assertEquals($retrievedActionsArray[1], $recordsArray[1]->getAction());

        $flow = "Attendance";
        $state = "INITIAL";
        $role = "ADMIN";
        $recordsArray = null;

        $acessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('getAllowedActions'));
        $acessFlowStateMachineDaoMock->expects($this->once())
                ->method('getAllowedActions')
                ->with($flow, $state, $role)
                ->will($this->returnValue($recordsArray));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($acessFlowStateMachineDaoMock);
        $retrievedActionsArray = $this->accessFlowStateMachineService->getAllowedActions($flow, $state, $role);

        $this->assertNull($retrievedActionsArray);
    }
    
    public function testGetAllowedWorkflowItems() {
        $flow = "Time";
        $state = "SUBMITTED";
        $role = "ESS USER";
        $fetchedRecord1 = TestDataService::fetchObject('WorkflowStateMachine', 10);
        $fetchedRecord2 = TestDataService::fetchObject('WorkflowStateMachine', 12);
        $expected = Doctrine_Collection::create('WorkflowStateMachine');
        $expected->add($fetchedRecord1);
        $expected->add($fetchedRecord2);

        $acessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('getAllowedWorkflowItems'));
        $acessFlowStateMachineDaoMock->expects($this->once())
                ->method('getAllowedWorkflowItems')
                ->with($flow, $state, $role)
                ->will($this->returnValue($expected));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($acessFlowStateMachineDaoMock);
        $results = $this->accessFlowStateMachineService->getAllowedWorkflowItems($flow, $state, $role);        
        $this->assertEquals($expected, $results);     
    } 

    public function testGetNextState() {

        $flow = "Time";
        $state = "SUBMITTED";
        $role = "ADMIN";
        $action = "APPROVE";

        $fetchedRecord1 = TestDataService::fetchObject('WorkflowStateMachine', 1);

        $acessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('getNextState'));
        $acessFlowStateMachineDaoMock->expects($this->once())
                ->method('getNextState')
                ->with($flow, $state, $role, $action)
                ->will($this->returnValue($fetchedRecord1));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($acessFlowStateMachineDaoMock);
        $retrievedState = $this->accessFlowStateMachineService->getNextState($flow, $state, $role, $action);

        $this->assertEquals($retrievedState, $fetchedRecord1->getResultingState());
        
        //checking the null case
        
        $flow = "Attendace";
        $state = "SUBMITTED";
        $role = "ADMIN";
        $action = "APPROVE";

        $acessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('getNextState'));
        $acessFlowStateMachineDaoMock->expects($this->once())
                ->method('getNextState')
                ->with($flow, $state, $role, $action)
                ->will($this->returnValue(null));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($acessFlowStateMachineDaoMock);
        $retrievedState = $this->accessFlowStateMachineService->getNextState($flow, $state, $role, $action);

        $this->assertNull($retrievedState);
    }

    public function testGetActionableStates() {

        $actions = array("APPROVE", "REJECT");
        $workFlow = "Time";
        $userRole = "ADMIN";

        $fetchedRecord1 = TestDataService::fetchObject('WorkflowStateMachine', 1);
        $fetchedRecord2 = TestDataService::fetchObject('WorkflowStateMachine', 5);
        $tempArray = array($fetchedRecord1, $fetchedRecord2);

        $acessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('getActionableStates'));
        $acessFlowStateMachineDaoMock->expects($this->once())
                ->method('getActionableStates')
                ->with($workFlow, $userRole, $actions)
                ->will($this->returnValue($tempArray));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($acessFlowStateMachineDaoMock);
        $record = $this->accessFlowStateMachineService->getActionableStates($workFlow, $userRole, $actions);

        $this->assertEquals(2, count($record));
        $this->assertEquals($fetchedRecord1->getState(), $record[0]);
        $this->assertEquals($fetchedRecord2->getState(), $record[1]);
    }

    public function testSaveWorkflowStateMachineRecord() {


        $workflowStateMachineRecords = TestDataService::loadObjectList('WorkflowStateMachine', $this->fixture, 'WorkflowStateMachine');

        $workflowStateMachineRecord = $workflowStateMachineRecords[0];

        $accessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('saveWorkflowStateMachineRecord'));

        $accessFlowStateMachineDaoMock->expects($this->once())
                ->method('saveWorkflowStateMachineRecord')
                ->with($workflowStateMachineRecord)
                ->will($this->returnValue($workflowStateMachineRecord));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);

        $this->assertTrue($this->accessFlowStateMachineService->saveWorkflowStateMachineRecord($workflowStateMachineRecord) instanceof WorkflowStateMachine);
    }

    public function testDeleteWorkflowStateMachineRecord() {
        $flow = "Time";
        $state = "SUPERVISOR APPROVED";
        $role = "ADMIN";
        $action = "VIEW TIMESHEET";
        $resultingState = "SUPERVISOR APPROVED";
        $isSuccess = true;

        $acessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('deleteWorkflowStateMachineRecord'));
        $acessFlowStateMachineDaoMock->expects($this->once())
                ->method('deleteWorkflowStateMachineRecord')
                ->with($flow, $state, $role, $action, $resultingState)
                ->will($this->returnValue($isSuccess));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($acessFlowStateMachineDaoMock);
        $retunedValue = $this->accessFlowStateMachineService->deleteWorkflowStateMachineRecord($flow, $state, $role, $action, $resultingState);

        $this->assertEquals($isSuccess, $retunedValue);

        $flow = "Time";
        $state = "SUPERVISOR APPROVED";
        $role = "ADMIN";
        $action = "VIEW TIMESHEET";
        $resultingState = "SUPERVISOR APPROVED";
        $isSuccess = false;

        $acessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('deleteWorkflowStateMachineRecord'));
        $acessFlowStateMachineDaoMock->expects($this->once())
                ->method('deleteWorkflowStateMachineRecord')
                ->with($flow, $state, $role, $action, $resultingState)
                ->will($this->returnValue($isSuccess));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($acessFlowStateMachineDaoMock);
        $retunedValue = $this->accessFlowStateMachineService->deleteWorkflowStateMachineRecord($flow, $state, $role, $action, $resultingState);

        $this->assertEquals($isSuccess, $retunedValue);
    }
    
    public function testGetWorkFlowStateMachineRecordsService() {
        $workflowStateMachineRecords = TestDataService::loadObjectList('WorkflowStateMachine', $this->fixture, 'WorkflowStateMachine');

        $workflowStateMachineRecord = $workflowStateMachineRecords[12];

        $accessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao');

        $accessFlowStateMachineDaoMock->expects($this->once())
                ->method('getWorkFlowStateMachineRecords')
                ->with(PluginWorkflowStateMachine::FLOW_EMPLOYEE)
                ->will($this->returnValue($workflowStateMachineRecord));
        
        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);

        $this->assertTrue($this->accessFlowStateMachineService->getWorkFlowStateMachineRecords(PluginWorkflowStateMachine::FLOW_EMPLOYEE, null) instanceof WorkflowStateMachine);
    }
    
    public function testIsActionAllowedService() {
        $accessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao');

        $accessFlowStateMachineDaoMock->expects($this->once())
                ->method('isActionAllowed')
                ->with(PluginWorkflowStateMachine::FLOW_EMPLOYEE, 'NOT EXIST', 'ADMIN', 
                        PluginWorkflowStateMachine::EMPLOYEE_ACTION_ADD)
                ->will($this->returnValue(TRUE));
        
        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);
        
        $isAllowed = $this->accessFlowStateMachineService->isActionAllowed(PluginWorkflowStateMachine::FLOW_EMPLOYEE, 
                'NOT EXIST', 'ADMIN', PluginWorkflowStateMachine::EMPLOYEE_ACTION_ADD);
        $this->assertTrue($isAllowed);       
    }

    public function testGetWorkflowItemsByStateActionAndRole() {
        $item = new WorkflowStateMachine();
        $item->fromArray(array('id' => 9, 'workflow' => Time, 'state' => 'APPROVED', 'role' => 'SUPERVISOR',
            'action' => 'VIEW TIMESHEET','resulting_state' => 'APPROVED'));
        $accessFlowStateMachineDaoMock = $this->getMock('AccessFlowStateMachineDao', array('getWorkflowItemByStateActionAndRole'));

        $accessFlowStateMachineDaoMock->expects($this->once())
                ->method('getWorkflowItemByStateActionAndRole')
                ->with('Time', 'NOT SUBMITTED', 'SAVE', 'XYZ')
                ->will($this->returnValue($item));
        
        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);
        
        $result = $this->accessFlowStateMachineService->getWorkflowItemByStateActionAndRole('Time', 'NOT SUBMITTED', 'SAVE', 'XYZ');
        $this->assertEquals($item, $result);
    }    
    
    public function testDeleteWorkflowRecordsForUserRole() {
        $flow = "Time";
        $role = "ADMIN";

        $mockDao = $this->getMock('AccessFlowStateMachineDao', array('deleteWorkflowRecordsForUserRole'));
        $mockDao->expects($this->once())
                ->method('deleteWorkflowRecordsForUserRole')
                ->with($flow, $role)
                ->will($this->returnValue(true));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($mockDao);
        $returnedValue = $this->accessFlowStateMachineService->deleteWorkflowRecordsForUserRole($flow, $role);

        $this->assertTrue($returnedValue);
    }    
    
    public function testHandleUserRoleRename() {
        $oldName = "ADMIN";
        $newName = "MANAGER";

        $mockDao = $this->getMock('AccessFlowStateMachineDao', array('handleUserRoleRename'));
        $mockDao->expects($this->once())
                ->method('handleUserRoleRename')
                ->with($oldName, $newName)
                ->will($this->returnValue(true));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($mockDao);
        $returnedValue = $this->accessFlowStateMachineService->handleUserRoleRename($oldName, $newName);

        $this->assertTrue($returnedValue);        
    }

}

