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

use OrangeHRM\Entity\EmployeeSkill;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\Skill;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class EmployeeSkillTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmployeeSkill::class, Employee::class, Skill::class]);
    }

    public function testEmpDependentEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $skill = new Skill();
        $skill->setId(1);
        $skill->setName('Driving');
        $skill->setDescription('Driving Skills');
        $this->persist($skill);

        $employeeSkill = new EmployeeSkill();
        $employeeSkill->setEmployee($employee);
        $employeeSkill->setSkill($skill);
        $employeeSkill->setYearsOfExp(5);
        $employeeSkill->setComments('comment');
        $this->persist($employeeSkill);

        /** @var EmployeeSkill[] $employeeSkills */
        $employeeSkills = $this->getRepository(EmployeeSkill::class)->findBy(['employee' => 1, 'skill' => 1]);
        $employeeSkill = $employeeSkills[0];
        $this->assertEquals('0001', $employeeSkill->getEmployee()->getEmployeeId());
        $this->assertEquals(1, $employeeSkill->getSkill()->getId());
        $this->assertEquals(5, $employeeSkill->getYearsOfExp());
        $this->assertEquals('comment', $employeeSkill->getComments());
    }
}
