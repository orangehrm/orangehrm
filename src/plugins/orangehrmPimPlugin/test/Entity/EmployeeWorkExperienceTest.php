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
use OrangeHRM\Entity\EmpWorkExperience;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class EmployeeWorkExperienceTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmpWorkExperience::class, Employee::class]);
    }

    public function testEmployeeWorkExperienceEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $employeeWorkExperience = new EmpWorkExperience();
        $employeeWorkExperience->setEmployee($employee);
        $employeeWorkExperience->setSeqNo('4');
        $employeeWorkExperience->setEmployer('OHRM');
        $employeeWorkExperience->setJobTitle('SE');
        $employeeWorkExperience->setComments('test');
        $employeeWorkExperience->setInternal(3);
        $employeeWorkExperience->setFromDate(new DateTime('2017-01-01'));
        $employeeWorkExperience->setToDate(new DateTime('2020-12-31'));
        $this->persist($employeeWorkExperience);

        /** @var EmpWorkExperience[] $employeeWorkExperiences */
        $employeeWorkExperiences = $this->getRepository(EmpWorkExperience::class)->findBy(
            ['employee' => 1, 'seqNo' => 4]
        );
        $employeeWorkExperience = $employeeWorkExperiences[0];
        $this->assertEquals('0001', $employeeWorkExperience->getEmployee()->getEmployeeId());
        $this->assertEquals(4, $employeeWorkExperience->getSeqNo());
        $this->assertEquals("OHRM", $employeeWorkExperience->getEmployer());
        $this->assertEquals("SE", $employeeWorkExperience->getJobTitle());
        $this->assertEquals("test", $employeeWorkExperience->getComments());
        $this->assertEquals(3, $employeeWorkExperience->getInternal());
        $this->assertEquals(new DateTime("2017-01-01"), $employeeWorkExperience->getFromDate());
        $this->assertEquals(new DateTime("2020-12-31"), $employeeWorkExperience->getToDate());
    }
}
