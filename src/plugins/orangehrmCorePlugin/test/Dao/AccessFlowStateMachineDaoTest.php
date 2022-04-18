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

namespace OrangeHRM\Tests\Core\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\AccessFlowStateMachineDao;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * Description of AccessFlowStateMachineDaoTest
 *
 * @group Core
 * @group Dao
 */
class AccessFlowStateMachineDaoTest extends TestCase
{
    /**
     * @var AccessFlowStateMachineDao
     */
    private AccessFlowStateMachineDao $accessFlowStateMachineDao;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->accessFlowStateMachineDao = new AccessFlowStateMachineDao();
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmCorePlugin/test/fixtures/AccessFlowStateMachineDao.yml'
        );
    }

    public function testGetAllowedActionsForExistingAction(): void
    {
        $actionsArray = $this->accessFlowStateMachineDao->getAllowedActions("Time", "SUBMITTED", "ADMIN");
        $this->assertTrue($actionsArray[0] instanceof WorkflowStateMachine);
        $this->assertEquals("APPROVE", $actionsArray[0]->getAction());
        $this->assertEquals("APPROVED", $actionsArray[0]->getResultingState());
    }

    public function testGetAllowedActionsForNonExistingAction(): void
    {
        $actionsArray = $this->accessFlowStateMachineDao->getAllowedActions("Time", "admin", "ADMIN");
        $this->assertNull($actionsArray);
    }

    public function testGetNextStateForExistingState(): void
    {
        $nextState = $this->accessFlowStateMachineDao->getNextState("Time", "NOT SUBMITTED", "ESS USER", "SUBMIT");
        $this->assertEquals("SUBMITTED", $nextState->getResultingState());
    }

    public function testGetNextStateForInvalidState(): void
    {
        $nextState = $this->accessFlowStateMachineDao->getNextState("Time", "Invalid", "ESS USER", "SUBMIT");
        $this->assertNull($nextState);
    }

    public function testGetActionableStates(): void
    {
        $actions = ["APPROVE", "REJECT"];
        $actionableStates = $this->accessFlowStateMachineDao->getActionableStates("Time", "ADMIN", $actions);

        $this->assertEquals("SUBMITTED", $actionableStates[0]->getState());

        $actions = ["EDIT"];
        $actionableStates = $this->accessFlowStateMachineDao->getActionableStates("Time", "ADMIN", $actions);

        $this->assertNull($actionableStates);
    }

    public function testSaveWorkflowStateMachineRecord(): void
    {
        $workflowStateMachineRecord = new WorkflowStateMachine();

        $workflowStateMachineRecord->setAction(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME);
        $workflowStateMachineRecord->setState("CREATED");
        $workflowStateMachineRecord->setResultingState("CREATED");
        $workflowStateMachineRecord->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachineRecord->setRole("ESS USER");

        $this->accessFlowStateMachineDao->saveWorkflowStateMachineRecord($workflowStateMachineRecord);

        $this->assertNotNull($workflowStateMachineRecord->getId());
        $this->assertEquals(
            $workflowStateMachineRecord->getAction(),
            WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME
        );
        $this->assertEquals($workflowStateMachineRecord->getState(), "CREATED");
    }

    public function testDeleteWorkflowStateMachineRecord(): void
    {
        $isSuccess = $this->accessFlowStateMachineDao->deleteWorkflowStateMachineRecord(
            "Time",
            "NOT SUBMITTED",
            "ESS USER",
            "VIEW TIMESHEET",
            "NOT SUBMITTED"
        );
        $this->assertTrue($isSuccess);
    }

    public function testGetWorkFlowStateMachineRecords(): void
    {
        $workFlow = $this->accessFlowStateMachineDao->getWorkFlowStateMachineRecords(
            WorkflowStateMachine::FLOW_EMPLOYEE
        );
        $this->assertTrue($workFlow[0] instanceof WorkflowStateMachine);
        $this->assertEquals("NOT EXIST", $workFlow[0]->getState());
        $this->assertEquals(WorkflowStateMachine::EMPLOYEE_ACTION_ADD, $workFlow[0]->getAction());
        $this->assertEquals("ACTIVE", $workFlow[0]->getResultingState());
    }

    public function testGetWorkFlowStateMachineRecordsNullWorkflow(): void
    {
        $workFlow = $this->accessFlowStateMachineDao->getWorkFlowStateMachineRecords('10');
        $this->assertCount(0, $workFlow);
    }

    public function testIsActionAllowed(): void
    {
        $isAllowed = $this->accessFlowStateMachineDao->isActionAllowed(
            WorkflowStateMachine::FLOW_EMPLOYEE,
            'NOT EXIST',
            'ADMIN',
            WorkflowStateMachine::EMPLOYEE_ACTION_ADD
        );
        $this->assertTrue($isAllowed);
    }

    public function testIsActionAllowedForNonEntry(): void
    {
        $isAllowed = $this->accessFlowStateMachineDao->isActionAllowed(
            WorkflowStateMachine::FLOW_EMPLOYEE,
            'ACTIVE',
            'ADMIN',
            WorkflowStateMachine::EMPLOYEE_ACTION_ADD
        );
        $this->assertTrue(!$isAllowed);
    }

    public function testGetAllowedWorkflowItemsExisting(): void
    {
        $items = $this->accessFlowStateMachineDao->getAllowedWorkflowItems('Time', 'SUBMITTED', 'ADMIN');
        $this->assertEquals(1, count($items));
        $this->assertEquals(1, $items[0]->getId());
    }

    public function testGetAllowedWorkflowItemsAllStates(): void
    {
        $items = $this->accessFlowStateMachineDao->getAllowedWorkflowItems('Time', null, 'ADMIN');
        $this->assertEquals(3, count($items));
    }

    public function testGetAllowedWorkflowItemsNotExisting(): void
    {
        $items = $this->accessFlowStateMachineDao->getAllowedWorkflowItems('Time', 'SUBMITTED', 'XYZ');
        $this->assertEquals(0, count($items));
    }

    public function testGetWorkflowItemsByStateActionAndRole(): void
    {
        $item = $this->accessFlowStateMachineDao->getWorkflowItemByStateActionAndRole(
            'Time',
            'SUBMITTED',
            'APPROVE',
            'ADMIN'
        );
        $this->assertTrue($item instanceof WorkflowStateMachine);
        $this->assertEquals(1, $item->getId());

        $item = $this->accessFlowStateMachineDao->getWorkflowItemByStateActionAndRole(
            'Time',
            'NOT SUBMITTED',
            'SAVE',
            'ESS USER'
        );
        $this->assertTrue($item instanceof WorkflowStateMachine);
        $this->assertEquals(4, $item->getId());

        $item = $this->accessFlowStateMachineDao->getWorkflowItemByStateActionAndRole(
            'Time',
            'NOT SUBMITTED',
            'SAVE',
            'XYZ'
        );
        $this->assertTrue(!$item);
    }

    public function testDeleteWorkflowRecordsForUserRole(): void
    {
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

    public function testDeleteWorkflowRecordsForUserRoleAllWorkflows(): void
    {
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

    public function testHandleUserRoleRename(): void
    {
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

    protected function getWorkflowItemIdsForRole($flow, $role)
    {
        $conn = $this->getEntityManager()->getConnection()->getWrappedConnection();

        $query = "SELECT id from ohrm_workflow_state_machine WHERE workflow = ? AND role = ?";
        $statement = $conn->prepare($query);
        $result = $statement->execute([$flow, $role]);
        return $result->fetchFirstColumn();
    }
}
