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

namespace OrangeHRM\Tests\Leave\Api\Model;

use DateTime;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Entity\LeaveComment;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\Model\LeaveCommentModel;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Leave
 * @group Model
 */
class LeaveCommentModelTest extends KernelTestCase
{
    public function testToArray()
    {
        $resultArray = [
            "id" => 1,
            'comment' => 'test comment',
            'leave' => [
                'id' => 1
            ],
            'createdByEmployee' => [
                'empNumber' => 1,
                'lastName' => 'Abbey',
                'firstName' => 'Kayla',
                'middleName' => '',
                'employeeId' => null,
                'employeeTerminationRecord' => [
                    'terminationId' => 1
                ]
            ],
            'date' => '2020-12-25',
            'time' => '07:20'
        ];

        $employeeTerminationRecord = new EmployeeTerminationRecord();
        $employeeTerminationRecord->setId(1);
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeTerminationRecord($employeeTerminationRecord);

        $leaveComment = new LeaveComment();
        $leaveComment->setId(1);
        $leaveComment->getDecorator()->setLeaveById(1);
        $leaveComment->setComment('test comment');
        $dateTime = new DateTime('2020-12-25 07:20:21');
        $leaveComment->setCreatedAt($dateTime);
        $leaveComment->setCreatedByEmployee($employee);
        $leaveComment->getDecorator()->setCreatedByUserById(1);

        $leaveCommentModel = new LeaveCommentModel($leaveComment);

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $this->assertEquals($resultArray, $leaveCommentModel->toArray());
    }
}
