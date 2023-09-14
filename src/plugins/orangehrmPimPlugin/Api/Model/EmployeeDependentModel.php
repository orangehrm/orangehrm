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
use OrangeHRM\Entity\EmpDependent;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeDependentModel",
 *     type="object",
 *     @OA\Property(property="id", description="The numerical ID of the dependent", type="integer"),
 *     @OA\Property(property="name", description="The name of the dependent", type="string"),
 *     @OA\Property(property="relationshipType", description="The relationship type between the employee and the dependent (child or other)", type="string"),
 *     @OA\Property(property="relationship", description="Additional details of the relationship", type="string"),
 *     @OA\Property(property="dateOfBirth", description="The date of birth of the dependent", type="string", format="date")
 * )
 */
class EmployeeDependentModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param EmpDependent $empDependent
     */
    public function __construct(EmpDependent $empDependent)
    {
        $this->setEntity($empDependent);
        $this->setFilters(
            [
                'seqNo',
                'name',
                'relationshipType',
                'relationship',
                ['getDecorator', 'getDateOfBirth'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'name',
                'relationshipType',
                'relationship',
                'dateOfBirth',
            ]
        );
    }
}
