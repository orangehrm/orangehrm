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
use OrangeHRM\Entity\Education;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeEducation;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class EmployeeEducationTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmployeeEducation::class, Employee::class, Education::class]);
    }

    public function testEmployeeEducationEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $education = new Education();
        $education->setId(1);
        $education->setName('BSc');
        $this->persist($education);

        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setEmployee($employee);
        $employeeEducation->setEducation($education);
        $employeeEducation->setInstitute('UCSC');
        $employeeEducation->setMajor('CS');
        $employeeEducation->setYear(2020);
        $employeeEducation->setScore('First Class');
        $employeeEducation->setStartDate(new DateTime('2017-01-01'));
        $employeeEducation->setEndDate(new DateTime('2020-12-31'));
        $this->persist($employeeEducation);

        /** @var EmployeeEducation[] $employeeEducations */
        $employeeEducations = $this->getRepository(EmployeeEducation::class)->findBy(
            ['employee' => 1, 'education' => 1]
        );
        $employeeEducation = $employeeEducations[0];
        $this->assertEquals('0001', $employeeEducation->getEmployee()->getEmployeeId());
        $this->assertEquals(1, $employeeEducation->getEducation()->getId());
        $this->assertEquals("UCSC", $employeeEducation->getInstitute());
        $this->assertEquals("CS", $employeeEducation->getMajor());
        $this->assertEquals("First Class", $employeeEducation->getScore());
        $this->assertEquals(2020, $employeeEducation->getYear());
        $this->assertEquals(new DateTime("2017-01-01"), $employeeEducation->getStartDate());
        $this->assertEquals(new DateTime("2020-12-31"), $employeeEducation->getEndDate());
    }
}
