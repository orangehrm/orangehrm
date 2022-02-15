<?php

namespace OrangeHRM\Performance\test\Api;

use Exception;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Api\PerformanceTrackerAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class PerformanceTrackerAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetOne
     *
     * @throws Exception
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('PerformanceTrackerAPITestCases.yaml', 'GetOne');
    }

    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('PerformanceTrackerAPITestCases.yaml', 'GetAll');
    }

    /**
     * @dataProvider dataProviderForTestCreate
     */
    public function testCreate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'create', $testCaseParams);
    }

    public function dataProviderForTestCreate(): array
    {
        return $this->getTestCases('PerformanceTrackerAPITestCases.yaml', 'Create');
    }

    /**
     * @dataProvider dataProviderForTestUpdate
     */
    public function testUpdate(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'update', $testCaseParams);
    }

    public function dataProviderForTestUpdate(): array
    {
        return $this->getTestCases('PerformanceTrackerAPITestCases.yaml', 'Update');
    }

    /**
     * @dataProvider dataProviderForTestDelete
     */
    public function testDelete(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'delete', $testCaseParams);
    }

    public function dataProviderForTestDelete(): array
    {
        return $this->getTestCases('PerformanceTrackerAPITestCases.yaml', 'Delete');
    }
}
