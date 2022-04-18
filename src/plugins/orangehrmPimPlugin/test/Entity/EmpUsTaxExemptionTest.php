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
use OrangeHRM\Entity\EmpUsTaxExemption;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Entity
 */
class EmpUsTaxExemptionTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([EmpUsTaxExemption::class, Employee::class]);
    }

    public function testEmpUsTaxExemptionEntity(): void
    {
        $employee = new Employee();
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeId('0001');
        $this->persist($employee);

        $empUsTaxExemption = new EmpUsTaxExemption();
        $empUsTaxExemption->setEmployee($employee);
        $empUsTaxExemption->setFederalStatus('Single');
        $empUsTaxExemption->setFederalExemptions(2);
        $empUsTaxExemption->setState('AK');
        $empUsTaxExemption->setStateStatus('Single');
        $empUsTaxExemption->setStateExemptions(1);
        $empUsTaxExemption->setUnemploymentState('AK');
        $empUsTaxExemption->setWorkState('AK');
        $this->persist($empUsTaxExemption);

        /** @var EmpUsTaxExemption[] $empUsTaxExemptions */
        $empUsTaxExemptions = $this->getRepository(EmpUsTaxExemption::class)->findBy([
            'employee' => 1
        ]);
        $empUsTaxExemption = $empUsTaxExemptions[0];
        $this->assertEquals('0001', $empUsTaxExemption->getEmployee()->getEmployeeId());
        $this->assertEquals('Single', $empUsTaxExemption->getFederalStatus());
        $this->assertEquals(2, $empUsTaxExemption->getFederalExemptions());
        $this->assertEquals('AK', $empUsTaxExemption->getState());
        $this->assertEquals('Single', $empUsTaxExemption->getStateStatus());
        $this->assertEquals(1, $empUsTaxExemption->getStateExemptions());
        $this->assertEquals('AK', $empUsTaxExemption->getUnemploymentState());
        $this->assertEquals('AK', $empUsTaxExemption->getWorkState());
    }
}
