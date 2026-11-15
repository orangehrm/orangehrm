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
use OrangeHRM\Entity\ReviewerRating;
use OrangeHRM\Framework\Services;
use OrangeHRM\ORM\Doctrine;
use OrangeHRM\Performance\Api\EmployeeEvaluationAPI;
use OrangeHRM\Performance\Service\PerformanceReviewService;
use OrangeHRM\Pim\Service\EmployeeService;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class EmployeeEvaluationAPITest extends EndpointIntegrationTestCase
{
    // Users in EmployeeEvaluationAPITest.yaml
    private const ADMIN = 1; // emp 1
    private const SUPERVISOR_NOT_REVIEWER_OF_8 = 2; // emp 2; supervises 5, 7, 16
    private const SUPERVISOR_REVIEWER_OF_8 = 4; // emp 3; supervisor reviewer of review 8

    // Review 8: the employee has completed the self evaluation; rating row 6 is theirs (KPI 6)
    private const REVIEW_ID = 8;
    private const SELF_RATING_ID = 6;

    /**
     * Calls update as if the row rule had accepted the request, so only the workflow check decides
     *
     * @dataProvider dataProviderForTestSupervisorUpdatePastRowRule
     */
    public function testSupervisorUpdatePastRowRule(int $userId, bool $allowed): void
    {
        $this->populateFixtures('EmployeeEvaluationAPITest.yaml');
        $testCaseParams = new TestCaseParams();
        $testCaseParams->setUserId($userId);
        $testCaseParams->setAttributes([EmployeeEvaluationAPI::PARAMETER_REVIEW_ID => self::REVIEW_ID]);
        $testCaseParams->setBody([
            EmployeeEvaluationAPI::PARAMETER_RATINGS => [[
                EmployeeEvaluationAPI::PARAMETER_KPI_ID => 6,
                EmployeeEvaluationAPI::PARAMETER_RATING => '7',
                EmployeeEvaluationAPI::PARAMETER_COMMENT => 'updated comment',
            ]],
            EmployeeEvaluationAPI::PARAMETER_COMPLETE => true,
        ]);
        $testCaseParams->setServices([
            Services::PERFORMANCE_REVIEW_SERVICE => PerformanceReviewService::class,
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
        $api = $this->getApiEndpointMock(EmployeeEvaluationAPI::class, $testCaseParams);

        $forbidden = false;
        AccessFlowStateMachineService::resetWorkflowCache();
        try {
            $api->update();
        } catch (ForbiddenException $e) {
            $forbidden = true;
        } finally {
            AccessFlowStateMachineService::resetWorkflowCache();
        }

        Doctrine::getEntityManager()->clear();
        $rating = Doctrine::getEntityManager()->find(ReviewerRating::class, self::SELF_RATING_ID);
        if ($allowed) {
            $this->assertFalse($forbidden);
            $this->assertEquals(7, $rating->getRating());
            $this->assertSame('updated comment', $rating->getComment());
        } else {
            $this->assertTrue($forbidden);
            $this->assertEquals(2, $rating->getRating());
            $this->assertSame('testSelfb2', $rating->getComment());
        }
    }

    public function dataProviderForTestSupervisorUpdatePastRowRule(): array
    {
        return [
            'supervisor, not the reviewer' => [self::SUPERVISOR_NOT_REVIEWER_OF_8, false],
            'assigned reviewer' => [self::SUPERVISOR_REVIEWER_OF_8, true],
            'admin, not the reviewer' => [self::ADMIN, true],
        ];
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     *
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('EmployeeEvaluationAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(EmployeeEvaluationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('EmployeeEvaluationAPITestCases.yaml', 'GetAll');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     *
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('EmployeeEvaluationAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(EmployeeEvaluationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('EmployeeEvaluationAPITestCases.yaml', 'Update');
    }

    public function testDelete(): void
    {
        $api = new EmployeeEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new EmployeeEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testCreate(): void
    {
        $api = new EmployeeEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new EmployeeEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testGetOne(): void
    {
        $api = new EmployeeEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getOne();
    }

    public function testGetValidationRuleForgetOne(): void
    {
        $api = new EmployeeEvaluationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetOne();
    }
}
