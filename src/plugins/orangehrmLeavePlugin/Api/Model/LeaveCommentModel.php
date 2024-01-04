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

namespace OrangeHRM\Leave\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\LeaveComment;

/**
 * @OA\Schema(
 *     schema="Leave-LeaveCommentModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="leave", type="object",
 *         @OA\Property(property="id", type="integer"),
 *     ),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="time", type="string", format="time"),
 *     @OA\Property(property="createdByEmployee", type="object",
 *         @OA\Property(property="empNumber", type="string"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="employeeId", type="integer"),
 *         @OA\Property(property="terminationId", type="integer"),
 *     ),
 *     @OA\Property(property="comment", type="string"),
 * )
 */

class LeaveCommentModel implements Normalizable
{
    use ModelTrait;

    public function __construct(LeaveComment $leaveComment)
    {
        $this->setEntity($leaveComment);
        $this->setFilters(
            [
                'id',
                ['getLeave','getId'],
                ['getDecorator','getCreatedAtDate'],
                ['getDecorator','getCreatedAtTime'],
                ['getCreatedByEmployee','getEmpNumber'],
                ['getCreatedByEmployee','getLastName'],
                ['getCreatedByEmployee','getFirstName'],
                ['getCreatedByEmployee','getMiddleName'],
                ['getCreatedByEmployee','getEmployeeId'],
                ['getCreatedByEmployee','getEmployeeTerminationRecord', 'getId'],
                'comment',
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                ['leave','id'],
                'date',
                'time',
                ['createdByEmployee','empNumber'],
                ['createdByEmployee','lastName'],
                ['createdByEmployee','firstName'],
                ['createdByEmployee','middleName'],
                ['createdByEmployee','employeeId'],
                ['createdByEmployee','terminationId'],
                'comment',
            ]
        );
    }
}
