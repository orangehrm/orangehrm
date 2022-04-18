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

use Orangehrm\Rest\Api\Pim\EmployeeSupervisorAPI;
use Orangehrm\Rest\Api\Pim\Entity\Supervisor;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiSupervisorAPITest extends PHPUnit_Framework_TestCase
{
    private $employeeSupervisorAPI;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeSupervisorAPI = new EmployeeSupervisorAPI($request);
    }

    public function testGetEmployeeSupervisors()
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

        $empNumber = 1;
        $employeeSupervisor = new \Employee();
        $employeeSupervisor->setLastName('Lewis');
        $employeeSupervisor->setFirstName('Shane');
        $employeeSupervisor->setEmpNumber(2);
        $employeeSupervisor->setEmployeeId(2021);
        $employeeSupervisor->setJoinedDate("2016-04-15");
        $employeeSupervisor->setEmpWorkEmail("mdriggs@hrm.com");
        $employeeSupervisor->setEmpMobile(0754343435);

        $empSupervisorTest = new \ReportTo();
        $empSupervisorTest->setSupervisor($employeeSupervisor);
        $reportingMethod = new ReportingMethod();
        $reportingMethod->setName('Direct');
        $empSupervisorTest->setReportingMethod($reportingMethod);

        $supervisorsList = new Doctrine_Collection('ReportTo');
        $supervisorsList[] = $empSupervisorTest;


        $this->employeeSupervisorAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getImmediateSupervisors')
            ->with($empNumber)
            ->will($this->returnValue($supervisorsList));

        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $this->employeeSupervisorAPI->setEmployeeService($pimEmployeeService);
        $returned = $this->employeeSupervisorAPI->getEmployeeSupervisors();

        // creating the employee json array
        $empSupervisor = new Supervisor('Shane Lewis', '2', '2021','Direct');

        $jsonEmployeeSupervisorsArray = $empSupervisor->toArray();

        $assertResponse = new Response(array($jsonEmployeeSupervisorsArray), array());

        $this->assertEquals($assertResponse, $returned);

    }


    public function testDeleteSupervisor(){


        $filters[] = array();

        $filters['id'] =1;
        $filters['supervisorId'] =2;
        $filters['reportingMethod'] ='Direct';

        $empSupervisorTest = new \ReportTo();
        $reportingMethod = new ReportingMethod();
        $reportingMethod->setId(1);
        $reportingMethod->setName('Direct');
        $empSupervisorTest->setReportingMethod($reportingMethod);

        $supervisorsList = new Doctrine_Collection('ReportTo');
        $supervisorsList[] = $empSupervisorTest;

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getReportToObject')
            ->with(2,1)
            ->will($this->returnValue($empSupervisorTest));

        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with(1)
            ->will($this->returnValue(1));

        $pimEmployeeService->expects($this->any())
            ->method('removeSupervisor')
            ->with(2,1,1)
            ->will($this->returnValue(true));

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeSupervisorAPI = $this->getMock('Orangehrm\Rest\Api\Pim\EmployeeSupervisorAPI',array('filterParameters'),array($request));
        $this->employeeSupervisorAPI->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));


        $reportingMethodConfigService = $this->getMockBuilder('ReportingMethodConfigurationService')->getMock();
        $reportingMethodConfigService->expects($this->any())
            ->method('getReportingMethodByName')
            ->with(null)
            ->will($this->returnValue($reportingMethod));

        $this->employeeSupervisorAPI->setEmployeeService($pimEmployeeService);
        $this->employeeSupervisorAPI->setReportingMethodConfigurationService($reportingMethodConfigService);
        $returned = $this->employeeSupervisorAPI->deleteEmployeeSupervisor();

       $mockedResponse =  new Response(array('success' => 'Successfully Deleted'));


        $this->assertEquals($mockedResponse, $returned);
    }



}