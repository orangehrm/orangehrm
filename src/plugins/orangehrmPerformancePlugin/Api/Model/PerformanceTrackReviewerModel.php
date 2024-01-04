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
use OrangeHRM\Entity\PerformanceTrackerReviewer;

/**
 * @OA\Schema(
 *     schema="Performance-PerformanceTrackReviewerModel",
 *     type="object",
 *     @OA\Property(property="empNumber", type="integer"),
*      @OA\Property(property="firstName", type="string"),
 *     @OA\Property(property="lastName", type="string"),
 *     @OA\Property(property="middleName", type="string"),
 *     @OA\Property(property="employeeId", type="string"),
 *     @OA\Property(property="terminationId", type="integer"),
 * )
 */
class PerformanceTrackReviewerModel implements Normalizable
{
    use ModelTrait;

    public function __construct(PerformanceTrackerReviewer $performanceTrackerReviewer)
    {
        $this->setEntity($performanceTrackerReviewer);
        $this->setFilters(
            [
                'id',
                ['getReviewer', 'getEmpNumber'],
                ['getReviewer', 'getLastName'],
                ['getReviewer', 'getFirstName'],
                ['getReviewer', 'getMiddleName'],
                ['getReviewer', 'getEmployeeId'],
                ['getReviewer', 'getEmployeeTerminationRecord', 'getId'],

            ]
        );
        $this->setAttributeNames(
            [
                'id',
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
