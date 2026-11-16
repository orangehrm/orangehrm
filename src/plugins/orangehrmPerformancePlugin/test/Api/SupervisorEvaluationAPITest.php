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
use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Authorization\Manager\UserRoleManagerFactory;
use OrangeHRM\Core\Service\AccessFlowStateMachineService;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Performance\Api\SupervisorEvaluationAPI;
use OrangeHRM\Performance\Service\KpiService;
use OrangeHRM\Performance\Service\PerformanceReviewService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class SupervisorEvaluationAPITest extends EndpointIntegrationTestCase
{
    // Users in SupervisorEvaluationAPITest.yaml
    private const ADMIN = 1; // emp 1; supervisor reviewer of review 6
    private const SUPERVISOR_REVIEWER_OF_4_5 = 2; // emp 2; supervises 5, 7, 16
    private const ESS_SUBJECT_OF_2 = 3; // emp 6; supervises nobody; employee of reviews 2 and 8
    private const SUPERVISOR_SUBJECT_OF_6 = 4; // emp 3; supervises 6; reviewer of reviews 1, 2, 3, 8; employee of review 6
    private const SUPERVISOR_NOT_IN_REVIEWS = 5; // emp 4; supervises 5

    private const FORBIDDEN = 'forbidden';

    // Supervisor reviewer rating row and KPI per review, from the fixture
    private const SUPERVISOR_RATING = [2 => [1, 4], 4 => [16, 6], 6 => [19, 4], 8 => [3, 6]];

    /**
     * @param int $userId
     * @param string $operation getAll or update
     * @param int $reviewId
     * @param array $body
     * @param bool $validate false to call the endpoint as if the row rule had accepted the request
     * @return string|array Normalized result with meta, or FORBIDDEN
     */
    private function callAs(int $userId, string $operation, int $reviewId, array $body = [], bool $validate = true)
    {
        $testCaseParams = new TestCaseParams();
        $testCaseParams->setUserId($userId);
        $testCaseParams->setAttributes([SupervisorEvaluationAPI::PARAMETER_REVIEW_ID => $reviewId]);
        $testCaseParams->setBody($body);
        $testCaseParams->setServices([
            Services::PERFORMANCE_REVIEW_SERVICE => PerformanceReviewService::class,
            Services::KPI_SERVICE => KpiService::class,
            Services::CONFIG_SERVICE => ConfigService::class,
            Services::USER_SERVICE => UserService::class,
            Services::EMPLOYEE_SERVICE => EmployeeService::class,
            Services::NORMALIZER_SERVICE => NormalizerService::class,
        ]);
        $testCaseParams->setFactories([
            Services::USER_ROLE_MANAGER => [UserRoleManagerFactory::class, 'getNewUserRoleManager'],
        ]);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(SupervisorEvaluationAPI::class, $testCaseParams);

        // getAllowedActions() renames the action on the workflow items, which the service keeps in a static
        // cache; start and end with an empty cache so that does not carry over (one request in production)
        AccessFlowStateMachineService::resetWorkflowCache();
        try {
            if ($validate) {
                $this->validate(
                    array_merge($testCaseParams->getAttributes(), $body),
                    $api->{'getValidationRuleFor' . ucfirst($operation)}()
                );
            }
            $result = $api->$operation();
            return ['data' => $result->normalize(), 'meta' => $result->getMeta() ? $result->getMeta()->all() : []];
        } catch (ForbiddenException $e) {
            return self::FORBIDDEN;
        } finally {
            AccessFlowStateMachineService::resetWorkflowCache();
        }
    }

    /**
     * @return array [rating, comment, review status, supervisor reviewer status]
     */
    private function getSupervisorSectionState(int $reviewId): array
    {
        Doctrine::getEntityManager()->clear();
        $rating = Doctrine::getEntityManager()->find(ReviewerRating::class, self::SUPERVISOR_RATING[$reviewId][0]);
        $review = Doctrine::getEntityManager()->find(PerformanceReview::class, $reviewId);
        return [
            $rating->getRating(),
            $rating->getComment(),
            $review->getStatusId(),
            $review->getDecorator()->getSupervisorReviewer()->getStatus(),
        ];
    }

    /**
     * @dataProvider dataProviderForTestUpdateByRole
     */
    public function testUpdateByRole(int $userId, int $reviewId, bool $allowed): void
    {
        $this->assertUpdate($userId, $reviewId, $allowed, true);
    }

    /**
     * The workflow check must hold on its own, also when the row rule lets the request through
     *
     * @dataProvider dataProviderForTestUpdatePastRowRule
     */
    public function testUpdatePastRowRule(int $userId, int $reviewId, bool $allowed): void
    {
        $this->assertUpdate($userId, $reviewId, $allowed, false);
    }

    public function dataProviderForTestUpdatePastRowRule(): array
    {
        return [
            'own review, supervises another employee' => [self::SUPERVISOR_SUBJECT_OF_6, 6, false],
            'supervisor, not a reviewer of the review' => [self::SUPERVISOR_NOT_IN_REVIEWS, 6, false],
            'reviewer of other reviews' => [self::SUPERVISOR_REVIEWER_OF_4_5, 6, false],
            'assigned reviewer' => [self::SUPERVISOR_SUBJECT_OF_6, 2, true],
            'admin, not the reviewer' => [self::ADMIN, 2, true],
        ];
    }

    private function assertUpdate(int $userId, int $reviewId, bool $allowed, bool $validate): void
    {
        $this->populateFixtures('SupervisorEvaluationAPITest.yaml');
        $before = $this->getSupervisorSectionState($reviewId);
        $body = [
            SupervisorEvaluationAPI::PARAMETER_RATINGS => [[
                SupervisorEvaluationAPI::PARAMETER_KPI_ID => self::SUPERVISOR_RATING[$reviewId][1],
                SupervisorEvaluationAPI::PARAMETER_RATING => '7',
                SupervisorEvaluationAPI::PARAMETER_COMMENT => 'matrix comment',
            ]],
            SupervisorEvaluationAPI::PARAMETER_GENERAL_COMMENT => 'matrix general comment',
        ];

        $result = $this->callAs($userId, 'update', $reviewId, $body, $validate);

        $after = $this->getSupervisorSectionState($reviewId);
        if ($allowed) {
            $this->assertIsArray($result);
            $this->assertEquals(7, $after[0]);
            $this->assertSame('matrix comment', $after[1]);
        } else {
            $this->assertSame(self::FORBIDDEN, $result);
            $this->assertSame($before, $after);
        }
    }

    public function dataProviderForTestUpdateByRole(): array
    {
        return [
            // The subject of the review, who supervises someone else, is not the review's reviewer
            'own review, supervises another employee' => [self::SUPERVISOR_SUBJECT_OF_6, 6, false],
            // The fixture gives ESS transitions in the review workflow (a real install has none)
            'own review, ESS only' => [self::ESS_SUBJECT_OF_2, 2, false],
            'supervisor, not a reviewer of the review' => [self::SUPERVISOR_NOT_IN_REVIEWS, 6, false],
            'reviewer of other reviews' => [self::SUPERVISOR_REVIEWER_OF_4_5, 6, false],
            'assigned reviewer, activated review' => [self::SUPERVISOR_SUBJECT_OF_6, 2, true],
            'assigned reviewer, in progress review' => [self::SUPERVISOR_REVIEWER_OF_4_5, 4, true],
            'assigned reviewer, completed review' => [self::SUPERVISOR_SUBJECT_OF_6, 8, false],
            'admin, not the reviewer' => [self::ADMIN, 2, true],
            'admin, assigned reviewer' => [self::ADMIN, 6, true],
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetAllAllowedActionsByRole
     */
    public function testGetAllAllowedActionsByRole(int $userId, int $reviewId, array $expectedActions): void
    {
        $this->populateFixtures('SupervisorEvaluationAPITest.yaml');
        $result = $this->callAs($userId, 'getAll', $reviewId);

        $this->assertIsArray($result);
        $this->assertEqualsCanonicalizing(
            $expectedActions,
            array_column($result['meta'][SupervisorEvaluationAPI::PARAMETER_ALLOWED_ACTIONS], 'action')
        );
    }

    public function dataProviderForTestGetAllAllowedActionsByRole(): array
    {
        return [
            'own review, supervises another employee' => [self::SUPERVISOR_SUBJECT_OF_6, 6, []],
            'own review, ESS only' => [self::ESS_SUBJECT_OF_2, 2, []],
            'assigned reviewer' => [self::SUPERVISOR_SUBJECT_OF_6, 2, ['save', 'complete']],
            'admin, assigned reviewer' => [self::ADMIN, 6, ['save', 'complete']],
            'admin, not the reviewer' => [self::ADMIN, 2, ['save', 'complete']],
        ];
    }

    public function testGetAllOwnReviewKeepsReviewerRatingsHidden(): void
    {
        $this->populateFixtures('SupervisorEvaluationAPITest.yaml');
        $result = $this->callAs(self::SUPERVISOR_SUBJECT_OF_6, 'getAll', 6);

        $this->assertIsArray($result);
        $this->assertSame([], $result['data']);
        $this->assertNull($result['meta'][SupervisorEvaluationAPI::PARAMETER_GENERAL_COMMENT]);
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     *
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('SupervisorEvaluationAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(SupervisorEvaluationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('SupervisorEvaluationAPITestCases.yaml', 'GetAll');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     *
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('SupervisorEvaluationAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(SupervisorEvaluationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('SupervisorEvaluationAPITestCases.yaml', 'Update');
    }

    public function testDelete(): void
    {
        $api = new SupervisorEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new SupervisorEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new SupervisorEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new SupervisorEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testGetOne(): void
    {
        $api = new SupervisorEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getOne();
    }

    public function testGetValidationRuleForgetOne(): void
    {
        $api = new SupervisorEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetOne();
    }
}
