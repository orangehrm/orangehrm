<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\Performance\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Performance\Dao\PerformanceTrackerLogDao;
use OrangeHRM\Performance\Dto\PerformanceTrackerLogSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Dao
 */
class PerformanceTrackerLogDaoTest extends KernelTestCase
{
    protected PerformanceTrackerLogDao $performanceTrackerLogDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->performanceTrackerLogDao = new PerformanceTrackerLogDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR).'/orangehrmPerformancePlugin/test/fixtures/PerformanceTrackerLogDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetPerformanceTrackerLogById(): void
    {
        $trackerLogId = 1;
        $result = $this->performanceTrackerLogDao->getPerformanceTrackerLogById($trackerLogId);
        $this->assertEquals(1, $result->getPerformanceTracker()->getId());
        $this->assertEquals(2, $result->getEmployee()->getEmpNumber());

        $trackerLogId2 = 10;
        $result2 = $this->performanceTrackerLogDao->getPerformanceTrackerLogById($trackerLogId2);
        $this->assertNull($result2);
    }

    public function testGetPerformanceTrackerLogsByTrackerId(): void
    {
        $existingTrackerIdWithLogs = 1;
        $existingTrackerIdWithoutLogs = 3;
        $nonExistentTrackerId = 4;

        $performanceTrackerLogSearchFilterParams = new PerformanceTrackerLogSearchFilterParams();
        $performanceTrackerLogSearchFilterParams->setTrackerId($existingTrackerIdWithLogs);
        $result = $this->performanceTrackerLogDao
            ->getPerformanceTrackerLogsByTrackerId($performanceTrackerLogSearchFilterParams);
        $this->assertCount(2, $result);

        $performanceTrackerLogSearchFilterParams2 = new PerformanceTrackerLogSearchFilterParams();
        $performanceTrackerLogSearchFilterParams2->setTrackerId($existingTrackerIdWithoutLogs);
        $result2 = $this->performanceTrackerLogDao
            ->getPerformanceTrackerLogsByTrackerId($performanceTrackerLogSearchFilterParams2);
        $this->assertCount(0, $result2);

        $performanceTrackerLogSearchFilterParams3 = new PerformanceTrackerLogSearchFilterParams();
        $performanceTrackerLogSearchFilterParams3->setTrackerId($nonExistentTrackerId);
        $result3 = $this->performanceTrackerLogDao
            ->getPerformanceTrackerLogsByTrackerId($performanceTrackerLogSearchFilterParams3);
        $this->assertCount(0, $result3);
    }

    public function testGetPerformanceTrackerLogsRateCount(): void
    {
        $existingTrackerIdWithLogs = 1;
        $positiveLogs = $this->performanceTrackerLogDao
            ->getPerformanceTrackerLogsRateCount(PerformanceTrackerLog::POSITIVE_ACHIEVEMENT, $existingTrackerIdWithLogs);
        $this->assertEquals(1, $positiveLogs);

        $negativeLogs = $this->performanceTrackerLogDao
            ->getPerformanceTrackerLogsRateCount(PerformanceTrackerLog::NEGATIVE_ACHIEVEMENT, $existingTrackerIdWithLogs);
        $this->assertEquals(1, $negativeLogs);
    }
}
