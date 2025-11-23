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

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\ORM\Exception\TransactionException;
use OrangeHRM\Performance\Dao\PerformanceTrackerDao;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Dao
 */
class PerformanceTrackerDaoTest extends TestCase
{
    private PerformanceTrackerDao $performanceTrackerDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->performanceTrackerDao = new PerformanceTrackerDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/PerformanceTracker.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSavePerformanceTrackerWithTransactionException(): void
    {
        $performanceTrackerDaoMock = $this->getMockBuilder(PerformanceTrackerDao::class)
            ->onlyMethods(['persist'])
            ->getMock();

        $performanceTrackerDaoMock->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function () {
                throw new Exception();
            });

        $performanceTracker = new PerformanceTracker();

        $this->expectException(TransactionException::class);
        $performanceTrackerDaoMock->savePerformanceTracker($performanceTracker, []);
    }

    public function testIsTrackerReviewerWithNullEmpNumber(): void
    {
        $this->assertFalse($this->performanceTrackerDao->isTrackerReviewer(null));
    }

    public function testUpdatePerformanceTrackerWithEmptyReviewerArray(): void
    {
        $performanceTracker = $this->getEntityManager()->getRepository(PerformanceTracker::class)->find(1);

        $result = $this->performanceTrackerDao->updatePerformanceTracker($performanceTracker, []);
        $this->assertEquals($result, $performanceTracker);
    }
}
