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
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Entity\AttendanceRecord;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;

class EmployeePunchInOutController extends AbstractVueController
{
    use AttendanceServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->query->has('employeeId')) {
            $empNumber = $request->query->getInt('employeeId');
            // check if previous record is a punch in.
            $attendanceRecord = $this->getAttendanceService()
                ->getAttendanceDao()
                ->getLastPunchRecordByEmployeeNumberAndActionableList(
                    $empNumber,
                    [AttendanceRecord::STATE_PUNCHED_IN]
                );

            //previous record is not present redirect to punch in
            if (!$attendanceRecord instanceof AttendanceRecord) {
                $component = new Component('attendance-punch-in');
            } else {
                $component = new Component('attendance-punch-out');
            }

            $component->addProp(new Prop('is-editable', Prop::TYPE_BOOLEAN, true));
            $component->addProp(new Prop('employee-id', Prop::TYPE_NUMBER, $empNumber));
            $component->addProp(new Prop('is-timezone-editable', Prop::TYPE_BOOLEAN, true));

            if ($attendanceRecord) {
                $component->addProp(new Prop('attendance-record-id', Prop::TYPE_NUMBER, $attendanceRecord->getId()));
            }

            if ($request->query->has('date')) {
                $component->addProp(new Prop('date', Prop::TYPE_STRING, $request->query->get('date')));
            }
        } else {
            throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }
        $this->setComponent($component);
    }
}
