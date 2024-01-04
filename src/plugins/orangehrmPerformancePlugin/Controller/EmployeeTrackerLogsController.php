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

namespace OrangeHRM\Performance\Controller;

use OrangeHRM\Core\Authorization\Controller\CapableViewController;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Controller\Common\NoRecordsFoundController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Entity\PerformanceTracker;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Performance\Traits\Service\PerformanceTrackerServiceTrait;

class EmployeeTrackerLogsController extends AbstractVueController implements CapableViewController
{
    use PerformanceTrackerServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $id = $request->attributes->getInt('id');
        $component = new Component('employee-tracker-logs');

        $tracker = $this->getPerformanceTrackerService()->getPerformanceTrackerDao()->getPerformanceTracker($id);
        if (!is_null($tracker)) {
            $component->addProp(new Prop('tracker-id', Prop::TYPE_NUMBER, $tracker->getId()));
            $component->addProp(new Prop('emp-number', Prop::TYPE_NUMBER, $tracker->getEmployee()->getEmpNumber()));
        }

        $this->setComponent($component);
    }

    /**
     * @inheritDoc
     */
    public function isCapable(Request $request): bool
    {
        $id = $request->attributes->getInt('id');
        $performanceTracker = $this->getPerformanceTrackerService()
            ->getPerformanceTrackerDao()
            ->getPerformanceTracker($id);
        if (is_null($performanceTracker) || !is_null($performanceTracker->getEmployee()->getPurgedAt())) {
            throw new RequestForwardableException(NoRecordsFoundController::class . '::handle');
        }
        return $this->getUserRoleManager()->isEntityAccessible(PerformanceTracker::class, $id);
    }
}
