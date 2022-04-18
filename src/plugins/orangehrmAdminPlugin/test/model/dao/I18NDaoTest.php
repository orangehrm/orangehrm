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
class I18NDaoTest extends PHPUnit\Framework\TestCase
{
    /**
     * @var I18NDao|null
     */
    private $i18NDao = null;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->i18NDao = new I18NDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/I18NDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetLanguageByCode()
    {
        $lang = $this->i18NDao->getLanguageByCode('en_US');
        $this->assertTrue($lang instanceof I18NLanguage);
        $this->assertTrue($lang->getEnabled());
        $this->assertTrue($lang->getAdded());

        $lang = $this->i18NDao->getLanguageByCode('zz_ZZ');
        $this->assertTrue($lang instanceof I18NLanguage);
        $this->assertFalse($lang->getEnabled());
        $this->assertFalse($lang->getAdded());
    }

    public function testSearchLanguages()
    {
        $searchParams = new ParameterObject(['enabled' => true]);
        $langs = $this->i18NDao->searchLanguages($searchParams);
        $this->assertEquals(3, $langs->count());

        $searchParams = new ParameterObject();
        $langs = $this->i18NDao->searchLanguages($searchParams);
        $this->assertEquals(4, $langs->count());

        $searchParams = new ParameterObject(['enabled' => true, 'added' => true]);
        $langs = $this->i18NDao->searchLanguages($searchParams);
        $this->assertEquals(2, $langs->count());
    }

    public function testGetLanguageById()
    {
        $lang = $this->i18NDao->getLanguageById(1);
        $this->assertTrue($lang instanceof I18NLanguage);
        $this->assertTrue($lang->getEnabled());
        $this->assertTrue($lang->getAdded());
        $this->assertEquals('zh_Hans_CN', $lang->getCode());

        $lang = $this->i18NDao->getLanguageById(4);
        $this->assertTrue($lang instanceof I18NLanguage);
        $this->assertTrue($lang->getEnabled());
        $this->assertFalse($lang->getAdded());
        $this->assertEquals('af_NA', $lang->getCode());
    }

    public function testGetI18NSource()
    {
        $i18nSource = $this->i18NDao->getI18NSource('symfony/apps/orangehrm/i18n/messages.zz_ZZ.xml');
        $this->assertTrue($i18nSource instanceof I18NSource);
        $this->assertTrue(is_null($i18nSource->getModifiedAt()));
    }

    public function testSaveI18NSource()
    {
        $i18nSource = $this->i18NDao->getI18NSource('symfony/apps/orangehrm/i18n/messages.zz_ZZ.xml');
        $this->assertTrue(is_null($i18nSource->getModifiedAt()));
        $i18nSource->setModifiedAt(date("Y-m-d H:i:s"));
        $savedI18nSource = $this->i18NDao->saveI18NSource($i18nSource);
        $this->assertFalse(is_null($savedI18nSource->getModifiedAt()));
    }

    public function testGetI18NGroups()
    {
        $groups = $this->i18NDao->getI18NGroups();
        $this->assertEquals(3, $groups->count());
    }

    public function testSaveI18NLanguage()
    {
        $lang = new I18NLanguage();
        $lang->setName("English");
        $lang->setCode("en_Test");
        $lang->setEnabled(true);
        $savedLang = $this->i18NDao->saveI18NLanguage($lang);
        $this->assertTrue($savedLang instanceof I18NLanguage);
        $this->assertTrue($savedLang->getEnabled());
        $this->assertFalse($savedLang->getAdded());
        $this->assertEquals('en_Test', $savedLang->getCode());
    }

    public function testSaveI18NLangString()
    {
        $langString = new I18NLangString();
        $langString->setUnitId(1);
        $langString->setSourceId(1);
        $langString->setGroupId(1);
        $langString->setValue('Test');
        $savedLangString = $this->i18NDao->saveI18NLangString($langString);
        $this->assertTrue($savedLangString instanceof I18NLangString);
        $this->assertEquals('general', $savedLangString->getI18NGroup()->getName());
        $this->assertEquals(
            'symfony/apps/orangehrm/i18n/messages.zz_ZZ.xml',
            $savedLangString->getI18NSource()->getSource()
        );
        $this->assertEquals('Test', $savedLangString->getValue());
    }

    public function testSaveI18NTranslate()
    {
        $i18NTranslate = new I18NTranslate();
        $i18NTranslate->setLangStringId(1);
        $i18NTranslate->setLanguageId(2);
        $i18NTranslate->setValue('FR_Test');
        $savedI18NTranslate = $this->i18NDao->saveI18NTranslate($i18NTranslate);
        $this->assertTrue($savedI18NTranslate instanceof I18NTranslate);
        $this->assertEquals('general', $savedI18NTranslate->getI18NLangString()->getI18NGroup()->getName());
        $this->assertEquals(
            'symfony/apps/orangehrm/i18n/messages.zz_ZZ.xml',
            $savedI18NTranslate->getI18NLangString()->getI18NSource()->getSource()
        );
        $this->assertEquals('FR_Test', $savedI18NTranslate->getValue());
        $this->assertEquals('Test String', $savedI18NTranslate->getI18NLangString()->getValue());
        $this->assertEquals('en_US', $savedI18NTranslate->getI18NLanguage()->getCode());
    }

    public function testGetI18NTranslateById()
    {
        $i18NTranslate = $this->i18NDao->getI18NTranslateById(1);
        $this->assertTrue($i18NTranslate instanceof I18NTranslate);
        $i18NTranslate = $this->i18NDao->getI18NTranslateById(100);
        $this->assertTrue(is_null($i18NTranslate));
    }

    public function testGetI18NTranslate()
    {
        $i18NTranslate = $this->i18NDao->getI18NTranslate(1, 1);
        $this->assertTrue($i18NTranslate instanceof I18NTranslate);
        $this->assertEquals('FR_Test String', $i18NTranslate->getValue());
    }

    public function testGetMessages()
    {
        $messages = $this->i18NDao->getMessages('zh_Hans_CN');
        $this->assertEquals(1, $messages->count());

        $messages = $this->i18NDao->getMessages('zh_Hans_CN', false);
        $this->assertEquals(3, $messages->count());
    }

    public function testSearchTranslations()
    {
        $searchParams = new ParameterObject(['langCode' => 'zh_Hans_CN']);
        $translations = $this->i18NDao->searchTranslations($searchParams);
        $this->assertEquals(3, $translations->count());

        $searchParams = new ParameterObject(['langCode' => 'zh_Hans_CN', 'translated' => true]);
        $translations = $this->i18NDao->searchTranslations($searchParams);
        $this->assertEquals(3, $translations->count());

        $searchParams = new ParameterObject(['langCode' => 'zh_Hans_CN', 'sourceText' => 'Test String']);
        $translations = $this->i18NDao->searchTranslations($searchParams);
        $this->assertEquals(3, $translations->count());

        $searchParams = new ParameterObject(['langCode' => 'en_US', 'translated' => true]);
        $translations = $this->i18NDao->searchTranslations($searchParams);
        $this->assertEquals(1, $translations->count());
    }
}
