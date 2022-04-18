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

namespace OrangeHRM\Tests\Core\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\AccessFlowStateMachineDao;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Description of AccessFlowStateMachineServiceTest
 *
 * @group Core
 * @group Service
 */
class AccessFlowStateMachineServiceTest extends TestCase
{
    /**
     * @var AccessFlowStateMachineService
     */
    private AccessFlowStateMachineService $accessFlowStateMachineService;
    /**
     * @var string
     */
    private string $fixture;

    protected function setUp(): void
    {
        $this->accessFlowStateMachineService = new AccessFlowStateMachineService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) .
            '/orangehrmCorePlugin/test/fixtures/AccessFlowStateMachineService.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetAccessFlowStateMachineDao(): void
    {
        $accessFlowStateMachineDao = $this->accessFlowStateMachineService->getAccessFlowStateMachineDao();
        $this->assertTrue($accessFlowStateMachineDao instanceof AccessFlowStateMachineDao);
    }

    public function testSetAccessFlowStateMachineDao(): void
    {
        $accessFlowStateMachineDao = new AccessFlowStateMachineDao();
        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDao);

        $this->assertTrue(
            $this->accessFlowStateMachineService->getAccessFlowStateMachineDao() instanceof AccessFlowStateMachineDao
        );
    }

    public function testGetAllowedActions(): void
    {
        $flow = "Time";
        $state = "SUBMITTED";
        $role = "ESS USER";
        $fetchedRecord1 = TestDataService::fetchObject(WorkflowStateMachine::class, 10);
        $fetchedRecord2 = TestDataService::fetchObject(WorkflowStateMachine::class, 12);
        $recordsArray = [$fetchedRecord1, $fetchedRecord2];

        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['getAllowedActions'])
            ->getMock();
        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('getAllowedActions')
            ->with($flow, $state, $role)
            ->will($this->returnValue($recordsArray));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);
        $retrievedActionsArray = $this->accessFlowStateMachineService->getAllowedActions($flow, $state, $role);

        $this->assertEquals($retrievedActionsArray[0], $recordsArray[0]->getAction());
        $this->assertEquals($retrievedActionsArray[1], $recordsArray[1]->getAction());

        $flow = "Attendance";
        $state = "INITIAL";
        $role = "ADMIN";
        $recordsArray = null;

        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['getAllowedActions'])
            ->getMock();
        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('getAllowedActions')
            ->with($flow, $state, $role)
            ->will($this->returnValue($recordsArray));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);
        $retrievedActionsArray = $this->accessFlowStateMachineService->getAllowedActions($flow, $state, $role);

        $this->assertNull($retrievedActionsArray);
    }

    public function testGetAllowedWorkflowItems(): void
    {
        $flow = "Time";
        $state = "SUBMITTED";
        $role = "ESS USER";
        $fetchedRecord1 = TestDataService::fetchObject(WorkflowStateMachine::class, 10);
        $fetchedRecord2 = TestDataService::fetchObject(WorkflowStateMachine::class, 12);
        $expected = [$fetchedRecord1, $fetchedRecord2];

        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['getAllowedWorkflowItems'])
            ->getMock();
        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('getAllowedWorkflowItems')
            ->with($flow, $state, $role)
            ->will($this->returnValue($expected));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);
        $results = $this->accessFlowStateMachineService->getAllowedWorkflowItems($flow, $state, $role);
        $this->assertEquals($expected, $results);
    }

    public function testGetNextState(): void
    {
        $flow = "Time";
        $state = "SUBMITTED";
        $role = "ADMIN";
        $action = "APPROVE";

        $fetchedRecord1 = TestDataService::fetchObject(WorkflowStateMachine::class, 1);

        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['getNextState'])
            ->getMock();
        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('getNextState')
            ->with($flow, $state, $role, $action)
            ->will($this->returnValue($fetchedRecord1));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);
        $retrievedState = $this->accessFlowStateMachineService->getNextState($flow, $state, $role, $action);

        $this->assertEquals($retrievedState, $fetchedRecord1->getResultingState());

        //checking the null case

        $flow = "Attendace";
        $state = "SUBMITTED";
        $role = "ADMIN";
        $action = "APPROVE";

        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['getNextState'])
            ->getMock();
        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('getNextState')
            ->with($flow, $state, $role, $action)
            ->will($this->returnValue(null));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);
        $retrievedState = $this->accessFlowStateMachineService->getNextState($flow, $state, $role, $action);

        $this->assertNull($retrievedState);
    }

    public function testGetActionableStates(): void
    {
        $actions = ["APPROVE", "REJECT"];
        $workFlow = "Time";
        $userRole = "ADMIN";

        $fetchedRecord1 = TestDataService::fetchObject(WorkflowStateMachine::class, 1);
        $fetchedRecord2 = TestDataService::fetchObject(WorkflowStateMachine::class, 5);
        $tempArray = [$fetchedRecord1, $fetchedRecord2];

        $acessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['getActionableStates'])
            ->getMock();
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

    public function testSaveWorkflowStateMachineRecord(): void
    {
        $workflowStateMachineRecords = TestDataService::loadObjectList(
            WorkflowStateMachine::class,
            $this->fixture,
            'WorkflowStateMachine'
        );

        $workflowStateMachineRecord = $workflowStateMachineRecords[0];

        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['saveWorkflowStateMachineRecord'])
            ->getMock();

        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('saveWorkflowStateMachineRecord')
            ->with($workflowStateMachineRecord)
            ->will($this->returnValue($workflowStateMachineRecord));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);

        $this->assertTrue(
            $this->accessFlowStateMachineService->saveWorkflowStateMachineRecord(
                $workflowStateMachineRecord
            ) instanceof WorkflowStateMachine
        );
    }

    public function testDeleteWorkflowStateMachineRecord(): void
    {
        $flow = "Time";
        $state = "SUPERVISOR APPROVED";
        $role = "ADMIN";
        $action = "VIEW TIMESHEET";
        $resultingState = "SUPERVISOR APPROVED";
        $isSuccess = true;

        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['deleteWorkflowStateMachineRecord'])
            ->getMock();
        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('deleteWorkflowStateMachineRecord')
            ->with($flow, $state, $role, $action, $resultingState)
            ->will($this->returnValue($isSuccess));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);
        $returnedValue = $this->accessFlowStateMachineService->deleteWorkflowStateMachineRecord(
            $flow,
            $state,
            $role,
            $action,
            $resultingState
        );

        $this->assertEquals($isSuccess, $returnedValue);

        $flow = "Time";
        $state = "SUPERVISOR APPROVED";
        $role = "ADMIN";
        $action = "VIEW TIMESHEET";
        $resultingState = "SUPERVISOR APPROVED";
        $isSuccess = false;

        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['deleteWorkflowStateMachineRecord'])
            ->getMock();
        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('deleteWorkflowStateMachineRecord')
            ->with($flow, $state, $role, $action, $resultingState)
            ->will($this->returnValue($isSuccess));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);
        $returnedValue = $this->accessFlowStateMachineService->deleteWorkflowStateMachineRecord(
            $flow,
            $state,
            $role,
            $action,
            $resultingState
        );

        $this->assertEquals($isSuccess, $returnedValue);
    }

    public function testGetWorkFlowStateMachineRecordsService(): void
    {
        $workflowStateMachineRecords = TestDataService::loadObjectList(
            WorkflowStateMachine::class,
            $this->fixture,
            'WorkflowStateMachine'
        );

        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['getWorkFlowStateMachineRecords'])
            ->getMock();

        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('getWorkFlowStateMachineRecords')
            ->with(WorkflowStateMachine::FLOW_EMPLOYEE)
            ->will($this->returnValue($workflowStateMachineRecords));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);

        $result = $this->accessFlowStateMachineService->getWorkFlowStateMachineRecords(
            WorkflowStateMachine::FLOW_EMPLOYEE,
            null
        );
        $this->assertCount(13, $result);
    }

    public function testIsActionAllowedService(): void
    {
        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['isActionAllowed'])
            ->getMock();

        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('isActionAllowed')
            ->with(
                WorkflowStateMachine::FLOW_EMPLOYEE,
                'NOT EXIST',
                'ADMIN',
                WorkflowStateMachine::EMPLOYEE_ACTION_ADD
            )
            ->will($this->returnValue(true));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);

        $isAllowed = $this->accessFlowStateMachineService->isActionAllowed(
            WorkflowStateMachine::FLOW_EMPLOYEE,
            'NOT EXIST',
            'ADMIN',
            WorkflowStateMachine::EMPLOYEE_ACTION_ADD
        );
        $this->assertTrue($isAllowed);
    }

    public function testGetWorkflowItemsByStateActionAndRole(): void
    {
        $item = new WorkflowStateMachine();
        $item->setId(9);
        $item->setWorkflow('Time');
        $item->setState('APPROVED');
        $item->setRole('SUPERVISOR');
        $item->setAction('VIEW TIMESHEET');
        $item->setResultingState('APPROVED');
        $accessFlowStateMachineDaoMock = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['getWorkflowItemByStateActionAndRole'])
            ->getMock();

        $accessFlowStateMachineDaoMock->expects($this->once())
            ->method('getWorkflowItemByStateActionAndRole')
            ->with('Time', 'NOT SUBMITTED', 'SAVE', 'XYZ')
            ->will($this->returnValue($item));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($accessFlowStateMachineDaoMock);

        $result = $this->accessFlowStateMachineService->getWorkflowItemByStateActionAndRole(
            'Time',
            'NOT SUBMITTED',
            'SAVE',
            'XYZ'
        );
        $this->assertEquals($item, $result);
    }

    public function testDeleteWorkflowRecordsForUserRole(): void
    {
        $flow = "Time";
        $role = "ADMIN";

        $mockDao = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['deleteWorkflowRecordsForUserRole'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('deleteWorkflowRecordsForUserRole')
            ->with($flow, $role)
            ->will($this->returnValue(1));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($mockDao);
        $returnedValue = $this->accessFlowStateMachineService->deleteWorkflowRecordsForUserRole($flow, $role);

        $this->assertEquals(1, $returnedValue);
    }

    public function testHandleUserRoleRename(): void
    {
        $oldName = "ADMIN";
        $newName = "MANAGER";

        $mockDao = $this->getMockBuilder(AccessFlowStateMachineDao::class)
            ->onlyMethods(['handleUserRoleRename'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('handleUserRoleRename')
            ->with($oldName, $newName)
            ->will($this->returnValue(1));

        $this->accessFlowStateMachineService->setAccessFlowStateMachineDao($mockDao);
        $returnedValue = $this->accessFlowStateMachineService->handleUserRoleRename($oldName, $newName);

        $this->assertEquals(1, $returnedValue);
    }
}
