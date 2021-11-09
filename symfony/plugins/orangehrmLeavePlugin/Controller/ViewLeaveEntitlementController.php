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

namespace OrangeHRM\Leave\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Leave\Controller\Traits\PermissionTrait;
use OrangeHRM\Leave\Traits\Service\LeaveTypeServiceTrait;
use OrangeHRM\Pim\Traits\Service\EmployeeServiceTrait;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;

class ViewLeaveEntitlementController extends AbstractVueController
{
    use UserRoleManagerTrait;
    use EmployeeServiceTrait;
    use LeaveTypeServiceTrait;
    use PermissionTrait;
    use LeavePeriodServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('leave-view-entitlement');
        $empNumber = $request->get('empNumber');
        if (!is_null($empNumber)) {
            if (!$this->getUserRoleManagerHelper()->isEmployeeAccessible($empNumber)) {
                throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
            }

            $component->addProp(
                new Prop(
                    'employee',
                    Prop::TYPE_OBJECT,
                    $this->getEmployeeService()->getEmployeeAsArray($empNumber)
                )
            );
        }
        $this->addLeaveTypeProp($request, $component);
        $this->addLeavePeriodProp($request, $component);

        $this->setComponent($component);

        // $empNumber can be null
        $this->setPermissionsForEmployee(['leave_entitlements'], $empNumber);
    }

    /**
     * @param Request $request
     * @param Component $component
     */
    protected function addLeaveTypeProp(Request $request, Component $component): void
    {
        $leaveTypeId = $request->get('leaveTypeId');
        if (!is_null($leaveTypeId)) {
            $leaveType = $this->getLeaveTypeService()->getLeaveTypeAsArray($leaveTypeId);
            $component->addProp(new Prop('leave-type', Prop::TYPE_OBJECT, $leaveType));
        }
    }

    /**
     * @param Request $request
     * @param Component $component
     */
    protected function addLeavePeriodProp(Request $request, Component $component): void
    {
        $startDate = $request->get('startDate');
        $endDate = $request->get('endDate');
        if ($startDate && $endDate) {
            $leavePeriod = [
                'id' => "${startDate}_${endDate}",
                'label' => "$startDate - $endDate",
                'startDate' => "$startDate",
                'endDate' => "$endDate"
            ];
        } else {
            $leavePeriod = $this->getLeavePeriodService()->getNormalizedCurrentLeavePeriod();
            $leavePeriod = [
                'id' => $leavePeriod['startDate'] . '_' . $leavePeriod['endDate'],
                'label' => $leavePeriod['startDate'] . ' - ' . $leavePeriod['endDate'],
                'startDate' => $leavePeriod['startDate'],
                'endDate' => $leavePeriod['endDate'],
            ];
        }
        $component->addProp(new Prop('leave-period', Prop::TYPE_OBJECT, $leavePeriod));
    }
}
