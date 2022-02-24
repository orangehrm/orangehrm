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

use OrangeHRM\Attendance\Api\AttendanceRecordAPI;
use OrangeHRM\Attendance\Service\AttendanceService;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

/**
 * @group Attendance
 * @group APIv2
 */
class AttendanceRecordAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('AttendanceRecord.yaml', null, true);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(AttendanceRecordAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('AttendanceRecordAPITestCases.yaml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('AttendanceRecord.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(AttendanceRecordAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public static function enableUserCanModifyAttendancePreHook()
    {
        $workflowStateMachine = new WorkflowStateMachine();
        $workflowStateMachine->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachine->setState(AttendanceRecord::STATE_PUNCHED_IN);
        $workflowStateMachine->setRole(AttendanceService::ESS_USER);
        $workflowStateMachine->setAction(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME);
        $workflowStateMachine->setResultingState(AttendanceRecord::STATE_PUNCHED_IN);
        Doctrine::getEntityManager()->persist($workflowStateMachine);
        Doctrine::getEntityManager()->flush($workflowStateMachine);
        $workflowStateMachine = new WorkflowStateMachine();
        $workflowStateMachine->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachine->setState(AttendanceRecord::STATE_PUNCHED_OUT);
        $workflowStateMachine->setRole(AttendanceService::ESS_USER);
        $workflowStateMachine->setAction(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME);
        $workflowStateMachine->setResultingState(AttendanceRecord::STATE_PUNCHED_OUT);
        Doctrine::getEntityManager()->persist($workflowStateMachine);
        Doctrine::getEntityManager()->flush($workflowStateMachine);

        AccessFlowStateMachineService::resetWorkflowCache();
    }

    public static function enableSupervisorCanModifyAttendancePreHook()
    {
        $workflowStateMachine = new WorkflowStateMachine();
        $workflowStateMachine->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachine->setState(AttendanceRecord::STATE_PUNCHED_IN);
        $workflowStateMachine->setRole(AttendanceService::SUPERVISOR);
        $workflowStateMachine->setAction(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME);
        $workflowStateMachine->setResultingState(AttendanceRecord::STATE_PUNCHED_IN);
        Doctrine::getEntityManager()->persist($workflowStateMachine);
        Doctrine::getEntityManager()->flush($workflowStateMachine);
        $workflowStateMachine = new WorkflowStateMachine();
        $workflowStateMachine->setWorkflow(WorkflowStateMachine::FLOW_ATTENDANCE);
        $workflowStateMachine->setState(AttendanceRecord::STATE_PUNCHED_OUT);
        $workflowStateMachine->setRole(AttendanceService::SUPERVISOR);
        $workflowStateMachine->setAction(WorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME);
        $workflowStateMachine->setResultingState(AttendanceRecord::STATE_PUNCHED_OUT);
        Doctrine::getEntityManager()->persist($workflowStateMachine);
        Doctrine::getEntityManager()->flush($workflowStateMachine);

        AccessFlowStateMachineService::resetWorkflowCache();
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('AttendanceRecordAPITestCases.yaml', 'Update');
    }

    public function testDelete(): void
    {
        $api = new AttendanceRecordAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new AttendanceRecordAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
