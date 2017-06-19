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
 * @group
 */


use Orangehrm\Rest\Api\Pim\EmployeeService;
use Orangehrm\Rest\Api\Pim\Entity\Employee;

class HttpEmployeeServiceTest extends PHPUnit_Framework_TestCase
{
    private $employeeService;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $this->employeeService = new EmployeeService();
    }

    public function testGetEmployeeDetails(){

        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')->setMethods(['getQueryParam'])->getMock();
        $requestParams->expects($this->once())
            ->method('getQueryParam')
            ->with('id')
            ->will($this->returnValue(1));

        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);

        //mock employee dao
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $pimEmployeeService = new \EmployeeService();
        $pimEmployeeService->setEmployeeDao($mockDao);
        $this->employeeService->setEmployeeService($pimEmployeeService);
        $employeeReturned = $this->employeeService->getEmployeeDetails($requestParams);

        // creating the employee json array
        $apiEmployee = new Employee($employee->getFirstName(), $employee->getMiddleName(), $employee->getLastName(), 25);
        $apiEmployee->buildEmployee($employee);
        $jsonEmployeeArray = $apiEmployee->toArray();


        $this->assertEquals($jsonEmployeeArray, $employeeReturned[0]);

    }

    public function testGetEmployeeDependants(){

        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')->setMethods(['getQueryParam'])->getMock();
        $requestParams->expects($this->once())
            ->method('getQueryParam')
            ->with('id')
            ->will($this->returnValue(1));

        $empNumber = 1;
        $employeeDependant = new \EmpDependent();
        $employeeDependant->setName("Shane Lewis");
        $employeeDependant->setDateOfBirth("2012-09-03");
        $employeeDependant->setRelationship(1);
        $employeeDependantsMockList = array();
        $employeeDependantsMockList [] = $employeeDependant;

        //mock employee dao
        $mockDao = $this->getMockBuilder('EmployeeDao')->getMock();
        $mockDao->expects($this->once())
            ->method('getEmployeeDependents')
            ->with($empNumber)
            ->will($this->returnValue($employeeDependantsMockList));

        $pimEmployeeService = new \EmployeeService();
        $pimEmployeeService->setEmployeeDao($mockDao);
        $this->employeeService->setEmployeeService($pimEmployeeService);
        $employeeDependantsList = $this->employeeService->getEmployeeDependants($requestParams);

        // creating the employee dependants  json array
        $empDependant = new EmployeeDependant($employeeDependant->getName(), $employeeDependant->getRelationship(), $employeeDependant->getDateOfBirth());
        $jsonEmployeeDependantArray = $empDependant->toArray();
        $this->assertEquals($jsonEmployeeDependantArray, $employeeDependantsList[0]);

    }

}