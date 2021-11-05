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

namespace OrangeHRM\Tests\Leave\Entity;

use DateTime;
use Generator;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveEntitlement;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Entity
 */
class LeaveEntitlementTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([LeaveEntitlement::class]);
    }

    /**
     * @dataProvider leaveEntitlementDataProvider
     */
    public function testLeaveEntitlement($entitlement, $expected)
    {
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $leaveType = new LeaveType();
        $leaveType->setName('Medical');

        $leaveEntitlement = new LeaveEntitlement();
        $leaveEntitlement->setNoOfDays($entitlement);
        $leaveEntitlement->setEmployee($employee);
        $leaveEntitlement->setLeaveType($leaveType);
        $leaveEntitlement->setFromDate(new DateTime('2021-01-01'));
        $leaveEntitlement->setToDate(new DateTime('2021-12-31'));

        $this->assertEquals($expected, $leaveEntitlement->getNoOfDays());
        $this->assertEquals('0', $leaveEntitlement->getDaysUsed());
    }

    /**
     * @return Generator
     */
    public function leaveEntitlementDataProvider(): Generator
    {
        yield ['10000.00', 10000];
        yield ['1000.50', 1000.5];
        yield ['1000.3333333333333333', 1000.3333];
        yield ['1000.6666666666666666', 1000.6667];
    }
}
