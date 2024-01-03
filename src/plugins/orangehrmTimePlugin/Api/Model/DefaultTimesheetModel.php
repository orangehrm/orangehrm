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

use OrangeHRM\Core\Api\V2\Serializer\Normalizable;
use OrangeHRM\Entity\Timesheet;

/**
 * @OA\Schema(
 *     schema="Time-DefaultTimesheetModel",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="status", type="object",
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="integer"),
 *     ),
 *     @OA\Property(property="startDate", type="string", format="date"),
 *     @OA\Property(property="endDate", type="string", format="date")
 * )
 */
class DefaultTimesheetModel implements Normalizable
{
    /**
     * @var Timesheet
     */
    private Timesheet $timesheet;

    public function __construct(Timesheet $timesheet)
    {
        $this->timesheet = $timesheet;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $dates = [
            'id' => null,
            'status' => [
                'id' => null,
                'name' => null
            ],
            'startDate' => $this->timesheet->getDecorator()->getStartDate(),
            'endDate' => $this->timesheet->getDecorator()->getEndDate()
        ];
        if ($this->timesheet->getId() === 0) {
            return $dates;
        }
        $dates['id'] = $this->timesheet->getId();
        $dates['status'] = [
            'id' => $this->timesheet->getState(),
            'name' => $this->timesheet->getDecorator()->getTimesheetState()
        ];
        return $dates;
    }
}
