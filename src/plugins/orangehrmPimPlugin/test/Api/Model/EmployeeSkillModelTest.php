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

namespace OrangeHRM\Tests\Pim\Api\Model;

use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeSkill;
use OrangeHRM\Entity\Skill;
use OrangeHRM\Pim\Api\Model\EmployeeSkillModel;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Pim
 * @group Model
 */
class EmployeeSkillModelTest extends TestCase
{
    public function testToArray()
    {
        $resultArray = [
            'yearsOfExperience' => 5,
            'comments' => 'test comment',
            "skill" => [
                "id" => 1,
                "name" => "Driving",
                "description" => "Driving Skills"
            ]
        ];

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('First');
        $employee->setMiddleName('Middle');
        $employee->setLastName('Last');
        $employee->setEmployeeId('0001');
        $employee->setEmployeeTerminationRecord(null);

        $skill = new Skill();
        $skill->setId(1);
        $skill->setName('Driving');
        $skill->setDescription('Driving Skills');

        $employeeSkill = new EmployeeSkill();
        $employeeSkill->setEmployee($employee);
        $employeeSkill->setSkill($skill);
        $employeeSkill->setYearsOfExp(5);
        $employeeSkill->setComments('test comment');

        $employeeModel = new EmployeeSkillModel($employeeSkill);

        $this->assertEquals($resultArray, $employeeModel->toArray());
    }
}
