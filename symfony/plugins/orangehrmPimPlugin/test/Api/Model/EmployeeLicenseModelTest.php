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

use DateTime;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeLicense;
use OrangeHRM\Entity\License;
use OrangeHRM\Pim\Api\Model\EmployeeLicenseModel;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Pim
 * @group Model
 */
class EmployeeLicenseModelTest extends TestCase
{
    public function testToArray()
    {
        $resultArray = [
            'licenseNo' => '02',
            'Decorator' =>[
                'licenseIssuedDate' => '2019-05-19',
                'licenseExpiryDate'=> '2020-05-19'
            ],
            "license" => [
                "id" => 1,
                "name" => "CIMA"
            ]
        ];

        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('First');
        $employee->setMiddleName('Middle');
        $employee->setLastName('Last');
        $employee->setEmployeeId('0001');
        $employee->setEmployeeTerminationRecord(null);

        $license = new License();
        $license->setId(1);
        $license->setName('CIMA');

        $employeeLicense = new EmployeeLicense();
        $employeeLicense->setEmployee($employee);
        $employeeLicense->setLicense($license);
        $employeeLicense->setLicenseNo('02');
        $employeeLicense->getDecorator()->setLicenseIssuedDate(new DateTime('2019-05-19'));
        $employeeLicense->getDecorator()->setLicenseExpiryDate(new DateTime('2020-05-19'));

        $employeeModel = new EmployeeLicenseModel($employeeLicense);

        $this->assertEquals($resultArray, $employeeModel->toArray());
    }
}
