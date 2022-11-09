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
use OrangeHRM\Pim\Api\Model\EmployeeDetailedModel;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Pim
 * @group Model
 */
class EmployeeDetailedModelTest extends TestCase
{
    public function testToArray()
    {
        $resultArray = [
            'empNumber' => 1,
            'lastName' => 'Last',
            'firstName' => 'First',
            'middleName' => 'Middle',
            'employeeId' => '0001',
            'terminationId' => null,
            'jobTitle' => [
                'id' => null,
                'title' => null,
                'isDeleted' => null,
            ],
            'subunit' => [
                'id' => null,
                'name' => null,
            ],
            'empStatus' => [
                'id' => null,
                'name' => null,
            ],
            'supervisors' => []
        ];

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('First');
        $employee->setMiddleName('Middle');
        $employee->setLastName('Last');
        $employee->setEmployeeId('0001');
        $employee->setEmployeeTerminationRecord(null);

        $employeeModel = new EmployeeDetailedModel($employee);
        $this->assertEquals($resultArray, $employeeModel->toArray());
    }
}
