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

namespace OrangeHRM\Tests\Pim\Service;

use OrangeHRM\Pim\Dao\CustomFieldDao;
use OrangeHRM\Pim\Service\CustomFieldService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Pim
 * @group Service
 */
class CustomFieldServiceTest extends TestCase
{
    private CustomFieldService $customFieldService;

    protected function setUp(): void
    {
        $this->customFieldService = new CustomFieldService();
    }

    public function testGetCustomFieldDao(): void
    {
        $this->assertTrue($this->customFieldService->getCustomFieldDao() instanceof CustomFieldDao);
    }

    public function testGenerateGettersByFieldNumbers(): void
    {
        $this->assertEquals(
            [1 => 'getCustom1', 2 => 'getCustom2', 10 => 'getCustom10'],
            $this->customFieldService->generateGettersByFieldNumbers([1, 2, 10])
        );
    }

    public function testGenerateFieldKeyByFieldId(): void
    {
        $this->assertEquals(
            'custom1',
            $this->customFieldService->generateFieldKeyByFieldId(1)
        );
    }

    public function testGenerateSetterByFieldKey(): void
    {
        $this->assertEquals(
            'setCustom1',
            $this->customFieldService->generateSetterByFieldKey('custom1')
        );
    }

    public function testExtractFieldNumbersFromFieldKeys(): void
    {
        $this->assertEquals(
            [1, 2],
            $this->customFieldService->extractFieldNumbersFromFieldKeys(['custom1', 'custom2'])
        );
    }
}
