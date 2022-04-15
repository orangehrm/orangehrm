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
use Orangehrm\Rest\Api\Leave\Entity\LeaveRequest;
use Orangehrm\Rest\Api\Leave\Entity\Leave;

class ApiLeaveRequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * Set up method
     */
    protected function setUp()
    {

    }

    public function testToArray()
    {

        $leave = new Leave();
        $leave->setDate('2016-05-02');
        $leave->setLeaveType('Annual');
        $leave->setComments('');
        $leave->setDuration('8.0');
        $leave->setStatus('Pending');

        $testArray = array(

            'employeeName' => null,
            'employeeId' => null,
            'leaveBalance' => '8',
            'numberOfDays' => '3',
            'id' => '1',
            'fromDate' => '2016-05-04',
            'toDate' => '2016-05-06',
            'days' => $leave->toArray(),
            'type' => 'Annual',
            'comments' => ''
        );

        $leaveRequest = new LeaveRequest("1", 'Annual');

        $leaveRequest->setDate('2016-05-06');
        $leaveRequest->setLeaveBalance('8');
        $leaveRequest->setNumberOfDays('3');
        $leaveRequest->setStatus('Cancelled');
        $leaveRequest->setComments('');
        $leaveRequest->setFromDate('2016-05-04');
        $leaveRequest->setToDate('2016-05-06');
        $leaveRequest->setDays($leave->toArray());


        $this->assertEquals($testArray, $leaveRequest->toArray());

    }

}