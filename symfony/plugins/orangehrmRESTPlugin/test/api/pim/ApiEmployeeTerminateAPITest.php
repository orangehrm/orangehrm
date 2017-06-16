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

use Orangehrm\Rest\Api\Pim\EmployeeTerminateAPI;
use Orangehrm\Rest\Api\Pim\Entity\Employee;
use Orangehrm\Rest\Http\Request;
use Orangehrm\Rest\Http\Response;

class ApiEmployeeTerminateAPITest extends PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);
        $this->employeeTerminateAPi = new EmployeeTerminateAPI($request);
    }

    public function testTerminateEmployee()
    {
        $empNumber = 1;
        $employee = new \Employee();
        $employee->setLastName('Last Name');
        $employee->setFirstName('First Name');
        $employee->setEmpNumber($empNumber);
        $employee->setEmployeeId($empNumber);
        $employee->setJoinedDate("2016-04-15");
        $employee->setEmpWorkEmail("mdriggs@hrm.com");
        $employee->setEmpMobile(0754343435);

        $employeeTerminationRecord = new \EmployeeTerminationRecord();
        $employeeTerminationRecord->setDate('2016-04-15');
        $employeeTerminationRecord->setReasonId(1);
        $employeeTerminationRecord->setEmpNumber(1);
        $employeeTerminationRecord->setNote('test');

        $filters = array();
        $filters[EmployeeTerminateAPI::PARAMETER_ID] = '1';
        $filters[EmployeeTerminateAPI::PARAMETER_REASON] = '1';
        $filters[EmployeeTerminateAPI::PARAMETER_TERMINATION_DATE] = '2016-04-15';
        $filters[EmployeeTerminateAPI::PARAMETER_NOTE] = 'test';

        $sfEvent   = new sfEventDispatcher();
        $sfRequest = new sfWebRequest($sfEvent);
        $request = new Request($sfRequest);

        $this->employeeTerminateAPi = $this->getMockBuilder('Orangehrm\Rest\Api\Pim\EmployeeTerminateAPI')
            ->setMethods(array('filterParameters','validateInputs','buildTerminationRecord'))
            ->setConstructorArgs(array($request))
            ->getMock();
        $this->employeeTerminateAPi->expects($this->once())
            ->method('filterParameters')
            ->will($this->returnValue($filters));

        $this->employeeTerminateAPi->expects($this->once())
            ->method('validateInputs')
            ->with($filters)
            ->will($this->returnValue($filters));

        $this->employeeTerminateAPi->expects($this->once())
            ->method('buildTerminationRecord')
            ->with($filters)
            ->will($this->returnValue($employeeTerminationRecord));

        $pimEmployeeService = $pimEmployeeService = $this->getMockBuilder('EmployeeService')->getMock();
        $pimEmployeeService->expects($this->any())
            ->method('getEmployee')
            ->with(1)
            ->will($this->returnValue($employee));

        $pimEmployeeService->expects($this->any())
            ->method('terminateEmployment')
            ->with($employeeTerminationRecord)
            ->will($this->returnValue($employeeTerminationRecord));

        $this->employeeTerminateAPi->setEmployeeService($pimEmployeeService);

        $returned = $this->employeeTerminateAPi->terminateEmployee();
        $testResponse = new Response( array('success' => 'Successfully Terminated'));

        $this->assertEquals($returned, $testResponse);

    }

}