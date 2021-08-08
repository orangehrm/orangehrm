<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace Api;

use OrangeHRM\Admin\Api\LocalizationAPI;
use OrangeHRM\Admin\Service\LocalizationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Api\CommonParams;
use OrangeHRM\Core\Api\V2\RequestParams;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Tests\Util\EndpointTestCase;
use OrangeHRM\Tests\Util\MockObject;
use Symfony\Component\Yaml\Yaml;

/**
 * @group Admin
 * @group APIv2
 */
class LocalizationAPITest extends EndpointTestCase
{
    public function testGetOne(): void
    {
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods([
                'getAdminLocalizationUseBrowserLanguage',
                'getAdminLocalizationDefaultLanguage',
                'getAdminLocalizationDefaultDateFormat'
            ])
            ->getMock();
        $configService->expects($this->once())
            ->method('getAdminLocalizationUseBrowserLanguage')
            ->will($this->returnValue('Yes'));
        $configService->expects($this->once())
            ->method('getAdminLocalizationDefaultLanguage')
            ->will($this->returnValue('fr'));
        $configService->expects($this->once())
            ->method('getAdminLocalizationDefaultDateFormat')
            ->will($this->returnValue('Y/m/d'));

        /** @var MockObject&LocalizationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LocalizationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 0
                ]
            ]
        )->onlyMethods(['getConfigService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getConfigService')
            ->will($this->returnValue($configService));

        $result = $api->getOne();
        $this->assertEquals(
            [
                "defaultLanguage" => 'fr',
                "defaultDateFormat" => 'Y/m/d',
                "useBrowserLanguage" => true
            ],
            $result->normalize()
        );
    }

    public function testGetValidationRuleForGetOne(): void
    {
        $api = new LocalizationAPI($this->getRequest());
        $rules = $api->getValidationRuleForGetOne();
        $this->assertTrue(
            $this->validate(
                [CommonParams::PARAMETER_ID => 0],
                $rules
            )
        );
    }

    public function testUpdate(): void
    {
        $language = 'de';
        $dateFormat = 'm-d-Y';
        $configService = $this->getMockBuilder(ConfigService::class)
            ->onlyMethods([
                'setAdminLocalizationDefaultDateFormat',
                'setAdminLocalizationDefaultLanguage',
                'setAdminLocalizationUseBrowserLanguage'
            ])
            ->getMock();
        $configService->expects($this->once())
            ->method('setAdminLocalizationDefaultDateFormat')
            ->with($dateFormat);
        $configService->expects($this->once())
            ->method('setAdminLocalizationDefaultLanguage')
            ->with($language);
        $configService->expects($this->once())
            ->method('setAdminLocalizationUseBrowserLanguage')
            ->with('Yes');

        $localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getSupportedLanguages'])
            ->getMock();
        $localizationService->expects($this->once())
            ->method('getSupportedLanguages')
            ->will($this->returnValue([['id' => 'fr'], ['id' => 'de']]));
        /** @var MockObject&LocalizationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LocalizationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 0
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    LocalizationAPI::PARAMETER_LANGUAGE => 'fr',
                    LocalizationAPI::PARAMETER_DATE_FORMAT => $dateFormat,
                    LocalizationAPI::PARAMETER_BROWSER_LANGUAGE => $language,
                    LocalizationAPI::PARAMETER_USE_BROWSER_LANGUAGE => true,
                ]
            ]
        )->onlyMethods(['getConfigService', 'getLocalizationService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getConfigService')
            ->will($this->returnValue($configService));
        $api->expects($this->once())
            ->method('getLocalizationService')
            ->will($this->returnValue($localizationService));
        $result = $api->update();
        $this->assertEquals(
            [
                "defaultLanguage" => $language,
                "defaultDateFormat" => $dateFormat,
                "useBrowserLanguage" => true
            ],
            $result->normalize()
        );
    }

    /**
     * @param $parameters
     * @param $isValid
     * @dataProvider getValidationRuleForUpdateDataProvider
     */
    public function testGetValidationRuleForUpdate($parameters, $isValid): void
    {
        $localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getSupportedLanguages'])
            ->getMock();
        $localizationService->expects($this->once())
            ->method('getSupportedLanguages')
            ->will($this->returnValue([
                ['id' => 'fr'],
                ['id' => 'de'],
                ['id' => 'nl'],
                ['id' => 'es'],
                ['id' => 'en_US']
            ]));
        /** @var MockObject&LocalizationAPI $api */
        $api = $this->getApiEndpointMockBuilder(
            LocalizationAPI::class,
            [
                RequestParams::PARAM_TYPE_ATTRIBUTE => [
                    CommonParams::PARAMETER_ID => 0
                ],
                RequestParams::PARAM_TYPE_BODY => [
                    $parameters
                ]
            ]
        )->onlyMethods(['getLocalizationService'])
            ->getMock();
        $api->expects($this->once())
            ->method('getLocalizationService')
            ->will($this->returnValue($localizationService));

        if (!$isValid) {
            $this->expectInvalidParamException();
        }
        $rules = $api->getValidationRuleForUpdate();
        $result = $this->validate($parameters, $rules);
        if ($isValid) {
            $this->assertTrue($result);
        }
    }

    public function testAll(): void
    {
        $api = new LocalizationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getAll();
    }

    public function testGetValidationRuleForAll(): void
    {
        $api = new LocalizationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForGetAll();
    }

    public function testCreate(): void
    {
        $api = new LocalizationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->create();
    }

    public function testGetValidationRuleForCreate(): void
    {
        $api = new LocalizationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForCreate();
    }

    public function testDelete(): void
    {
        $api = new LocalizationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->delete();
    }

    public function testGetValidationRuleForDelete(): void
    {
        $api = new LocalizationAPI($this->getRequest());
        $this->expectNotImplementedException();
        $api->getValidationRuleForDelete();
    }

    public function getTestCases($key)
    {
        $testCases = Yaml::parseFile(Config::get(Config::PLUGINS_DIR) .
            '/orangehrmAdminPlugin/test/testCases/LocalizationAPITestCases.yml');
        return $testCases[$key];
    }

    public function getValidationRuleForUpdateDataProvider()
    {
        return $this->getTestCases('getValidationRuleForUpdate');
    }
}
