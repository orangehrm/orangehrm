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
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Framework\Http\Request;

class PunchInController extends AbstractVueController
{
    use AttendanceServiceTrait;
    use AuthUserTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        // check if previous record is a punch in.
        $attendanceRecord = $this->getAttendanceService()
            ->getAttendanceDao()
            ->getLastPunchRecordByEmployeeNumberAndActionableList(
                $this->getAuthUser()->getEmpNumber(),
                [AttendanceRecord::STATE_PUNCHED_IN]
            );
        //previous record is punched in, redirect to punch out
        if ($attendanceRecord instanceof AttendanceRecord) {
            $this->setResponse($this->redirect('/attendance/punchOut'));
            return;
        }

        $component = new Component('attendance-punch-in');
        //if configuration enabled, editable is true
        if ($this->getAttendanceService()->canUserChangeCurrentTime()) {
            $component->addProp(new Prop('is-editable', Prop::TYPE_BOOLEAN, true));
        }
        $this->setComponent($component);
    }
}
