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

class HttpEmployeeTest extends PHPUnit_Framework_TestCase
{

    /**
     * Set up method
     */
    protected function setUp()
    {

    }

    public function testToArray(){

        $employee = new Employee('Nina','Jane','Lewis',1);
        $employee->setEmployeeId(1);
        $employee->setEmpBirthDate('1985-05-05');
        $employee->setEmployeeNumber('001');
        $employee->setJobTitle('Marketing');

        $array = array(
            'firstName' =>'Nina',
            'middleName' => 'Jane',
            'id'         => 1,
            'employeeNumber'=>'001',
            'lastName' => 'Lewis',
            'fullName' => 'Nina Jane Lewis',
            'status'   => 'Active',
            'dob'      => '1985-05-05',
            'unit'    =>'',
            'jobTitle'=> 'Marketing',
            'supervisor' => ''

        );

        $this->assertEquals($array, $employee->toArray());
    }

}