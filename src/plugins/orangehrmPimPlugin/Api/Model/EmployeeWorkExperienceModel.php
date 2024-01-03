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
use OrangeHRM\Entity\EmpWorkExperience;

/**
 * @OA\Schema(
 *     schema="Pim-EmployeeWorkExperienceModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="company", type="string"),
 *     @OA\Property(property="jobTitle", type="string"),
 *     @OA\Property(property="comment", type="string"),
 *     @OA\Property(property="fromDate", type="string", format="date"),
 *     @OA\Property(property="toDate", type="string", format="date"),
 *     @OA\Property(property="education", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *     ),
 * )
 */
class EmployeeWorkExperienceModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param EmpWorkExperience $empWorkExperience
     */
    public function __construct(EmpWorkExperience $empWorkExperience)
    {
        $this->setEntity($empWorkExperience);
        $this->setFilters(
            [
                'seqNo',
                'employer',
                'jobTitle',
                'comments',
                ['getDecorator', 'getFromDate'],
                ['getDecorator', 'getToDate'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'company',
                'jobTitle',
                'comment',
                'fromDate',
                'toDate',
                ['education', 'id'],
                ['education', 'name'],
            ]
        );
    }
}
