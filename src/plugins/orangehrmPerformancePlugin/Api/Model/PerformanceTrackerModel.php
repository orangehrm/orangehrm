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

namespace OrangeHRM\Performance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\ModelTrait;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\PerformanceTracker;

/**
 * @OA\Schema(
 *     schema="Performance-PerformanceTrackerModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="trackerName", type="string"),
 *     @OA\Property(property="addedDate", type="number"),
 *     @OA\Property(property="modifiedDate", type="number"),
 *     @OA\Property(property="status", type="integer"),
 *     @OA\Property(
 *         property="employee",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="terminationId", type="integer"),
 *     ),
 * )
 */
class PerformanceTrackerModel implements Normalizable
{
    use ModelTrait;

    public function __construct(PerformanceTracker $performanceTracker)
    {
        $this->setEntity($performanceTracker);
        $this->setFilters(
            [
                'id',
                'trackerName',
                ['getDecorator','getAddedDate'],
                ['getDecorator','getModifiedDate'],
                'status',
                ['getEmployee', 'getEmpNumber',],
                ['getEmployee', 'getLastName'],
                ['getEmployee', 'getFirstName'],
                ['getEmployee', 'getEmployeeTerminationRecord', 'getId' ],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'trackerName',
                'addedDate',
                'modifiedDate',
                'status',
                ['employee', 'empNumber'],
                ['employee', 'lastName'],
                ['employee', 'firstName'],
                ['employee', 'terminationId'],
            ]
        );
    }
}
