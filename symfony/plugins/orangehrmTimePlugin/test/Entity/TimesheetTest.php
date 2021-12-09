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

namespace OrangeHRM\Tests\Time\Entity;

use DateTime;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Entity\WorkflowStateMachine;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group @Time
 * @group @Entity
 */
class TimesheetTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([Timesheet::class]);
    }

    public function testTimesheetEntity(): void
    {
        $timesheet = new Timesheet();
        $accessFlowStateMachineService = new AccessFlowStateMachineService();
        $tempNextState = $accessFlowStateMachineService->getNextState(
            WorkflowStateMachine::FLOW_TIME_TIMESHEET,
            Timesheet::STATE_INITIAL,
            "SYSTEM",
            WorkflowStateMachine::TIMESHEET_ACTION_CREATE
        );
        $timesheet->setState($tempNextState);
        $timesheet->setStartDate(new DateTime("2021-01-01"));
        $timesheet->setEndDate(new DateTime("2021-01-06"));
        $timesheet->setEmployeeId(1);
        $this->persist($timesheet);

        /** @var Timesheet $timesheet */
        $timesheet = $this->getRepository(Timesheet::class)->find(1);
        $this->assertEquals(new DateTime("2021-01-01"), $timesheet->getStartDate());
        $this->assertEquals(new DateTime("2021-01-06"), $timesheet->getEndDate());
        $this->assertEquals($tempNextState, $timesheet->getState());
        $this->assertEquals(1, $timesheet->getEmployeeId());
    }
}
