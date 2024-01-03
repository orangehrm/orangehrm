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
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Leave\Traits\Service\LeavePeriodServiceTrait;

class MyLeaveEntitlementReport extends AbstractVueController
{
    use LeavePeriodServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('my-leave-entitlement-report');
        $leavePeriod = $this->getLeavePeriodService()->getNormalizedCurrentLeavePeriod();
        $leavePeriod = [
            "id" => $leavePeriod['startDate'] . "_" . $leavePeriod['endDate'],
            "label" => $leavePeriod['startDate'] . " - " . $leavePeriod['endDate'],
            "startDate" => $leavePeriod['startDate'],
            "endDate" => $leavePeriod['endDate'],
        ];

        $component->addProp(new Prop('leave-period', Prop::TYPE_OBJECT, $leavePeriod));
        $this->setComponent($component);
    }
}
