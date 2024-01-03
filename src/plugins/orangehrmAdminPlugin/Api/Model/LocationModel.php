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
use OrangeHRM\Entity\Location;

/**
 * @OA\Schema(
 *     schema="Admin-LocationModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(
 *         property="country",
 *         type="object",
 *         @OA\Property(property="countryCode", type="string"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="countryName", type="string"),
 *     ),
 *     @OA\Property(property="province", type="string"),
 *     @OA\Property(property="city", type="string"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="zipCode", type="string"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="fax", type="string"),
 *     @OA\Property(property="note", type="string"),
 *     @OA\Property(property="noOfEmployees", type="integer"),
 * )
 */
class LocationModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param Location $location
     */
    public function __construct(Location $location)
    {
        $this->setEntity($location);
        $this->setFilters(
            [
                'id',
                'name',
                ['getCountry', 'getCountryCode'],
                ['getCountry', 'getName'],
                ['getCountry', 'getCountryName'],
                'province',
                'city',
                'address',
                'zipCode',
                'phone',
                'fax',
                'note',
                ['getDecorator', 'getNoOfEmployees'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'name',
                ['country', 'countryCode'],
                ['country', 'name'],
                ['country', 'countryName'],
                'province',
                'city',
                'address',
                'zipCode',
                'phone',
                'fax',
                'note',
                'noOfEmployees',
            ]
        );
    }
}
