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

use Orangehrm\Rest\Api\Pim\EmployeeDependentAPI;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeDependent;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiEmployeeDependentAPITest extends PHPUnit_Framework_TestCase
{
    private $employeeDependantAPI;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeDependantAPI = new EmployeeDependentAPI($request);
    }

    public function testGetEmployeeDependants()
    {

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
        $employee->setJoinedDate("2016-04-15");
        $employee->setEmpWorkEmail("mdriggs@hrm.com");
        $employee->setEmpMobile(0754343435);


        $employeeCategory = new JobCategory();
        $employeeCategory->setName("Engineer");

        //   $employee->setContracts(array($employeeContract)) ;
        $employee->setJobCategory($employeeCategory);


        $this->employeeDependantAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMock('EmployeeService');
        $pimEmployeeService->expects($this->any())
            ->method('getEmployeeDependents')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $this->employeeDependantAPI->setEmployeeService($pimEmployeeService);
        $returned = $this->employeeDependantAPI->getEmployeeDependants();

        // creating the employee json array
        $employeeDependant = new EmployeeDependent('Shane Lewis', 'Son', '2015-05-14');

        $jsonEmployeeDependantsArray = $employeeDependant->toArray();

        $assertResponse = new Response($jsonEmployeeDependantsArray, array());

        $this->assertEquals($assertResponse, $returned);

    }

    public function testSaveEmployeeDependants(){

        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);
        $employee->setEmployeeId($empNumber);
        $employee->setJoinedDate("2016-04-15");
        $employee->setEmpWorkEmail("mdriggs@hrm.com");
        $employee->setEmpMobile(0754343435);

        $filters = array();
        $filters[EmployeeDependentAPI::PARAMETER_DOB] = '2016-05-01';
        $filters[EmployeeDependentAPI::PARAMETER_NAME] = 'Nesham Mendis';
        $filters[EmployeeDependentAPI::PARAMETER_RELATIONSHIP] = 'son';
        $filters[EmployeeDependentAPI::PARAMETER_TYPE] = 'other';
        $filters[EmployeeDependentAPI::PARAMETER_ID] = '1';

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeDependantAPI = $this->getMock('Orangehrm\Rest\Api\Pim\EmployeeDependantAPI',array('filterParameters'),array($request));
        $this->employeeDependantAPI->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));

        $pimEmployeeService = $this->getMock('EmployeeService');
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with(1)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('saveEmployee')
            ->with($employee)
            ->will($this->returnValue($employee));

        $this->$pimEmployeeService->setEmployeeService($pimEmployeeService);

        $returned = $this->$pimEmployeeService->saveEmployeeDependants();
        $testResponse = array('success' => 'successfully saved');

        $this->assertEquals($returned, $testResponse);
    }
}