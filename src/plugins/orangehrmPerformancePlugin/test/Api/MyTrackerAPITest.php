<?php

namespace OrangeHRM\Tests\Performance\Api;

use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Api\MyTrackerAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class MyTrackerAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetAll
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('MyTrackerAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(MyTrackerAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    /**
     * @return array
     */
    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('MyTrackerAPITestCases.yaml', 'GetAll');
    }
}
