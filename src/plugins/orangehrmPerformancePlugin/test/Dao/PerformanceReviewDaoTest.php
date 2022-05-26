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

use OrangeHRM\Config\Config;
use OrangeHRM\Performance\Dao\PerformanceReviewDao;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class PerformanceReviewDaoTest extends KernelTestCase
{
    private PerformanceReviewDao $performanceReviewDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->performanceReviewDao = new PerformanceReviewDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/PerformanceReview.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetPerformanceReviewList(): void
    {
        $performanceReviewSearchFilterParams = new PerformanceReviewSearchFilterParams();
        $performanceReviewSearchFilterParams->setEmpNumber(2);
        $performanceReviewSearchFilterParams->setExcludeInactiveReviews(true);
        $result = $this->performanceReviewDao->getPerformanceReviewList($performanceReviewSearchFilterParams);
        $this->assertCount(1, $result);
    }

    public function testGetPerformanceReviewCount(): void
    {
        $performanceReviewSearchFilterParams = new PerformanceReviewSearchFilterParams();
        $performanceReviewSearchFilterParams->setEmpNumber(2);
        $performanceReviewSearchFilterParams->setExcludeInactiveReviews(true);
        $result = $this->performanceReviewDao->getPerformanceReviewCount($performanceReviewSearchFilterParams);
        $this->assertEquals(1, $result);
    }

    public function testGetEditableReviewById(): void
    {
        $reviewId = 6;
        $result = $this->performanceReviewDao->getEditableReviewById($reviewId);
        $this->assertEmpty($result);

        $reviewId = 5;
        $result = $this->performanceReviewDao->getEditableReviewById($reviewId);
        $this->assertEquals(5, $result->getId());
        $this->assertEquals(1, $result->getJobTitle()->getId());
        $this->assertEquals(1, $result->getEmployee()->getEmpNumber());
    }

    public function testGetSupervisorRecord(): void
    {
        $employeeId = 5;
        $existingSupervisor = 2;
        $nonExistentSupervisor = 3;
        $pastSupervisor = 6;

        $result = $this->performanceReviewDao->getSupervisorRecord($employeeId, $existingSupervisor);
        $this->assertNotNull($result);

        $result2 = $this->performanceReviewDao->getSupervisorRecord($employeeId, $nonExistentSupervisor);
        $this->assertCount(0, $result2);

        $result3 = $this->performanceReviewDao->getSupervisorRecord($employeeId, $pastSupervisor);
        $this->assertCount(0, $result3);
    }
}
