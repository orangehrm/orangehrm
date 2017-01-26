<?php

/**
 * Test class of Api/EmployeeService
 *
 * @group API
 */


use Orangehrm\Rest\Api\Pim\EmployeeService;
use Orangehrm\Rest\http\SearchQuery;

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


        $searchParams = array(
            'empId' => '1',
        );

        $request = '';

        $searchQuery = $this->getMock('SearchQuery', array('getSearchParams'));
        $searchQuery->expects($this->once())
            ->method('getSearchParams')
            ->with($request)
            ->will($this->returnValue($searchParams));
        $this->employeeService->setSearchQuery($searchQuery);

        $employee = $this->employeeService->getEmployeeDetails($request);



    }

}