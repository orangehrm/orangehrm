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

namespace OrangeHRM\Tests\Pim\Entity;

use DateTime;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeMembership;
use OrangeHRM\Entity\Membership;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class EmployeeMembershipTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmployeeMembership::class, Employee::class, Membership::class]);
    }

    public function testEmployeeMembershipEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $membership = new Membership();
        $membership->setId(1);
        $membership->setName('membership1');
        $this->persist($membership);

        $employeeMembership = new EmployeeMembership();
        $employeeMembership->setEmployee($employee);
        $employeeMembership->setMembership($membership);
        $employeeMembership->setSubscriptionFee('4');
        $employeeMembership->setSubscriptionPaidBy('individual');
        $employeeMembership->setSubscriptionCurrency('Rs');
        $employeeMembership->setSubscriptionCommenceDate(new DateTime('2011-05-20'));
        $employeeMembership->setSubscriptionRenewalDate(new DateTime('2011-05-22'));
        $this->persist($employeeMembership);

        /** @var EmployeeMembership[] $employeeMemberships */
        $employeeMemberships = $this->getRepository(EmployeeMembership::class)->findBy(
            ['employee' => 1, 'membership' => 1]
        );
        $employeeMembership = $employeeMemberships[0];
        $this->assertEquals('0001', $employeeMembership->getEmployee()->getEmployeeId());
        $this->assertEquals(1, $employeeMembership->getMembership()->getId());
        $this->assertEquals("4", $employeeMembership->getSubscriptionFee());
        $this->assertEquals("individual", $employeeMembership->getSubscriptionPaidBy());
        $this->assertEquals("Rs", $employeeMembership->getSubscriptionCurrency());
        $this->assertEquals(new DateTime('2011-05-20'), $employeeMembership->getSubscriptionCommenceDate());
        $this->assertEquals(new DateTime('2011-05-22'), $employeeMembership->getSubscriptionRenewalDate());
    }
}
