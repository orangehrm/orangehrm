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

use OrangeHRM\Core\Traits\EventDispatcherTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Dashboard\Service\ChartService;
use OrangeHRM\Dashboard\Service\EmployeeActionSummaryService;
use OrangeHRM\Dashboard\Service\EmployeeOnLeaveService;
use OrangeHRM\Dashboard\Service\EmployeeTimeAtWorkService;
use OrangeHRM\Dashboard\Service\QuickLaunchService;
use OrangeHRM\Dashboard\Subscriber\BuzzModuleStatusChangeSubscriber;
use OrangeHRM\Dashboard\Subscriber\LeaveModuleStatusChangeSubscriber;
use OrangeHRM\Dashboard\Subscriber\TimeModuleStatusChangeSubscriber;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\PluginConfigurationInterface;
use OrangeHRM\Framework\Services;

class DashboardPluginConfiguration implements PluginConfigurationInterface
{
    use ServiceContainerTrait;
    use EventDispatcherTrait;

    /**
     * @inheritDoc
     */
    public function initialize(Request $request): void
    {
        $this->getContainer()->register(
            Services::EMPLOYEE_ON_LEAVE_SERVICE,
            EmployeeOnLeaveService::class
        );

        $this->getContainer()->register(
            Services::CHART_SERVICE,
            ChartService::class
        );
        $this->getContainer()->register(
            Services::QUICK_LAUNCH_SERVICE,
            QuickLaunchService::class
        );

        $this->getContainer()->register(
            Services::EMPLOYEE_TIME_AT_WORK_SERVICE,
            EmployeeTimeAtWorkService::class
        );

        $this->getContainer()->register(
            Services::EMPLOYEE_ACTION_SUMMARY_SERVICE,
            EmployeeActionSummaryService::class
        );

        $this->getEventDispatcher()->addSubscriber(new TimeModuleStatusChangeSubscriber());
        $this->getEventDispatcher()->addSubscriber(new LeaveModuleStatusChangeSubscriber());
        $this->getEventDispatcher()->addSubscriber(new BuzzModuleStatusChangeSubscriber());
    }
}
