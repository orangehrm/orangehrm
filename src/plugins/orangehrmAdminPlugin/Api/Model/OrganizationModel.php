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
use OrangeHRM\Entity\Organization;

/**
 * @OA\Schema(
 *     schema="Admin-OrganizationModel",
 *     type="object",
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="registrationNumber", type="string"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="fax", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="country", type="string"),
 *     @OA\Property(property="province", type="string"),
 *     @OA\Property(property="city", type="string"),
 *     @OA\Property(property="zipCode", type="string"),
 *     @OA\Property(property="street1", type="string"),
 *     @OA\Property(property="street2", type="string"),
 *     @OA\Property(property="note", type="string"),
 * )
 */
class OrganizationModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Organization $organization)
    {
        $this->setEntity($organization);
        $this->setFilters(
            [
                'name',
                'taxId',
                'registrationNumber',
                'phone',
                'fax',
                'email',
                'country',
                'province',
                'city',
                'zipCode',
                'street1',
                'street2',
                'note',
            ]
        );
    }
}
