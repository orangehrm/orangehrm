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
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Entity\PerformanceReview;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Performance\Api\PerformanceReviewFinalEvaluationAPI;
use OrangeHRM\Performance\Service\PerformanceReviewService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class PerformanceReviewFinalEvaluationAPITest extends EndpointIntegrationTestCase
{
    // Users in PerformanceReviewFinalEvaluationAPITest.yaml
    private const ADMIN = 1; // emp 1; supervisor reviewer of review 6
    private const SUPERVISOR_OF_8_9 = 2; // emp 2; not a reviewer of reviews 2 and 6
    private const SUPERVISOR_SUBJECT_OF_6 = 4; // emp 3; supervisor reviewer of review 2; employee of review 6

    /**
     * Calls update as if the row rule had accepted the request, so only the workflow check decides
     *
     * @dataProvider dataProviderForTestCompletePastRowRule
     */
    public function testCompletePastRowRule(int $userId, int $reviewId, bool $allowed): void
    {
        $this->populateFixtures('PerformanceReviewFinalEvaluationAPITest.yaml');
        $testCaseParams = new TestCaseParams();
        $testCaseParams->setUserId($userId);
        $testCaseParams->setAttributes([PerformanceReviewFinalEvaluationAPI::PARAMETER_REVIEW_ID => $reviewId]);
        $testCaseParams->setBody([
            PerformanceReviewFinalEvaluationAPI::PARAMETER_COMPLETE => true,
            PerformanceReviewFinalEvaluationAPI::PARAMETER_FINAL_RATING => 77,
            PerformanceReviewFinalEvaluationAPI::PARAMETER_FINAL_COMMENT => 'final comment',
            PerformanceReviewFinalEvaluationAPI::PARAMETER_COMPLETED_DATE => '2022-06-09',
        ]);
        $testCaseParams->setServices([
            Services::PERFORMANCE_REVIEW_SERVICE => PerformanceReviewService::class,
            Services::CONFIG_SERVICE => ConfigService::class,
            Services::USER_SERVICE => UserService::class,
            Services::EMPLOYEE_SERVICE => EmployeeService::class,
        ]);
        $testCaseParams->setFactories([
            Services::USER_ROLE_MANAGER => [UserRoleManagerFactory::class, 'getNewUserRoleManager'],
        ]);
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceReviewFinalEvaluationAPI::class, $testCaseParams);

        $forbidden = false;
        try {
            $api->update();
        } catch (ForbiddenException $e) {
            $forbidden = true;
        }

        Doctrine::getEntityManager()->clear();
        $review = Doctrine::getEntityManager()->find(PerformanceReview::class, $reviewId);
        if ($allowed) {
            $this->assertFalse($forbidden);
            $this->assertSame(PerformanceReview::STATUS_COMPLETED, $review->getStatusId());
            $this->assertEquals(77, $review->getFinalRate());
        } else {
            $this->assertTrue($forbidden);
            $this->assertSame(PerformanceReview::STATUS_IN_PROGRESS, $review->getStatusId());
            $this->assertNull($review->getFinalRate());
        }
    }

    public function dataProviderForTestCompletePastRowRule(): array
    {
        return [
            'own review, supervises other employees' => [self::SUPERVISOR_SUBJECT_OF_6, 6, false],
            'supervisor, not the reviewer' => [self::SUPERVISOR_OF_8_9, 6, false],
            'assigned reviewer' => [self::SUPERVISOR_SUBJECT_OF_6, 2, true],
            'admin, assigned reviewer' => [self::ADMIN, 6, true],
            'admin, not the reviewer' => [self::ADMIN, 2, true],
        ];
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceReviewFinalEvaluationAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);

        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceReviewFinalEvaluationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('PerformanceReviewFinalEvaluationAPITestCases.yaml', 'Update');
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceReviewFinalEvaluationAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);

        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceReviewFinalEvaluationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('PerformanceReviewFinalEvaluationAPITestCases.yaml', 'GetOne');
    }

    public function testDelete(): void
    {
        $api = new PerformanceReviewFinalEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new PerformanceReviewFinalEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }
}
