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
 *     @OA\Property(property="street1", description="The employee's street address", type="string"),
 *     @OA\Property(property="street2", description="Additional information on the street address", type="string"),
 *     @OA\Property(property="city", description="The employee's city", type="string"),
 *     @OA\Property(property="province", description="The employee's state/province", type="string"),
 *     @OA\Property(property="zipCode", description="The employee's zipcode", type="string"),
 *     @OA\Property(property="countryCode", description="The employee's country code", type="string"),
 *     @OA\Property(property="homeTelephone", description="The employee's home telephone number", type="string"),
 *     @OA\Property(property="workTelephone", description="The employee's work telephone number", type="string"),
 *     @OA\Property(property="mobile", description="The employee's mobile phone number", type="string"),
 *     @OA\Property(property="workEmail", description="The employee's work email", type="string"),
 *     @OA\Property(property="otherEmail", description="The employee's other email", type="string")
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
