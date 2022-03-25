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

namespace OrangeHRM\Tests\Admin\Dao;

use Exception;
use DateTime;
use OrangeHRM\Admin\Dao\WorkShiftDao;
use OrangeHRM\Admin\Dto\WorkShiftSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\WorkShift;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class WorkShiftDaoTest extends TestCase
{
    /**
     * @var WorkShiftDao
     */
    private WorkShiftDao $workShiftDao;

    /**
     * @var string
     */
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->workShiftDao = new WorkShiftDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/WorkShiftDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetWorkShiftById(): void
    {
        $workShift = $this->workShiftDao->getWorkShiftById(1);
        $this->assertEquals('Shift 1', $workShift->getName());
        $this->assertEquals(8, $workShift->getHoursPerDay());
        $this->assertEquals('07:00:00', $workShift->getStartTime()->format('H:i:s'));
        $this->assertEquals('15:00:00', $workShift->getEndTime()->format('H:i:s'));
    }

    public function testGetWorkShiftList(): void
    {
        $workShiftSearchFilterParams = new WorkShiftSearchFilterParams();
        $result = $this->workShiftDao->getWorkShiftList($workShiftSearchFilterParams);
        $this->assertCount(2, $result);
        $this->assertTrue($result[0] instanceof WorkShift);
    }

    public function testSaveWorkShift(): void
    {
        $workShift = new WorkShift();
        $workShift->setName("TEST");
        $workShift->setHoursPerDay(8.00);
        $workShift->setStartTime(new DateTime('08:00:00'));
        $workShift->setEndTime(new DateTime('17:00:00'));
        $result = $this->workShiftDao->saveWorkShift($workShift, []);

        $this->assertTrue($result instanceof WorkShift);
        $this->assertEquals('TEST', $workShift->getName());
        $this->assertEquals(8.00, $workShift->getHoursPerDay());
        $this->assertEquals('08:00:00', $workShift->getStartTime()->format('H:i:s'));
        $this->assertEquals('17:00:00', $workShift->getEndTime()->format('H:i:s'));
    }

    public function testEditWorkShift(): void
    {
        $workShift = $this->workShiftDao->getWorkShiftById(1);
        $workShift->setName("TEST0002");
        $workShift->setHoursPerDay(8.00);
        $workShift->setStartTime(new DateTime('08:00:00'));
        $workShift->setEndTime(new DateTime('17:00:00'));

        $result = $this->workShiftDao->updateWorkShift($workShift, []);
        $this->assertTrue($result instanceof WorkShift);
        $this->assertEquals('TEST0002', $workShift->getName());
        $this->assertEquals(8.00, $workShift->getHoursPerDay());
        $this->assertEquals('08:00:00', $workShift->getStartTime()->format('H:i:s'));
        $this->assertEquals('17:00:00', $workShift->getEndTime()->format('H:i:s'));
        $this->assertEquals(1, $result->getId());
    }

    public function testDeleteWorkShifts(): void
    {
        $toTobedeletedIds = [1,2];
        $result = $this->workShiftDao->deleteWorkShifts($toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testGetEmployeeListByWorkShiftId(): void
    {
        $firstWorkShiftEmployees = $this->workShiftDao->getEmployeeListByWorkShiftId(1);
        $this->assertCount(2, $firstWorkShiftEmployees);
        $this->assertEquals('Kayla', $firstWorkShiftEmployees[0]->getFirstName());
        $this->assertEquals('Ashley', $firstWorkShiftEmployees[1]->getFirstName());

        $secondWorkShiftEmployees = $this->workShiftDao->getEmployeeListByWorkShiftId(2);
        $this->assertCount(0, $secondWorkShiftEmployees);
    }
}
