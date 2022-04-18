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

use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\LeaveType;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\LeaveTypeAPI;
use OrangeHRM\Leave\Dao\LeaveTypeDao;
use OrangeHRM\Leave\Service\LeaveTypeService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group APIv2
 */
class LeaveTypeAPITest extends EndpointTestCase
{
    protected function loadFixtures(): void
    {
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmLeavePlugin/test/fixtures/LeaveType.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetOne(): void
    {
        $empNumber = 1;
        $leaveTypeDao = $this->getMockBuilder(LeaveTypeDao::class)
            ->onlyMethods(['getLeaveTypeById'])
            ->getMock();

        $leaveType = new LeaveType();
        $leaveType->setId(1);
        $leaveType->setName('Casual');
        $leaveType->setDeleted(false);
        $leaveType->setOperationalCountry(null);
        $leaveType->setSituational(false);

        $leaveTypeDao->expects($this->exactly(1))
            ->method('getLeaveTypeById')
            ->with(1)
            ->will($this->returnValue($leaveType));

        $leaveTypeService = $this->getMockBuilder(LeaveTypeService::class)
            ->onlyMethods(['getLeaveTypeDao'])
            ->getMock();

        $leaveTypeService->expects($this->exactly(1))
            ->method('getLeaveTypeDao')
            ->willReturn($leaveTypeDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $leaveTypeService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&LeaveTypeAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveTypeAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => $empNumber,
                    CommonParams::PARAMETER_ID => 1
                ]
            ]
        )->onlyMethods(['getLeaveTypeService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getLeaveTypeService')
            ->will($this->returnValue($leaveTypeService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "id"=> 1,
                "name"=> "Casual",
                "deleted"=> false,
                "situational"=> false
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new LeaveTypeAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );
    }

    public function testUpdate()
    {
        $leaveTypeDao = $this->getMockBuilder(LeaveTypeDao::class)
            ->onlyMethods(['saveLeaveType', 'getLeaveTypeById'])
            ->getMock();

        $leaveType = new LeaveType();
        $leaveType->setId(1);
        $leaveType->setName('Casual New');
        $leaveType->setDeleted(false);
        $leaveType->setOperationalCountry(null);
        $leaveType->setSituational(false);

        $leaveTypeDao->expects($this->exactly(1))
            ->method('getLeaveTypeById')
            ->with(1)
            ->willReturn($leaveType);

        $leaveTypeDao->expects($this->exactly(1))
            ->method('saveLeaveType')
            ->will(
                $this->returnCallback(
                    function (LeaveType $leaveType) {
                        return $leaveType;
                    }
                )
            );

        $leaveTypeService = $this->getMockBuilder(LeaveTypeService::class)
            ->onlyMethods(['getLeaveTypeDao'])
            ->getMock();

        $leaveTypeService->expects($this->exactly(2))
            ->method('getLeaveTypeDao')
            ->willReturn($leaveTypeDao);

        $this->createKernelWithMockServices(
            [
                Services::EMPLOYEE_SERVICE => $leaveTypeService,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        /** @var MockObject&LeaveTypeAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveTypeAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 1
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveTypeAPI::PARAMETER_NAME => 'Casual New',
                    LeaveTypeAPI::PARAMETER_SITUATIONAL => false,
                ]
            ]
        )->onlyMethods(['getLeaveTypeService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getLeaveTypeService')
            ->will($this->returnValue($leaveTypeService));

        $result = $api->update();
        $this->assertEquals(
            [
                "id"=> 1,
                "name"=> "Casual New",
                "deleted"=> false,
                "situational"=> false,
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new LeaveTypeAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 1,
                    LeaveTypeAPI::PARAMETER_NAME => 'Casual New',
                    LeaveTypeAPI::PARAMETER_SITUATIONAL => true,
                ],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $leaveTypeDao = $this->getMockBuilder(LeaveTypeDao::class)
            ->onlyMethods(['deleteLeaveType'])
            ->getMock();

        $leaveTypeDao->expects($this->exactly(1))
            ->method('deleteLeaveType')
            ->with([1])
            ->willReturn(1);

        $leaveTypeService = $this->getMockBuilder(LeaveTypeService::class)
            ->onlyMethods(['getLeaveTypeDao'])
            ->getMock();

        $leaveTypeService->expects($this->exactly(1))
            ->method('getLeaveTypeDao')
            ->willReturn($leaveTypeDao);

        /** @var MockObject&LeaveTypeAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveTypeAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    CommonParams::PARAMETER_IDS => [1],
                ]
            ]
        )->onlyMethods(['getLeaveTypeService'])
            ->getMock();
        $api->expects($this->exactly(1))
            ->method('getLeaveTypeService')
            ->will($this->returnValue($leaveTypeService));

        $result = $api->delete();
        $this->assertEquals(
            [
                1
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new LeaveTypeAPI($this->getRequest());
        $rules = $api->getValidationRuleForDelete();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_IDS => [1],
                ],
                $rules
            )
        );
    }

    public function testCreate()
    {
        $this->loadFixtures();

        $leaveTypeDao = $this->getMockBuilder(LeaveTypeDao::class)
            ->onlyMethods(['saveLeaveType', 'getLeaveTypeById'])
            ->getMock();

        $leaveType = new LeaveType();
        $leaveType->setId(1);
        $leaveType->setName('Casual');
        $leaveType->setDeleted(false);
        $leaveType->setOperationalCountry(null);
        $leaveType->setSituational(false);

        $leaveTypeDao->expects($this->never())
            ->method('getLeaveTypeById')
            ->with(1)
            ->willReturn($leaveType);

        $leaveTypeDao->expects($this->once())
            ->method('saveLeaveType')
            ->will(
                $this->returnCallback(
                    function (LeaveType $leaveType) {
                        $leaveType->setId(1);
                        return $leaveType;
                    }
                )
            );

        $leaveTypeService = $this->getMockBuilder(LeaveTypeService::class)
            ->onlyMethods(['getLeaveTypeDao'])
            ->getMock();

        $leaveTypeService->expects($this->once())
            ->method('getLeaveTypeDao')
            ->willReturn($leaveTypeDao);

        /** @var MockObject&LeaveTypeAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveTypeAPI::class,
            [
                RequestParams::PARAM_TYPE_BODY => [
                    LeaveTypeAPI::PARAMETER_NAME => 'Casual',
                    LeaveTypeAPI::PARAMETER_SITUATIONAL => false,
                ]
            ]
        )->onlyMethods(['getLeaveTypeService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getLeaveTypeService')
            ->will($this->returnValue($leaveTypeService));

        $result = $api->create();
        $this->assertEquals(
            [
                "id"=> 1,
                "name"=> "Casual",
                "deleted"=> false,
                "situational"=> false
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new LeaveTypeAPI($this->getRequest());
        $rules = $api->getValidationRuleForCreate();
        $this->assertTrue(
            $this->validate(
                [
                    LeaveTypeAPI::PARAMETER_NAME => 'Casual New',
                    LeaveTypeAPI::PARAMETER_SITUATIONAL => true,
                ],
                $rules
            )
        );
    }


    public function testGetAll()
    {
        $empNumber = 1;
        $leaveTypeDao = $this->getMockBuilder(LeaveTypeDao::class)
            ->onlyMethods(['searchLeaveType', 'getSearchLeaveTypesCount'])
            ->getMock();


        $leaveType1 = new LeaveType();
        $leaveType1->setId(1);
        $leaveType1->setName('Casual');
        $leaveType1->setDeleted(false);
        $leaveType1->setOperationalCountry(null);
        $leaveType1->setSituational(false);

        $leaveType2 = new LeaveType();
        $leaveType2->setId(2);
        $leaveType2->setName('Medical');
        $leaveType2->setDeleted(false);
        $leaveType2->setOperationalCountry(null);
        $leaveType2->setSituational(false);

        $leaveTypeDao->expects($this->exactly(1))
            ->method('searchLeaveType')
            ->willReturn([$leaveType1, $leaveType2]);

        $leaveTypeDao->expects($this->exactly(1))
            ->method('getSearchLeaveTypesCount')
            ->willReturn(2);

        $leaveTypeService = $this->getMockBuilder(LeaveTypeService::class)
            ->onlyMethods(['getLeaveTypeDao'])
            ->getMock();

        $leaveTypeService->expects($this->exactly(2))
            ->method('getLeaveTypeDao')
            ->willReturn($leaveTypeDao);

        /** @var MockObject&LeaveTypeAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LeaveTypeAPI::class,
            []
        )->onlyMethods(['getLeaveTypeService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getLeaveTypeService')
            ->will($this->returnValue($leaveTypeService));

        $result = $api->getAll();
        $this->assertEquals(
            [
                [
                    "id"=> 1,
                    "name"=> "Casual",
                    "deleted"=> false,
                    "situational"=> false,
                ],
                [
                    "id"=> 2,
                    "name"=> "Medical",
                    "deleted"=> false,
                    "situational"=> false,
                ]
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetAll(): void
    {
        $api = new LeaveTypeAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetAll();
        $this->assertTrue(
            $this->validate(
                [
                    LeaveTypeAPI::FILTER_NAME => 'Test',
                ],
                $rules
            )
        );
    }
}
