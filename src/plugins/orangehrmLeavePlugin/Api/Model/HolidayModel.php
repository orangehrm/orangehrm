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
use OrangeHRM\Entity\Holiday;

/**
 * @OA\Schema(
 *     schema="Leave-HolidayModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="recurring", type="boolean"),
 *     @OA\Property(property="length", type="string", enum={ 0, 4}),
 *     @OA\Property(property="lengthName", type="string", enum={ "Full Day", "Half Day"})
 * )
 */
class HolidayModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Holiday $holiday)
    {
        $this->setEntity($holiday);
        $this->setFilters(
            [
                'id',
                'name',
                ['getDecorator', 'getDate'],
                ['isRecurring'],
                'length',
                ['getDecorator', 'getLengthName'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'name',
                'date',
                'recurring',
                'length',
                'lengthName',
            ]
        );
    }
}
