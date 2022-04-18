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

namespace OrangeHRM\Tests\Leave\Service;

use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Leave\Dao\LeaveTypeDao;
use OrangeHRM\Leave\Entitlement\LeaveBalance;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeaveTypeService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Leave
 * @group Service
 */
class LeaveTypeServiceTest extends TestCase
{
    /**
     * @var LeaveTypeService
     */
    private LeaveTypeService $leaveTypeService;

    public function setup(): void
    {
        $this->leaveTypeService = new LeaveTypeService();
    }

    public function testGetLeaveTypeDao(): void
    {
        $this->assertTrue($this->leaveTypeService->getLeaveTypeDao() instanceof LeaveTypeDao);
    }

    public function testGetEligibleLeaveTypesByEmpNumber(): void
    {
        $empNumber = 1;
        $leaveType1 = new LeaveType();
        $leaveType1->setId(1);
        $leaveType1->setName('Annual');
        $leaveType2 = new LeaveType();
        $leaveType2->setId(2);
        $leaveType2->setName('Medical');
        $leaveTypeList = [$leaveType1, $leaveType2];

        $mockDao = $this->getMockBuilder(LeaveTypeDao::class)
            ->onlyMethods(['getLeaveTypeList'])
            ->getMock();
        $mockDao->expects($this->once())
            ->method('getLeaveTypeList')
            ->willReturn($leaveTypeList);

        $leaveBalanceMockBuilder = $this->getMockBuilder(LeaveBalance::class)
            ->onlyMethods(['updateBalance']);
        $leaveBalance1 = $leaveBalanceMockBuilder->setConstructorArgs([2])->getMock();
        $leaveBalance2 = $leaveBalanceMockBuilder->setConstructorArgs([0])->getMock();

        $leaveEntitlementService = $this->getMockBuilder(LeaveEntitlementService::class)
            ->onlyMethods(['getLeaveBalance'])
            ->getMock();
        $leaveEntitlementService->expects($this->exactly(2))
            ->method('getLeaveBalance')
            ->willReturnMap(
                [
                    [$empNumber, 1, null, null, $leaveBalance1],
                    [$empNumber, 2, null, null, $leaveBalance2]
                ]
            );

        $this->leaveTypeService = $this->getMockBuilder(LeaveTypeService::class)
            ->onlyMethods(['getLeaveTypeDao', 'getLeaveEntitlementService'])
            ->getMock();
        $this->leaveTypeService->expects($this->once())
            ->method('getLeaveTypeDao')
            ->willReturn($mockDao);
        $this->leaveTypeService->expects($this->exactly(2))
            ->method('getLeaveEntitlementService')
            ->willReturn($leaveEntitlementService);

        $leaveTypes = $this->leaveTypeService->getEligibleLeaveTypesByEmpNumber($empNumber);
        $this->assertEquals([$leaveType1], $leaveTypes);
    }
}
