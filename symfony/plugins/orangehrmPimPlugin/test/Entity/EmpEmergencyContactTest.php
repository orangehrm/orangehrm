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

use OrangeHRM\Entity\EmpEmergencyContact;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class EmpEmergencyContactTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmpEmergencyContact::class, Employee::class]);
    }

    public function testEmpEmergencyContactEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $empEmergencyContact = new EmpEmergencyContact();
        $empEmergencyContact->setEmployee($employee);
        $empEmergencyContact->setSeqNo('1');
        $empEmergencyContact->setRelationship('Parent');
        $empEmergencyContact->setHomePhone("0335445678");
        $empEmergencyContact->setMobilePhone("0776734567");
        $empEmergencyContact->setOfficePhone("0113456787");
        $this->persist($empEmergencyContact);

        /** @var EmpEmergencyContact[] $empEmergencyContacts */
        $empEmergencyContacts = $this->getRepository(EmpEmergencyContact::class)->findBy([
            'employee' => 1,
            'seqNo' => '1'
        ]);
        $empEmergencyContact = $empEmergencyContacts[0];
        $this->assertEquals('0001', $empEmergencyContact->getEmployee()->getEmployeeId());
        $this->assertEquals('1', $empEmergencyContact->getSeqNo());
        $this->assertEquals('Parent', $empEmergencyContact->getRelationship());
        $this->assertEquals("0335445678", $empEmergencyContact->getHomePhone());
        $this->assertEquals("0776734567", $empEmergencyContact->getMobilePhone());
        $this->assertEquals("0113456787", $empEmergencyContact->getOfficePhone());
    }
}
