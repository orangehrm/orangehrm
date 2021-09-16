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
use OrangeHRM\Admin\Dto\WorkShiftStartAndEndTime;
use OrangeHRM\Admin\Service\WorkShiftService;
use OrangeHRM\Entity\EmployeeWorkShift;
use OrangeHRM\Entity\WorkShift;
use OrangeHRM\Leave\WorkSchedule\BasicWorkSchedule;
use OrangeHRM\Pim\Dao\EmployeeDao;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Leave
 * @group Service
 */
class BasicWorkScheduleTest extends TestCase
{
    public function testGetWorkShiftStartEndTime(): void
    {
        $employeeWorkShift = new EmployeeWorkShift();
        $workShift = new WorkShift();
        $startDate = new DateTime('2021-12-25 09:00');
        $endDate = new DateTime('2021-12-25 17:00');
        $workShift->setStartTime($startDate);
        $workShift->setEndTime($endDate);
        $employeeWorkShift->setWorkShift($workShift);
        $employeeDao = $this->getMockBuilder(EmployeeDao::class)
            ->onlyMethods(['getEmployeeWorkShift'])
            ->getMock();

        $employeeDao->expects($this->once())
            ->method('getEmployeeWorkShift')
            ->with(1)
            ->willReturn($employeeWorkShift);

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeDao'])
            ->getMock();

        $employeeService->expects($this->once())
            ->method('getEmployeeDao')
            ->willReturn($employeeDao);

        $basicWorkSchedule = $this->getMockBuilder(BasicWorkSchedule::class)
            ->onlyMethods(['getEmployeeService'])
            ->getMock();

        $basicWorkSchedule->expects($this->once())
            ->method('getEmployeeService')
            ->willReturn($employeeService);
        $basicWorkSchedule->setEmpNumber(1);

        $expectResult = new WorkShiftStartAndEndTime($startDate, $endDate);

        $this->assertEquals($expectResult, $basicWorkSchedule->getWorkShiftStartEndTime());
    }

    public function testGetWorkShiftStartEndTimeEmployeeWorkShiftEmpty(): void
    {
        $employeeDao = $this->getMockBuilder(EmployeeDao::class)
            ->onlyMethods(['getEmployeeWorkShift'])
            ->getMock();

        $employeeDao->expects($this->once())
            ->method('getEmployeeWorkShift')
            ->with(1)
            ->willReturn(null);


        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeDao'])
            ->getMock();

        $employeeService->expects($this->once())
            ->method('getEmployeeDao')
            ->willReturn($employeeDao);


        $startDate = new DateTime('2021-12-25 09:00');
        $endDate = new DateTime('2021-12-25 17:00');
        $defaultWorkShift = new WorkShiftStartAndEndTime($startDate, $endDate);

        $workShiftService = $this->getMockBuilder(WorkShiftService::class)
            ->onlyMethods(['getWorkShiftDefaultStartAndEndTime'])
            ->getMock();

        $workShiftService->expects($this->once())
            ->method('getWorkShiftDefaultStartAndEndTime')
            ->willReturn($defaultWorkShift);

        $basicWorkSchedule = $this->getMockBuilder(BasicWorkSchedule::class)
            ->onlyMethods(['getEmployeeService', 'getWorkShiftService'])
            ->getMock();

        $basicWorkSchedule->expects($this->once())
            ->method('getEmployeeService')
            ->willReturn($employeeService);
        $basicWorkSchedule->expects($this->once())
            ->method('getWorkShiftService')
            ->willReturn($workShiftService);
        $basicWorkSchedule->setEmpNumber(1);

        $expectResult = new WorkShiftStartAndEndTime($startDate, $endDate);

        $this->assertEquals($expectResult, $basicWorkSchedule->getWorkShiftStartEndTime());
    }

    public function testGetWorkShiftLength(): void
    {
        $employeeWorkShift = new EmployeeWorkShift();
        $workShift = new WorkShift();
        $workShift->setStartTime(new DateTime('18:00'));
        $workShift->setEndTime(new DateTime('23:45'));
        $workShift->setHoursPerDay(5.75);
        $employeeWorkShift->setWorkShift($workShift);

        $employeeDao = $this->getMockBuilder(EmployeeDao::class)
            ->onlyMethods(['getEmployeeWorkShift'])
            ->getMock();
        $employeeDao->expects($this->once())
            ->method('getEmployeeWorkShift')
            ->with(1)
            ->willReturn($employeeWorkShift);

        $employeeService = $this->getMockBuilder(EmployeeService::class)
            ->onlyMethods(['getEmployeeDao'])
            ->getMock();
        $employeeService->expects($this->once())
            ->method('getEmployeeDao')
            ->willReturn($employeeDao);

        $basicWorkSchedule = $this->getMockBuilder(BasicWorkSchedule::class)
            ->onlyMethods(['getEmployeeService'])
            ->getMock();
        $basicWorkSchedule->expects($this->once())
            ->method('getEmployeeService')
            ->willReturn($employeeService);
        $basicWorkSchedule->setEmpNumber(1);

        $this->assertEquals(5.75, $basicWorkSchedule->getWorkShiftLength());
    }
}
