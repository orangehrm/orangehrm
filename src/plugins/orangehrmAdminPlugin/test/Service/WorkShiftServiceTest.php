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

namespace OrangeHRM\Tests\Admin\Service;

use DateTime;
use Exception;
use OrangeHRM\Admin\Dao\WorkShiftDao;
use OrangeHRM\Admin\Dto\WorkShiftSearchFilterParams;
use OrangeHRM\Admin\Service\WorkShiftService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\WorkShift;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class WorkShiftServiceTest extends TestCase
{
    private WorkShiftService $workShiftService;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->workShiftService = new WorkShiftService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/WorkShiftService.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveWorkShift(): void
    {
        $workShift = new WorkShift();
        $workShift->setName("Morning");
        $workShift->setHoursPerDay(8.00);
        $workShift->setStartTime(new DateTime('08:00:00'));
        $workShift->setEndTime(new DateTime('17:00:00'));
        $workShiftDao = $this->getMockBuilder(WorkShiftDao::class)->getMock();
        $workShiftDao->expects($this->once())
            ->method('saveWorkShift')
            ->with($workShift, [])
            ->will($this->returnValue($workShift));
        $result = $workShiftDao->saveWorkShift($workShift, []);
        $this->assertEquals($workShift, $result);
    }

    public function testGetAllWorkShifts(): void
    {
        $workShiftList = TestDataService::loadObjectList('WorkShift', $this->fixture, 'WorkShift');
        $workShiftSearchParam = new WorkShiftSearchFilterParams();
        $workShiftDao = $this->getMockBuilder(WorkShiftDao::class)->getMock();
        $workShiftDao->expects($this->once())
            ->method('getWorkShiftList')
            ->with($workShiftSearchParam)
            ->will($this->returnValue($workShiftList));

        $this->workShiftService->setWorkShiftDao($workShiftDao);
        $result = $this->workShiftService->getWorkShiftList($workShiftSearchParam);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof WorkShift);
    }

    public function testGetWorkShiftById(): void
    {
        $workShiftList = TestDataService::loadObjectList('WorkShift', $this->fixture, 'WorkShift');
        $workShiftDao = $this->getMockBuilder(WorkShiftDao::class)->getMock();
        $workShiftDao->expects($this->once())
            ->method('getWorkShiftById')
            ->with(1)
            ->will($this->returnValue($workShiftList[0]));
        $this->workShiftService->setWorkShiftDao($workShiftDao);
        $result = $this->workShiftService->getWorkShiftById(1);
        $this->assertEquals($workShiftList[0], $result);
    }
}
