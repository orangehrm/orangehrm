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
use OrangeHRM\Entity\EmpEmergencyContact;

/**
 * @OA\Schema(
 *     schema="Pim-EmpEmergencyContactModel",
 *     type="object",
 *     @OA\Property(property="id", description="The numerical ID of the emergency contact", type="integer"),
 *     @OA\Property(property="name", description="The name of the emergency contact", type="string"),
 *     @OA\Property(property="relationship", description="The relationship between the employee and the emergency contact", type="string"),
 *     @OA\Property(property="homePhone", description="The contact's home phone number", type="string"),
 *     @OA\Property(property="officePhone", description="The contact's office phone number", type="string"),
 *     @OA\Property(property="mobilePhone", description="The contact's mobile phone number", type="string")
 * )
 */
class EmpEmergencyContactModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param EmpEmergencyContact $empEmergencyContact
     */
    public function __construct(EmpEmergencyContact $empEmergencyContact)
    {
        $this->setEntity($empEmergencyContact);
        $this->setFilters(
            [
                'seqNo',
                'name',
                'relationship',
                'homePhone',
                'officePhone',
                'mobilePhone',
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'name',
                'relationship',
                'homePhone',
                'officePhone',
                'mobilePhone',
            ]
        );
    }
}
