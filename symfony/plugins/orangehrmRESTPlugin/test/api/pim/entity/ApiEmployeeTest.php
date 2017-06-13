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


/**
 * Test class of Api/EmployeeService
 *
 * @group API
 */
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Api\Pim\Entity\Supervisor;

class ApiEmployeeTest extends PHPUnit_Framework_TestCase
{

    /**
     * Set up method
     */
    protected function setUp()
    {

    }

    public function testToArray()
    {


        $supervisor = new Supervisor("Nina Lewis", 1);
        $supervisorsList = array();
        $supervisorsList[] = $supervisor;

        $testEmployeeArray = array(

            'firstName' => 'Martin',
            'middleName' => 'Riggs',
            'employeeId' => '1',
            'lastName' => 'Dan',
            'fullName' => 'Martin Riggs Dan',
            'status' => 'active',
            'dob' => '2016-05-04',
            'unit' => '',
            'jobTitle' => 'Engineer',
            'code' => '1',
            'driversLicenseNumber' => '',
            'licenseExpiryDate' => null,
            'maritalStatus' => null,
            'gender' => null,
            'otherId' => null,
            'nationality' => null,
            'supervisor' => $supervisorsList,
            'sinNumber'  => null,
            'ssnNumber'  => null

        );

        \OrangeConfig::getInstance()->setAppConfValue(\ConfigService::KEY_PIM_SHOW_SIN, true);
        \OrangeConfig::getInstance()->setAppConfValue(\ConfigService::KEY_PIM_SHOW_SSN, true);

        $employee = new Employee('Martin', 'Riggs', 'Dan', '1');
        $employee->setEmployeeFullName('Martin Riggs Dan');
        $employee->setEmployeeStatus('active');
        $employee->setEmpBirthDate('2016-05-04');
        $employee->setUnit('');
        $employee->setJobTitle('Engineer');
        $employee->setEmployeeNumber('1');

        $employee->setSupervisors($supervisorsList);

        $this->assertEquals($testEmployeeArray, $employee->toArray());

        \OrangeConfig::getInstance()->setAppConfValue(\ConfigService::KEY_PIM_SHOW_SIN, false);
        unset($testEmployeeArray['sinNumber']);
        $this->assertEquals($testEmployeeArray, $employee->toArray());

        \OrangeConfig::getInstance()->setAppConfValue(\ConfigService::KEY_PIM_SHOW_SSN, false);
        unset($testEmployeeArray['ssnNumber']);
        $this->assertEquals($testEmployeeArray, $employee->toArray());
    }

}