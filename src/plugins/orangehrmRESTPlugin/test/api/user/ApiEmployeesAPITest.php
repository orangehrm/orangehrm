<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http=>//www.orangehrm.com
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

use Orangehrm\Rest\Api\User\EmployeesAPI;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

/**
 * @group API
 */
class ApiEmployeesAPITest extends PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
    }

    public function testGetEmployees()
    {
        $requestParams = $this->getMockBuilder('\Orangehrm\Rest\Http\RequestParams')
            ->disableOriginalConstructor()
            ->setMethods(['getQueryParam'])
            ->getMock();
        $requestParams->expects($this->exactly(3))
            ->method('getQueryParam')
            ->will(
                $this->returnCallback(
                    function ($param) {
                        if ($param === EmployeesAPI::PARAMETER_ACTION) {
                            return 'assign_leave';
                        } else if ($param === EmployeesAPI::PARAMETER_PROPERTIES) {
                            return ['firstName', 'lastName', 'termination_id', 'employeeId'];
                        } else if ($param === EmployeesAPI::PARAMETER_PAST_EMPLOYEE) {
                            return true;
                        }
                        return null;
                    }
                )
            );

        $sfEvent = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $accessibleEmployees = [
            [
                'firstName' => 'Test',
                'lastName' => 'Name',
                'termination_id' => 10,
                'employeeId' => '001'
            ]
        ];

        $employeesAPI = $this->getMockBuilder('Orangehrm\Rest\Api\User\EmployeesAPI')
            ->setMethods(['getAccessibleEmployees'])
            ->setConstructorArgs(array($request))
            ->getMock();
        $employeesAPI->expects($this->once())
            ->method('getAccessibleEmployees')
            ->will($this->returnValue($accessibleEmployees));
        $employeesAPI->setRequestParams($requestParams);

        $response = $employeesAPI->getEmployees();
        $success = new Response($accessibleEmployees);
        $this->assertEquals($success, $response);
    }
}
