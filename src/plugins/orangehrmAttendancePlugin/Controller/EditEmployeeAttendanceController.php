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

namespace OrangeHRM\Attendance\Controller;

use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\Common\DisabledModuleController;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Framework\Http\Request;

class EditEmployeeAttendanceController extends AbstractVueController
{
    use AttendanceServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->attributes->has('id')) {
            $attendanceRecordId = $request->attributes->getInt('id');
            $attendanceRecord = $this->getAttendanceService()
                ->getAttendanceDao()
                ->getAttendanceRecordById($attendanceRecordId);
            //no attendance record for the given id
            if (!$attendanceRecord instanceof AttendanceRecord) {
                throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
            }
            //check auth user's permission to update attendance record
            if (!$this->getAttendanceService()->isAuthUserAllowedToPerformTheEditActions($attendanceRecord)) {
                throw new RequestForwardableException(DisabledModuleController::class . '::handle');
            }
            $component = new Component('edit-attendance');
            $component->addProp(new Prop('is-employee-edit', Prop::TYPE_BOOLEAN, true));
            $component->addProp(new Prop('is-timezone-editable', Prop::TYPE_BOOLEAN, true));
            $component->addProp(new Prop('attendance-id', Prop::TYPE_NUMBER, $attendanceRecordId));
        } else {
            throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }
        $this->setComponent($component);
    }
}
