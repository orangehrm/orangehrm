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

    public function testGetEmployeeDependents()
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

        $empDependentTest = new \EmpDependent();
        $empDependentTest->setDateOfBirth('2015-05-14');
        $empDependentTest->setEmpNumber(1);
        $empDependentTest->setName('Shane Lewis');
        $empDependentTest->setRelationship('Son');
        $empDependentTest->setSeqno(1);
        $empDependentTest->setRelationshipType('other');

        $employeeDependentsList = new Doctrine_Collection('EmpDependent');
        $employeeDependentsList[] = $empDependentTest;

        $employeeCategory = new JobCategory();
        $employeeCategory->setName("Engineer");

        //   $employee->setContracts(array($employeeContract)) ;
        $employee->setJobCategory($employeeCategory);


        $this->employeeDependantAPI->setRequestParams($requestParams);

        $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployeeDependents')
            ->with($empNumber)
            ->will($this->returnValue($employeeDependentsList));
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with($empNumber)
            ->will($this->returnValue($employee));

        $this->employeeDependantAPI->setEmployeeService($pimEmployeeService);
        $returned = $this->employeeDependantAPI->getEmployeeDependents();

        // creating the employee json array
        $employeeDependant = new EmployeeDependent('Shane Lewis', 'Son', '2015-05-14',1);

        $jsonEmployeeDependantsArray = $employeeDependant->toArray();

        $assertResponse = new Response(array($jsonEmployeeDependantsArray), array());

        $this->assertEquals($assertResponse, $returned);

    }

//    public function testSaveEmployeeDependents(){
//
//        $empDependentTest = new \EmpDependent();
//        $empDependentTest->setDateOfBirth('2016-05-14');
//        $empDependentTest->setEmpNumber('1');
//        $empDependentTest->setName('Shane Lewis');
//        $empDependentTest->setRelationship('Son');
//        $empDependentTest->setRelationshipType('other');
//
//        $empDependentTest->setSeqno(1);
//
//        $filters = array();
//        $filters[EmployeeDependentAPI::PARAMETER_DOB] = '2016-05-14';
//        $filters[EmployeeDependentAPI::PARAMETER_NAME] = 'Shane Lewis';
//        $filters[EmployeeDependentAPI::PARAMETER_RELATIONSHIP] = 'Son';
//        $filters[EmployeeDependentAPI::PARAMETER_TYPE] = 'other';
//        $filters[EmployeeDependentAPI::PARAMETER_ID] = '1';
//        $filters[EmployeeDependentAPI::PARAMETER_SEQ_NUMBER] = '1';
//
//        $sfEvent   = new sfEventDispatcher();
//        $sfRequest = new sfWebRequest($sfEvent);
//        $request = new Request($sfRequest);
//
//
//        $employeeDependantAPI = $this->getMock('Orangehrm\Rest\Api\Pim\EmployeeDependentAPI',array('filterParameters','buildEmployeeDependants'),array($request));
//        $employeeDependantAPI->expects($this->once())
//            ->method('filterParameters')
//            ->will($this->returnValue($filters));
//
//        $employeeDependantAPI->expects($this->once())
//            ->method('buildEmployeeDependents')
//            ->with($filters)
//            ->will($this->returnValue($empDependentTest));
//
//        $pimEmployeeService = $this->getMock('EmployeeService',array('saveEmployeeDependent'));
//        $pimEmployeeService->expects($this->any())
//            ->method('saveEmployeeDependent')
//            ->with($empDependentTest)
//            ->will($this->returnValue($empDependentTest));
//
//        $employeeDependantAPI->setEmployeeService($pimEmployeeService);
//
//        $returned = $employeeDependantAPI->saveEmployeeDependents();
//
//        $testResponse = new Response(array('success' => 'successfully saved'));
//
//        $this->assertEquals($returned, $testResponse);
//    }

    public function testDeleteEmployeeDependents(){

        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);
        $employee->setEmployeeId($empNumber);
        $employee->setJoinedDate("2016-04-15");
        $employee->setEmpWorkEmail("mdriggs@hrm.com");
        $employee->setEmpMobile(0754343435);

        $empDependentTest = new \EmpDependent();
        $empDependentTest->setDateOfBirth('2016-05-14');
        $empDependentTest->setEmpNumber('1');
        $empDependentTest->setName('Shane Lewis');
        $empDependentTest->setRelationship('Son');
        $empDependentTest->setRelationshipType('other');

        $empDependentTest->setSeqno(1);

        $filters = array();

        $filters[EmployeeDependentAPI::PARAMETER_ID] = '1';
        $filters[EmployeeDependentAPI::PARAMETER_SEQ_NUMBER] = '1';

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);


        $employeeDependantAPI = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeDependentAPI')
            ->setMethods(array('filterParameters','buildEmployeeDependants'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $employeeDependantAPI->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));


        $pimEmployeeService = $this->getMockBuilder('EmployeeService')
            ->setMethods(array('deleteEmployeeDependents','getEmployee'))
            ->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('deleteEmployeeDependents')
            ->with(1,array(1))
            ->will($this->returnValue(1));
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with(1)
            ->will($this->returnValue($employee));

        $employeeDependantAPI->setEmployeeService($pimEmployeeService);

        $returned = $employeeDependantAPI->deleteEmployeeDependents();

        $testResponse = new Response(array('success' => 'Successfully Deleted'));

        $this->assertEquals($returned, $testResponse);
    }
}