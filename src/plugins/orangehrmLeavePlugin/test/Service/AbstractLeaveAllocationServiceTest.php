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

namespace OrangeHRM\Tests\Leave\Service;

use DateTime;
use Generator;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Service\AbstractLeaveAllocationService;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Leave
 * @group Service
 */
class AbstractLeaveAllocationServiceTest extends KernelTestCase
{
    /**
     * @dataProvider getAddHoursDurationDataProvider
     * @param DateTime $time
     * @param float $hoursToAdd
     * @param string $expected H:i format
     */
    public function testAddHoursDuration(DateTime $time, float $hoursToAdd, string $expected): void
    {
        $leaveAllocationService = $this->getMockBuilder(AbstractLeaveAllocationService::class)
            ->onlyMethods(
                [
                    'isWeekend',
                    'isHoliday',
                    'isHalfDay',
                    'isHalfdayHoliday',
                    'getApplicableLeaveDuration',
                    'updateLeaveDurationParameters',
                    'getLeaveRequestStatus'
                ]
            )
            ->getMockForAbstractClass();

        /** @var DateTime $result */
        $result = $this->invokeProtectedMethodOnMock(
            AbstractLeaveAllocationService::class,
            $leaveAllocationService,
            'addHoursDuration',
            [$time, $hoursToAdd]
        );
        $this->assertEquals($expected, $result->format('H:i'));
    }

    /**
     * @return Generator
     */
    public function getAddHoursDurationDataProvider(): Generator
    {
        yield [new DateTime('09:00'), 4, '13:00'];
        yield [new DateTime('09:00'), 4.5, '13:30'];
    }

    /**
     * @dataProvider getDurationInHoursDataProvider
     * @param DateTime $fromTime
     * @param DateTime $toTime
     * @param float $expected
     */
    public function testGetDurationInHours(DateTime $fromTime, DateTime $toTime, float $expected): void
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $leaveAllocationService = $this->getMockBuilder(AbstractLeaveAllocationService::class)
            ->onlyMethods([])
            ->getMockForAbstractClass();

        /** @var DateTime $result */
        $result = $this->invokeProtectedMethodOnMock(
            AbstractLeaveAllocationService::class,
            $leaveAllocationService,
            'getDurationInHours',
            [$fromTime, $toTime]
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * @return Generator
     */
    public function getDurationInHoursDataProvider(): Generator
    {
        yield [new DateTime('09:00'), new DateTime('17:00'), 8];
        yield [new DateTime('09:00'), new DateTime('13:00'), 4];
        yield [new DateTime('09:00'), new DateTime('09:30'), 0.5];
        yield [new DateTime('09:00'), new DateTime('09:15'), 0.25];
        // TODO:: decimal points also need to handle
        //yield [new DateTime('09:00'), new DateTime('09:10'), 0.16];
    }
}
