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

namespace OrangeHRM\Admin\Api\Model;

use OpenApi\Annotations as OA;
use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\User;

/**
 * @OA\Schema(
 *     schema="Admin-UserModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="userName", type="string"),
 *     @OA\Property(property="deleted", type="boolean"),
 *     @OA\Property(property="status", type="boolean"),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="employeeId", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="terminationId", type="integer"),
 *     ),
 *     @OA\Property(
 *         property="userRole",
 *         type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="displayName", type="string"),
 *     )
 * )
 */
class UserModel implements Normalizable
{
    use ModelTrait;

    /**
     * @param User $systemUser
     */
    public function __construct(User $systemUser)
    {
        $this->setEntity($systemUser);
        $this->setFilters(
            [
                'id',
                'userName',
                ['isDeleted'],
                'status',
                ['getEmployee', 'getEmpNumber'],
                ['getEmployee', 'getEmployeeId'],
                ['getEmployee', 'getFirstName'],
                ['getEmployee', 'getMiddleName'],
                ['getEmployee', 'getLastName'],
                ['getEmployee', 'getEmployeeTerminationRecord', 'getId'],
                ['getUserRole', 'getId'],
                ['getUserRole', 'getName'],
                ['getUserRole', 'getDisplayName'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'userName',
                'deleted',
                'status',
                ['employee', 'empNumber'],
                ['employee', 'employeeId'],
                ['employee', 'firstName'],
                ['employee', 'middleName'],
                ['employee', 'lastName'],
                ['employee', 'terminationId'],
                ['userRole', 'id'],
                ['userRole', 'name'],
                ['userRole', 'displayName'],
            ]
        );
    }
}
