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

namespace OrangeHRM\Tests\Util;

use Exception;
use OrangeHRM\Core\Api\V2\Endpoint;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\Mock\MockAuthUser;
use ReflectionClass;

abstract class EndpointIntegrationTestCase extends EndpointTestCase
{
    /**
     * @param string $fixture
     * @param string|null $pathToFixturesDir
     * @param bool $resetCache
     */
    protected function populateFixtures(string $fixture, ?string $pathToFixturesDir = null, bool $resetCache = false)
    {
        $this->getEntityManager()->clear();
        if (is_null($pathToFixturesDir)) {
            $reflection = new ReflectionClass($this);
            $pathToFixturesDir = realpath(dirname($reflection->getFileName()) . '/../../test/fixtures');
            if (!$pathToFixturesDir) {
                throw new Exception('Invalid `test/fixtures` directory');
            }
        }

        TestDataService::populate($pathToFixturesDir . DIRECTORY_SEPARATOR . $fixture, $resetCache);
    }

    /**
     * @param string $testCasesFilePath
     * @param string $testCaseKey
     * @param string|null $pathToTestCasesDir
     * @return array<string, Integration\TestCaseParams[]>
     */
    protected function getTestCases(
        string $testCasesFilePath,
        string $testCaseKey,
        ?string $pathToTestCasesDir = null
    ): array {
        if (is_null($pathToTestCasesDir)) {
            $reflection = new ReflectionClass($this);
            $pathToTestCasesDir = realpath(dirname($reflection->getFileName()) . '/../../test/fixtures/testcases');
            if (!$pathToTestCasesDir) {
                throw new Exception('Invalid `test/fixtures/testcases` directory');
            }
        }

        $testCasesAssoc = TestDataService::loadFixtures(
            $pathToTestCasesDir . DIRECTORY_SEPARATOR . $testCasesFilePath,
            $testCaseKey
        );

        $testCases = [];
        foreach ($testCasesAssoc as $name => $params) {
            $testCaseParams = new TestCaseParams();
            $testCaseParams->setName($name);
            $testCaseParams->setUserId($params['userId'] ?? null);
            $testCaseParams->setServices($params['services'] ?? null);
            $testCaseParams->setFactories($params['factories'] ?? null);
            $testCaseParams->setAttributes($params['attributes'] ?? null);
            $testCaseParams->setBody($params['body'] ?? null);
            $testCaseParams->setQuery($params['query'] ?? null);
            $testCaseParams->setResultData($params['data'] ?? null);
            $testCaseParams->setResultMeta($params['meta'] ?? null);
            $testCaseParams->setInvalidOnly($params['invalidOnly'] ?? null);
            $testCaseParams->setNowFromArray($params['now'] ?? null);
            if (isset($params['exception'])) {
                $testCaseParams->setExceptionClass($params['exception']['class'] ?? null);
                $testCaseParams->setExceptionMessage($params['exception']['message'] ?? null);
            }
            $testCaseParams->setHooks($params['hooks'] ?? null);
            $testCases[$name] = [$testCaseParams];
        }
        return $testCases;
    }

