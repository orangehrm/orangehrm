<?php

namespace OrangeHRM\Tests\Performance\Api;

use OrangeHRM\Framework\Services;
use OrangeHRM\Performance\Api\ReviewEmployeeSupervisorAPI;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;

class ReviewEmployeeSupervisorAPITest extends EndpointIntegrationTestCase
{
    /**
     * @dataProvider dataProviderForTestGetAll
     *
     * @throws Exception
     */
    public function testGetAll(TestCaseParams $testCaseParams): void
    {
        $this->populateFixtures('ReviewEmployeeSupervisorAPITest.yaml');
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $this->registerMockDateTimeHelper($testCaseParams);
        $api = $this->getApiEndpointMock(ReviewEmployeeSupervisorAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getAll', $testCaseParams);
    }

    public function dataProviderForTestGetAll(): array
    {
        return $this->getTestCases('ReviewEmployeeSupervisorAPITestCases.yaml', 'GetAll');
    }

    public function testCreate(): void
    {
        $api = new ReviewEmployeeSupervisorAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new ReviewEmployeeSupervisorAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testDelete(): void
    {
        $api = new ReviewEmployeeSupervisorAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new ReviewEmployeeSupervisorAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function testGetOne(): void
    {
        $api = new ReviewEmployeeSupervisorAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getOne();
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new ReviewEmployeeSupervisorAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetOne();
    }

    public function testUpdate(): void
    {
        $api = new ReviewEmployeeSupervisorAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new ReviewEmployeeSupervisorAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }
}
