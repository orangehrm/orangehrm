<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

namespace OrangeHRM\Attendance\Api\Model;

use OrangeHRM\Core\Api\V2\Serializer\CollectionNormalizable;
use OrangeHRM\Core\Api\V2\Serializer\ModelConstructorArgsAwareInterface;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Traits\Service\NumberHelperTrait;

class AttendanceRecordListModel implements CollectionNormalizable, ModelConstructorArgsAwareInterface
{
    use NumberHelperTrait;
    use DateTimeHelperTrait;

    /**
     * @var array
     */
    private array $attendanceRecords;

    public function __construct(array $attendanceRecords)
    {
        $this->attendanceRecords = $attendanceRecords;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->attendanceRecords as $employeeAttendanceRecord) {
            $result[] = [
                'id' => $employeeAttendanceRecord['id'],
                'punchIn' => [
                    'userDate' => $this->getDateTimeHelper()->formatDateTimeToYmd(
                        $employeeAttendanceRecord['punchInTime']
                    ),
                    'userTime' => $this->getDateTimeHelper()->formatDateTimeToTimeString(
                        $employeeAttendanceRecord['punchInTime']
                    ),
                    'offset' => $employeeAttendanceRecord['punchInTimeOffset'],
                    'note' => $employeeAttendanceRecord['punchInNote'],
                ],
                'punchOut' => [
                    'userDate' => $this->getDateTimeHelper()->formatDateTimeToYmd(
                        $employeeAttendanceRecord['punchOutTime']
                    ),
                    'userTime' => $this->getDateTimeHelper()->formatDateTimeToTimeString(
                        $employeeAttendanceRecord['punchOutTime']
                    ),
                    'offset' => $employeeAttendanceRecord['punchOutTimeOffset'],
                    'note' => $employeeAttendanceRecord['punchOutNote'],
                ],
                'duration' => $this->getNumberHelper()
                    ->numberFormat((float)$employeeAttendanceRecord['total'] / 3600, 2)
            ];
        }
        return $result;
    }
}
