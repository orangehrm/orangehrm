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
use OrangeHRM\Entity\EmployeeImmigrationRecord;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class EmployeeImmigrationRecordTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmployeeImmigrationRecord::class, Employee::class]);
    }

    public function testEmpEmergencyContactEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $employeeImmigrationRecord = new EmployeeImmigrationRecord();
        $employeeImmigrationRecord->setEmployee($employee);
        $employeeImmigrationRecord->setRecordId('1');
        $employeeImmigrationRecord->setNumber('HVN0003472');
        $employeeImmigrationRecord->setType(1);
        $employeeImmigrationRecord->setIssuedDate(new DateTime('2010-12-12'));
        $employeeImmigrationRecord->setExpiryDate(new DateTime('2011-12-12'));
        $employeeImmigrationRecord->setStatus('some status');
        $employeeImmigrationRecord->setComment('test Comment');
        $employeeImmigrationRecord->setReviewDate(new DateTime('2011-12-30'));
        $employeeImmigrationRecord->setCountryCode('UK');
        $this->persist($employeeImmigrationRecord);

        /** @var EmployeeImmigrationRecord[] $employeeImmigrationRecords */
        $employeeImmigrationRecords = $this->getRepository(EmployeeImmigrationRecord::class)->findBy([
            'employee' => 1,
            'recordId' => '1'
        ]);
        $employeeImmigrationRecord = $employeeImmigrationRecords[0];
        $this->assertEquals('0001', $employeeImmigrationRecord->getEmployee()->getEmployeeId());
        $this->assertEquals('1', $employeeImmigrationRecord->getRecordId());
        $this->assertEquals('HVN0003472', $employeeImmigrationRecord->getNumber());
        $this->assertEquals(1, $employeeImmigrationRecord->getType());
        $this->assertEquals(new DateTime("2010-12-12"), $employeeImmigrationRecord->getIssuedDate());
        $this->assertEquals(new DateTime("2011-12-12"), $employeeImmigrationRecord->getExpiryDate());
        $this->assertEquals(new DateTime("2011-12-30"), $employeeImmigrationRecord->getReviewDate());
        $this->assertEquals("some status", $employeeImmigrationRecord->getStatus());
        $this->assertEquals("test Comment", $employeeImmigrationRecord->getComment());
        $this->assertEquals("UK", $employeeImmigrationRecord->getCountryCode());
    }
}
