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

namespace OrangeHRM\Buzz\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Employee;

/**
 * @OA\Schema(
 *     schema="Buzz-EmployeeAnniversaryModel",
 *     type="object",
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="employeeId", type="string"),
 *         @OA\Property(property="terminationId", type="integer")
 *     ),
 *     @OA\Property(
 *         property="jobTitle",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="title", type="string"),
 *         @OA\Property(property="isDeleted", type="boolean"),
 *     ),
 *     @OA\Property(property="joinedDate", type="string", format="date")
 * )
 */
class EmployeeAnniversaryModel implements Normalizable
{
    use ModelTrait;

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
                ['getDecorator', 'getJoinedDate'],
            ]
        );
        $this->setAttributeNames(
            [
                ['employee', 'empNumber'],
                ['employee', 'lastName'],
                ['employee', 'firstName'],
                ['employee', 'middleName'],
                ['employee', 'employeeId'],
                ['employee', 'terminationId'],
                ['jobTitle', 'id'],
                ['jobTitle', 'title'],
                ['jobTitle', 'isDeleted'],
                'joinedDate',
            ]
        );
    }
}
