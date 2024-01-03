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
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Entity\PerformanceTrackerLog;
use OrangeHRM\Performance\Api\Traits\PerformanceTrackerPermissionTrait;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerLogServiceTrait;

/**
 * @OA\Schema(
 *     schema="Performance-PerformanceTrackerLogModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="log", type="string"),
 *     @OA\Property(property="comment", type="string"),
 *     @OA\Property(property="achievement", type="string"),
 *     @OA\Property(property="addedDate", type="number"),
 *     @OA\Property(property="modifiedDate", type="number"),
 *     @OA\Property(
 *         property="reviewer",
 *         type="object",
 *         @OA\Property(property="empNumber", type="integer"),
 *         @OA\Property(property="lastName", type="string"),
 *         @OA\Property(property="firstName", type="string"),
 *         @OA\Property(property="middleName", type="string"),
 *         @OA\Property(property="terminationId", type="integer"),
 *     ),
 * )
 */
class PerformanceTrackerLogModel implements Normalizable
{
    use ModelTrait {ModelTrait::toArray as entityToArray;}
    use PerformanceTrackerLogServiceTrait;
    use PerformanceTrackerPermissionTrait;
    use UserRoleManagerTrait;

    public function __construct(PerformanceTrackerLog $performanceTrackerLog)
    {
        $this->setEntity($performanceTrackerLog);
        $this->setFilters(
            [
                'id',
                'log',
                'comment',
                'achievement',
                ['getDecorator', 'getAddedDate'],
                ['getDecorator', 'getModifiedDate'],
                ['getEmployee', 'getEmpNumber'],
                ['getEmployee', 'getLastName'],
                ['getEmployee', 'getFirstName'],
                ['getEmployee', 'getMiddleName'],
                ['getEmployee', 'getEmployeeTerminationRecord', 'getId'],
            ]
        );
        $this->setAttributeNames(
            [
                'id',
                'log',
                'comment',
                'achievement',
                'addedDate',
                'modifiedDate',
                ['reviewer', 'empNumber'],
                ['reviewer', 'lastName'],
                ['reviewer', 'firstName'],
                ['reviewer', 'middleName'],
                ['reviewer', 'terminationId'],
            ]
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $editability = $this->getUserRoleManager()->isEntityAccessible(
            PerformanceTrackerLog::class,
            $this->getEntity()->getId()
        );
        $result = $this->entityToArray();
        $result['editable'] = $editability;
        return $result;
    }
}
