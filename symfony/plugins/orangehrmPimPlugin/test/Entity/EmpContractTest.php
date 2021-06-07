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
use OrangeHRM\Entity\EmpContract;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class EmpContractTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmpContract::class, Employee::class]);
    }

    public function testEmpContractEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $empContract = new EmpContract();
        $empContract->setEmployee($employee);
        $empContract->setContractId('1');
        $empContract->setStartDate(new DateTime('2020-05-23'));
        $empContract->setEndDate(new DateTime('2021-05-23'));
        $this->persist($empContract);

        /** @var EmpContract[] $empContracts */
        $empContracts = $this->getRepository(EmpContract::class)->findBy(['employee' => 1, 'contractId' => '1']);
        $empContract = $empContracts[0];
        $this->assertEquals('0001', $empContract->getEmployee()->getEmployeeId());
        $this->assertEquals('1', $empContract->getContractId());
        $this->assertEquals('2020-05-23', $empContract->getStartDate()->format('Y-m-d'));
        $this->assertEquals('2021-05-23', $empContract->getEndDate()->format('Y-m-d'));
    }
}
