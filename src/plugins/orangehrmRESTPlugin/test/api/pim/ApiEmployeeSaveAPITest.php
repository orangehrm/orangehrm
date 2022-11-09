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
 *
 *
 * @group API
 */

use Orangehrm\Rest\Api\Pim\EmployeeSaveAPI;
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiEmployeeSaveAPITest extends PHPUnit_Framework_TestCase
{

    public function testSaveEmployee()
    {
        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Rodgers');
        $employee->setFirstName('Hayden');
//        $employee->setEmpNumber($empNumber);
        $employee->setEmployeeId($empNumber);
        $employee->setMiddleName('Phil');


        $filters = array();
        $filters[EmployeeSaveAPI::PARAMETER_EMPLOYEE_ID] = 1;
        $filters[EmployeeSaveAPI::PARAMETER_FIRST_NAME] = 'Hayden';
        $filters[EmployeeSaveAPI::PARAMETER_LAST_NAME] = 'Rodgers';
        $filters[EmployeeSaveAPI::PARAMETER_MIDDLE_NAME] = 'Phil';
//
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $employeeSaveApi = $this->getMock('Orangehrm\Rest\Api\Pim\EmployeeSaveAPI',array('filterParameters','buildEmployee'),array($request));
        $employeeSaveApi->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));
        $employeeSaveApi->expects($this->once())
            ->method('buildEmployee')
            ->with($filters)
            ->will($this->returnValue($employee));

        $pimEmployeeService = $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('saveEmployee')
            ->with($employee)
            ->will($this->returnValue($employee));

        $employeeSaveApi->setEmployeeService($pimEmployeeService);

        $returned = $employeeSaveApi->saveEmployee();
        $testResponse = new Response( array('success' => 'Successfully Saved','id' => '001'));

        $this->assertEquals($returned, $testResponse);

    }

}