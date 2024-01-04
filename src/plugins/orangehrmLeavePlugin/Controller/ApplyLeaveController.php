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

namespace OrangeHRM\Leave\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Leave\Traits\Service\WorkScheduleServiceTrait;

class ApplyLeaveController extends AbstractVueController
{
    use WorkScheduleServiceTrait;
    use DateTimeHelperTrait;
    use AuthUserTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('leave-apply');

        $workShiftStartEndTime = $this->getWorkScheduleService()
            ->getWorkSchedule($this->getAuthUser()->getEmpNumber())
            ->getWorkShiftStartEndTime();
        $workShift = [
            'startTime' => $this->getDateTimeHelper()
                ->formatDateTimeToTimeString($workShiftStartEndTime->getStartTime()),
            'endTime' => $this->getDateTimeHelper()
                ->formatDateTimeToTimeString($workShiftStartEndTime->getEndTime()),
        ];
        $component->addProp(new Prop('work-shift', Prop::TYPE_OBJECT, $workShift));
        $this->setComponent($component);
    }
}
