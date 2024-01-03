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

namespace OrangeHRM\Time\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\TimesheetActionLog;

/**
 * @OA\Schema(
 *     schema="Time-TimesheetActionLogModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="action", type="object",
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="label", type="string"),
 *     ),
 *     @OA\Property(property="comment", type="string"),
 *     @OA\Property(property="date", type="string", format="date"),
 *     @OA\Property(property="performedEmployee", type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="terminationId", type="integer")
 *     )
 * )
 */
class TimesheetActionLogModel implements Normalizable
{
    use ModelTrait;

    public function __construct(TimesheetActionLog $timesheetActionLog)
    {
        $this->setEntity($timesheetActionLog);
        $this->setFilters([
            'id',
            'action',
            ['getDecorator', 'getActionLabel'],
            'comment',
            ['getDecorator', 'getDate'],
            ['getPerformedUser', 'getEmployee', 'getEmpNumber'],
            ['getPerformedUser', 'getEmployee', 'getLastName'],
            ['getPerformedUser', 'getEmployee', 'getFirstName'],
            ['getPerformedUser', 'getEmployee', 'getMiddleName'],
            ['getPerformedUser', 'getEmployee', 'getEmployeeId'],
            ['getPerformedUser', 'getEmployee', 'getEmployeeTerminationRecord', 'getId'],
        ]);

        $this->setAttributeNames([
            'id',
            ['action', 'name'],
            ['action', 'label'],
            'comment',
            'date',
            ['performedEmployee', 'empNumber'],
            ['performedEmployee', 'lastName'],
            ['performedEmployee', 'firstName'],
            ['performedEmployee', 'middleName'],
            ['performedEmployee', 'employeeId'],
            ['performedEmployee', 'terminationId'],
        ]);
    }
}
