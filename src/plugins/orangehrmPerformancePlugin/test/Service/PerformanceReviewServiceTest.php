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

namespace OrangeHRM\Tests\Performance\Service;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Dao\PerformanceReviewDao;
use OrangeHRM\Performance\Exception\ReviewServiceException;
use OrangeHRM\Performance\Service\PerformanceReviewService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Performance
 * @group Service
 */
class PerformanceReviewServiceTest extends KernelTestCase
{
    private PerformanceReviewService $performanceReviewService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->performanceReviewService = new PerformanceReviewService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmPerformancePlugin/test/fixtures/PerformanceReviewService.yaml';
        TestDataService::populate($this->fixture);
    }

    public function testGetPerformanceReviewDao(): void
    {
        $result = $this->performanceReviewService->getPerformanceReviewDao();
        $this->assertInstanceOf(PerformanceReviewDao::class, $result);
    }

    public function testGenerateReviewReviewerRatingKey(): void
    {
        $this->assertEquals('1_2_3_', $this->performanceReviewService->generateReviewReviewerRatingKey(1, 2, 3));
    }

    public function testActivateReviewWithException1(): void
    {
        // Test activate without job title
        $performanceReview = new PerformanceReview();
        $performanceReview->getDecorator()->setEmployeeByEmpNumber(15);

        $this->expectException(ReviewServiceException::class);
        $this->expectExceptionMessage(ReviewServiceException::activateWithoutJobTitle()->getMessage());
        $this->performanceReviewService->activateReview($performanceReview, 15);
    }

    public function testActivateReviewWithException2(): void
    {
        // Test activate without KPIs
        $performanceReview = new PerformanceReview();
        $performanceReview->getDecorator()->setEmployeeByEmpNumber(16);
        $performanceReview->getDecorator()->setJobTitleById(5);

        $this->expectException(ReviewServiceException::class);
        $this->expectExceptionMessage(ReviewServiceException::activateWithoutKPI()->getMessage());
        $this->performanceReviewService->activateReview($performanceReview, 16);
    }

    public function testUpdateActivateReviewWithException1(): void
    {
        $this->createKernelWithMockServices([Services::EMPLOYEE_SERVICE => new EmployeeService()]);

        $performanceReview = new PerformanceReview();
        $performanceReview->getDecorator()->setEmployeeByEmpNumber(3);
        $performanceReview->getDecorator()->setJobTitleById(2);

        $this->expectException(ReviewServiceException::class);
        $this->expectExceptionMessage(ReviewServiceException::pastEmployeeForReviewer()->getMessage());
        $this->performanceReviewService->updateActivateReview($performanceReview, 10);
    }

    public function testUpdateActivateReviewWithException2(): void
    {
        $this->createKernelWithMockServices([Services::EMPLOYEE_SERVICE => new EmployeeService()]);

        $performanceReview = new PerformanceReview();
        $performanceReview->getDecorator()->setEmployeeByEmpNumber(3);
        $performanceReview->getDecorator()->setJobTitleById(2);

        $this->expectException(ReviewServiceException::class);
        $this->expectExceptionMessage(ReviewServiceException::invalidSupervisor()->getMessage());
        $this->performanceReviewService->updateActivateReview($performanceReview, 2);
    }
}
