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
 *     schema="Pim-EmployeeModel",
 *     type="object",
 *     @OA\Property(property="empNumber", description="The employee number of the employee", type="integer"),
 *     @OA\Property(property="lastName", description="The last name of the employee", type="string"),
 *     @OA\Property(property="firstName", description="The first name of the employee", type="string"),
 *     @OA\Property(property="middleName", description="The middle name of the employee", type="string"),
 *     @OA\Property(property="employeeId", description="The employee ID of the employee", type="string"),
 *     @OA\Property(property="terminationId", description="The numerical ID of the employee's termination record", type="integer"),
 * )
 */
class EmployeeModel implements Normalizable
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
            ]
        );
    }
}
