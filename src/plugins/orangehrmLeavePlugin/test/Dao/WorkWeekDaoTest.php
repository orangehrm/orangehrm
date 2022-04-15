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

namespace OrangeHRM\Tests\Leave\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\WorkWeek;
use OrangeHRM\Leave\Dao\WorkWeekDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Dao
 */
class WorkWeekDaoTest extends TestCase
{
    /**
     * @var WorkWeekDao
     */
    private WorkWeekDao $workWeekDao;

    protected function setUp(): void
    {
        $this->workWeekDao = new WorkWeekDao();
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/WorkWeekDao.yml'
        );
    }

    public function testSaveWorkWeek(): void
    {
        $day = 2;
        $length = 8;

        /** @var WorkWeek $workWeek */
        $workWeek = TestDataService::fetchObject(WorkWeek::class, $day);
        $workWeek->setTuesday($length);

        $this->workWeekDao->saveWorkWeek($workWeek);
        /** @var WorkWeek $savedWorkWeek */
        $savedWorkWeek = TestDataService::fetchObject(WorkWeek::class, $day);
        $this->assertEquals($length, $savedWorkWeek->getTuesday());
    }

    public function testGetWorkWeekById(): void
    {
        $workWeek = $this->workWeekDao->getWorkWeekById(1);
        $this->assertEquals(1, $workWeek->getId());
        $this->assertEquals(0, $workWeek->getMonday());
        $this->assertEquals(0, $workWeek->getTuesday());
        $this->assertEquals(0, $workWeek->getWednesday());
        $this->assertEquals(0, $workWeek->getThursday());
        $this->assertEquals(0, $workWeek->getFriday());
        $this->assertEquals(4, $workWeek->getSaturday());
        $this->assertEquals(8, $workWeek->getSunday());
    }
}
