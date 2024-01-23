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

namespace OrangeHRM\Pim\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\CustomField;

/**
 * @OA\Schema(
 *     schema="Pim-CustomFieldModel",
 *     type="object",
 *     @OA\Property(property="id", description="The numerical ID of the custom field", type="integer"),
 *     @OA\Property(property="fieldName", description="The name of the field", type="string"),
 *     @OA\Property(property="fieldType", description="The type of the field (text/number or dropdown)", type="string"),
 *     @OA\Property(property="extraData", description="The options for dropdown type fields", type="string"),
 *     @OA\Property(property="screen", description="The PIM screen this field is displayed", type="string")
 * )
 */
class CustomFieldModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param CustomField $customField
     */
    public function __construct(CustomField $customField)
    {
        $this->setEntity($customField);
        $this->setFilters(
            [
                'fieldNum',
                'name',
                'type',
                'extraData',
                'screen'
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'fieldName',
                'fieldType',
                'extraData',
                'screen'
            ]
        );
    }
}
