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

namespace OrangeHRM\Tests\Admin\Service;

use OrangeHRM\Admin\Dao\LanguageDao;
use OrangeHRM\Admin\Dto\LanguageSearchFilterParams;
use OrangeHRM\Admin\Service\LanguageService;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\Language;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Admin
 * @group Service
 */
class LanguageServiceTest extends TestCase
{
    private LanguageService $languageService;
    private string $fixture;

    /**
     * Set up method
     */
    protected function setUp(): void
    {
        $this->languageService = new LanguageService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmAdminPlugin/test/fixtures/LanguageDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetLanguageList(): void
    {
        $languageList = TestDataService::loadObjectList('Language', $this->fixture, 'Language');
        $languageFilterParams = new LanguageSearchFilterParams();
        $languageDao = $this->getMockBuilder(LanguageDao::class)->getMock();
        $languageDao->expects($this->once())
            ->method('getLanguageList')
            ->with($languageFilterParams)
            ->will($this->returnValue($languageList));
        $this->languageService->setLanguageDao($languageDao);
        $result = $this->languageService->getLanguageList($languageFilterParams);
        $this->assertCount(3, $result);
        $this->assertTrue($result[0] instanceof Language);
    }

    public function testDeleteLanguages(): void
    {
        $toBeDeletedLanguageIds = [1, 2];
        $languageDao = $this->getMockBuilder(LanguageDao::class)->getMock();
        $languageDao->expects($this->once())
            ->method('deleteLanguages')
            ->with($toBeDeletedLanguageIds)
            ->will($this->returnValue(2));
        $this->languageService->setLanguageDao($languageDao);
        $result = $this->languageService->deleteLanguages($toBeDeletedLanguageIds);
        $this->assertEquals(2, $result);
    }

    public function testGetLanguageById(): void
    {
        $languageList = TestDataService::loadObjectList('Language', $this->fixture, 'Language');
        $languageDao = $this->getMockBuilder(LanguageDao::class)->getMock();
        $languageDao->expects($this->once())
            ->method('getLanguageById')
            ->with(1)
            ->will($this->returnValue($languageList[0]));
        $this->languageService->setLanguageDao($languageDao);
        $result = $this->languageService->getLanguageById(1);
        $this->assertEquals($languageList[0], $result);
    }

    public function testGetLanguageByName(): void
    {
        $languageList = TestDataService::loadObjectList('Language', $this->fixture, 'Language');
        $languageDao = $this->getMockBuilder(LanguageDao::class)->getMock();
        $languageDao->expects($this->once())
            ->method('getLanguageByName')
            ->with(1)
            ->will($this->returnValue($languageList[0]));
        $this->languageService->setLanguageDao($languageDao);
        $result = $this->languageService->getLanguageByName(1);
        $this->assertEquals($result, $languageList[0]);
    }
}
