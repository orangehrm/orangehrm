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

use DateTime;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Dao\PerformanceReviewDao;
use OrangeHRM\Performance\Dto\PerformanceReviewSearchFilterParams;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Dao
 */
class PerformanceReviewDaoTest extends KernelTestCase
{
    use EntityManagerHelperTrait;

    private PerformanceReviewDao $performanceReviewDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService()]);
        $this->performanceReviewDao = new PerformanceReviewDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/PerformanceReviewDao.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetPerformanceReviewList(): void
    {
        $performanceReviewSearchFilterParams = new PerformanceReviewSearchFilterParams();
        $performanceReviewSearchFilterParams->setJobTitleId(2);
        $performanceReviewSearchFilterParams->setStatusId(1);
        $performanceReviewSearchFilterParams->setFromDate(DateTime::createFromFormat("Y-m-d", "2022-05-01"));
        $performanceReviewSearchFilterParams->setToDate(DateTime::createFromFormat("Y-m-d", "2022-05-31"));
        $performanceReviewSearchFilterParams->setIncludeEmployees('onlyCurrent');

        $result = $this->performanceReviewDao->getPerformanceReviewList($performanceReviewSearchFilterParams);
        $this->assertCount(1, $result);

        $this->assertEquals('Seungcheol', $result[0]->getEmployee()->getFirstName());
        $this->assertEquals('Choi', $result[0]->getEmployee()->getLastName());
        $this->assertEquals('Devi', $result[0]->getDecorator()->getSupervisorReviewer()->getEmployee()->getFirstName());
        $this->assertEquals('Admin', $result[0]->getDecorator()->getSupervisorReviewer()->getEmployee()->getLastName());
    }

    public function testGetPerformanceReviewCount(): void
    {
        $performanceReviewSearchAndFilterParams = new PerformanceReviewSearchFilterParams();

        $performanceReviewSearchAndFilterParams->setIncludeEmployees('onlyCurrent');
        $result = $this->performanceReviewDao->getPerformanceReviewCount($performanceReviewSearchAndFilterParams);
        $this->assertEquals(26, $result);

        $performanceReviewSearchAndFilterParams->setSupervisorId(1);
        $result = $this->performanceReviewDao->getPerformanceReviewCount($performanceReviewSearchAndFilterParams);
        $this->assertEquals(7, $result);
    }

    public function testDeletePerformanceReviews(): void
    {
        $result = $this->performanceReviewDao->deletePerformanceReviews([1, 2]);
        $this->assertEquals(2, $result);

        $this->assertEmpty($this->getRepository(PerformanceReview::class)->find(1));
        $this->assertEmpty($this->getRepository(Reviewer::class)->findBy(['review' => 1]));
        $this->assertEmpty($this->getRepository(ReviewerRating::class)->findBy(['performanceReview' => 1]));

        $this->assertEmpty($this->getRepository(PerformanceReview::class)->find(2));
        $this->assertEmpty($this->getRepository(Reviewer::class)->findBy(['review' => 2]));
        $this->assertEmpty($this->getRepository(ReviewerRating::class)->findBy(['performanceReview' => 2]));
    }
}
