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
use Exception;
use OrangeHRM\Admin\Dto\WorkShiftStartAndEndTime;
use OrangeHRM\Core\Exception\ConfigurationException;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\WorkScheduleService;
use OrangeHRM\Leave\WorkSchedule\WorkScheduleInterface;
use OrangeHRM\Tests\Util\KernelTestCase;
use TypeError;

/**
 * @group Leave
 * @group Service
 */
class WorkScheduleServiceTest extends KernelTestCase
{
    /**
     * @var WorkScheduleService
     */
    private WorkScheduleService $service;

    protected function setUp(): void
    {
        $this->service = new WorkScheduleService();
    }

    /**
     * Test for work schedule class not defined
     */
    public function testGetWorkScheduleNotDefined(): void
    {
        $mockService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getWorkScheduleImplementation'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getWorkScheduleImplementation')
            ->will($this->returnValue(null));

        $this->createKernelWithMockServices([Services::LEAVE_CONFIG_SERVICE => $mockService]);

        try {
            $this->service->getWorkSchedule(1);
            $this->fail('TypeError expected');
        } catch (TypeError $e) {
        }
    }

    /**
     * Test for unavailable work schedule class
     */
    public function testGetWorkScheduleClassNotFound(): void
    {
        $mockService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getWorkScheduleImplementation'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getWorkScheduleImplementation')
            ->will($this->returnValue('xYzNotAvailable'));

        $this->createKernelWithMockServices(
            [Services::LEAVE_CONFIG_SERVICE => $mockService, Services::CLASS_HELPER => new ClassHelper()]
        );

        try {
            $this->service->getWorkSchedule(1);
            $this->fail('ConfigurationException expected');
        } catch (ConfigurationException $e) {
        }
    }

    /**
     * Test for workschedule class not implementing work schedule interface
     */
    public function testGetWorkScheduleInvalidClass(): void
    {
        $mockService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getWorkScheduleImplementation'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getWorkScheduleImplementation')
            ->will($this->returnValue(TestWorkScheduleInvalidClass::class));

        $this->createKernelWithMockServices(
            [Services::LEAVE_CONFIG_SERVICE => $mockService, Services::CLASS_HELPER => new ClassHelper()]
        );

        try {
            $this->service->getWorkSchedule(1);
            $this->fail('ConfigurationException expected');
        } catch (ConfigurationException $e) {
        }
    }

    /**
     * Test for exception thrown when construction work schedule class
     */
    public function testGetWorkScheduleClassExceptionInConstructor(): void
    {
        $mockService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getWorkScheduleImplementation'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getWorkScheduleImplementation')
            ->will($this->returnValue(TestWorkScheduleInvalidClassExceptionInConstructor::class));

        $this->createKernelWithMockServices(
            [Services::LEAVE_CONFIG_SERVICE => $mockService, Services::CLASS_HELPER => new ClassHelper()]
        );

        try {
            $this->service->getWorkSchedule(1);
            $this->fail('ConfigurationException expected');
        } catch (ConfigurationException $e) {
        }
    }

    public function testGetWorkSchedule(): void
    {
        $mockService = $this->getMockBuilder(LeaveConfigurationService::class)
            ->onlyMethods(['getWorkScheduleImplementation'])
            ->getMock();
        $mockService->expects($this->once())
            ->method('getWorkScheduleImplementation')
            ->will($this->returnValue(TestWorkScheduleValidClass::class));

        $this->createKernelWithMockServices(
            [Services::LEAVE_CONFIG_SERVICE => $mockService, Services::CLASS_HELPER => new ClassHelper()]
        );

        $workSchedule = $this->service->getWorkSchedule(3);
        $this->assertTrue($workSchedule instanceof TestWorkScheduleValidClass);
        $this->assertEquals(3, $workSchedule->getEmpNumber());
    }
}

class TestWorkScheduleInvalidClass
{
}

class TestWorkScheduleInvalidClassExceptionInConstructor
{
    public function __construct()
    {
        throw new Exception('Exception in constructor');
    }
}

class TestWorkScheduleValidClass implements WorkScheduleInterface
{
    protected ?int $empNumber = null;

    public function getWorkShiftLength(): float
    {
        return 5;
    }

    public function setEmpNumber(?int $empNumber): void
    {
        $this->empNumber = $empNumber;
    }

    public function getEmpNumber()
    {
        return $this->empNumber;
    }

    public function isHalfDay(DateTime $day): bool
    {
        return false;
    }

    public function isHalfDayHoliday(DateTime $day): bool
    {
        return false;
    }

    public function isHoliday(DateTime $day): bool
    {
        return false;
    }

    public function isNonWorkingDay(DateTime $day, bool $fullDay): bool
    {
        return false;
    }

    public function getWorkShiftStartEndTime(): WorkShiftStartAndEndTime
    {
        return new WorkShiftStartAndEndTime(new DateTime('09:00'), new DateTime('17:00'));
    }
}
