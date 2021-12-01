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

namespace OrangeHRM\Core\Subscriber;

use OrangeHRM\Core\Controller\Common\TimeSheetPeriodNotDefinedController;
use OrangeHRM\Core\Controller\Exception\RequestForwardableException;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Time\Controller\EmployeeTimeSheetController;
use OrangeHRM\Time\Controller\TimeSheetPeriodConfigController;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TimeSheetPeriodSubscriber extends AbstractEventSubscriber
{
    use TextHelperTrait;
    use ConfigServiceTrait;
    use UserRoleManagerTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequestEvent', 100],
            ],
        ];
    }

    /**
     * @param RequestEvent $event
     * @return void
     * @throws RequestForwardableException
     */
    public function onRequestEvent(RequestEvent $event): void
    {
        if ($event->isMasterRequest()) {
            if ($this->getTextHelper()->strStartsWith($event->getRequest()->getPathInfo(), '/' . 'time')) {
                /**if time sheet period start day is not defined
                 * Admin user -> will navigate to configuration page of defining the start day
                 * normal user -> warning page
                 */
                $status = $this->getConfigService()->isTimesheetPeriodDefined();
                if (!$status) {
                    $employeeRole = $this->getUserRoleManager()->getUser()->getUserRole()->getName();
                    if ($employeeRole === 'Admin') {
                        // Admin user -> will navigate to configuration page of defining the start day
                        throw new RequestForwardableException(TimeSheetPeriodConfigController::class . '::handle');
                    } else {
                        // normal user -> warning page
                        throw new RequestForwardableException(TimeSheetPeriodNotDefinedController::class . '::handle');
                    }
                }
            }
            // need to block the user once time period start day is set
            if ($this->getTextHelper()->strStartsWith($event->getRequest()->getPathInfo(), '/time/viewTimeModule')) {
                $status = $this->getConfigService()->isTimesheetPeriodDefined();
                if ($status) {
                    throw new RequestForwardableException(EmployeeTimeSheetController::class . '::handle');
                }
            }
        }
    }
}
