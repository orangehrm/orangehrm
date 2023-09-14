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
 *     schema="Pim-MyInfoDetailedModel",
 *     type="object",
 *     @OA\Property(property="lastName", description="My last name", type="string"),
 *     @OA\Property(property="firstName", description="My first name", type="string"),
 *     @OA\Property(property="middleName", description="My middle name", type="string"),
 *     @OA\Property(property="employeeId", description="My employee ID", type="string"),
 *     @OA\Property(property="terminationId", description="My termination ID", type="integer", nullable=true),
 *     @OA\Property(
 *         property="jobTitle",
 *         type="object",
 *         @OA\Property(property="id", description="The numerical ID of my job title", type="integer"),
 *         @OA\Property(property="title", description="The title of my job", type="string"),
 *         @OA\Property(property="isDeleted", description="The deleted status of my job title", type="boolean")
 *     ),
 *     @OA\Property(
 *         property="subunit",
 *         type="object",
 *         @OA\Property(property="id", description="The numerical ID of my subunit", type="integer"),
 *         @OA\Property(property="name", description="The name of my subunit", type="string")
 *     )
 * )
 */
class MyInfoDetailedModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param Employee $employee
     */
    public function __construct(Employee $employee)
    {
        $this->setEntity($employee);
        $this->setFilters(
            [
                'empNumber',
                'lastName',
                'firstName',
                'middleName',
                'employeeId',
                ['getEmployeeTerminationRecord', 'getId'],
                ['getJobTitle', 'getId'],
                ['getJobTitle', 'getJobTitleName'],
                ['getJobTitle', 'isDeleted'],
                ['getSubDivision', 'getId'],
                ['getSubDivision', 'getName'],
            ]
        );
        $this->setAttributeNames(
            [
                'empNumber',
                'lastName',
                'firstName',
                'middleName',
                'employeeId',
                'terminationId',
                ['jobTitle', 'id'],
                ['jobTitle', 'title'],
                ['jobTitle', 'isDeleted'],
                ['subunit', 'id'],
                ['subunit', 'name'],
            ]
        );
    }
}
