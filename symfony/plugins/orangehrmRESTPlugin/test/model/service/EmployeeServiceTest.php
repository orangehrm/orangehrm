<?php

/**
 * Created by PhpStorm.
 * User: pola
 * Date: 1/24/17
 * Time: 9:31 AM
 */

/**
 * Test class of Api/EmployeeService
 *
 *
 */
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Api\Pim\EmployeeService;

class EmployeeServiceTest extends PHPUnit_Framework_TestCase
{
    private $employeeService;


    /**
     * Set up method
     */
    protected function setUp() {

        $this->employeeService	= new EmployeeService();
    }


    public function testGetEmployeeResponse()
    {

      //  $httpRequest = new Request();

        $empParams = array();
        $empParams['search'] = "empId==1;age<25";;

        $httpRequest = $this->getMock('request');
        $httpRequest->expects($this->once())
            ->method('getEmployeeSearchParams')
            ->will($this->returnValue($empParams));


        $returnParams = $this->employeeService->getEmployeeResponse($httpRequest);

        $this->assertEquals("empId==1;age<25", $returnParams['search']);
    }

}