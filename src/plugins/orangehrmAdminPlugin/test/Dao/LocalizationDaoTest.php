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

namespace OrangeHRM\Tests\Admin\Dao;

use Exception;
use OrangeHRM\Admin\Dao\LocalizationDao;
use OrangeHRM\Admin\Dto\I18NLanguageSearchFilterParams;
use OrangeHRM\Config\Config;
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
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/I18NDao.yml';
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
        $laguage = $this->i18NDao->getLanguageById(1);
        $this->assertInstanceOf(I18NLanguage::class, $laguage);
        $this->assertEquals(1, $laguage->getId());
        $this->assertEquals('Chinese (Simplified, China) - 中文（简体，中国）', $laguage->getName());
        $this->assertEquals('zh_Hans_CN', $laguage->getCode());
    }

    public function testSaveI18NLanguage(): void
    {
        $language = new I18NLanguage();
        $language->setName('Special Langauege');
        $language->setCode('spec lan');
        $language->setEnabled(true);
        $language->setAdded(true);

        $result = $this->i18NDao->saveI18NLanguage($language);
        $this->assertInstanceOf(I18NLanguage::class,$result);
        $this->assertEquals('Special Langauege',$result->getName());
        $this->assertEquals(true,$result->isAdded());
    }
}
