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

use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Nationality;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class EmployeeTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([Employee::class, Nationality::class]);
    }

    public function testEmployeeEntityWithNationality(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        /** @var Employee $employee */
        $employee = $this->getRepository(Employee::class)->find(1);

        $this->assertEquals('0001', $employee->getEmployeeId());
        $this->assertNull($employee->getNationality());

        $nationality = new Nationality();
        $nationality->setName('Afghan');
        $this->persist($nationality);

        /** @var Nationality $nationality */
        $nationality = $this->getRepository(Nationality::class)->find(1);
        $employee->setNationality($nationality);
        $this->persist($employee);

        /** @var Employee $employee */
        $employee = $this->getRepository(Employee::class)->find(1);
        $this->assertEquals('0001', $employee->getEmployeeId());
        $this->assertEquals('Afghan', $employee->getNationality()->getName());
        $this->assertEquals(1, $employee->getNationality()->getId());
    }

    public function testEmployeeDetailsTrim(): void
    {
        TestDataService::truncateSpecificTables([Employee::class, Nationality::class]);

        $employee = new Employee();
        $employee->setFirstName('Kayla   ');
        $employee->setLastName(' junior ');
        $employee->setMiddleName('Abbey ');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        /** @var Employee $employee */
        $employee = $this->getRepository(Employee::class)->find(1);
        $this->assertEquals('Kayla Abbey junior', $employee->getDecorator()->getFullName());
    }
}
