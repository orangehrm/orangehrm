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

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\Timesheet;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Time\Traits\Service\TimesheetServiceTrait;

class MyTimesheetController extends AbstractVueController
{
    use AuthUserTrait;
    use DateTimeHelperTrait;
    use TimesheetServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $this->createDefaultTimesheetIfNotExist();
        $component = new Component('my-timesheet');
        if ($request->query->has('startDate')) {
            $component->addProp(new Prop('start-date', Prop::TYPE_STRING, $request->query->get('startDate')));
        }
        $this->setComponent($component);
    }

    /**
     * @return void
     */
    private function createDefaultTimesheetIfNotExist(): void
    {
        $currentDate = $this->getDateTimeHelper()->getNow();
        $status = $this->getTimesheetService()->hasTimesheetForDate($this->getAuthUser()->getEmpNumber(), $currentDate);
        if (!$status) {
            $timesheet = new Timesheet();
            $timesheet->getDecorator()->setEmployeeByEmployeeNumber($this->getAuthUser()->getEmpNumber());
            $this->getTimesheetService()->createTimesheetByDate($timesheet, $currentDate);
        }
    }
}
