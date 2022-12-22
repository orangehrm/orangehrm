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

namespace OrangeHRM\Time\Subscriber;

use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Time\Controller\TimeModuleController;
use OrangeHRM\Time\Controller\TimesheetStartDateIndependentController;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TimesheetPeriodSubscriber extends AbstractEventSubscriber
{
    use TextHelperTrait;
    use ConfigServiceTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onControllerEvent', 0],
            ],
        ];
    }

    /**
     * @param ControllerEvent $event
     */
    public function onControllerEvent(ControllerEvent $event): void
    {
        if ($event->isMainRequest()) {
            $isTimeControllerPath = $this->getTextHelper()->strStartsWith($event->getRequest()->getPathInfo(), '/time');
            $isTimeApiPath = $this->getTextHelper()->strStartsWith($event->getRequest()->getPathInfo(), '/api/v2/time');
            if ($isTimeControllerPath || $isTimeApiPath) {
                $status = $this->getConfigService()->isTimesheetPeriodDefined();
                if ($status) {
                    // If timesheet start date define, all good
                    return;
                }

                if ($event->getController()[0] instanceof TimesheetStartDateIndependentController) {
                    return;
                }
                if ($this->getTextHelper()->strStartsWith(
                    $event->getRequest()->getPathInfo(),
                    '/api/v2/time/time-sheet-period'
                )) {
                    // Allow only this API when timesheet start date not defined
                    return;
                }
                if ($isTimeApiPath) {
                    throw new ForbiddenException('Timesheet start date not defined');
                }

                $event->setController([new TimeModuleController(), 'handle']);
            }
        }
    }
}
