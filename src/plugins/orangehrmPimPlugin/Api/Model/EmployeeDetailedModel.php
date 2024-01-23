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
 *     schema="Pim-EmployeeDetailedModel",
 *     type="object",
 *     @OA\Property(property="empNumber", description="The employee number of the employee", type="string"),
 *     @OA\Property(property="lastName", description="The last name of the employee", type="string"),
 *     @OA\Property(property="firstName", description="The first name of the employee", type="string"),
 *     @OA\Property(property="middleName", description="The middle name of the employee", type="string"),
 *     @OA\Property(property="employeeId", description="The employee ID of the employee", type="string"),
 *     @OA\Property(property="terminationId", description="The numerical ID of the employee's termination record", type="integer", nullable=true),
 *     @OA\Property(
 *         property="jobTitle",
 *         type="object",
 *         @OA\Property(property="id", description="The numerical ID  of the job title", type="integer"),
 *         @OA\Property(property="title", description="The title of the job", type="string"),
 *         @OA\Property(property="isDeleted", description="The deleted status of the job title", type="boolean")
 *     ),
 *     @OA\Property(
 *         property="subunit",
 *         type="object",
 *         @OA\Property(property="id", description="The numerical ID of the subunit", type="integer"),
 *         @OA\Property(property="name", description="The name of the subunit", type="string")
 *     ),
 *     @OA\Property(
 *         property="empStatus",
 *         type="object",
 *         @OA\Property(property="id", description="The numerical ID of the employee status", type="integer"),
 *         @OA\Property(property="name", description="The name of the employee status", type="string")
 *     ),
 *     @OA\Property(
 *         property="supervisors",
 *         type="array",
 *         description="A list of the employee's supervisors",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="empNumber", description="The employee number of the employee's supervisor", type="string"),
 *             @OA\Property(property="lastName", description="The last name of the employee's supervisor", type="string"),
 *             @OA\Property(property="firstName", description="The first name of the employee's supervisor", type="string"),
 *             @OA\Property(property="middleName", description="The middle name of the employee's supervisor", type="string")
 *         )
 *     )
 * )
 */
class EmployeeDetailedModel implements Normalizable
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
                ['getEmpStatus', 'getId'],
                ['getEmpStatus', 'getName'],
                ['getSupervisors', ['getEmpNumber', 'getLastName', 'getFirstName', 'getMiddleName']],
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
                ['empStatus', 'id'],
                ['empStatus', 'name'],
                ['supervisors', ['empNumber', 'lastName', 'firstName', 'middleName']]
            ]
        );
    }
}
