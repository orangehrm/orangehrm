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

namespace OrangeHRM\Tests\Performance\Dao;

use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Dao\PerformanceTrackerDao;
use OrangeHRM\Config\Config;
use OrangeHRM\Performance\Dto\PerformanceTrackerSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use OrangeHRM\Entity\PerformanceTracker;

class PerformanceTrackerDaoTest extends KernelTestCase
{
    private $performanceTrackerDao;
    protected $fixture;

    protected function setUp(): void
    {
        $this->performanceTrackerDao = new PerformanceTrackerDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/PerformanceTracker2.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetPerformanceTrack(): void
    {
        $result =$this->performanceTrackerDao->getPerformanceTrack(1);
        $this->assertEquals('test tracker name', $result->getTrackerName());
    }

    public function testGetPerformanceTrackList(): void
    {
        $performanceTrackerFilterParams = new PerformanceTrackerSearchFilterParams();
        $result = $this->performanceTrackerDao->getPerformanceTrackList($performanceTrackerFilterParams);
        $this->assertCount(2, $result);
    }

    public function testGetPerformanceTrackerCount(): void
    {
        $performanceTrackerFilterParams = new PerformanceTrackerSearchFilterParams();
        $result = $this->performanceTrackerDao->getPerformanceTrackerCount($performanceTrackerFilterParams);
        $this->assertEquals(2, $result);
    }


    /*public function testSavePerformanceTracker(): void
    {
        $performanceTracker = new PerformanceTracker();
        $performanceTracker->setTrackerName('Devp vue apps');
        $performanceTracker->getDecorator()->setEmployeeByEmpNumber(1);

        $result =$this->performanceTrackerDao->savePerformanceTracker($performanceTracker);
        $this->assertEquals("Devp vue apps", $result->getTrackerName());
    }*/

    public function testSavePerformanceTracker(): void
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $performanceTracker = new PerformanceTracker();
        $performanceTracker->setTrackerName('Devp vue apps');
        $performanceTracker->getDecorator()->setEmployeeByEmpNumber(1);
        $reviewArray = [1,2];
        $result =$this->performanceTrackerDao->savePerformanceTracker($performanceTracker, $reviewArray);
        $this->assertEquals("Devp vue apps", $result->getTrackerName());
    }

    public function testDeletePerformanceTracker(): void
    {
        $toDeleteIds =[1,2];
        $result = $this->performanceTrackerDao->deletePerformanceTracker($toDeleteIds);
        $this->assertEquals(2, $result);
    }

    public function testUpdatePerformanceTracker(): void
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $performancanceTracker =$this->performanceTrackerDao->getPerformanceTrack(1);
        $performancanceTracker->setTrackerName('UpdatedTracker');
        $reviewers = [1,3];
        $result = $this->performanceTrackerDao->updatePerformanceTracker($performancanceTracker, $reviewers);
        $this->assertEquals("UpdatedTracker", $result->getTrackerName());
    }
}
