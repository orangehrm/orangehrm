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

use Orangehrm\Rest\Api\Pim\EmployeeWorkExperienceAPI;
use Orangehrm\Rest\Api\Pim\Entity\WorkExperience;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiEmployeeWorkExperienceAPITest extends PHPUnit_Framework_TestCase
{
    private $employeeWorkExperienceAPI;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeWorkExperienceAPI = new EmployeeWorkExperienceAPI($request);
    }

    public function testGetWorkExperience(){

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

        $workExperience =  new \EmpWorkExperience();
        $workExperience->setSeqno(1);
        $workExperience->setComments("Comments");
        $workExperience->setEmployer("NSW");

        $workExperienceList[] = $workExperience;


        $this->employeeWorkExperienceAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('getEmployeeWorkExperienceRecords')
            ->with($empNumber)
            ->will($this->returnValue($workExperienceList));

        $this->employeeWorkExperienceAPI->setEmployeeService($pimEmployeeService);
        $response = $this->employeeWorkExperienceAPI->getEmployeeWorkExperience();

        // creating the exp json array
        $workExp = new WorkExperience();
        $workExp->build($workExperience);
        $workExpList[] = $workExp->toArray();

        $assertResponse = new Response($workExpList);
        $this->assertEquals($assertResponse, $response);

    }

    public function testSaveWorkExperience(){

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

        $workExperience =  new \EmpWorkExperience();
        $workExperience->setSeqno(1);
        $workExperience->setComments("Comments");
        $workExperience->setEmployer("NSW");

        $workExperienceList[] = $workExperience;

        $filters = array();
        $filters[EmployeeWorkExperienceAPI::PARAMETER_SEQ_ID] = 1;
        $filters[EmployeeWorkExperienceAPI::PARAMETER_ID] = 1;
        $filters[EmployeeWorkExperienceAPI::PARAMETER_COMMENT] = 'Test';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_COMPANY] = 'NSW';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_JOB_TITLE] = 'Captain';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_FROM_DATE] = '2014-05-06';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_TO_DATE] = '2016-07-04';

        $this->employeeWorkExperienceAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('saveEmployeeWorkExperience')
            ->with($workExperience)
            ->will($this->returnValue($workExperience));

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeWorkExperienceAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeWorkExperienceAPI')
            ->setMethods(array('getFilterParameters','buildEmployeeWorkExperience'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $this->employeeWorkExperienceAPI->expects($this->once())
            ->method('getFilterParameters')
            ->will($this->returnValue($filters));
        $this->employeeWorkExperienceAPI->expects($this->once())
            ->method('buildEmployeeWorkExperience')
            ->with($filters)
            ->will($this->returnValue($workExperience));

        $this->employeeWorkExperienceAPI->setEmployeeService($pimEmployeeService);
        $response = $this->employeeWorkExperienceAPI->saveEmployeeWorkExperience();

        // creating the exp json array
        $workExp = new WorkExperience();
        $workExp->build($workExperience);
        $assertResponse = new Response(array('success' => 'Successfully Saved', 'seqId' => 1 ));;

        $this->assertEquals($assertResponse, $response);

    }

    public function testUpdateWorkExperience(){

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

        $workExperience =  new \EmpWorkExperience();
        $workExperience->setSeqno(1);
        $workExperience->setComments("Comments");
        $workExperience->setEmployer("NSW");

        $workExperienceList[] = $workExperience;

        $filters = array();
        $filters[EmployeeWorkExperienceAPI::PARAMETER_SEQ_ID] = 1;
        $filters[EmployeeWorkExperienceAPI::PARAMETER_ID] = 1;
        $filters[EmployeeWorkExperienceAPI::PARAMETER_COMMENT] = 'Test';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_COMPANY] = 'NSW';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_JOB_TITLE] = 'Captain';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_FROM_DATE] = '2014-05-06';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_TO_DATE] = '2016-07-04';

        $this->employeeWorkExperienceAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('getEmployeeWorkExperienceRecords')
            ->with(1,1)
            ->will($this->returnValue($workExperience));

        $pimEmployeeService->expects($this->any())
            ->method('saveEmployeeWorkExperience')
            ->with($workExperience)
            ->will($this->returnValue($workExperience));

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeWorkExperienceAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeWorkExperienceAPI')
            ->setMethods(array('getFilterParameters','buildEmployeeWorkExperience'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $this->employeeWorkExperienceAPI->expects($this->once())
            ->method('getFilterParameters')
            ->will($this->returnValue($filters));
        $this->employeeWorkExperienceAPI->expects($this->once())
            ->method('buildEmployeeWorkExperience')
            ->with($filters,$workExperience)
            ->will($this->returnValue($workExperience));

        $this->employeeWorkExperienceAPI->setEmployeeService($pimEmployeeService);
        $response = $this->employeeWorkExperienceAPI->updateEmployeeWorkExperience();

        // creating the exp json array
        $workExp = new WorkExperience();
        $workExp->build($workExperience);
        $assertResponse = new Response(array('success' => 'Successfully Updated', 'seqId' => 1 ));;

        $this->assertEquals($assertResponse, $response);

    }

    public function testDeleteWorkExperience(){

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

        $workExperience =  new \EmpWorkExperience();
        $workExperience->setSeqno(1);
        $workExperience->setComments("Comments");
        $workExperience->setEmployer("NSW");

        $workExperienceList[] = $workExperience;

        $filters = array();
        $filters[EmployeeWorkExperienceAPI::PARAMETER_SEQ_ID] = 1;
        $filters[EmployeeWorkExperienceAPI::PARAMETER_ID] = 1;
        $filters[EmployeeWorkExperienceAPI::PARAMETER_COMMENT] = 'Test';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_COMPANY] = 'NSW';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_JOB_TITLE] = 'Captain';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_FROM_DATE] = '2014-05-06';
        $filters[EmployeeWorkExperienceAPI::PARAMETER_TO_DATE] = '2016-07-04';

        $this->employeeWorkExperienceAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('getEmployeeWorkExperienceRecords')
            ->with(1,1)
            ->will($this->returnValue($workExperience));

        $pimEmployeeService->expects($this->any())
            ->method('deleteEmployeeWorkExperienceRecords')
            ->with(1,array(1))
            ->will($this->returnValue(1));

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeWorkExperienceAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeWorkExperienceAPI')
            ->setMethods(array('getFilterParameters','buildEmployeeWorkExperience'))
            ->setConstructorArgs(array($request))
            ->getMock();

        $this->employeeWorkExperienceAPI->expects($this->once())
            ->method('getFilterParameters')
            ->will($this->returnValue($filters));

        $this->employeeWorkExperienceAPI->setEmployeeService($pimEmployeeService);
        $response = $this->employeeWorkExperienceAPI->deleteEmployeeWorkExperience();

        // creating the exp json array
        $workExp = new WorkExperience();
        $workExp->build($workExperience);
        $assertResponse = new Response(array('success' => 'Successfully Deleted' ));;

        $this->assertEquals($assertResponse, $response);

    }
}