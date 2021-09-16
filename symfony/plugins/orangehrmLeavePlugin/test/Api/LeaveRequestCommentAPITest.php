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

namespace OrangeHRM\Tests\Leave\Api;

use DateTime;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LeaveRequest;
use OrangeHRM\Entity\LeaveRequestComment;
use OrangeHRM\Entity\EmployeeTerminationRecord;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\LeaveRequestCommentAPI;
use OrangeHRM\Leave\Dao\LeaveRequestCommentDao;
use OrangeHRM\Leave\Service\LeaveRequestCommentService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Leave
 * @group APIv2
 */
class LeaveRequestCommentAPITest extends EndpointTestCase
{
    public function testGetLeaveRequestCommentService(): void
    {
        $api = new LeaveRequestCommentAPI($this->getRequest());
        $this->assertTrue($api->getLeaveRequestCommentService() instanceof LeaveRequestCommentService);
    }

    public function testCreate()
    {
        $leaveRequestCommentDao = $this->getMockBuilder(LeaveRequestCommentDao::class)
            ->onlyMethods(['saveLeaveRequestComment', 'getLeaveRequestById'])
            ->getMock();

        $leaveRequest = new LeaveRequest();
        $leaveRequest->setId(1);
        $dateTime = new DateTime('2020-12-25 07:20:21');
        $leaveRequest->setDateApplied($dateTime);

        $employeeTerminationRecord = new EmployeeTerminationRecord();
        $employeeTerminationRecord->setId(1);
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeTerminationRecord($employeeTerminationRecord);
        $leaveRequestComment = new LeaveRequestComment();
        $leaveRequestComment->setId(1);
        $leaveRequestComment->setLeaveRequest($leaveRequest);
        $leaveRequestComment->setComment('test comment');
        $dateTime = new DateTime('2020-12-25 07:20:21');
        $leaveRequestComment->setCreatedAt($dateTime);
        $leaveRequestComment->setCreatedByEmployee($employee);
        $leaveRequestComment->getDecorator()->setCreatedByUserById(1);

        $leaveRequestCommentDao->expects($this->once())
            ->method('saveLeaveRequestComment')
            ->willReturn($leaveRequestComment);

        $leaveRequestCommentDao->expects($this->once())
            ->method('getLeaveRequestById')
            ->with(1)
            ->willReturn($leaveRequest);

        $leaveRequestCommentService = $this->getMockBuilder(LeaveRequestCommentService::class)
            ->onlyMethods(['getLeaveRequestCommentDao'])
            ->getMock();

        $leaveRequestCommentService->expects($this->exactly(2))
            ->method('getLeaveRequestCommentDao')
            ->willReturn($leaveRequestCommentDao);

        /** @var MockObject&LeaveRequestCommentAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveRequestCommentAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveRequestCommentAPI::PARAMETER_LEAVE_REQUEST_ID => 1,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveRequestCommentAPI::PARAMETER_COMMENT => "test comment",
                ]
            ]
        )->onlyMethods(['getLeaveRequestCommentService', 'checkLeaveRequestAccessible', 'getAuthUser'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getLeaveRequestCommentService')
            ->will($this->returnValue($leaveRequestCommentService));

        $user = $this->getMockBuilder(User::class)
            ->onlyMethods(['getEmpNumber', 'getUserId'])
            ->disableOriginalConstructor()
            ->getMock();
        $user->expects($this->exactly(1))
            ->method('getEmpNumber')
            ->will($this->returnValue(1));
        $user->expects($this->exactly(1))
            ->method('getUserId')
            ->will($this->returnValue(1));


        $api->expects($this->exactly(2))
            ->method('getAuthUser')
            ->will($this->returnValue($user));

        $api->expects($this->exactly(1))
            ->method('checkLeaveRequestAccessible');

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
                Services::SESSION => new Session(),
                Services::AUTH_USER => User::getInstance(),
            ]
        );
        $result = $api->create();
        $this->assertEquals(
            [
                "id" => 1,
                'comment' => 'test comment',
                'leaveRequest' => [
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
            ],
            $result->normalize()
        );
    }

    public function testCreateLeaveRequestRecordNotFound()
    {
        $leaveRequestCommentDao = $this->getMockBuilder(LeaveRequestCommentDao::class)
            ->onlyMethods(['getLeaveRequestById'])
            ->getMock();

        $leaveRequestCommentDao->expects($this->once())
            ->method('getLeaveRequestById')
            ->with(1)
            ->willReturn(null);

        $leaveRequestCommentService = $this->getMockBuilder(LeaveRequestCommentService::class)
            ->onlyMethods(['getLeaveRequestCommentDao'])
            ->getMock();

        $leaveRequestCommentService->expects($this->exactly(1))
            ->method('getLeaveRequestCommentDao')
            ->willReturn($leaveRequestCommentDao);

        /** @var MockObject&LeaveRequestCommentAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveRequestCommentAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveRequestCommentAPI::PARAMETER_LEAVE_REQUEST_ID => 1,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveRequestCommentAPI::PARAMETER_COMMENT => "test comment",
                ]
            ]
        )->onlyMethods(['getLeaveRequestCommentService', 'checkLeaveRequestAccessible', 'getAuthUser'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getLeaveRequestCommentService')
            ->will($this->returnValue($leaveRequestCommentService));

        $this->expectRecordNotFoundException();
        $result = $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new LeaveRequestCommentAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    LeaveRequestCommentAPI::PARAMETER_LEAVE_REQUEST_ID => 1,
                    LeaveRequestCommentAPI::PARAMETER_COMMENT => 'test comment',
                ],
                $rules
            )
        );
    }


    public function testGetAll()
    {
        $leaveRequest = new LeaveRequest();
        $leaveRequest->setId(1);
        $dateTime = new DateTime('2020-12-25 07:20:21');
        $leaveRequest->setDateApplied($dateTime);

        $employeeTerminationRecord = new EmployeeTerminationRecord();
        $employeeTerminationRecord->setId(1);
        $employee = new Employee();
        $employee->setEmpNumber(1);
        $employee->setFirstName('Kayla');
        $employee->setLastName('Abbey');
        $employee->setEmployeeTerminationRecord($employeeTerminationRecord);

        $leaveRequestComment1 = new LeaveRequestComment();
        $leaveRequestComment1->setId(1);
        $leaveRequestComment1->getDecorator()->setLeaveRequestById(1);
        $leaveRequestComment1->setComment('test comment');
        $dateTime1 = new DateTime('2020-12-25 07:20:21');
        $leaveRequestComment1->setCreatedAt($dateTime1);
        $leaveRequestComment1->setCreatedByEmployee($employee);
        $leaveRequestComment1->getDecorator()->setCreatedByUserById(1);

        $leaveRequestComment2 = new LeaveRequestComment();
        $leaveRequestComment2->setId(2);
        $leaveRequestComment2->getDecorator()->setLeaveRequestById(1);
        $leaveRequestComment2->setComment('test comment2');
        $dateTime2 = new DateTime('2020-12-26 07:20:21');
        $leaveRequestComment2->setCreatedAt($dateTime2);
        $leaveRequestComment2->setCreatedByEmployee($employee);
        $leaveRequestComment2->getDecorator()->setCreatedByUserById(1);

        $leaveRequestCommentDao = $this->getMockBuilder(LeaveRequestCommentDao::class)
            ->onlyMethods(['searchLeaveRequestComments', 'getSearchLeaveRequestCommentsCount', 'getLeaveRequestById'])
            ->getMock();

        $leaveRequestCommentDao->expects($this->exactly(1))
            ->method('searchLeaveRequestComments')
            ->willReturn([$leaveRequestComment1, $leaveRequestComment2]);

        $leaveRequestCommentDao->expects($this->exactly(1))
            ->method('getSearchLeaveRequestCommentsCount')
            ->willReturn(2);

        $leaveRequestCommentDao->expects($this->exactly(1))
            ->method('getLeaveRequestById')
            ->willReturn($leaveRequest);

        $leaveRequestCommentService = $this->getMockBuilder(LeaveRequestCommentService::class)
            ->onlyMethods(['getLeaveRequestCommentDao'])
            ->getMock();

        $leaveRequestCommentService->expects($this->exactly(3))
            ->method('getLeaveRequestCommentDao')
            ->willReturn($leaveRequestCommentDao);


        /** @var MockObject&LeaveRequestCommentAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveRequestCommentAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveRequestCommentAPI::PARAMETER_LEAVE_REQUEST_ID => 1,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveRequestCommentAPI::PARAMETER_COMMENT => "test comment",
                ]
            ]
        )->onlyMethods(['getLeaveRequestCommentService', 'checkLeaveRequestAccessible'])
            ->getMock();
        $api->expects($this->exactly(3))
            ->method('getLeaveRequestCommentService')
            ->will($this->returnValue($leaveRequestCommentService));

        $api->expects($this->exactly(1))
            ->method('checkLeaveRequestAccessible');

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id" => 1,
                    'comment' => 'test comment',
                    'leaveRequest' => [
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
                ],
                [
                    "id" => 2,
                    'comment' => 'test comment2',
                    'leaveRequest' => [
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
                    'date' => '2020-12-26',
                    'time' => '07:20'
                ]
            ],
            $result->normalize()
        );
    }

    public function testGetAllLeaveRequestRecordNotFound()
    {
        $leaveRequestCommentDao = $this->getMockBuilder(LeaveRequestCommentDao::class)
            ->onlyMethods(['getLeaveRequestById'])
            ->getMock();

        $leaveRequestCommentDao->expects($this->once())
            ->method('getLeaveRequestById')
            ->with(1)
            ->willReturn(null);

        $leaveRequestCommentService = $this->getMockBuilder(LeaveRequestCommentService::class)
            ->onlyMethods(['getLeaveRequestCommentDao'])
            ->getMock();

        $leaveRequestCommentService->expects($this->exactly(1))
            ->method('getLeaveRequestCommentDao')
            ->willReturn($leaveRequestCommentDao);

        /** @var MockObject&LeaveRequestCommentAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveRequestCommentAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveRequestCommentAPI::PARAMETER_LEAVE_REQUEST_ID => 1,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveRequestCommentAPI::PARAMETER_COMMENT => "test comment",
                ]
            ]
        )->onlyMethods(['getLeaveRequestCommentService', 'checkLeaveRequestAccessible', 'getAuthUser'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getLeaveRequestCommentService')
            ->will($this->returnValue($leaveRequestCommentService));

        $this->expectRecordNotFoundException();
        $result = $api->getAll();
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new LeaveRequestCommentAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [
                    LeaveRequestCommentAPI::PARAMETER_LEAVE_REQUEST_ID => 1,
                ],
                $rules
            )
        );
    }

    public function testDelete(): void
    {
        $api = new LeaveRequestCommentAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new LeaveRequestCommentAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
