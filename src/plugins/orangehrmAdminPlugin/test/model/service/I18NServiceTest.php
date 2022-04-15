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

/**
 * @group Admin
 * @group I18N
 */
class I18NServiceTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var I18NService|null
     */
    private $i18nService = null;

    protected function setUp(): void
    {
        $this->i18nService = new I18NService();
    }

    public function testGetLanguagePacksFromSources()
    {
        $sources = $this->i18nService->getLanguagePacksFromSources();
        $this->assertTrue(array_key_exists('symfony/apps/orangehrm/i18n', $sources));
    }

    public function testCheckDuplicates()
    {
        $duplicates = $this->i18nService->checkDuplicates();
        foreach ($duplicates as $duplicate) {
            $this->assertTrue(empty($duplicate));
        }
    }

//    public function testContinueNumberingUnitId()
//    {
//        $duplicates = $this->i18nService->continueNumberingUnitId();
//    }

    public function testGetDevLanguagePackName()
    {
        $name = $this->i18nService->getDevLanguagePackName();
        $this->assertEquals('messages.zz_ZZ.xml', $name);
    }

    public function testGetXliffXml()
    {
        $xml = $this->i18nService->getXliffXml('fr_FR');

        $file = $xml->xpath('//file')[0];
        $this->assertEquals($file['source-language'], 'en_US');
        $this->assertEquals($file['target-language'], 'fr_FR');
        $this->assertEquals($file['datatype'], 'xml');
        $this->assertEquals($file['original'], 'messages');
        $this->assertEquals($file['product-name'], 'messages');
        $body = $xml->xpath('//body')[0];
        $this->assertTrue($body instanceof SimpleXMLElement);
    }

    public function testGenerateTargetRelativeSource()
    {
        $relativeSource = $this->i18nService->generateTargetRelativeSource(
            'symfony/apps/orangehrm/i18n/messages.zz_ZZ.xml',
            'en_US'
        );
        $this->assertEquals('symfony/apps/orangehrm/i18n/messages.en_US.xml', $relativeSource);
    }

    public function testGetXliffGroupedBySources()
    {
        $translations = [$this->getTestTranslation()];

        $xliffGroupedBySources = $this->i18nService->getXliffGroupedBySources(
            $translations,
            'en_US'
        );
        $xml = $xliffGroupedBySources['symfony/apps/orangehrm/i18n/messages.en_US.xml'];
        $this->assertTrue($xml instanceof SimpleXMLElement);

        $xmlString = $xml->asXML();
        $xmlString = preg_replace('/date=".+Z"/', 'date=""', $xmlString);
        $this->assertEquals(
            "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE xliff PUBLIC \"-//XLIFF//DTD XLIFF//EN\" \"http://www.oasis-open.org/committees/xliff/documents/xliff.dtd\">
<xliff version=\"1.0\"><header/><file source-language=\"en_US\" target-language=\"en_US\" datatype=\"xml\" original=\"messages\" date=\"\" product-name=\"messages\"><body><trans-unit id=\"1\" group=\"test\" version=\"4.6\"><source>Test</source><target>FR_Test</target><note>Test Note</note></trans-unit></body></file></xliff>
",
            $xmlString
        );
    }

    public function testExportLanguagePack()
    {
        $i18nService = $this->getMockBuilder('I18NService')
            ->setMethods(['syncI18NTranslations', 'searchTranslations'])
            ->getMock();

        $i18nService->expects($this->once())
            ->method('syncI18NTranslations');
        $i18nService->expects($this->once())
            ->method('searchTranslations')
            ->will($this->returnValue([$this->getTestTranslation()]));

        $filePath = $i18nService->exportLanguagePack('en_US');
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($filePath, ZipArchive::CREATE));
        $this->assertEquals(1, $zip->numFiles);
        $this->assertEquals('symfony/apps/orangehrm/i18n/messages.en_US.xml', $zip->getNameIndex(0));
        $zip->close();
    }

    public function testGetI18NGroupsAssoc()
    {
        $i18nService = $this->getMockBuilder('I18NService')
            ->setMethods(['getI18NGroups'])
            ->getMock();
        $i18nService->expects($this->once())
            ->method('getI18NGroups')
            ->will($this->returnValue([$this->getTestGroup()]));

        $groups = $i18nService->getI18NGroupsAssoc();
        $this->assertEquals(['test' => 1], $groups);
    }

    public function testGetI18NGroups()
    {
        $i18nDao = $this->getMockBuilder('I18NDao')
            ->setMethods(['getI18NGroups'])
            ->getMock();
        $i18nDao->expects($this->once())
            ->method('getI18NGroups')
            ->will($this->returnValue([$this->getTestGroup()]));
        $this->i18nService->setI18NDao($i18nDao);
        $groups = $this->i18nService->getI18NGroups();
        $this->assertEquals(1, count($groups));
        $this->assertEquals('test', $groups[0]->getName());
        $this->assertEquals('Test', $groups[0]->getTitle());
        $this->assertEquals(1, $groups[0]->getId());
    }

    public function testMarkLanguageAsAdded()
    {
        $i18nDao = $this->getMockBuilder('I18NDao')
            ->setMethods(['getLanguageByCode', 'saveI18NLanguage'])
            ->getMock();
        $i18nDao->expects($this->once())
            ->method('getLanguageByCode')
            ->with('en_US')
            ->will($this->returnValue($this->getTestLanguage()));
        $i18nDao->expects($this->once())
            ->method('saveI18NLanguage')
            ->will(
                $this->returnCallback(
                    function ($lang) {
                        return $lang;
                    }
                )
            );
        $this->i18nService->setI18NDao($i18nDao);
        $language = $this->i18nService->markLanguageAsAdded('en_US');
        $this->assertEquals('en_US', $language->getCode());
        $this->assertEquals(true, $language->getAdded());
    }

    public function testMarkLanguageAsModified()
    {
        $i18nDao = $this->getMockBuilder('I18NDao')
            ->setMethods(['getLanguageByCode', 'saveI18NLanguage'])
            ->getMock();
        $testLang = $this->getTestLanguage();
        $this->assertTrue(is_null($testLang->getModifiedAt()));

        $i18nDao->expects($this->once())
            ->method('getLanguageByCode')
            ->with('en_US')
            ->will($this->returnValue($testLang));
        $i18nDao->expects($this->once())
            ->method('saveI18NLanguage')
            ->will(
                $this->returnCallback(
                    function ($lang) {
                        return $lang;
                    }
                )
            );
        $this->i18nService->setI18NDao($i18nDao);
        $language = $this->i18nService->markLanguageAsModified('en_US');
        $this->assertEquals('en_US', $language->getCode());
        $this->assertEquals(false, $language->getAdded());
        $this->assertTrue(!is_null($language->getModifiedAt()));
    }

    public function testGetLangPackPath()
    {
        $basePath = '/var/www/html/symfony/apps/orangehrm/i18n';
        $path = $this->i18nService->getLangPackPath($basePath);
        $this->assertEquals($basePath . DIRECTORY_SEPARATOR . 'messages.zz_ZZ.xml', $path);
        $path = $this->i18nService->getLangPackPath($basePath, 'en_US');
        $this->assertEquals($basePath . DIRECTORY_SEPARATOR . 'messages.en_US.xml', $path);
    }

    public function testGetI18NSourceDirs()
    {
        $sourceDirs = $this->i18nService->getI18NSourceDirs();
        $path = realpath(sfConfig::get('sf_root_dir') . '/apps/orangehrm/i18n');
        $this->assertTrue(in_array($path, $sourceDirs));
    }

    public function testReadSource()
    {
        $path = realpath(sfConfig::get('sf_root_dir') . '/apps/orangehrm/i18n/messages.zz_ZZ.xml');
        $translationUnits = $this->i18nService->readSource($path);
        $this->assertTrue(is_array($translationUnits));
        $this->assertTrue($translationUnits[0] instanceof SimpleXMLElement);

        $translationUnits = $this->i18nService->readSource('wrong/path');
        $this->assertTrue(is_null($translationUnits));
    }

    public function testGetTranslationUnitsAssoc()
    {
        $unit = new SimpleXMLElement(
            "<trans-unit id=\"1\" group=\"test\" version=\"4.6\">
        <source>Test</source>
        <target>FR_Test</target>
      </trans-unit>"
        );

        $translationUnits = $this->i18nService->getTranslationUnitsAssoc([$unit]);
        $this->assertEquals(['Test' => 'FR_Test'], $translationUnits);
    }

    /**
     * @return I18NGroup
     */
    private function getTestGroup()
    {
        $i18nGroup = new I18NGroup();
        $i18nGroup->setId(1);
        $i18nGroup->setName('test');
        $i18nGroup->setTitle('Test');
        return $i18nGroup;
    }

    /**
     * @return I18NLanguage
     */
    private function getTestLanguage()
    {
        $i18nLanguage = new I18NLanguage();
        $i18nLanguage->setCode('en_US');
        $i18nLanguage->setName('English');
        $i18nLanguage->setAdded(false);
        $i18nLanguage->setModifiedAt(null);
        return $i18nLanguage;
    }

    /**
     * @return I18NTranslate
     */
    private function getTestTranslation()
    {
        $i18nSource = new I18NSource();
        $i18nSource->setSource('symfony/apps/orangehrm/i18n/messages.zz_ZZ.xml');

        $i18nGroup = new I18NGroup();
        $i18nGroup->setName('test');

        $i18nLangString = new I18NLangString();
        $i18nLangString->setValue('Test');
        $i18nLangString->setUnitId('1');
        $i18nLangString->setI18NSource($i18nSource);
        $i18nLangString->setI18NGroup($i18nGroup);
        $i18nLangString->setVersion('4.6');
        $i18nLangString->setNote('Test Note');

        $i18nLanguage = new I18NLanguage();
        $i18nLanguage->setCode('en_US');

        $i18nTranslate = new I18NTranslate();
        $i18nTranslate->setValue("FR_Test");
        $i18nTranslate->setI18NLangString($i18nLangString);
        $i18nTranslate->setI18NLanguage($i18nLanguage);
        return $i18nTranslate;
    }
}
