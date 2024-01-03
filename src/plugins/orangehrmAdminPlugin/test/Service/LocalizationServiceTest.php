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

namespace OrangeHRM\Tests\Admin\Service;

use DateTime;
use Exception;
use OrangeHRM\Admin\Controller\File\LanguagePackage;
use OrangeHRM\Admin\Dao\LocalizationDao;
use OrangeHRM\Admin\Dto\I18NGroupSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Service\LocalizationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class LocalizationServiceTest extends KernelTestCase
{
    private LocalizationService $localizationService;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->localizationService = new LocalizationService();

        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmAdminPlugin/test/fixtures/I18NTranslationExport.yml';
        TestDataService::populate($fixture);
    }

    public function testGetLocalizationDateFormats(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturnCallback(fn () => new DateTime('2022-06-05'));

        $this->createKernelWithMockServices([Services::DATETIME_HELPER_SERVICE => $dateTimeHelper]);
        $formats = $this->localizationService->getLocalizationDateFormats();
        $this->assertCount(11, $formats);
        $this->assertEquals('Y-m-d', $formats[0]['id']);
        $this->assertEquals('yyyy-mm-dd ( 2022-06-05 )', $formats[0]['label']);
        $this->assertEquals('l, d-M-Y', $formats[9]['id']);
        $this->assertEquals('DD, dd-M-yyyy ( Sunday, 05-Jun-2022 )', $formats[9]['label']);
        $this->assertEquals('D, d M Y', $formats[10]['id']);
        $this->assertEquals('D, dd M yyyy ( Sun, 05 Jun 2022 )', $formats[10]['label']);
    }

    public function testGetSupportedLanguages(): void
    {
        $expectedResult = ['A', 5, '%'];
        $this->localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getLanguagesArray'])
            ->getMock();
        $this->localizationService->expects($this->once())
            ->method('getLanguagesArray')
            ->will($this->returnValue($expectedResult));
        $this->assertEquals($expectedResult, $this->localizationService->getSupportedLanguages());
    }

    public function testSearchLanguages(): void
    {
        $expectedArray = ['A', 5, '%'];
        $i18nLanguageFilterParams = new I18NLanguageSearchFilterParams();
        $localizationDao = $this->getMockBuilder(LocalizationDao::class)->getMock();
        $localizationDao->expects($this->once())
            ->method('searchLanguages')
            ->with($i18nLanguageFilterParams)
            ->will($this->returnValue($expectedArray));
        $localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getLocalizationDao'])
            ->getMock();
        $localizationService->expects($this->once())
            ->method('getLocalizationDao')
            ->willReturn($localizationDao);
        $result = $localizationService->searchLanguages($i18nLanguageFilterParams);
        $this->assertEquals($expectedArray, $result);
    }

    public function testGetCountryArray(): void
    {
        $language = new I18NLanguage();
        $language->setName('Valerian');
        $language->setCode('VLR');

        $localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getNormalizerService', 'searchLanguages'])
            ->getMock();

        $i18nLanguageFilterParams = new I18NLanguageSearchFilterParams();
        $localizationService->expects($this->once())
            ->method('searchLanguages')
            ->with($i18nLanguageFilterParams)
            ->will($this->returnValue([$language]));
        $localizationService->expects($this->once())
            ->method('getNormalizerService')
            ->will($this->returnValue(new NormalizerService()));

        $languages = $localizationService->getLanguagesArray($i18nLanguageFilterParams);
        $this->assertCount(1, $languages);
        $this->assertEquals('Valerian', $languages[0]['label']);
        $this->assertEquals('VLR', $languages[0]['id']);
    }

    public function testGenerateLangStringLanguageKey(): void
    {
        $this->assertEquals('1_2_', $this->localizationService->generateLangStringLanguageKey(1, 2));
    }

    public function testExportLanguagePackage(): void
    {
        $i18NGroupSearchFilterParams = new I18NGroupSearchFilterParams();
        $groups = $this->localizationService->getLocalizationDao()->searchGroups($i18NGroupSearchFilterParams);
        $this->assertCount(3, $groups);
    }

    public function testGetXliffXmlSources(): void
    {
        $this->createKernelWithMockServices([
            Services::LOCALIZATION_SERVICE => new LocalizationService(),
        ]);
        $controller = new LanguagePackage();
        $request = $this->getHttpRequest([], [], ['languageId' => '1']);
        $response =  $controller->handle($request);

        $xml =  simplexml_load_string($response->getContent());
        $json = json_encode($xml);
        $result = json_decode($json, true);

        $this->assertEquals('application/xml', $response->headers->get('content-type'));
        $this->assertEquals('2.0', $result['@attributes']['version']);
        $this->assertCount(3, $result['file']['group']);
        $this->assertEquals('Add Job Title', $result['file']['group'][0]['unit'][0]['segment']['source']);
        $this->assertEquals('使用 SMTP 验证', $result['file']['group'][0]['unit'][1]['segment']['target']);
        $this->assertCount(2, $result['file']['group'][0]['unit'][1]);
        $this->assertEquals('([{\|/?!~#@$%^&amp;*)-=_+;&quot;&gt;&lt;}]', $result['file']['group'][2]['unit']['segment']['target']);

        $request = $this->getHttpRequest([], [], ['languageId' => '3']);
        $response =  $controller->handle($request);

        $xml =  simplexml_load_string($response->getContent());
        $json = json_encode($xml);
        $result = json_decode($json, true);
        $this->assertCount(3, $result['file']['group']);
        $this->assertEquals('Edit Subscriber', $result['file']['group'][1]['unit'][1]['segment']['source']);
        $this->assertEquals('රැකියා මාතෘකාව එක් කරන්න', $result['file']['group'][0]['unit'][0]['segment']['target']);
        $this->assertEquals('SMTP සත්‍යාපනය භාවිතා කරන්න', $result['file']['group'][0]['unit'][1]['segment']['target']);
        $this->assertCount(2, $result['file']['group'][0]['unit'][1]);

        $request = $this->getHttpRequest([], [], ['languageId' => '22']);
        $response =  $controller->handle($request);

        $xml =  simplexml_load_string($response->getContent());
        $json = json_encode($xml);
        $result = json_decode($json, true);

        $this->assertCount(3, $result['file']['group']);
        $this->assertEquals('Add Job Title', $result['file']['group'][0]['unit'][0]['segment']['source']);
        $this->assertEquals('حول', $result['file']['group'][1]['unit'][0]['segment']['target']);
        $this->assertCount(2, $result['file']['group'][1]['unit'][1]);
    }
}
