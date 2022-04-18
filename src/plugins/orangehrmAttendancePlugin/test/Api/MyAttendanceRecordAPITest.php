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

namespace OrangeHRM\Tests\Attendance\Api;

use OrangeHRM\Attendance\Api\MyAttendanceRecordAPI;
use OrangeHRM\Attendance\Service\AttendanceService;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Attendance
 * @group APIv2
 */
class MyAttendanceRecordAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('MyAttendanceRecord.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(MyAttendanceRecordAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('MyAttendanceRecordAPITestCases.yaml', 'GetAll');
    }

    public function testGetOne(): void
    {
        $api = new MyAttendanceRecordAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getOne();
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new MyAttendanceRecordAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetOne();
    }

    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreate(TestCaseParams $testCaseParams): void
    {
        TestDataService::truncateSpecificTables([WorkflowStateMachine::class]);
        $this->populateFixtures('MyAttendanceRecord.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(MyAttendanceRecordAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'create', $testCaseParams);
    }

    public function dataProviderForTestCreate(): array
    {
        return $this->getTestCases('MyAttendanceRecordAPITestCases.yaml', 'Create');
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('AttendanceRecord.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(MyAttendanceRecordAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'delete', $testCaseParams);
    }

    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCases('MyAttendanceRecordAPITestCases.yaml', 'Delete');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        TestDataService::truncateSpecificTables([WorkflowStateMachine::class]);
        $this->populateFixtures('MyAttendanceRecord.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(MyAttendanceRecordAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('MyAttendanceRecordAPITestCases.yaml', 'Update');
    }

    public static function enableUserCanModifyAttendancePreHook()
    {
        $workflowStateMachine = new WorkflowStateMachine();
        $workflowStateMachine->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachine->setState(AttendanceRecord::STATE_PUNCHED_IN);
        $workflowStateMachine->setRole(AttendanceService::ESS_USER);
        $workflowStateMachine->setAction(WorkflowStateMachine::ATTENDANCE_ACTION_DELETE);
        $workflowStateMachine->setResultingState(AttendanceRecord::STATE_PUNCHED_IN);
        Doctrine::getEntityManager()->persist($workflowStateMachine);
        Doctrine::getEntityManager()->flush($workflowStateMachine);
        $workflowStateMachine = new WorkflowStateMachine();
        $workflowStateMachine->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachine->setState(AttendanceRecord::STATE_PUNCHED_OUT);
        $workflowStateMachine->setRole(AttendanceService::ESS_USER);
        $workflowStateMachine->setAction(WorkflowStateMachine::ATTENDANCE_ACTION_DELETE);
        $workflowStateMachine->setResultingState(AttendanceRecord::STATE_PUNCHED_OUT);
        Doctrine::getEntityManager()->persist($workflowStateMachine);
        Doctrine::getEntityManager()->flush($workflowStateMachine);

        AccessFlowStateMachineService::resetWorkflowCache();
    }

    public static function enableUserCanChangeCurrentTimePreHook()
    {
        $workflowStateMachine = new WorkflowStateMachine();
        $workflowStateMachine->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachine->setState(AttendanceRecord::STATE_INITIAL);
        $workflowStateMachine->setRole(AttendanceService::ESS_USER);
        $workflowStateMachine->setAction(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME);
        $workflowStateMachine->setResultingState(AttendanceRecord::STATE_INITIAL);
        Doctrine::getEntityManager()->persist($workflowStateMachine);
        Doctrine::getEntityManager()->flush($workflowStateMachine);
        $workflowStateMachine = new WorkflowStateMachine();
        $workflowStateMachine->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachine->setState(AttendanceRecord::STATE_PUNCHED_IN);
        $workflowStateMachine->setRole(AttendanceService::ESS_USER);
        $workflowStateMachine->setAction(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_TIME);
        $workflowStateMachine->setResultingState(AttendanceRecord::STATE_PUNCHED_IN);
        Doctrine::getEntityManager()->persist($workflowStateMachine);
        Doctrine::getEntityManager()->flush($workflowStateMachine);

        AccessFlowStateMachineService::resetWorkflowCache();
    }
}
