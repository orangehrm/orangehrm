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

use OrangeHRM\Entity\CustomField;
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

    public function testGetAllFieldsInUse()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['isCustomFieldInUse'])
            ->getMock();

        $customFieldDao
            ->method('isCustomFieldInUse')
            ->will($this->returnCallback(function () {
                $args = func_get_args();
                if ($args[0] ==1 || $args[0] ==5) {
                    return true;
                } else {
                    return false;
                }
            }));

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();
        $customFieldService
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);
        $result = $customFieldService->getAllFieldsInUse();
        $this->assertEquals([1,5], $result);
    }

    public function testDeleteRelatedEmployeeCustomFieldsExtraDataWhenNotDeleteOption()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['getCustomFieldById','updateEmployeesIfDropDownValueInUse'])
            ->getMock();

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');

        $customFieldDao->expects($this->exactly(1))
            ->method('getCustomFieldById')
            ->with(1)
            ->willReturn($customField);

        $customFieldDao->expects($this->exactly(0)) // if success this function not will be called
            ->method('updateEmployeesIfDropDownValueInUse');

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();
        $customFieldService
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        $customFieldService->deleteRelatedEmployeeCustomFieldsExtraData(1, 'level1, level2');
        $this->assertTrue(true); // to avoid risky msg, check handled in method call counting
    }


    public function testDeleteRelatedEmployeeCustomFieldsExtraDataWhenDeleteOption()
    {
        $customFieldDao = $this->getMockBuilder(CustomFieldDao::class)
            ->onlyMethods(['getCustomFieldById','updateEmployeesIfDropDownValueInUse'])
            ->getMock();

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');

        $customFieldDao->expects($this->exactly(1))
            ->method('getCustomFieldById')
            ->with(1)
            ->willReturn($customField);

        $customFieldDao->expects($this->exactly(1)) // if success this function will be called exactly once
            ->method('updateEmployeesIfDropDownValueInUse');

        $customFieldService = $this->getMockBuilder(CustomFieldService::class)
            ->onlyMethods(['getCustomFieldDao'])
            ->getMock();
        $customFieldService
            ->method('getCustomFieldDao')
            ->willReturn($customFieldDao);

        $customFieldService->deleteRelatedEmployeeCustomFieldsExtraData(1, 'level1');
        $this->assertTrue(true); // to avoid risky msg, check handled in method call counting
    }
}
