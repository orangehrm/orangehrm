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

namespace OrangeHRM\Leave\Subscriber;

use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Leave\Controller\LeaveModuleController;
use OrangeHRM\Leave\Controller\LeavePeriodUnnecessaryController;
use OrangeHRM\Leave\Traits\Service\LeaveConfigServiceTrait;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LeavePeriodSubscriber extends AbstractEventSubscriber
{
    use TextHelperTrait;
    use LeaveConfigServiceTrait;

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
            $isControllerPath = $this->getTextHelper()->strStartsWith($event->getRequest()->getPathInfo(), '/leave');
            $isApiPath = $this->getTextHelper()->strStartsWith($event->getRequest()->getPathInfo(), '/api/v2/leave');
            if ($isControllerPath || $isApiPath) {
                $status = $this->getLeaveConfigService()->isLeavePeriodDefined();
                if ($status) {
                    // If leave period define, all good
                    return;
                }

                if ($event->getController()[0] instanceof LeavePeriodUnnecessaryController) {
                    return;
                }
                if ($this->getTextHelper()->strStartsWith(
                    $event->getRequest()->getPathInfo(),
                    '/api/v2/leave/leave-period'
                )) {
                    // Allow only following APIs when leave period not defined
                    // * /api/v2/leave/leave-period
                    // * /api/v2/leave/leave-periods
                    return;
                }
                if ($isApiPath) {
                    throw new ForbiddenException('Leave period not defined');
                }

                $event->setController([new LeaveModuleController(), 'handle']);
            }
        }
    }
}
