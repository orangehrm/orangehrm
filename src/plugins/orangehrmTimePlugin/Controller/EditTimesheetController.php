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

namespace OrangeHRM\Time\Controller;

use OrangeHRM\Core\Authorization\Controller\CapableViewController;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class EditTimesheetController extends AbstractVueController implements CapableViewController
{
    use AuthUserTrait;
    use TimesheetServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        // TODO: show 404 if no id
        if ($request->attributes->has('id')) {
            $timesheetId = $request->attributes->getInt('id');
            $component = new Component('edit-timesheet');
            $component->addProp(new Prop('timesheet-id', Prop::TYPE_NUMBER, $timesheetId));

            $timesheet = $this->getTimesheetService()->getTimesheetDao()->getTimesheetById($timesheetId);
            $timesheetOwnerEmpNumber = $timesheet->getEmployee()->getEmpNumber();
            $currentUserEmpNumber = $this->getAuthUser()->getEmpNumber();
            if ($timesheetOwnerEmpNumber === $currentUserEmpNumber) {
                $component->addProp(new Prop('my-timesheet', Prop::TYPE_BOOLEAN, true));
            }
        }

        $this->setComponent($component);
    }

    /**
     * @inheritDoc
     */
    public function isCapable(Request $request): bool
    {
        if ($request->attributes->has('id')) {
            $timesheet = $this->getTimesheetService()
                ->getTimesheetDao()
                ->getTimesheetById($request->attributes->getInt('id'));
            if ($timesheet instanceof Timesheet) {
                if ($this->getUserRoleManagerHelper()->isSelfByEmpNumber($timesheet->getEmployee()->getEmpNumber())
                    && $timesheet->getState() === 'APPROVED') {
                    return false;
                }
                return $this->getUserRoleManagerHelper()
                    ->isEmployeeAccessible($timesheet->getEmployee()->getEmpNumber());
            }
            throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }
        return true;
    }
}
