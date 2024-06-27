<?php

namespace OrangeHRM\Tests\Admin\Api;

use OrangeHRM\Admin\Api\I18NTranslationValidationAPI;
use OrangeHRM\Entity\I18NError;
use OrangeHRM\Entity\I18NGroup;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EndpointIntegrationTestCase;
use OrangeHRM\Tests\Util\Integration\TestCaseParams;
use OrangeHRM\Tests\Util\TestDataService;

class I18NTranslationValidationAPITest extends EndpointIntegrationTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([
            I18NError::class,
            I18NLangString::class,
            I18NLanguage::class,
            I18NGroup::class
        ]);
        $this->populateFixtures('I18NTranslationValidationAPITest.yml');
    }

    /**
     * @dataProvider dataProviderForTestGetOne
     */
    public function testGetOne(TestCaseParams $testCaseParams): void
    {
        $this->createKernelWithMockServices([Services::AUTH_USER => $this->getMockAuthUser($testCaseParams)]);
        $this->registerServices($testCaseParams);
        $api = $this->getApiEndpointMock(I18NTranslationValidationAPI::class, $testCaseParams);
        $this->assertValidTestCase($api, 'getOne', $testCaseParams);
    }

    public function dataProviderForTestGetOne(): array
    {
        return $this->getTestCases('I18NTranslationValidationAPITestCases.yml', 'GetOne');
    }

    public function testUpdate(): void
    {
        $api = new I18NTranslationValidationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->update();
    }

    public function testGetValidationRuleForUpdate(): void
    {
        $api = new I18NTranslationValidationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForUpdate();
    }

    public function testDelete(): void
    {
        $api = new I18NTranslationValidationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new I18NTranslationValidationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

}