    /**
     * @template T
     * @param class-string<T> $apiClassName
     * @param TestCaseParams $testCaseParams
     * @return T|MockObject
     */
    protected function getApiEndpointMock(string $apiClassName, TestCaseParams $testCaseParams): Endpoint
    {
        return $this->getApiEndpointMockBuilder(
            $apiClassName,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => $testCaseParams->getAttributes() ?? [],
                RequestParams::PARAM_TYPE_QUERY => $testCaseParams->getQuery() ?? [],
                RequestParams::PARAM_TYPE_BODY => $testCaseParams->getBody() ?? [],
            ]
        )->onlyMethods([])
            ->getMock();
    }

    /**
     * @param Endpoint $api
     * @param string $operation
     * @param TestCaseParams $testCaseParams
     */
    protected function assertValidTestCase(Endpoint $api, string $operation, TestCaseParams $testCaseParams): void
    {
        $callable = $testCaseParams->getHook(TestCaseParams::HOOK_PRE_ASSERT_VALID_TEST_CASE);
        is_null($callable) ?: call_user_func_array(
            $callable,
            [$testCaseParams, $api, $operation]
        );
        $validationMethod = 'getValidationRuleFor' . ucfirst($operation);
        $params = array_merge(
            $testCaseParams->getAttributes() ?? [],
            $testCaseParams->getQuery() ?? [],
            $testCaseParams->getBody() ?? []
        );

        if (!is_null($testCaseParams->getExceptionClass())) {
            $this->expectException($testCaseParams->getExceptionClass());
            if (!is_null($testCaseParams->getExceptionMessage())) {
                $this->expectExceptionMessage($testCaseParams->getExceptionMessage());
            }
        }

        if ($testCaseParams->isInvalid()) {
            $this->assertInvalidParamException(
                fn () => $this->validate($params, $api->$validationMethod()),
                $testCaseParams->getInvalidOnly() ?? []
            );
            return;
        } else {
            $this->validate($params, $api->$validationMethod());
        }

        $result = $api->$operation();
        $this->assertEquals(
            $testCaseParams->getResultData() ?? [],
            $result->normalize()
        );
        if (!is_null($result->getMeta())) {
            $this->assertEquals(
                $testCaseParams->getResultMeta(),
                $result->getMeta()->all()
            );
        }

        $this->assertServicesInitialized($testCaseParams);
    }

    /**
     * @param TestCaseParams $testCaseParams
     */
    private function assertServicesInitialized(TestCaseParams $testCaseParams): void
    {
        if (!is_null($testCaseParams->getServices())) {
            $definedServices = array_keys($testCaseParams->getServices());
            $initializedServices = [];
            foreach ($testCaseParams->getServices() as $serviceId => $class) {
                if ($this->getContainer()->initialized($serviceId)) {
                    $initializedServices[] = $serviceId;
                }
            }
            $notUsedServices = implode('`, `', array_diff($definedServices, $initializedServices));
            $this->assertEquals(
                $initializedServices,
                $definedServices,
                "Services `$notUsedServices` not used."
            );
        }
        if (!is_null($testCaseParams->getFactories())) {
            $definedFactories = array_keys($testCaseParams->getFactories());
            $initializedFactories = [];
            foreach ($testCaseParams->getFactories() as $serviceId => $class) {
                if ($this->getContainer()->initialized($serviceId)) {
                    $initializedFactories[] = $serviceId;
                }
            }
            $notUsedServices = implode('`, `', array_diff($definedFactories, $initializedFactories));
            $this->assertEquals(
                $initializedFactories,
                $definedFactories,
                "Services factory `$notUsedServices` not used."
            );
        }
    }

    /**
     * @param TestCaseParams $testCaseParams
     */
    protected function registerServices(TestCaseParams $testCaseParams): void
    {
        if (!is_null($testCaseParams->getServices())) {
            foreach ($testCaseParams->getServices() as $serviceId => $class) {
                $this->getContainer()->register($serviceId, $class);
            }
        }
        if (!is_null($testCaseParams->getFactories())) {
            foreach ($testCaseParams->getFactories() as $serviceId => $factory) {
                $this->getContainer()->register($serviceId)->setFactory($factory);
            }
        }
    }

    /**
     * @param TestCaseParams $testCaseParams
     * @return MockAuthUser
     */
    protected function getMockAuthUser(TestCaseParams $testCaseParams): MockAuthUser
    {
        $authUser = $this->getMockBuilder(MockAuthUser::class)
            ->onlyMethods(['getUserId', 'getEmpNumber', 'getUserRoleName'])
            ->disableOriginalConstructor()
            ->getMock();
        $authUser->method('getUserId')
            ->willReturn($testCaseParams->getUserId());
        /** @var User $user */
        $user = $this->getEntityReference(
            User::class,
            $testCaseParams->getUserId()
        );
        $authUser->method('getEmpNumber')
            ->willReturn($user->getEmployee()->getEmpNumber());
        $authUser->method('getUserRoleName')
            ->willReturn($user->getUserRole()->getName());
        return $authUser;
    }

    /**
     * @param TestCaseParams $testCaseParams
     */
    protected function registerMockDateTimeHelper(TestCaseParams $testCaseParams): void
    {
        if (!is_null($testCaseParams->getNow())) {
            $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
                ->onlyMethods(['getNow'])
                ->getMock();
            $dateTimeHelper->expects($this->atLeastOnce())
                ->method('getNow')
                ->willReturnCallback(fn () => clone $testCaseParams->getNow());

            $this->getContainer()->set(Services::DATETIME_HELPER_SERVICE, $dateTimeHelper);
        }
    }
}
