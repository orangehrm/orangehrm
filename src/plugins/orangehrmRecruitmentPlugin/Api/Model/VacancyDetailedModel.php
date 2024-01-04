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

namespace OrangeHRM\Recruitment\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Vacancy;

/**
 * @OA\Schema(
 *     schema="Recruitment-VacancyDetailedModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="numOfPositions", type="integer"),
 *     @OA\Property(property="status", type="boolean"),
 *     @OA\Property(property="isPublished", type="boolean"),
 *     @OA\Property(property="jobTitle", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="title", type="string"),
 *         @OA\Property(property="isDeleted", type="boolean")
 *     ),
 *     @OA\Property(property="hiringManager", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="terminationId", type="integer", nullable=true)
 *     )
 * )
 */
class VacancyDetailedModel implements Normalizable
{
    use ModelTrait;

    public function __construct(Vacancy $vacancy)
    {
        $this->setEntity($vacancy);
        $this->setFilters([
            'id',
            'name',
            'description',
            'numOfPositions',
            'status',
            ['isPublished'],
            ['getJobTitle', 'getId'],
            ['getJobTitle', 'getJobTitleName'],
            ['getJobTitle', 'isDeleted'],
            ['getHiringManager', 'getEmpNumber'],
            ['getHiringManager', 'getFirstName'],
            ['getHiringManager', 'getMiddleName'],
            ['getHiringManager', 'getLastName'],
            ['getHiringManager', 'getEmployeeTerminationRecord', 'getId'],
        ]);

        $this->setAttributeNames([
            'id',
            'name',
            'description',
            'numOfPositions',
            'status',
            'isPublished',
            ['jobTitle', 'id'],
            ['jobTitle', 'title'],
            ['jobTitle', 'isDeleted'],
            ['hiringManager', 'id'],
            ['hiringManager', 'firstName'],
            ['hiringManager', 'middleName'],
            ['hiringManager', 'lastName'],
            ['hiringManager', 'terminationId'],
        ]);
    }
}
