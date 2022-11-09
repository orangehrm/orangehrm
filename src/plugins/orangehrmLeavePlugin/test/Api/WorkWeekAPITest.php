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

use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Entity\WorkWeek;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Api\WorkWeekAPI;
use OrangeHRM\Leave\Dao\WorkWeekDao;
use OrangeHRM\Leave\Service\WorkWeekService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Leave
 * @group APIv2
 */
class WorkWeekAPITest extends EndpointTestCase
{
    public function testGetWorkWeekService(): void
    {
        $this->createKernelWithMockServices([Services::WORK_WEEK_SERVICE => new WorkWeekService()]);
        $this->assertTrue(
            $this->invokeProtectedMethod(
                WorkWeekAPI::class,
                'getWorkWeekService',
                [],
                [$this->getRequest()]
            ) instanceof WorkWeekService
        );
    }

    public function testGetOne(): void
    {
        $workWeek = new WorkWeek();
        $workWeek->setId(1);
        $workWeek->setFriday(WorkWeek::WORKWEEK_LENGTH_HALF_DAY);
        $workWeek->setSaturday(WorkWeek::WORKWEEK_LENGTH_NON_WORKING_DAY);

        $dao = $this->getMockBuilder(WorkWeekDao::class)
            ->onlyMethods(['getWorkWeekById'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getWorkWeekById')
            ->with(1)
            ->willReturn($workWeek);
        $service = $this->getMockBuilder(WorkWeekService::class)
            ->onlyMethods(['getWorkWeekDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getWorkWeekDao')
            ->willReturn($dao);

        /** @var MockObject&WorkWeekAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            WorkWeekAPI::class,
            [RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_ID => 1]]
        )
            ->onlyMethods(['getWorkWeekService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getWorkWeekService')
            ->willReturn($service);

        $result = $api->getOne();
        $this->assertEquals(
            [
                'monday' => 0,
                'tuesday' => 0,
                'wednesday' => 0,
                'thursday' => 0,
                'friday' => 4,
                'saturday' => 8,
                'sunday' => 0,
            ],
            $result->normalize()
        );
        $this->assertNull($result->getMeta());
    }

    public function testGetOneWithIndexedModel(): void
    {
        $workWeek = new WorkWeek();
        $workWeek->setId(1);
        $workWeek->setFriday(WorkWeek::WORKWEEK_LENGTH_HALF_DAY);
        $workWeek->setSaturday(WorkWeek::WORKWEEK_LENGTH_NON_WORKING_DAY);

        $dao = $this->getMockBuilder(WorkWeekDao::class)
            ->onlyMethods(['getWorkWeekById'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getWorkWeekById')
            ->with(1)
            ->willReturn($workWeek);
        $service = $this->getMockBuilder(WorkWeekService::class)
            ->onlyMethods(['getWorkWeekDao'])
            ->getMock();
        $service->expects($this->once())
            ->method('getWorkWeekDao')
            ->willReturn($dao);

        /** @var MockObject&WorkWeekAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            WorkWeekAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_ID => 1],
                RequestParams::PARAM_TYPE_QUERY => [WorkWeekAPI::FILTER_MODEL => WorkWeekAPI::MODEL_INDEXED],
            ]
        )
            ->onlyMethods(['getWorkWeekService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getWorkWeekService')
            ->willReturn($service);

        $result = $api->getOne();
        $this->assertEquals(
            [
                0 => 0,
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 4,
                6 => 8,
            ],
            $result->normalize()
        );
        $this->assertNull($result->getMeta());
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new WorkWeekAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 1,
                    WorkWeekAPI::FILTER_MODEL => WorkWeekAPI::MODEL_INDEXED
                ],
                $rules
            )
        );

        $this->expectInvalidParamException();
        $this->validate(
            [
                CommonParams::PARAMETER_ID => 1,
                WorkWeekAPI::FILTER_MODEL => 'invalid model'
            ],
            $rules
        );
    }

    public function testGetValidationRuleForGetOneWithModel(): void
    {
        $api = new WorkWeekAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );
    }

    public function testUpdate(): void
    {
        $workWeek = new WorkWeek();
        $workWeek->setId(1);
        $workWeek->setFriday(WorkWeek::WORKWEEK_LENGTH_HALF_DAY);
        $workWeek->setSaturday(WorkWeek::WORKWEEK_LENGTH_NON_WORKING_DAY);

        $dao = $this->getMockBuilder(WorkWeekDao::class)
            ->onlyMethods(['getWorkWeekById', 'saveWorkWeek'])
            ->getMock();
        $dao->expects($this->once())
            ->method('getWorkWeekById')
            ->with(1)
            ->willReturn($workWeek);
        $dao->expects($this->once())
            ->method('saveWorkWeek')
            ->willReturnCallback(
                function (WorkWeek $workWeek) {
                    return $workWeek;
                }
            );
        $service = $this->getMockBuilder(WorkWeekService::class)
            ->onlyMethods(['getWorkWeekDao'])
            ->getMock();
        $service->expects($this->exactly(2))
            ->method('getWorkWeekDao')
            ->willReturn($dao);

        /** @var MockObject&WorkWeekAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            WorkWeekAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [CommonParams::PARAMETER_ID => 1],
                RequestParams::PARAM_TYPE_BODY => [
                    WorkWeekAPI::PARAMETER_MONDAY => 8,
                    WorkWeekAPI::PARAMETER_TUESDAY => 0,
                    WorkWeekAPI::PARAMETER_WEDNESDAY => 0,
                    WorkWeekAPI::PARAMETER_THURSDAY => 0,
                    WorkWeekAPI::PARAMETER_FRIDAY => 0,
                    WorkWeekAPI::PARAMETER_SATURDAY => 4,
                    WorkWeekAPI::PARAMETER_SUNDAY => 8,
                ]
            ]
        )
            ->onlyMethods(['getWorkWeekService'])
            ->getMock();
        $api->expects($this->exactly(2))
            ->method('getWorkWeekService')
            ->willReturn($service);

        $result = $api->update();
        $this->assertEquals(
            [
                'monday' => 8,
                'tuesday' => 0,
                'wednesday' => 0,
                'thursday' => 0,
                'friday' => 0,
                'saturday' => 4,
                'sunday' => 8,
            ],
            $result->normalize()
        );
        $this->assertNull($result->getMeta());
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new WorkWeekAPI($this->getRequest());
        $rules = $api->getValidationRuleForUpdate();
        $this->assertTrue(
            $this->validate(
                [
                    CommonParams::PARAMETER_ID => 1,
                    WorkWeekAPI::PARAMETER_MONDAY => 8,
                    WorkWeekAPI::PARAMETER_TUESDAY => 0,
                    WorkWeekAPI::PARAMETER_WEDNESDAY => 0,
                    WorkWeekAPI::PARAMETER_THURSDAY => 0,
                    WorkWeekAPI::PARAMETER_FRIDAY => 0,
                    WorkWeekAPI::PARAMETER_SATURDAY => 4,
                    WorkWeekAPI::PARAMETER_SUNDAY => 8,
                ],
                $rules
            )
        );

        $this->expectInvalidParamException();
        $this->validate(
            [
                CommonParams::PARAMETER_ID => 1,
                WorkWeekAPI::PARAMETER_MONDAY => 'invalid param',
                WorkWeekAPI::PARAMETER_TUESDAY => 0,
                WorkWeekAPI::PARAMETER_WEDNESDAY => 0,
                WorkWeekAPI::PARAMETER_THURSDAY => 0,
                WorkWeekAPI::PARAMETER_FRIDAY => 0,
                WorkWeekAPI::PARAMETER_SATURDAY => 4,
                WorkWeekAPI::PARAMETER_SUNDAY => 8,
            ],
            $rules
        );
    }

    public function testDelete(): void
    {
        $api = new WorkWeekAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new WorkWeekAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
