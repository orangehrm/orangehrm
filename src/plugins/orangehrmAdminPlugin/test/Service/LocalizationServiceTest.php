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
use OrangeHRM\Admin\Dto\I18NImportErrorSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Exception\XliffFileProcessFailedException;
use OrangeHRM\Admin\Service\LocalizationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dto\Base64Attachment;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\NormalizerService;
use OrangeHRM\Entity\I18NError;
use OrangeHRM\Entity\I18NImportError;
use OrangeHRM\Entity\I18NLangString;
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
        $response = $controller->handle($request);

        $xml = simplexml_load_string($response->getContent());
        $json = json_encode($xml);
        $result = json_decode($json, true);

        $this->assertEquals('application/xliff+xml', $response->headers->get('content-type'));
        $this->assertEquals('2.0', $result['@attributes']['version']);
        $this->assertCount(3, $result['file']['group']);
        $this->assertEquals('Add Job Title', $result['file']['group'][0]['unit'][0]['segment']['source']);
        $this->assertEquals('使用 SMTP 验证', $result['file']['group'][0]['unit'][1]['segment']['target']);
        $this->assertCount(2, $result['file']['group'][0]['unit'][1]);
        $this->assertEquals(
            '([{\|/?!~#@$%^&*)-=_+;"><}]',
            $result['file']['group'][2]['unit']['segment']['target']
        );

        $request = $this->getHttpRequest([], [], ['languageId' => '3']);
        $response = $controller->handle($request);

        $xml = simplexml_load_string($response->getContent());
        $json = json_encode($xml);
        $result = json_decode($json, true);
        $this->assertCount(3, $result['file']['group']);
        $this->assertEquals('Edit Subscriber', $result['file']['group'][1]['unit'][1]['segment']['source']);
        $this->assertEquals('රැකියා මාතෘකාව එක් කරන්න', $result['file']['group'][0]['unit'][0]['segment']['target']);
        $this->assertEquals('SMTP සත්‍යාපනය භාවිතා කරන්න', $result['file']['group'][0]['unit'][1]['segment']['target']);
        $this->assertCount(2, $result['file']['group'][0]['unit'][1]);

        $request = $this->getHttpRequest([], [], ['languageId' => '22']);
        $response = $controller->handle($request);

        $xml = simplexml_load_string($response->getContent());
        $json = json_encode($xml);
        $result = json_decode($json, true);

        $this->assertCount(3, $result['file']['group']);
        $this->assertEquals('Add Job Title', $result['file']['group'][0]['unit'][0]['segment']['source']);
        $this->assertEquals('حول', $result['file']['group'][1]['unit'][0]['segment']['target']);
        $this->assertCount(2, $result['file']['group'][1]['unit'][1]);
    }

    private function getMockLocalizationServiceForInvalidSyntaxTest(): LocalizationService
    {
        $localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getI18NErrorByName'])
            ->getMock();

        $invalidSyntaxError = new I18NError();
        $invalidSyntaxError->setName(I18NError::INVALID_SYNTAX);

        $localizationService->expects($this->once())
            ->method('getI18NErrorByName')
            ->with(I18NError::INVALID_SYNTAX)
            ->willReturn($invalidSyntaxError);

        return $localizationService;
    }

    public function testValidateTargetStringWithInvalidSyntaxString(): void
    {
        $localizationService = $this->getMockLocalizationServiceForInvalidSyntaxTest();

        $result = $localizationService->validateTargetString(1, 'Test {');

        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::INVALID_SYNTAX, $result->getName());
    }

    private function getMockLocalizationServiceForValidateLangStringTesting(string $langStringValue, ?string $errorName = null): LocalizationService
    {
        $testLangString = new I18NLangString();
        $testLangString->setValue($langStringValue);
        $testLangString->setId(1);

        $localizationDao = $this->getMockBuilder(LocalizationDao::class)
            ->onlyMethods(['getLangStringById'])
            ->getMock();

        $localizationDao->expects($this->exactly(1))
            ->method('getLangStringById')
            ->with(1)
            ->willReturn($testLangString);

        $localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getLocalizationDao', 'getI18NErrorByName'])
            ->getMock();

        $localizationService->expects($this->exactly(1))
            ->method('getLocalizationDao')
            ->willReturn($localizationDao);

        if ($errorName) {
            $error = new I18NError();
            $error->setName($errorName);

            $localizationService->expects($this->once())
                ->method('getI18NErrorByName')
                ->with($errorName)
                ->willReturn($error);
        }

        return $localizationService;
    }

    public function testValidateTargetStringWithoutPlaceholders(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting('Test String');

        $result = $localizationService->validateTargetString(1, 'Test');
        $this->assertNull($result);
    }

    public function testValidateTargetStringWithoutPlaceholdersError(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting(
            'Test String',
            I18NError::PLACEHOLDER_MISMATCH
        );

        $result = $localizationService->validateTargetString(1, 'Test {param}');
        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::PLACEHOLDER_MISMATCH, $result->getName());
    }

    public function testValidateTargetStringWithPlaceholders(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting(
            'Test {with} {parameters}',
        );

        $result = $localizationService->validateTargetString(1, 'String {with} {parameters}');
        $this->assertNull($result);
    }

    public function testValidateTargetStringWithPlaceholdersInDifferentOrder(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting(
            'Test {with} {parameters}',
        );

        $result = $localizationService->validateTargetString(1, '{parameters} {with} String');
        $this->assertNull($result);
    }

    public function testValidateTargetStringWithDifferentPlaceholders(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting(
            'Test {with} {parameters}',
            I18NError::PLACEHOLDER_MISMATCH
        );

        $result = $localizationService->validateTargetString(1, 'String {different} {params}');
        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::PLACEHOLDER_MISMATCH, $result->getName());
    }

    public function testValidateTargetStringWithPluralString(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting(
            '{count,plural, =0{zero} one{single} other{several}}'
        );

        $result = $localizationService->validateTargetString(1, '{count,plural, =0{none} one{solo} other{multiple}}');
        $this->assertNull($result);
    }

    public function testValidateTargetStringWithDifferentPluralCategories(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting(
            '{count,plural, =0{zero} one{single} other{several}}'
        );

        $result = $localizationService->validateTargetString(1, '{count,plural, =0{none} one{solo} two{double} other{multiple}}');
        $this->assertNull($result);
    }

    public function testValidateTargetStringWithoutOtherPluralCategory(): void
    {
        $localizationService = $this->getMockLocalizationServiceForInvalidSyntaxTest();

        $result = $localizationService->validateTargetString(1, '{count,plural, =0{zero} one{single}}');
        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::INVALID_SYNTAX, $result->getName());
    }

    public function testValidateTargetStringWithMissingPluralVariable(): void
    {
        $localizationService = $this->getMockLocalizationServiceForInvalidSyntaxTest();

        $result = $localizationService->validateTargetString(1, '{plural, =0{zero} one{single} other{several}}');
        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::INVALID_SYNTAX, $result->getName());
    }

    public function testValidateTargetStringWithDifferentPluralVariable(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting(
            '{count,plural, =0{zero} one{single} other{several}}',
            I18NError::PLURAL_MISMATCH
        );

        $result = $localizationService->validateTargetString(1, '{newCount,plural, =0{zero} one{single} other{several}}');
        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::PLURAL_MISMATCH, $result->getName());
    }

    public function testValidateTargetStringWithSelectVariable(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting(
            '{action,select, Approve{Request Approved} Reject{Request Rejected} other {Request Pending}}'
        );

        $result = $localizationService->validateTargetString(1, '{action,select, Approve{Approved} Reject{Rejected} other{Pending}}');
        $this->assertNull($result);
    }

    public function testValidateTargetStringWithMissingSelectOtherCategory(): void
    {
        $localizationService = $this->getMockLocalizationServiceForInvalidSyntaxTest();

        $result = $localizationService->validateTargetString(1, '{action,select, Approve{Approved} Reject{Rejected}');
        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::INVALID_SYNTAX, $result->getName());
    }

    public function testValidateTargetStringWithMissingSelectVariable(): void
    {
        $localizationService = $this->getMockLocalizationServiceForInvalidSyntaxTest();

        $result = $localizationService->validateTargetString(1, '{select, Approve{Approved} Reject{Rejected} other{Pending}');
        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::INVALID_SYNTAX, $result->getName());
    }

    public function testValidateTargetStringWithDifferentSelectVariable(): void
    {
        $localizationService = $this->getMockLocalizationServiceForValidateLangStringTesting(
            '{action,select, Approve{Request Approved} Reject{Request Rejected} other {Request Pending}}',
            I18NError::SELECT_MISMATCH
        );

        $result = $localizationService->validateTargetString(1, '{newAction,select, Approve{Approved} Reject{Rejected} other{Pending}}');
        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::SELECT_MISMATCH, $result->getName());
    }

    public function testGetI18NErrorByName(): void
    {
        $localizationDao = $this->getMockBuilder(LocalizationDao::class)
            ->onlyMethods(['getI18NErrorByName'])
            ->getMock();

        $invalidSyntaxError = new I18NError();
        $invalidSyntaxError->setName(I18NError::INVALID_SYNTAX);

        $localizationDao->expects($this->once())
            ->method('getI18NErrorByName')
            ->with(I18NError::INVALID_SYNTAX)
            ->willReturn($invalidSyntaxError);

        $localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getLocalizationDao'])
            ->getMock();

        $localizationService->expects($this->once())
            ->method('getLocalizationDao')
            ->willReturn($localizationDao);

        $result = $localizationService->getI18NErrorByName(I18NError::INVALID_SYNTAX);
        $this->assertInstanceOf(I18NError::class, $result);
        $this->assertEquals(I18NError::INVALID_SYNTAX, $result->getName());
    }

    public function testGetI18NErrorWithInvalidErrorName(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Should be a valid I18N error');

        $this->localizationService->getI18NErrorByName('Custom Error');
    }

    public function testLanguageHasImportErrors(): void
    {
        $searchFilterParams = new I18NImportErrorSearchFilterParams();
        $searchFilterParams->setLanguageId(1);
        $searchFilterParams->setEmpNumber(1);

        $localizationDao = $this->getMockBuilder(LocalizationDao::class)
            ->onlyMethods(['getImportErrorCount'])
            ->getMock();

        $localizationDao->expects($this->once())
            ->method('getImportErrorCount')
            ->with($searchFilterParams)
            ->willReturn(2);

        $localizationService = $this->getMockBuilder(LocalizationService::class)
            ->onlyMethods(['getLocalizationDao'])
            ->getMock();

        $localizationService->expects($this->once())
            ->method('getLocalizationDao')
            ->willReturn($localizationDao);

        $result = $localizationService->languageHasImportErrors(1, 1);
        $this->assertTrue($result);
    }

    public function testClearImportErrorsForLangString(): void
    {
        $rows = [
            ['langStringId' => 1, 'translatedValue' => 'Value'],
            ['langStringId' => 2, 'translatedValue' => 'Value'],
            ['langStringId' => 3, 'translatedValue' => 'Value']
        ];

        $importErrorRepository = $this->getEntityManager()->getRepository(I18NImportError::class);
        $this->assertCount(3, $importErrorRepository->findAll());

        $this->localizationService->clearImportErrorsForLangStrings(1, $rows);
        $this->assertCount(0, $importErrorRepository->findAll());
    }

    private function getBase64AttachmentForTestingProcessXliff(string $content): Base64Attachment
    {
        return new Base64Attachment(
            "test.xlf",
            "application/xliff+xml",
            base64_encode($content),
            0
        );
    }

    public function testProcessXliffFileWithEmptyContent(): void
    {
        $this->expectException(XliffFileProcessFailedException::class);
        $this->expectExceptionMessage(XliffFileProcessFailedException::emptyFile()->getMessage());

        $attachment = $this->getBase64AttachmentForTestingProcessXliff("");

        $this->localizationService->processXliffFile($attachment, 1);
    }

    public function testProcessXliffFileWithInvalidContent(): void
    {
        $xliffContent = '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" version="2.0" srcLang="en_US" trgLang="zh_Hans_CN">
  <file id="1"
    <group id="general">
      <unit id="add_job_title">
        <segment>
          <source>Add Job Title</source>
          <target></target>
        </segment>
      </unit>
      <unit id="use_smtp_authentication">
        <segment>
          <source>Use SMTP Authentication</source>
          <target></target>
        </segment>
      </unit>
    </group>
  </file>
</xliff>';

        $attachment = $this->getBase64AttachmentForTestingProcessXliff($xliffContent);

        $this->expectException(XliffFileProcessFailedException::class);
        $this->expectExceptionMessage(XliffFileProcessFailedException::validationFailed()->getMessage());

        $this->localizationService->processXliffFile($attachment, 1);
    }

    public function testProcessXliffFileWithInvalidVersion(): void
    {
        $xliffContent = '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" version="sadasd" srcLang="en_US" trgLang="zh_Hans_CN">
  <file id="1"
    <group id="general">
      <unit id="add_job_title">
        <segment>
          <source>Add Job Title</source>
          <target></target>
        </segment>
      </unit>
      <unit id="use_smtp_authentication">
        <segment>
          <source>Use SMTP Authentication</source>
          <target></target>
        </segment>
      </unit>
    </group>
  </file>
</xliff>';

        $attachment = $this->getBase64AttachmentForTestingProcessXliff($xliffContent);

        $this->expectException(XliffFileProcessFailedException::class);
        $this->expectExceptionMessage(XliffFileProcessFailedException::validationFailed()->getMessage());

        $this->localizationService->processXliffFile($attachment, 1);
    }

    public function testProcessXliffFileWithMissingVersion(): void
    {
        $xliffContent = '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" srcLang="en_US" trgLang="zh_Hans_CN">
  <file id="1"
    <group id="general">
      <unit id="add_job_title">
        <segment>
          <source>Add Job Title</source>
          <target></target>
        </segment>
      </unit>
      <unit id="use_smtp_authentication">
        <segment>
          <source>Use SMTP Authentication</source>
          <target></target>
        </segment>
      </unit>
    </group>
  </file>
</xliff>';

        $attachment = $this->getBase64AttachmentForTestingProcessXliff($xliffContent);

        $this->expectException(XliffFileProcessFailedException::class);
        $this->expectExceptionMessage(XliffFileProcessFailedException::validationFailed()->getMessage());

        $this->localizationService->processXliffFile($attachment, 1);
    }

    public function testProcessXliffFileWithMissingSourceLanguage(): void
    {
        $xliffContent = '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" version="2.0" trgLang="zh_Hans_CN">
  <file id="1">
    <group id="general">
      <unit id="add_job_title">
        <segment>
          <source>Add Job Title</source>
          <target></target>
        </segment>
      </unit>
      <unit id="use_smtp_authentication">
        <segment>
          <source>Use SMTP Authentication</source>
          <target></target>
        </segment>
      </unit>
    </group>
  </file>
</xliff>';

        $attachment = $this->getBase64AttachmentForTestingProcessXliff($xliffContent);

        $this->expectException(XliffFileProcessFailedException::class);
        $this->expectExceptionMessage(XliffFileProcessFailedException::validationFailed()->getMessage());

        $this->localizationService->processXliffFile($attachment, 1);
    }

    public function testProcessXliffFileWithMissingTargetLanguage(): void
    {
        $xliffContent = '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" version="2.0" srcLang="en_US">
  <file id="1">
    <group id="general">
      <unit id="add_job_title">
        <segment>
          <source>Add Job Title</source>
          <target></target>
        </segment>
      </unit>
      <unit id="use_smtp_authentication">
        <segment>
          <source>Use SMTP Authentication</source>
          <target></target>
        </segment>
      </unit>
    </group>
  </file>
</xliff>';

        $attachment = $this->getBase64AttachmentForTestingProcessXliff($xliffContent);

        $this->expectException(XliffFileProcessFailedException::class);
        $this->expectExceptionMessage(XliffFileProcessFailedException::missingTargetLanguage()->getMessage());

        $this->localizationService->processXliffFile($attachment, 1);
    }

    public function testProcessXliffFileWithInvalidTargetLanguageForLanguageId(): void
    {
        $xliffContent = '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" version="2.0" srcLang="en_US" trgLang="si_LK">
  <file id="1">
    <group id="general">
      <unit id="add_job_title">
        <segment>
          <source>Add Job Title</source>
          <target></target>
        </segment>
      </unit>
      <unit id="use_smtp_authentication">
        <segment>
          <source>Use SMTP Authentication</source>
          <target></target>
        </segment>
      </unit>
    </group>
  </file>
</xliff>';

        $attachment = $this->getBase64AttachmentForTestingProcessXliff($xliffContent);

        $this->expectException(XliffFileProcessFailedException::class);
        $this->expectExceptionMessage(XliffFileProcessFailedException::invalidTargetLanguage()->getMessage());

        $this->localizationService->processXliffFile($attachment, 1);
    }

    public function testProcessXliffFileWithEmptyTargetString(): void
    {
        $xliffContent = '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" version="2.0" srcLang="en_US" trgLang="zh_Hans_CN">
  <file id="1">
    <group id="general">
      <unit id="add_job_title">
        <segment>
          <source>Add Job Title</source>
          <target></target>
        </segment>
      </unit>
      <unit id="use_smtp_authentication">
        <segment>
          <source>Use SMTP Authentication</source>
          <target></target>
        </segment>
      </unit>
    </group>
  </file>
</xliff>';

        $attachment = $this->getBase64AttachmentForTestingProcessXliff($xliffContent);

        $result = $this->localizationService->processXliffFile($attachment, 1);
        $this->assertCount(3, $result);

        list($validLangStrings, $invalidLangStrings, $skippedLangStrings) = $result;
        $this->assertEmpty($validLangStrings);
        $this->assertEmpty($invalidLangStrings);

        $this->assertCount(2, $skippedLangStrings);
        $expectedSkippedStrings = [
            ['langStringId' => 1, 'unitId' => 'add_job_title'],
            ['langStringId' => 2, 'unitId' => 'use_smtp_authentication']
        ];
        $this->assertEquals($expectedSkippedStrings, $skippedLangStrings);
    }

    public function testProcessXliffFileWithValidAndInvalidTargetStrings(): void
    {
        $xliffContent = '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" version="2.0" srcLang="en_US" trgLang="zh_Hans_CN">
  <file id="1">
    <group id="general">
      <unit id="add_job_title">
        <segment>
          <source>Add Job Title</source>
          <target>Add Job Title</target>
        </segment>
      </unit>
      <unit id="use_smtp_authentication">
        <segment>
          <source>Use SMTP Authentication</source>
          <target>Use {</target>
        </segment>
      </unit>
    </group>
  </file>
</xliff>';

        $attachment = $this->getBase64AttachmentForTestingProcessXliff($xliffContent);

        $result = $this->localizationService->processXliffFile($attachment, 1);
        $this->assertCount(3, $result);

        list($validLangStrings, $invalidLangStrings, $skippedLangStrings) = $result;
        $this->assertEmpty($skippedLangStrings);

        $this->assertCount(1, $validLangStrings);
        $expectedValidLangString = [
            ['langStringId' => 1, 'translatedValue' => 'Add Job Title']
        ];
        $this->assertEquals($expectedValidLangString, $validLangStrings);

        $this->assertCount(1, $invalidLangStrings);
        $expectedInvalidLangStrings = [
            ['langStringId' => 2, 'errorName' => I18NError::INVALID_SYNTAX]
        ];
        $this->assertEquals($expectedInvalidLangStrings, $invalidLangStrings);
    }

    public function testProcessXliffFileWithInvalidLangStrings(): void
    {
        $xliffContent = '<?xml version="1.0" encoding="UTF-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:2.0" version="2.0" srcLang="en_US" trgLang="zh_Hans_CN">
  <file id="1">
    <group id="general">
      <unit id="add_job_title">
        <segment>
          <source>Add Job Title</source>
          <target>Add {Job} Title</target>
        </segment>
      </unit>
      <unit id="use_smtp_authentication">
        <segment>
          <source>Use SMTP Authentication</source>
          <target>Use {</target>
        </segment>
      </unit>
    </group>
  </file>
</xliff>';

        $attachment = $this->getBase64AttachmentForTestingProcessXliff($xliffContent);

        $result = $this->localizationService->processXliffFile($attachment, 1);
        $this->assertCount(3, $result);

        list($validLangStrings, $invalidLangStrings, $skippedLangStrings) = $result;
        $this->assertEmpty($validLangStrings);
        $this->assertEmpty($skippedLangStrings);

        $this->assertCount(2, $invalidLangStrings);
        $expectedInvalidLangString = [
            ['langStringId' => 1, 'errorName' => I18NError::PLACEHOLDER_MISMATCH],
            ['langStringId' => 2, 'errorName' => I18NError::INVALID_SYNTAX]
        ];
        $this->assertEquals($expectedInvalidLangString, $invalidLangStrings);
    }
}
