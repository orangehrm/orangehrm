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

namespace OrangeHRM\Tests\Pim\Api\Model;

use OrangeHRM\Entity\CustomField;
use OrangeHRM\Pim\Api\Model\CustomFieldModel;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Pim
 * @group Model
 */
class CustomFieldModelTest extends KernelTestCase
{
    public function testToArray()
    {
        $resultArray = [
            "id" => 1,
            "fieldName" => "Level",
            "fieldType" => 1,
            "extraData" => 'level1, level2',
            "screen" => "Personal"
        ];

        $customField = new CustomField();
        $customField->setFieldNum(1);
        $customField->setName('Level');
        $customField->setType(1);
        $customField->setScreen('Personal');
        $customField->setExtraData('level1, level2');

        $customFieldModel = new CustomFieldModel($customField);

        $this->assertEquals($resultArray, $customFieldModel->toArray());
    }
}
