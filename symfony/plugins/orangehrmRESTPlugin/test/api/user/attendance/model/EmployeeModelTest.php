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

use Orangehrm\Rest\Api\User\Attendance\Model\EmployeeModel;

/**
 * @group API
 */
class EmployeeModelTest extends PHPUnit\Framework\TestCase
{
    public function testToArray()
    {
        $testArray = [
            'employeeId' => '1',
            'employeeName' => 'Test Employee',
            'code' => '0001',
            'jobTitle' => 'SE',
            'unit' => 'Subunit',
            'status' => 'Status',
        ];

        $jobTitle = new JobTitle();
        $jobTitle->setJobTitleName('SE');

        $unit = new Subunit();
        $unit->setName('Subunit');

        $status = new EmploymentStatus();
        $status->setName('Status');

        $employee = new Employee();
        $employee->setEmpNumber('1');
        $employee->setFirstName("Test");
        $employee->setLastName("Employee");
        $employee->setEmployeeId('0001');
        $employee->setJobTitle($jobTitle);
        $employee->setSubDivision($unit);
        $employee->setEmployeeStatus($status);

        $employeeModel = new EmployeeModel($employee);

        $this->assertEquals($testArray, $employeeModel->toArray());
    }
}
