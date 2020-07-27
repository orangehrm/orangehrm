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

use Orangehrm\Rest\Api\Leave\Entity\LeaveRequest;
use Orangehrm\Rest\Api\User\Model\LeaveRequestModel;

/**
 * @group API
 */
class ApiLeaveRequestModelTest extends PHPUnit\Framework\TestCase
{
    public function testToArray()
    {
        $testArray = array(
            'id' => 1,
            'leaveType' => 'Annual',
            'fromDate' => '2020-01-01',
            'toDate' => '2020-12-31',
            'appliedDate' => '2020-06-01',
            'leaveBalance' => '10.00',
            'numberOfDays' => '5',
            'days' => [],
            'comments' => [],
            'leaveBreakdown' => 'Scheduled(0.50)',
        );

        $leaveRequestEntity = new LeaveRequest(1, 'Annual');
        $leaveRequestEntity->setComments([]);
        $leaveRequestEntity->setDays([]);
        $leaveRequestEntity->setAppliedDate('2020-06-01');
        $leaveRequestEntity->setFromDate('2020-01-01');
        $leaveRequestEntity->setToDate('2020-12-31');
        $leaveRequestEntity->setLeaveBalance('10.00');
        $leaveRequestEntity->setNumberOfDays('5');
        $leaveRequestEntity->setLeaveBreakdown('Scheduled(0.50)');

        $leaveRequestModel = new LeaveRequestModel($leaveRequestEntity);

        $this->assertEquals($testArray, $leaveRequestModel->toArray());
    }
}
