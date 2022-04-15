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

use Orangehrm\Rest\Api\Pim\EmployeeJobDetailAPI;
use Orangehrm\Rest\Api\Pim\Entity\EmployeeJobDetail;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiEmployeeJobDetailAPITest extends PHPUnit_Framework_TestCase
{
    private $employeeJobDetailAPI;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeJobDetailAPI = new EmployeeJobDetailAPI($request);
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
        $employee->setJoinedDate("2016-04-15");

        $employeeCategory = new JobCategory();
        $employeeCategory->setName("Engineer");

        $employeeContract = new EmpContract();
        $employeeContract->setStartDate("2016-04-31");
        $employeeContract->setEndDate("2018-04-31");

     //   $employee->setContracts(array($employeeContract)) ;
        $employee->setJobCategory($employeeCategory);


        $this->employeeJobDetailAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $this->employeeJobDetailAPI->setEmployeeService($pimEmployeeService);
        $employeeReturned = $this->employeeJobDetailAPI->getEmployeeJobDetails();

        // creating the employee json array
        $employeeJobDetails = new EmployeeJobDetail();

        $employeeJobDetails->build($employee);

        $jsonEmployeeJobDetailArray = $employeeJobDetails->toArray();

        $assertResponse = new Response($jsonEmployeeJobDetailArray,array());

        $this->assertEquals($assertResponse, $employeeReturned);

    }


    public function testSaveJobDetails(){

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
        $filters[EmployeeJobDetailAPI::PARAMETER_ID] = '1';
        $filters[EmployeeJobDetailAPI::PARAMETER_TITLE] = '1';
        $filters[EmployeeJobDetailAPI::PARAMETER_CATEGORY] = '1';
        $filters[EmployeeJobDetailAPI::PARAMETER_END_DATE] = '2016-05-04';
        $filters[EmployeeJobDetailAPI::PARAMETER_START_DATE] = '2016-05-06';
        $filters[EmployeeJobDetailAPI::PARAMETER_JOINED_DATE] = '2018-05-06';

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $employeeJobDetailAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeJobDetailAPI')
            ->setMethods(array('filterParameters','validateInputs','buildEmployeeJobDetails'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $employeeJobDetailAPI->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));
        $employeeJobDetailAPI->expects($this->once())
            ->method('validateInputs')
            ->with($filters)
            ->will($this->returnValue(true));
        $employeeJobDetailAPI->expects($this->any())
            ->method('buildEmployeeJobDetails')
            ->with($employee,$filters);


        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with(1)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('saveEmployee')
            ->with($employee)
            ->will($this->returnValue($employee));

        $employeeJobDetailAPI->setEmployeeService($pimEmployeeService);

        $returned = $employeeJobDetailAPI->saveEmployeeJobDetails();
        $testResponse = new Response(array('success' => 'Successfully Saved'));

        $this->assertEquals($returned, $testResponse);

    }

    public function testUpdateJobDetails(){

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
        $filters[EmployeeJobDetailAPI::PARAMETER_ID] = '1';
        $filters[EmployeeJobDetailAPI::PARAMETER_TITLE] = '1';
        $filters[EmployeeJobDetailAPI::PARAMETER_CATEGORY] = '1';
        $filters[EmployeeJobDetailAPI::PARAMETER_END_DATE] = '2016-05-04';
        $filters[EmployeeJobDetailAPI::PARAMETER_START_DATE] = '2016-05-06';
        $filters[EmployeeJobDetailAPI::PARAMETER_JOINED_DATE] = '2018-05-06';
//        $filters[EmployeeJobDetailAPI::PARAMETER_SUBUNIT] = 'Engineering';
//        $filters[EmployeeJobDetailAPI::PARAMETER_LOCATION] = 'Eng Dept';
//        $filters[EmployeeJobDetailAPI::PARAMETER_STATUS] = 'Active';

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $employeeJobDetailAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeJobDetailAPI')
            ->setMethods(array('filterParameters','validateInputs', 'buildEmployeeJobDetails'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $employeeJobDetailAPI->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));
        $employeeJobDetailAPI->expects($this->once())
            ->method('validateInputs')
            ->with($filters)
            ->will($this->returnValue(true));
        $employeeJobDetailAPI->expects($this->any())
            ->method('buildEmployeeJobDetails')
            ->with($employee,$filters);


        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with(1)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('saveEmployee')
            ->with($employee)
            ->will($this->returnValue($employee));

        $employeeJobDetailAPI->setEmployeeService($pimEmployeeService);

        $returned = $employeeJobDetailAPI->saveEmployeeJobDetails();
        $testResponse = new Response(array('success' => 'Successfully Saved'));

        $this->assertEquals($returned, $testResponse);

    }


}