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
use OrangeHRM\Entity\Employee;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeContactDetailsModel",
 *     type="object",
 *     @OA\Property(property="street1", type="string"),
 *     @OA\Property(property="street2", type="string"),
 *     @OA\Property(property="city", type="string"),
 *     @OA\Property(property="province", type="string"),
 *     @OA\Property(property="zipCode", type="string"),
 *     @OA\Property(property="countryCode", type="string"),
 *     @OA\Property(property="homeTelephone", type="string"),
 *     @OA\Property(property="workTelephone", type="string"),
 *     @OA\Property(property="mobile", type="string"),
 *     @OA\Property(property="workEmail", type="string"),
 *     @OA\Property(property="otherEmail", type="string")
 * )
 */
class EmployeeContactDetailsModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Employee $employee)
    {
        $this->setEntity($employee);
        $this->setFilters(
            [
                'street1',
                'street2',
                'city',
                'province',
                'zipcode',
                'country',
                'homeTelephone',
                'workTelephone',
                'mobile',
                'workEmail',
                'otherEmail'
            ]
        );
        $this->setAttributeNames(
            [
                'street1',
                'street2',
                'city',
                'province',
                'zipCode',
                'countryCode',
                'homeTelephone',
                'workTelephone',
                'mobile',
                'workEmail',
                'otherEmail'
            ]
        );
    }
}
