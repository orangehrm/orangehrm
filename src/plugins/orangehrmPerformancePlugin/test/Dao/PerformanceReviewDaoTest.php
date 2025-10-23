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
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\Reviewer;
use OrangeHRM\Entity\ReviewerGroup;
use OrangeHRM\ORM\Exception\TransactionException;
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

    public function testCreateReviewWithTransactionException(): void
    {
        $performanceReviewDaoMock = $this->getMockBuilder(PerformanceReviewDao::class)
            ->onlyMethods(['persist'])
            ->getMock();

        $performanceReviewDaoMock->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function () {
                throw new Exception();
            });

        $performanceReview = new PerformanceReview();
        $performanceReview->setId(1);

        $this->expectException(TransactionException::class);
        $performanceReviewDaoMock->createReview($performanceReview, 1);
    }

    public function testUpdateReviewWithTransactionException(): void
    {
        $performanceReviewDaoMock = $this->getMockBuilder(PerformanceReviewDao::class)
            ->onlyMethods(['persist'])
            ->getMock();

        $performanceReviewDaoMock->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function () {
                throw new Exception();
            });

        $performanceReview = new PerformanceReview();
        $performanceReview->setId(1);

        $this->expectException(TransactionException::class);
        $performanceReviewDaoMock->updateReview($performanceReview, 1);
    }

    public function testGetPerformanceSelfReviewStatus(): void
    {
        $selfReviewer = new Reviewer();
        $performanceReview = new PerformanceReview();

        $performanceReviewDaoMock = $this->getMockBuilder(PerformanceReviewDao::class)
            ->onlyMethods(['getPerformanceSelfReviewer'])
            ->getMock();

        $performanceReviewDaoMock->expects($this->exactly(4))
            ->method('getPerformanceSelfReviewer')
            ->willReturn($selfReviewer);

        $selfReviewer->setStatus(Reviewer::STATUS_ACTIVATED);
        $this->assertEquals('Activated', $performanceReviewDaoMock->getPerformanceSelfReviewStatus($performanceReview));

        $selfReviewer->setStatus(Reviewer::STATUS_IN_PROGRESS);
        $this->assertEquals('In Progress', $performanceReviewDaoMock->getPerformanceSelfReviewStatus($performanceReview));

        $selfReviewer->setStatus(Reviewer::STATUS_COMPLETED);
        $this->assertEquals('Completed', $performanceReviewDaoMock->getPerformanceSelfReviewStatus($performanceReview));

        $selfReviewer->setStatus(null);
        $this->assertEquals('', $performanceReviewDaoMock->getPerformanceSelfReviewStatus($performanceReview));
    }

    public function testUpdateReviewerComment(): void
    {
        /** @var Reviewer $reviewerBefore */
        $reviewerBefore = $this->getEntityManager()->getRepository(Reviewer::class)->find(1);
        $this->assertEquals('Test Comment 1', $reviewerBefore->getComment());

        $performanceReview = $this->getEntityManager()->getRepository(PerformanceReview::class)->find(5);
        $this->performanceReviewDao->updateReviewerComment($performanceReview, ReviewerGroup::REVIEWER_GROUP_SUPERVISOR, 'Edited Comment');

        /** @var Reviewer $reviewerAfter */
        $reviewerAfter = $this->getEntityManager()->getRepository(Reviewer::class)->findOneBy(['comment' => 'Edited Comment']);
        $this->assertEquals(1, $reviewerAfter->getId());
        $this->assertEquals(ReviewerGroup::REVIEWER_GROUP_SUPERVISOR, $reviewerAfter->getGroup()->getName());
    }
}
