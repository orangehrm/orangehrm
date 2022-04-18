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

use Orangehrm\Rest\Api\Pim\EmployeeDetailAPI;
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiEmployeeDetailAPITest extends PHPUnit_Framework_TestCase
{
    private $employeeDetailAPI;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeDetailAPI = new EmployeeDetailAPI($request);
    }

    public function testGetEmployeeDetails(){

        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrlParam'))
            ->getMock();
        $requestParams->expects($this->any())
            ->method('getUrlParam')
            ->with('id')
            ->will($this->returnValue(1));

        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);
        $employee->setEmployeeId($empNumber);

        $this->employeeDetailAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $this->employeeDetailAPI->setEmployeeService($pimEmployeeService);
        $employeeReturned = $this->employeeDetailAPI->getEmployeeDetails();

        // creating the employee json array
        $apiEmployee = new Employee($employee->getFirstName(), $employee->getMiddleName(), $employee->getLastName(),1);
        $apiEmployee->buildEmployee($employee);
        $jsonEmployeeArray = $apiEmployee->toArray();

        $assertResponse = new Response($jsonEmployeeArray,array(
            'contact-detail' => '/employee/:id/contact-detail',
            'job-detail' => '/employee/:id/job-detail',
            'supervisor' => '/employee/:id/supervisor',
            'dependent' => '/employee/:id/dependent'));

        $this->assertEquals($assertResponse, $employeeReturned);

    }
}