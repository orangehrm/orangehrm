<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\CorporateDirectory\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\NormalizeException;
use OrangeHRM\Entity\Employee;
use OrangeHRM\CorporateDirectory\Api\Model\EmployeeDirectoryDetailedModel;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Directory
 * @group Model
 */
class EmployeeDirectoryDetailedModelTest extends TestCase
{
    /**
     * @return void
     * @throws NormalizeException
     */
    public function testToArray()
    {
        $resultArray = [
            'empNumber' => 1,
            'lastName' => 'Last',
            'firstName' => 'First',
            'middleName' => 'Middle',
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
            'location' => [
                'id' => null,
                'name' => null,
            ],
            'contactInfo' => [
                'workEmail' => null,
                'workTelephone' => null,
            ],
        ];

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('First');
        $employee->setMiddleName('Middle');
        $employee->setLastName('Last');
        $employee->setEmployeeTerminationRecord(null);
        $employee->setWorkEmail(null);
        $employee->setWorkTelephone(null);

        $employeeModel = new EmployeeDirectoryDetailedModel($employee);
        $this->assertEquals($resultArray, $employeeModel->toArray());
    }
}
