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

use Orangehrm\Rest\Api\Pim\EmployeeEducationAPI;
use Orangehrm\Rest\Api\Pim\Entity\Education;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiEmployeeEducationAPITest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EmployeeEducationAPI
     */
    private $employeeEducationAPI;


    /**
     * Set up method
     */
    protected function setUp()
    {
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeEducationAPI = new EmployeeEducationAPI($request);
    }

    public function testGetEducation(){

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

        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setId(1);
        $employeeEducation->setEducationId(1);
        $employeeEducation->setEmpNumber(1);
        $employeeEducation->setInstitute('UOM');
        $employeeEducation->setMajor("BSC");
        $employeeEducation->setScore('3.4');

        $employeeEducationList[] = $employeeEducation;

        $this->employeeEducationAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('getEmployeeEducations')
            ->with($empNumber)
            ->will($this->returnValue($employeeEducationList));

        $this->employeeEducationAPI->setEmployeeService($pimEmployeeService);
        $response = $this->employeeEducationAPI->getEmployeeEducation();

        // creating the education json array
        $education = new Education();
        $education->build($employeeEducation);
        $educationList[] = $education->toArray();

        $assertResponse = new Response($educationList);
        $this->assertEquals($assertResponse, $response);

    }

    public function testSaveEducation(){

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

        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setId(1);
        $employeeEducation->setEducationId(1);
        $employeeEducation->setEmpNumber(1);
        $employeeEducation->setInstitute('UOM');
        $employeeEducation->setMajor("BSC");
        $employeeEducation->setScore('3.4');

        $employeeEducationList[] = $employeeEducation;

        $filters = array();
        $filters[EmployeeEducationAPI::PARAMETER_SEQ_ID] = 1;
        $filters[EmployeeEducationAPI::PARAMETER_ID] = 1;
        $filters[EmployeeEducationAPI::PARAMETER_TO_DATE] = '2014-12-22';
        $filters[EmployeeEducationAPI::PARAMETER_FROM_DATE] = '2009-07-22';
        $filters[EmployeeEducationAPI::PARAMETER_GPA] = '3.4';
        $filters[EmployeeEducationAPI::PARAMETER_INSTITUTE] = 'UOM';
        $filters[EmployeeEducationAPI::PARAMETER_SPECIALIZATION] = 'BSC';
        $filters[EmployeeEducationAPI::PARAMETER_LEVEL] = '1';

        $this->employeeEducationAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('saveEmployeeEducation')
            ->with($employeeEducation)
            ->will($this->returnValue($employeeEducation));


        $education = new \Education();
        $education->setId(1);

        $pimEducationServiceService = $this->getMockBuilder('EducationService')->getMock();
        $pimEducationServiceService->expects($this->any())
            ->method('getEducationById')
            ->with('1')
            ->will($this->returnValue($education));

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeEducationAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeEducationAPI')
                ->setMethods(array('getFilterParameters','buildEmployeeEducation'))
                ->setConstructorArgs(array($request))
                ->getMock();

        $this->employeeEducationAPI->expects($this->once())
            ->method('getFilterParameters')
            ->will($this->returnValue($filters));
        $this->employeeEducationAPI->expects($this->once())
            ->method('buildEmployeeEducation')
            ->with($filters)
            ->will($this->returnValue($employeeEducation));

        $this->employeeEducationAPI->setEmployeeService($pimEmployeeService);
        $this->employeeEducationAPI->setEducationService($pimEducationServiceService);
        $response = $this->employeeEducationAPI->saveEmployeeEducation();

        $assertResponse = new Response(array('success' => 'Successfully Saved', 'seqId' => 1 ));;

        $this->assertEquals($assertResponse, $response);

    }
    public function testUpdateEducation(){

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

        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setId(1);
        $employeeEducation->setEducationId(1);
        $employeeEducation->setEmpNumber(1);
        $employeeEducation->setInstitute('UOM');
        $employeeEducation->setMajor("BSC");
        $employeeEducation->setScore('3.4');

        $employeeEducationList[] = $employeeEducation;

        $filters = array();
        $filters[EmployeeEducationAPI::PARAMETER_SEQ_ID] = 1;
        $filters[EmployeeEducationAPI::PARAMETER_ID] = 1;
        $filters[EmployeeEducationAPI::PARAMETER_TO_DATE] = '2014-12-22';
        $filters[EmployeeEducationAPI::PARAMETER_FROM_DATE] = '2010-07-22';
        $filters[EmployeeEducationAPI::PARAMETER_GPA] = '3.4';
        $filters[EmployeeEducationAPI::PARAMETER_INSTITUTE] = 'UOM';
        $filters[EmployeeEducationAPI::PARAMETER_SPECIALIZATION] = 'BSC';
        $filters[EmployeeEducationAPI::PARAMETER_LEVEL] = '1';

        $this->employeeEducationAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('saveEmployeeEducation')
            ->with($employeeEducation)
            ->will($this->returnValue($employeeEducation));

        $pimEmployeeService->expects($this->once())
            ->method('getEducation')
            ->will($this->returnValue($employeeEducation));

        $pimEmployeeService->expects($this->any())
            ->method('getEmployeeEducations')
            ->with('1')
            ->will($this->returnValue($employeeEducationList));


        $education = new \Education();
        $education->setId(1);

        $pimEducationServiceService = $this->getMockBuilder('EducationService')->getMock();
        $pimEducationServiceService->expects($this->any())
            ->method('getEducationById')
            ->with('1')
            ->will($this->returnValue($education));


        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeEducationAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeEducationAPI')
            ->setMethods(array('getFilterParameters','buildEmployeeEducation'))
            ->setConstructorArgs(array($request))
            ->getMock();

        $this->employeeEducationAPI->expects($this->once())
            ->method('getFilterParameters')
            ->will($this->returnValue($filters));
        $this->employeeEducationAPI->expects($this->once())
            ->method('buildEmployeeEducation')
            ->with($filters)
            ->will($this->returnValue($employeeEducation));

        $this->employeeEducationAPI->setEmployeeService($pimEmployeeService);
        $this->employeeEducationAPI->setEducationService($pimEducationServiceService);
        $response = $this->employeeEducationAPI->updateEmployeeEducation();

        $assertResponse = new Response(array('success' => 'Successfully Updated', 'seqId' => 1 ));;

        $this->assertEquals($assertResponse, $response);

    }
    public function testDeleteEducation(){

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

        $employeeEducation = new EmployeeEducation();
        $employeeEducation->setId(1);
        $employeeEducation->setEducationId(1);
        $employeeEducation->setEmpNumber(1);
        $employeeEducation->setInstitute('UOM');
        $employeeEducation->setMajor("BSC");
        $employeeEducation->setScore('3.4');

        $employeeEducationList[] = $employeeEducation;

        $filters = array();
        $filters[EmployeeEducationAPI::PARAMETER_SEQ_ID] = 1;
        $filters[EmployeeEducationAPI::PARAMETER_ID] = 1;
        $filters[EmployeeEducationAPI::PARAMETER_TO_DATE] = '2014-12-22';
        $filters[EmployeeEducationAPI::PARAMETER_FROM_DATE] = '2010-07-22';
        $filters[EmployeeEducationAPI::PARAMETER_GPA] = '3.4';
        $filters[EmployeeEducationAPI::PARAMETER_INSTITUTE] = 'UOM';
        $filters[EmployeeEducationAPI::PARAMETER_SPECIALIZATION] = 'BSC';
        $filters[EmployeeEducationAPI::PARAMETER_LEVEL] = '1';

        $this->employeeEducationAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('deleteEmployeeEducationRecords')
            ->with('1')
            ->will($this->returnValue(1));

        $pimEmployeeService->expects($this->any())
            ->method('getEmployeeEducations')
            ->with('1')
            ->will($this->returnValue($employeeEducationList));


        $education = new \Education();
        $education->setId(1);

        $pimEducationServiceService = $this->getMockBuilder('EducationService')->getMock();
        $pimEducationServiceService->expects($this->any())
            ->method('getEducationById')
            ->with('1')
            ->will($this->returnValue($education));


        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeEducationAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeEducationAPI')
            ->setMethods(array('getFilterParameters','buildEmployeeEducation'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $this->employeeEducationAPI->expects($this->once())
            ->method('getFilterParameters')
            ->will($this->returnValue($filters));

        $this->employeeEducationAPI->setEmployeeService($pimEmployeeService);
        $this->employeeEducationAPI->setEducationService($pimEducationServiceService);
        $response = $this->employeeEducationAPI->deleteEmployeeEducation();

        $assertResponse = new Response(array('success' => 'Successfully Deleted'));;

        $this->assertEquals($assertResponse, $response);

    }

}