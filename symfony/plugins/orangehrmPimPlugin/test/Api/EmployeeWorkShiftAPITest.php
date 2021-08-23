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

namespace OrangeHRM\Tests\Pim\Api;

use DateTime;
use OrangeHRM\Admin\Dto\WorkShiftStartAndEndTime;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Service\WorkScheduleService;
use OrangeHRM\Leave\WorkSchedule\BasicWorkSchedule;
use OrangeHRM\Pim\Api\CustomFieldAPI;
use OrangeHRM\Pim\Api\EmployeeWorkShiftAPI;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;

/**
 * @group Pim
 * @group APIv2
 */
class EmployeeWorkShiftAPITest extends EndpointTestCase
{
    public function testGetOne(): void
    {
        $startDate = new DateTime('2021-12-25 09:00');
        $endDate = new DateTime('2021-12-25 17:00');
        $workShiftStartAndEndTime = new WorkShiftStartAndEndTime($startDate, $endDate);

        $basicWorkSchedule = $this->getMockBuilder(BasicWorkSchedule::class)
            ->onlyMethods(['getWorkShiftStartEndTime'])
            ->getMock();

        $basicWorkSchedule->expects($this->exactly(1))
            ->method('getWorkShiftStartEndTime')
            ->willReturn($workShiftStartAndEndTime);

        $workScheduleService = $this->getMockBuilder(WorkScheduleService::class)
            ->onlyMethods(['getWorkSchedule'])
            ->getMock();

        $workScheduleService->expects($this->exactly(1))
            ->method('getWorkSchedule')
            ->willReturn($basicWorkSchedule);

        /** @var MockObject&EmployeeWorkShiftAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            EmployeeWorkShiftAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_EMP_NUMBER => 1
                ]
            ]
        )->onlyMethods(['getWorkScheduleService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getWorkScheduleService')
            ->will($this->returnValue($workScheduleService));

        $this->createKernelWithMockServices(
            [
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $result = $api->getOne();
        $this->assertEquals(
            [
                "startTime" => '09:00',
                "endTime" => '17:00',
            ],
            $result->normalize()
        );
        $this->assertEquals(
            [
                "empNumber" => 1,
            ],
            $result->getMeta()->all()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new CustomFieldAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_ID => 1],
                $rules
            )
        );
    }

    public function testDelete()
    {
        $api = new EmployeeWorkShiftAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }


    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeWorkShiftAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testUpdate(): void
    {
        $api = new EmployeeWorkShiftAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new EmployeeWorkShiftAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }
}
