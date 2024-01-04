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

namespace OrangeHRM\Attendance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\CollectionNormalizable;
use OrangeHRM\Core\Api\V2\Serializer\ModelConstructorArgsAwareInterface;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;

/**
 * @OA\Schema(
 *     schema="Attendance-EmployeeAttendanceSummeryListModel",
 *     type="object",
 *     @OA\Property(property="empNumber", type="integer"),
 *     @OA\Property(property="lastName", type="string"),
 *     @OA\Property(property="firstName", type="string"),
 *     @OA\Property(property="middleName", type="string"),
 *     @OA\Property(property="employeeId", type="string"),
 *     @OA\Property(property="terminationId", type="integer"),
 *     @OA\Property(
 *         property="sum",
 *         type="object",
 *         @OA\Property(property="hours", type="integer"),
 *         @OA\Property(property="minutes", type="integer"),
 *         @OA\Property(property="label", type="string")
 *     ),
 * )
 */
class EmployeeAttendanceSummaryListModel implements CollectionNormalizable, ModelConstructorArgsAwareInterface
{
    use DateTimeHelperTrait;
    use NumberHelperTrait;

    /**
     * @var array
     */
    private array $employeeAttendanceSummaryRecords;

    public function __construct(array $employeeAttendanceSummaryRecords)
    {
        $this->employeeAttendanceSummaryRecords = $employeeAttendanceSummaryRecords;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->employeeAttendanceSummaryRecords as $employeeAttendanceRecord) {
            $result[] = [
                'empNumber' => $employeeAttendanceRecord['empNumber'],
                'lastName' => $employeeAttendanceRecord['lastName'],
                'firstName' => $employeeAttendanceRecord['firstName'],
                'middleName' => $employeeAttendanceRecord['middleName'],
                'employeeId' => $employeeAttendanceRecord['employeeId'],
                'terminationId' => $employeeAttendanceRecord['terminationId'],
                'sum' => [
                    'hours' => floor((float)$employeeAttendanceRecord['total'] / 3600),
                    'minutes' => ((float)$employeeAttendanceRecord['total'] / 60) % 60,
                    'label' => $this->getNumberHelper()->numberFormat(
                        (float)$employeeAttendanceRecord['total'] / 3600,
                        2
                    ),
                ],
            ];
        }
        return $result;
    }
}
