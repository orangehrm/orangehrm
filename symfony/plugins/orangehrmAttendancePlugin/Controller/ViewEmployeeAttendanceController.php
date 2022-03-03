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

namespace OrangeHRM\Attendance\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Attendance\Traits\Service\AttendanceServiceTrait;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;

class ViewEmployeeAttendanceController extends AbstractVueController
{
    use AuthUserTrait;
    use UserRoleManagerTrait;
    use EmployeeServiceTrait;
    use AttendanceServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        if ($request->query->has('employeeId')) {
            $empNumber = $request->query->getInt('employeeId');
            if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible($empNumber)) {
                throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
            }

            $component = new Component('view-employee-attendance-detailed');
            $component->addProp(
                new Prop(
                    'employee',
                    Prop::TYPE_OBJECT,
                    $this->getEmployeeService()->getEmployeeAsArray($empNumber)
                )
            );

            if (
                $this->getAttendanceService()->canSupervisorModifyAttendanceConfiguration()
                && $this->getAuthUser()->getEmpNumber() !== $empNumber
            ) {
                $component->addProp(new Prop('is-editable', Prop::TYPE_BOOLEAN, true));
            }
        } else {
            $component = new Component('view-employee-attendance-summary');
        }

        if ($request->query->has('date')) {
            $component->addProp(new Prop('date', Prop::TYPE_STRING, $request->query->get('date')));
        }
        $this->setComponent($component);
    }
}
