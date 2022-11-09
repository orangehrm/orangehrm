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

use OrangeHRM\Admin\Dao\LanguageDao;
use OrangeHRM\Admin\Dto\LanguageSearchFilterParams;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Language;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Dao
 */
class LanguageDaoTest extends TestCase
{
    private LanguageDao $languageDao;
    protected string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->languageDao = new LanguageDao();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/LanguageDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testAddLanguage(): void
    {
        $language = new Language();
        $language->setName('Tamil');
        $this->languageDao->saveLanguage($language);
        $savedLanguage = TestDataService::fetchLastInsertedRecord('Language', 'a.id');
        $this->assertTrue($savedLanguage instanceof Language);
        $this->assertEquals('Tamil', $savedLanguage->getName());
    }

    public function testEditLanguage(): void
    {
        $language = TestDataService::fetchObject('Language', 3);
        $language->setName('Canadian French');
        $this->languageDao->saveLanguage($language);
        $savedLanguage = TestDataService::fetchLastInsertedRecord('Language', 'a.id');
        $this->assertTrue($savedLanguage instanceof Language);
        $this->assertEquals('Canadian French', $savedLanguage->getName());
    }

    public function testGetLanguageById(): void
    {
        $language = $this->languageDao->getLanguageById(1);
        $this->assertTrue($language instanceof Language);
        $this->assertEquals('Spanish', $language->getName());
    }

    public function testGetLanguageList(): void
    {
        $languageFilterParams = new LanguageSearchFilterParams();
        $result = $this->languageDao->getLanguageList($languageFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof Language);
    }

    public function testDeleteLanguages(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->languageDao->deleteLanguages($toTobedeletedIds);
        $this->assertEquals(2, $result);

        $result = $this->languageDao->deleteLanguages([]);
        $this->assertEquals(0, $result);
    }

    public function testDeleteWrongRecord(): void
    {
        $result = $this->languageDao->deleteLanguages([4]);
        $this->assertEquals(0, $result);
    }

    public function testIsExistingLanguageName(): void
    {
        $this->assertTrue($this->languageDao->isExistingLanguageName('Spanish'));
        $this->assertTrue($this->languageDao->isExistingLanguageName('SPANISH'));
        $this->assertTrue($this->languageDao->isExistingLanguageName('spanish'));
        $this->assertTrue($this->languageDao->isExistingLanguageName('  Spanish  '));
    }

    public function testGetLanguageByName(): void
    {
        $object = $this->languageDao->getLanguageByName('Spanish');
        $this->assertTrue($object instanceof Language);
        $this->assertEquals(1, $object->getId());

        $object = $this->languageDao->getLanguageByName('SPANISH');
        $this->assertTrue($object instanceof Language);
        $this->assertEquals(1, $object->getId());

        $object = $this->languageDao->getLanguageByName('spanish');
        $this->assertTrue($object instanceof Language);
        $this->assertEquals(1, $object->getId());

        $object = $this->languageDao->getLanguageByName('  Spanish  ');
        $this->assertTrue($object instanceof Language);
        $this->assertEquals(1, $object->getId());

        $object = $this->languageDao->getLanguageByName('Hindi');
        $this->assertFalse($object instanceof Language);
    }
}
