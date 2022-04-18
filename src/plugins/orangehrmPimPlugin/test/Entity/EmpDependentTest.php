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
use OrangeHRM\Entity\EmpDependent;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class EmpDependentTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmpDependent::class, Employee::class]);
    }

    public function testEmpDependentEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $empDependent = new EmpDependent();
        $empDependent->setEmployee($employee);
        $empDependent->setSeqNo('1');
        $empDependent->setRelationshipType(EmpDependent::RELATIONSHIP_TYPE_OTHER);
        $empDependent->setRelationship('Parent');
        $empDependent->setDateOfBirth(new DateTime('2001-05-23'));
        $this->persist($empDependent);

        /** @var EmpDependent[] $empDependents */
        $empDependents = $this->getRepository(EmpDependent::class)->findBy(['employee' => 1, 'seqNo' => '1']);
        $empDependent = $empDependents[0];
        $this->assertEquals('0001', $empDependent->getEmployee()->getEmployeeId());
        $this->assertEquals('1', $empDependent->getSeqNo());
        $this->assertEquals('other', $empDependent->getRelationshipType());
        $this->assertEquals('Parent', $empDependent->getRelationship());
        $this->assertEquals('2001-05-23', $empDependent->getDateOfBirth()->format('Y-m-d'));
    }
}
