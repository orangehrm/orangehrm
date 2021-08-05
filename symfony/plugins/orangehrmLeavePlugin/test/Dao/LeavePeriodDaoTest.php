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

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\LeavePeriodHistory;
use OrangeHRM\Leave\Dao\LeavePeriodDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Leave
 * @group Dao
 */
class LeavePeriodDaoTest extends TestCase
{
    /**
     * @var LeavePeriodDao
     */
    private LeavePeriodDao $leavePeriodDao;

    protected function setUp(): void
    {
        $this->leavePeriodDao = new LeavePeriodDao();
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmLeavePlugin/test/fixtures/LeavePeriodDao.yml'
        );
    }

    public function testSaveLeavePeriodHistory(): void
    {
        $leavePeriodHistory = new LeavePeriodHistory();
        $leavePeriodHistory->setLeavePeriodStartMonth(1);
        $leavePeriodHistory->setLeavePeriodStartDay(1);
        $leavePeriodHistory->setCreatedAt(new DateTime('2012-01-01'));

        $result = $this->leavePeriodDao->saveLeavePeriodHistory($leavePeriodHistory);
        $this->assertEquals(1, $result->getLeavePeriodStartMonth());
        $this->assertEquals(1, $result->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-01', $result->getCreatedAt()->format('Y-m-d'));
    }

    public function testGetCurrentLeavePeriodStartDateAndMonth(): void
    {
        $result = $this->leavePeriodDao->getCurrentLeavePeriodStartDateAndMonth();
        $this->assertEquals(1, $result->getLeavePeriodStartMonth());
        $this->assertEquals(3, $result->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-02', $result->getCreatedAt()->format('Y-m-d'));
    }

    public function testGetLeavePeriodHistoryList(): void
    {
        $result = $this->leavePeriodDao->getLeavePeriodHistoryList();
        $this->assertEquals(1, $result[0]->getLeavePeriodStartMonth());
        $this->assertEquals(4, $result[0]->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-01', $result[0]->getCreatedAt()->format('Y-m-d'));

        $this->assertEquals(1, $result[1]->getLeavePeriodStartMonth());
        $this->assertEquals(1, $result[1]->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-02', $result[1]->getCreatedAt()->format('Y-m-d'));

        $this->assertEquals(1, $result[2]->getLeavePeriodStartMonth());
        $this->assertEquals(2, $result[2]->getLeavePeriodStartDay());
        $this->assertEquals('2012-01-02', $result[2]->getCreatedAt()->format('Y-m-d'));

        $this->assertCount(4, $result);
    }
}
