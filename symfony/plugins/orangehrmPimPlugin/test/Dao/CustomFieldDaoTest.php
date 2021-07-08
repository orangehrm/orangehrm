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

namespace OrangeHRM\Tests\Pim\Dao;

use Exception;
use OrangeHRM\Config\Config;
use OrangeHRM\Entity\CustomField;
use OrangeHRM\Pim\Dao\CustomFieldDao;
use OrangeHRM\Pim\Dto\CustomFieldSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Pim
 * @group Dao
 */
class CustomFieldDaoTest extends TestCase
{

    private CustomFieldDao $customFieldDao;
    protected string $fixture;

    /**
     * Set up method
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->customFieldDao = new CustomFieldDao();
        $this->fixture = Config::get(
                Config::PLUGINS_DIR
            ) . '/orangehrmPimPlugin/test/fixtures/CustomFieldConfigurationDao.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetCustomFieldById(): void
    {
        $result = $this->customFieldDao->getCustomFieldById(1);
        $this->assertEquals('Age', $result->getName());
        $this->assertEquals(0, $result->getType());
        $this->assertEquals('personal', $result->getScreen());
    }

    public function testDeleteCustomField(): void
    {
        $toTobedeletedIds = [1, 2];
        $result = $this->customFieldDao->deleteCustomFields($toTobedeletedIds);
        $this->assertEquals(2, $result);
    }

    public function testSearchCustomField(): void
    {
        $customFieldSearchParams = new CustomFieldSearchFilterParams();
        $result = $this->customFieldDao->searchCustomField($customFieldSearchParams);
        $this->assertCount(5, $result);
        $this->assertTrue($result[0] instanceof CustomField);
    }

    public function testSearchCustomFieldWithLimit(): void
    {
        $customFieldSearchParams = new CustomFieldSearchFilterParams();
        $customFieldSearchParams->setLimit(1);

        $result = $this->customFieldDao->searchCustomField($customFieldSearchParams);
        $this->assertCount(1, $result);
    }

    public function testSaveCustomField(): void
    {
        $customField = new CustomField();
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');
        $result = $this->customFieldDao->saveCustomField($customField);
        $this->assertTrue($result instanceof CustomField);
        $this->assertEquals("Level", $result->getName());
        $this->assertEquals(1, $result->getType());
        $this->assertEquals("Personal", $result->getScreen());
        $this->assertEquals('level1, level2', $result->getExtraData());
    }

    public function testEditCustomField(): void
    {
        $customField = $this->customFieldDao->getCustomFieldById(1);
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');
        $result = $this->customFieldDao->saveCustomField($customField);
        $this->assertTrue($result instanceof CustomField);
        $this->assertEquals("Level", $result->getName());
        $this->assertEquals(1, $result->getType());
        $this->assertEquals("Personal", $result->getScreen());
        $this->assertEquals('level1, level2', $result->getExtraData());
    }

    public function testGetSearchCustomFieldsCount(): void
    {
        $customFieldSearchParams = new CustomFieldSearchFilterParams();
        $result = $this->customFieldDao->getSearchCustomFieldsCount($customFieldSearchParams);
        $this->assertEquals(5, $result);
    }
}
