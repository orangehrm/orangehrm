<?php

namespace OrangeHRM\Tests\Performance\Api;

use Exception;
use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Api\PerformanceTrackerReviewerAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class PerformanceTrackerReviewerTest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetAll
     *
     * @throws Exception
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('PerformanceTrackerAPITest.yml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(PerformanceTrackerReviewerAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('PerformanceTrackerReviewerAPITestCases.yaml', 'GetAll');
    }
}
