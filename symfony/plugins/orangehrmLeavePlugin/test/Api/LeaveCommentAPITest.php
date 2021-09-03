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
use OrangeHRM\Entity\Leave;
use OrangeHRM\Entity\LeaveComment;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\LeaveCommentAPI;
use OrangeHRM\Leave\Dao\LeaveCommentDao;
use OrangeHRM\Leave\Service\LeaveCommentService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Leave
 * @group APIv2
 */
class LeaveCommentAPITest extends EndpointTestCase
{
    public function testGetLeaveCommentService(): void
    {
        $api = new LeaveCommentAPI($this->getRequest());
        $this->assertTrue($api->getLeaveCommentService() instanceof LeaveCommentService);
    }

    public function testCreate()
    {
        $leaveCommentDao = $this->getMockBuilder(LeaveCommentDao::class)
            ->onlyMethods(['saveLeaveComment', 'getLeaveById'])
            ->getMock();

        $leave = new Leave();
        $leave->setId(1);
        $leave->setComment('test comment');

        $leaveComment = new LeaveComment();
        $leaveComment->setId(1);
        $leaveComment->setLeave($leave);
        $leaveComment->setComment('test comment');
        $dateTime = new DateTime('2020-12-25 07:20:21');
        $leaveComment->setCreatedAt($dateTime);
        $leaveComment->getDecorator()->setCreatedByEmployeeByEmpNumber(1);
        $leaveComment->getDecorator()->setCreatedByUserById(1);

        $leaveCommentDao->expects($this->once())
            ->method('saveLeaveComment')
            ->willReturn($leaveComment);

        $leaveCommentDao->expects($this->once())
            ->method('getLeaveById')
            ->with(1)
            ->willReturn($leave);

        $leaveCommentService = $this->getMockBuilder(LeaveCommentService::class)
            ->onlyMethods(['getLeaveCommentDao'])
            ->getMock();

        $leaveCommentService->expects($this->exactly(2))
            ->method('getLeaveCommentDao')
            ->willReturn($leaveCommentDao);

        /** @var MockObject&LeaveCommentAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveCommentAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommentAPI::PARAMETER_LEAVE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveCommentAPI::PARAMETER_COMMENT => "test comment",
                ]
            ]
        )->onlyMethods(['getLeaveCommentService', 'checkLeaveAccessible', 'getAuthUser'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getLeaveCommentService')
            ->will($this->returnValue($leaveCommentService));

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
            ->method('checkLeaveAccessible');

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
                'leave' => [
                    'id' => 1
                ],
                'createdByEmployee' => [
                    'empNumber' => 1
                ],
                'dateCreated' => '2020-12-25',
                'timeCreated' => '07:20'
            ],
            $result->normalize()
        );
    }

    public function testCreateLeaveRecordNotFound()
    {
        $leaveCommentDao = $this->getMockBuilder(LeaveCommentDao::class)
            ->onlyMethods(['getLeaveById'])
            ->getMock();

        $leaveCommentDao->expects($this->once())
            ->method('getLeaveById')
            ->with(1)
            ->willReturn(null);

        $leaveCommentService = $this->getMockBuilder(LeaveCommentService::class)
            ->onlyMethods(['getLeaveCommentDao'])
            ->getMock();

        $leaveCommentService->expects($this->exactly(1))
            ->method('getLeaveCommentDao')
            ->willReturn($leaveCommentDao);

        /** @var MockObject&LeaveCommentAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveCommentAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommentAPI::PARAMETER_LEAVE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveCommentAPI::PARAMETER_COMMENT => "test comment",
                ]
            ]
        )->onlyMethods(['getLeaveCommentService', 'checkLeaveAccessible', 'getAuthUser'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getLeaveCommentService')
            ->will($this->returnValue($leaveCommentService));

        $this->expectRecordNotFoundException();
        $result = $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new LeaveCommentAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    LeaveCommentAPI::PARAMETER_LEAVE_ID => 1,
                    LeaveCommentAPI::PARAMETER_COMMENT => 'test comment',
                ],
                $rules
            )
        );
    }


    public function testGetAll()
    {
        $leave = new Leave();
        $leave->setId(1);
        $leave->setComment('test comment');

        $leaveComment1 = new LeaveComment();
        $leaveComment1->setId(1);
        $leaveComment1->getDecorator()->setLeaveById(1);
        $leaveComment1->setComment('test comment');
        $dateTime1 = new DateTime('2020-12-25 07:20:21');
        $leaveComment1->setCreatedAt($dateTime1);
        $leaveComment1->getDecorator()->setCreatedByEmployeeByEmpNumber(1);
        $leaveComment1->getDecorator()->setCreatedByUserById(1);

        $leaveComment2 = new LeaveComment();
        $leaveComment2->setId(2);
        $leaveComment2->getDecorator()->setLeaveById(1);
        $leaveComment2->setComment('test comment2');
        $dateTime2 = new DateTime('2020-12-26 07:20:21');
        $leaveComment2->setCreatedAt($dateTime2);
        $leaveComment2->getDecorator()->setCreatedByEmployeeByEmpNumber(1);
        $leaveComment2->getDecorator()->setCreatedByUserById(1);

        $leaveCommentDao = $this->getMockBuilder(LeaveCommentDao::class)
            ->onlyMethods(['searchLeaveComments', 'getSearchLeaveCommentsCount', 'getLeaveById'])
            ->getMock();

        $leaveCommentDao->expects($this->exactly(1))
            ->method('searchLeaveComments')
            ->willReturn([$leaveComment1, $leaveComment2]);

        $leaveCommentDao->expects($this->exactly(1))
            ->method('getSearchLeaveCommentsCount')
            ->willReturn(2);

        $leaveCommentDao->expects($this->exactly(1))
            ->method('getLeaveById')
            ->willReturn($leave);

        $leaveCommentService = $this->getMockBuilder(LeaveCommentService::class)
            ->onlyMethods(['getLeaveCommentDao'])
            ->getMock();

        $leaveCommentService->expects($this->exactly(3))
            ->method('getLeaveCommentDao')
            ->willReturn($leaveCommentDao);


        /** @var MockObject&LeaveCommentAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveCommentAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommentAPI::PARAMETER_LEAVE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveCommentAPI::PARAMETER_COMMENT => "test comment",
                ]
            ]
        )->onlyMethods(['getLeaveCommentService', 'checkLeaveAccessible'])
            ->getMock();
        $api->expects($this->exactly(3))
            ->method('getLeaveCommentService')
            ->will($this->returnValue($leaveCommentService));

        $api->expects($this->exactly(1))
            ->method('checkLeaveAccessible');

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
                    'leave' => [
                        'id' => 1
                    ],
                    'createdByEmployee' => [
                        'empNumber' => 1
                    ],
                    'dateCreated' => '2020-12-25',
                    'timeCreated' => '07:20'
                ],
                [
                    "id" => 2,
                    'comment' => 'test comment2',
                    'leave' => [
                        'id' => 1
                    ],
                    'createdByEmployee' => [
                        'empNumber' => 1
                    ],
                    'dateCreated' => '2020-12-26',
                    'timeCreated' => '07:20'
                ]
            ],
            $result->normalize()
        );
    }

    public function testGetAllLeaveRecordNotFound()
    {
        $leaveCommentDao = $this->getMockBuilder(LeaveCommentDao::class)
            ->onlyMethods(['getLeaveById'])
            ->getMock();

        $leaveCommentDao->expects($this->once())
            ->method('getLeaveById')
            ->with(1)
            ->willReturn(null);

        $leaveCommentService = $this->getMockBuilder(LeaveCommentService::class)
            ->onlyMethods(['getLeaveCommentDao'])
            ->getMock();

        $leaveCommentService->expects($this->exactly(1))
            ->method('getLeaveCommentDao')
            ->willReturn($leaveCommentDao);

        /** @var MockObject&LeaveCommentAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveCommentAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    LeaveCommentAPI::PARAMETER_LEAVE_ID => 1,
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveCommentAPI::PARAMETER_COMMENT => "test comment",
                ]
            ]
        )->onlyMethods(['getLeaveCommentService', 'checkLeaveAccessible', 'getAuthUser'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getLeaveCommentService')
            ->will($this->returnValue($leaveCommentService));

        $this->expectRecordNotFoundException();
        $result = $api->getAll();
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new LeaveCommentAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [
                    LeaveCommentAPI::PARAMETER_LEAVE_ID => 1,
                ],
                $rules
            )
        );
    }

    public function testDelete(): void
    {
        $api = new LeaveCommentAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new LeaveCommentAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
