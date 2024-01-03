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

namespace OrangeHRM\Admin\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\I18NGroup;

/**
 * @OA\Schema(
 *     schema="Admin-I18NGroupModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string", example="admin,pim"),
 *     @OA\Property(property="title", type="string", example="Admin,PIM")
 * )
 */
class I18NGroupModel implements Normalizable
{
    use ModelTrait;

    public function __construct(I18NGroup $i18NGroup)
    {
        $this->setEntity($i18NGroup);
        $this->setFilters([
            'id',
            'name',
            'title',
        ]);

        $this->setAttributeNames([
            'id',
            'name',
            'title',
        ]);
    }
}
