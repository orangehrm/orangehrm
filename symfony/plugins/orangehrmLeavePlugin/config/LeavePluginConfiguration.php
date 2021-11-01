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

use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;
use OrangeHRM\Leave\Service\HolidayService;
use OrangeHRM\Leave\Service\LeaveConfigurationService;
use OrangeHRM\Leave\Service\LeaveEntitlementService;
use OrangeHRM\Leave\Service\LeavePeriodService;
use OrangeHRM\Leave\Service\LeaveRequestService;
use OrangeHRM\Leave\Service\LeaveTypeService;
use OrangeHRM\Leave\Service\WorkScheduleService;
use OrangeHRM\Leave\Service\WorkWeekService;
use OrangeHRM\Leave\Subscriber\LeaveEventSubscriber;

class LeavePluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;
    use EventDispatcherTrait;

    /**
     * @inheritDoc
     */
    public function initialize(Request $request): void
    {
        $this->getContainer()->register(
            Services::LEAVE_CONFIG_SERVICE,
            LeaveConfigurationService::class
        );
        $this->getContainer()->register(
            Services::LEAVE_TYPE_SERVICE,
            LeaveTypeService::class
        );
        $this->getContainer()->register(
            Services::LEAVE_ENTITLEMENT_SERVICE,
            LeaveEntitlementService::class
        );
        $this->getContainer()->register(
            Services::LEAVE_PERIOD_SERVICE,
            LeavePeriodService::class
        );
        $this->getContainer()->register(
            Services::LEAVE_REQUEST_SERVICE,
            LeaveRequestService::class
        );
        $this->getContainer()->register(
            Services::WORK_SCHEDULE_SERVICE,
            WorkScheduleService::class
        );
        $this->getContainer()->register(
            Services::HOLIDAY_SERVICE,
            HolidayService::class
        );
        $this->getContainer()->register(
            Services::WORK_WEEK_SERVICE,
            WorkWeekService::class
        );

        $this->getEventDispatcher()->addSubscriber(new LeaveEventSubscriber());
    }
}
