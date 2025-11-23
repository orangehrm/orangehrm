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

namespace OrangeHRM\Tests\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dao\LocalizationDao;
use OrangeHRM\Admin\Dto\I18NGroupSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NImportErrorSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Admin\Dto\I18NTranslationSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\I18NError;
use OrangeHRM\Entity\I18NGroup;
use OrangeHRM\Entity\I18NImportError;
use OrangeHRM\Entity\I18NLangString;
use OrangeHRM\Entity\I18NLanguage;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Yaml\Yaml;

/**
 * @group Admin
 * @group Dao
 */
class LocalizationDaoTest extends TestCase
{
    private LocalizationDao $i18NDao;
    protected string $fixture;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->i18NDao = new LocalizationDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/LocalizationDao.yml';
        TestDataService::populate($this->fixture);
    }

    /**
     * @param $addedOnly
     * @param $enabledOnly
     * @param $expectedIds
     * @dataProvider searchLanguagesDataProvider
     */
    public function testSearchLanguages($addedOnly, $enabledOnly, $expectedIds): void
    {
        $i18NLanguageSearchFilterParams = new I18NLanguageSearchFilterParams();
        $i18NLanguageSearchFilterParams->setAddedOnly($addedOnly);
        $i18NLanguageSearchFilterParams->setEnabledOnly($enabledOnly);
        $languages = $this->i18NDao->searchLanguages($i18NLanguageSearchFilterParams);
        $this->assertCount(count($expectedIds), $languages);
        foreach ($languages as $key => $language) {
            $this->assertEquals($expectedIds[$key], $language->getId());
        }
    }

    public function getTestCases($key)
    {
        $testCases = Yaml::parseFile(
            Config::get(Config::PLUGINS_DIR) .
            '/orangehrmAdminPlugin/test/testCases/I18NDaoTestCases.yml'
        );
        return $testCases[$key];
    }

    public function searchLanguagesDataProvider()
    {
        return $this->getTestCases('searchLanguages');
    }

    public function testGetLanguagesCount(): void
    {
        $i18NLanguageSearchParams = new I18NLanguageSearchFilterParams();
        $count = $this->i18NDao->getLanguagesCount($i18NLanguageSearchParams);
        $this->assertEquals(4, $count);

        $i18NLanguageSearchParams->setAddedOnly(true);
        $count = $this->i18NDao->getLanguagesCount($i18NLanguageSearchParams);
        $this->assertEquals(2, $count);
    }

    public function testGetLanguageById(): void
    {
        $language = $this->i18NDao->getLanguageById(1);
        $this->assertInstanceOf(I18NLanguage::class, $language);
        $this->assertEquals(1, $language->getId());
        $this->assertEquals('Chinese (Simplified, China) - 中文（简体，中国）', $language->getName());
        $this->assertEquals('zh_Hans_CN', $language->getCode());
    }

    public function testSaveI18NLanguage(): void
    {
        $language = new I18NLanguage();
        $language->setName('Special Language');
        $language->setCode('spec lan');
        $language->setEnabled(true);
        $language->setAdded(true);

        $result = $this->i18NDao->saveI18NLanguage($language);
        $this->assertInstanceOf(I18NLanguage::class, $result);
        $this->assertEquals('Special Language', $result->getName());
        $this->assertEquals(true, $result->isAdded());
    }

    public function testGetNormalizedTranslations(): void
    {
        $i18NTargetLangStringSearchFilterParams = new I18NTranslationSearchFilterParams();
        $i18NTargetLangStringSearchFilterParams->setLanguageId(1);
        $i18NTargetLangStringSearchFilterParams->setSourceText('e');
        $i18NTargetLangStringSearchFilterParams->setTranslatedText(null);
        $i18NTargetLangStringSearchFilterParams->setGroupId(null);
        $i18NTargetLangStringSearchFilterParams->setOnlyTranslated(null);
        $translates = $this->i18NDao->getNormalizedTranslations($i18NTargetLangStringSearchFilterParams);

        $this->assertTrue(is_array($this->i18NDao->getNormalizedTranslations($i18NTargetLangStringSearchFilterParams)));
        $this->assertCount(3, $translates);
    }

    public function testGetTranslationsCount(): void
    {
        $i18NTargetLangStringSearchFilterParams = new I18NTranslationSearchFilterParams();
        $i18NTargetLangStringSearchFilterParams->setLanguageId(2);
        $i18NTargetLangStringSearchFilterParams->setGroupId(null);
        $i18NTargetLangStringSearchFilterParams->setOnlyTranslated(null);
        $i18NTargetLangStringSearchFilterParams->setSourceText(null);
        $i18NTargetLangStringSearchFilterParams->setTranslatedText(null);
        $count = $this->i18NDao->getTranslationsCount($i18NTargetLangStringSearchFilterParams);
        $this->assertEquals(4, $count);

        $i18NTargetLangStringSearchFilterParams->setGroupId('2');
        $count = $this->i18NDao->getTranslationsCount($i18NTargetLangStringSearchFilterParams);
        $this->assertEquals(2, $count);

        $i18NTargetLangStringSearchFilterParams->setGroupId(null);
        $i18NTargetLangStringSearchFilterParams->setOnlyTranslated(true);
        $count = $this->i18NDao->getTranslationsCount($i18NTargetLangStringSearchFilterParams);
        $this->assertEquals(1, $count);

        $i18NTargetLangStringSearchFilterParams->setGroupId(null);
        $i18NTargetLangStringSearchFilterParams->setOnlyTranslated(false);
        $count = $this->i18NDao->getTranslationsCount($i18NTargetLangStringSearchFilterParams);
        $this->assertEquals(3, $count);

        $i18NTargetLangStringSearchFilterParams->setGroupId('1');
        $i18NTargetLangStringSearchFilterParams->setOnlyTranslated(null);
        $i18NTargetLangStringSearchFilterParams->setSourceText('employee');
        $count = $this->i18NDao->getTranslationsCount($i18NTargetLangStringSearchFilterParams);
        $this->assertEquals(0, $count);
    }

    public function testSearchGroups(): void
    {
        $i18NGroupSearchFilterParams = new I18NGroupSearchFilterParams();
        $groups = $this->i18NDao->searchGroups($i18NGroupSearchFilterParams);

        $this->assertTrue(is_array($this->i18NDao->searchGroups($i18NGroupSearchFilterParams)));
        $this->assertCount('2', $groups);
    }

    public function testGetI18NGroupCount(): void
    {
        $i18NGroupSearchFilterParams = new I18NGroupSearchFilterParams();
        $count = $this->i18NDao->getI18NGroupCount($i18NGroupSearchFilterParams);
        $this->assertEquals('2', $count);
    }

    public function testDeleteI18NLanguage(): void
    {
        $toBedeletedIds = [1, 2];
        $result = $this->i18NDao->deleteI18NLanguage($toBedeletedIds);
        $this->assertEquals($result, 2);
    }

    public function testGetI18NErrorByName(): void
    {
        $expectedError = $this->getEntityManager()->getRepository(I18NError::class)->findOneBy(['name' => I18NError::INVALID_SYNTAX]);
        $result = $this->i18NDao->getI18NErrorByName(I18NError::INVALID_SYNTAX);
        $this->assertEquals($expectedError, $result);
    }

    public function testGetI18NGroupByName(): void
    {
        $expectedGroup = $this->getEntityManager()->getRepository(I18NGroup::class)->find(1);
        $result = $this->i18NDao->getI18NGroupByName('general');
        $this->assertEquals($expectedGroup, $result);
    }

    public function testGetLangStringByUnitIdAndGroupID(): void
    {
        $expectedLangString = $this->getEntityManager()->getRepository(I18NLangString::class)->find(1);
        $result = $this->i18NDao->getLangStringByUnitIdAndGroupID('add_job_title', 1);
        $this->assertEquals($expectedLangString, $result);
    }

    public function testGetLangStringById(): void
    {
        $expectedLangString = $this->getEntityManager()->getRepository(I18NLangString::class)->find(1);
        $result = $this->i18NDao->getLangStringById(1);
        $this->assertEquals($expectedLangString, $result);
    }

    public function testClearImportErrorsForLangString(): void
    {
        $importErrorsForLanguage = $this->getEntityManager()->getRepository(I18NImportError::class)->findBy(['language' => 1]);
        $this->assertCount(2, $importErrorsForLanguage);
        $this->i18NDao->clearImportErrorsForLangStrings(
            1,
            [2, 3]
        );
        $importErrorsForLanguage = $this->getEntityManager()->getRepository(I18NImportError::class)->findBy(['language' => 1]);
        $this->assertCount(0, $importErrorsForLanguage);
    }

    public function testGetImportErrorList(): void
    {
        $expectedImportErrors = $this->getEntityManager()->getRepository(I18NImportError::class)->findBy(['language' => 1]);
        $searchFilterParams = new I18NImportErrorSearchFilterParams();
        $searchFilterParams->setLanguageId(1);
        $searchFilterParams->setEmpNumber(1);
        $result = $this->i18NDao->getImportErrorList($searchFilterParams);
        $this->assertEquals($expectedImportErrors, $result);
    }

    public function testGetImportErrorCount(): void
    {
        $expectedImportErrorCount = count($this->getEntityManager()->getRepository(I18NImportError::class)->findBy(['language' => 4]));
        $searchFilterParams = new I18NImportErrorSearchFilterParams();
        $searchFilterParams->setLanguageId(4);
        $searchFilterParams->setEmpNumber(1);
        $result = $this->i18NDao->getImportErrorCount($searchFilterParams);
        $this->assertEquals($expectedImportErrorCount, $result);
    }
}
