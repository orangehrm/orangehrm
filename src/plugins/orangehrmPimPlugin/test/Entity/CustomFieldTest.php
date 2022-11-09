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

namespace OrangeHRM\Tests\Pim\Entity;

use OrangeHRM\Entity\CustomField;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class CustomFieldTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([CustomField::class]);
    }

    public function testCustomFieldEntity(): void
    {
        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');

        $this->persist($customField);

        /** @var CustomField[] $customFields */
        $customFields = $this->getRepository(CustomField::class)->findBy(
            ['fieldNum' => 1]
        );
        $customField = $customFields[0];
        $this->assertEquals(1, $customField->getFieldNum());
        $this->assertEquals('Level', $customField->getName());
        $this->assertEquals(1, $customField->getType());
        $this->assertEquals('Personal', $customField->getScreen());
        $this->assertEquals('level1, level2', $customField->getExtraData());
    }
}
