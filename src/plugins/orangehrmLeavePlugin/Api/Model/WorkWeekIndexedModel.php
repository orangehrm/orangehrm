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

namespace OrangeHRM\Leave\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\WorkWeek;

/**
 * @OA\Schema(
 *     schema="Leave-WorkWeekIndexedModel",
 *     type="object",
 *     @OA\Property(property="0", type="integer"),
 *     @OA\Property(property="1", type="integer"),
 *     @OA\Property(property="2", type="integer"),
 *     @OA\Property(property="3", type="integer"),
 *     @OA\Property(property="4", type="integer"),
 *     @OA\Property(property="5", type="integer"),
 *     @OA\Property(property="6", type="integer")
 * )
 */
class WorkWeekIndexedModel implements Normalizable
{
    use ModelTrait;

    public function __construct(WorkWeek $workWeek)
    {
        $this->setEntity($workWeek);
        $this->setFilters(
            [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday',
            ]
        );
        $this->setAttributeNames([1, 2, 3, 4, 5, 6, 0]);
    }
}
