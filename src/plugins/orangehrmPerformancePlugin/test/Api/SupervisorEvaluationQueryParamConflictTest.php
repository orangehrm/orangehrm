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

namespace OrangeHRM\Tests\Performance\Api;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\InterviewInterviewer;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ProjectAdmin;
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\Entity\Vacancy;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Api\SupervisorEvaluationAPI;
use OrangeHRM\Performance\Service\KpiService;
use OrangeHRM\Performance\Service\PerformanceReviewService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\RestControllerTestTrait;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * The supervisor evaluation reads `reviewId` from the route. A query value that differs from it is refused (422),
 * so the accessible-review rule always checks the review the handler works on.
 *
 * @group Performance
 * @group APIv2
 */
class SupervisorEvaluationQueryParamConflictTest extends EndpointIntegrationTestCase
{
    use RestControllerTestTrait;

    // In SupervisorEvaluationAPITest.yaml
    private const REVIEWER_OF_4_5 = 2; // user of emp 2, supervisor reviewer of reviews 4 and 5, not of 6
    private const REVIEWER_OF_2 = 4; // user of emp 3, supervisor reviewer of review 2
    private const OWN_REVIEW = 4;
    private const OTHER_REVIEW = 6;
    private const OTHER_REVIEW_RATING = 19; // supervisor rating row of review 6, KPI 4
    private const OTHER_REVIEW_KPI = 4;

    private const SERVICES = [
        Services::PERFORMANCE_REVIEW_SERVICE => PerformanceReviewService::class,
        Services::KPI_SERVICE => KpiService::class,
        Services::CONFIG_SERVICE => ConfigService::class,
        Services::USER_SERVICE => UserService::class,
        Services::EMPLOYEE_SERVICE => EmployeeService::class,
        Services::NORMALIZER_SERVICE => NormalizerService::class,
    ];

    protected function setUp(): void
    {
        $this->populateFixtures('SupervisorEvaluationAPITest.yaml');
        // Project admins, hiring managers and interviewers get extra roles; other test classes leave such rows
        TestDataService::truncateSpecificTables([ProjectAdmin::class, Vacancy::class, InterviewInterviewer::class]);
        // The workflow service keeps a static cache, which would carry over between requests
        AccessFlowStateMachineService::resetWorkflowCache();
    }

    protected function tearDown(): void
    {
        AccessFlowStateMachineService::resetWorkflowCache();
        parent::tearDown();
    }

    private function evaluation(int $userId, string $method, int $reviewId, array $query, array $body = []): array
    {
        return $this->handleRestRequest(
            $userId,
            SupervisorEvaluationAPI::class,
            $method,
            ['reviewId' => $reviewId],
            $query,
            $body,
            self::SERVICES
        );
    }

    /**
     * @return array [rating, comment, review status, supervisor reviewer status, supervisor reviewer comment]
     */
    private function getOtherReviewState(): array
    {
        $rating = $this->getEntityManager()->find(ReviewerRating::class, self::OTHER_REVIEW_RATING);
        $review = $this->getEntityManager()->find(PerformanceReview::class, self::OTHER_REVIEW);
        $supervisorReviewer = $review->getDecorator()->getSupervisorReviewer();
        return [
            $rating->getRating(),
            $rating->getComment(),
            $review->getStatusId(),
            $supervisorReviewer->getStatus(),
            $supervisorReviewer->getComment(),
        ];
    }

    public function testReviewerReadsOwnReview(): void
    {
        $data = $this->assertHandled($this->evaluation(self::REVIEWER_OF_2, 'GET', 2, ['reviewId' => '2']));
        $this->assertNotEmpty($data);
    }

    public function testReviewerCannotReadOtherReviewWithOwnReviewQueryValue(): void
    {
        $this->assertConflictRejected(
            $this->evaluation(
                self::REVIEWER_OF_4_5,
                'GET',
                self::OTHER_REVIEW,
                ['reviewId' => (string)self::OWN_REVIEW]
            ),
            'reviewId'
        );
    }

    public function testReviewerCannotUpdateOtherReviewWithOwnReviewQueryValue(): void
    {
        $before = $this->getOtherReviewState();

        $response = $this->evaluation(
            self::REVIEWER_OF_4_5,
            'PUT',
            self::OTHER_REVIEW,
            ['reviewId' => (string)self::OWN_REVIEW],
            [
                SupervisorEvaluationAPI::PARAMETER_RATINGS => [[
                    SupervisorEvaluationAPI::PARAMETER_KPI_ID => self::OTHER_REVIEW_KPI,
                    SupervisorEvaluationAPI::PARAMETER_RATING => '7',
                    SupervisorEvaluationAPI::PARAMETER_COMMENT => 'Changed',
                ]],
                SupervisorEvaluationAPI::PARAMETER_GENERAL_COMMENT => 'Changed',
            ]
        );

        $this->assertConflictRejected($response, 'reviewId');
        $this->assertSame($before, $this->getOtherReviewState());
    }
}
