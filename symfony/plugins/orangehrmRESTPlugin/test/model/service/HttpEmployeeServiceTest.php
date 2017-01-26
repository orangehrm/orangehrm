<?php

/**
 * Test class of Api/EmployeeService
 *
 * @group API
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

        $requestParams = $this->getMock('\Orangehrm\Rest\Http\RequestParams', ['getQueryParam']);
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
        $mockDao = $this->getMock('EmployeeDao');
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

        $requestParams = $this->getMock('\Orangehrm\Rest\Http\RequestParams', ['getQueryParam']);
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
        $mockDao = $this->getMock('EmployeeDao');
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