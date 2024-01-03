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

use OrangeHRM\Attendance\Dto\AttendanceConfiguration;
use OrangeHRM\Core\Api\V2\Serializer\Normalizable;

/**
 * @OA\Schema(
 *     schema="Attendance-AttendanceConfigurationModel",
 *     type="object",
 *     @OA\Property(property="canUserChangeCurrentTime", type="boolean"),
 *     @OA\Property(property="canUserModifyAttendance", type="boolean"),
 *     @OA\Property(property="canSupervisorModifyAttendance", type="boolean"),
 * )
 */
class AttendanceConfigurationModel implements Normalizable
{
    private AttendanceConfiguration $attendanceConfiguration;

    /**
     * @param  AttendanceConfiguration  $attendanceConfiguration
     */
    public function __construct(AttendanceConfiguration $attendanceConfiguration)
    {
        $this->attendanceConfiguration = $attendanceConfiguration;
    }

    /**
     * @return AttendanceConfiguration
     */
    public function getAttendanceConfiguration(): AttendanceConfiguration
    {
        return $this->attendanceConfiguration;
    }

    public function toArray(): array
    {
        $attendanceConfiguration = $this->getAttendanceConfiguration();
        return [
            "canUserChangeCurrentTime" => $attendanceConfiguration->canUserChangeCurrentTime(),
            "canUserModifyAttendance" => $attendanceConfiguration->canUserModifyAttendance(),
            "canSupervisorModifyAttendance" => $attendanceConfiguration->canSupervisorModifyAttendance()
        ];
    }
}
