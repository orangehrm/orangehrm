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
use OrangeHRM\Entity\EmployeeEducation;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeEducationModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="institute", type="string"),
 *     @OA\Property(property="major", type="string"),
 *     @OA\Property(property="year", type="integer"),
 *     @OA\Property(property="score", type="string"),
 *     @OA\Property(property="startDate", type="string", format="date"),
 *     @OA\Property(property="endDate", type="string", format="date"),
 *     @OA\Property(property="education", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string")
 *     )
 * )
 */
class EmployeeEducationModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param EmployeeEducation $employeeEducation
     */
    public function __construct(EmployeeEducation $employeeEducation)
    {
        $this->setEntity($employeeEducation);
        $this->setFilters(
            [
                'id',
                'institute',
                'major',
                'year',
                'score',
                ['getDecorator', 'getStartDate'],
                ['getDecorator', 'getEndDate'],
                ['getEducation', 'getId'],
                ['getEducation', 'getName'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'institute',
                'major',
                'year',
                'score',
                'startDate',
                'endDate',
                ['education', 'id'],
                ['education', 'name'],
            ]
        );
    }
}
