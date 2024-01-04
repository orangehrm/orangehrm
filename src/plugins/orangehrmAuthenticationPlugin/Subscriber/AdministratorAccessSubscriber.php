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

namespace OrangeHRM\Authentication\Subscriber;

use OrangeHRM\Authentication\Controller\AdminPrivilegeController;
use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AdministratorAccessSubscriber extends AbstractEventSubscriber
{
    use AuthUserTrait;
    use TextHelperTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onControllerEvent', -10000]
            ]
        ];
    }

    /**
     * @param ControllerEvent $controllerEvent
     * @return void
     */
    public function onControllerEvent(ControllerEvent $controllerEvent): void
    {
        if ($controllerEvent->getController()[0] instanceof AdminPrivilegeController) {
            return;
        }
        if ($controllerEvent->getController()[0] instanceof AbstractVueController) {
            $this->getAuthUser()->setHasAdminAccess(false);
        }
    }
}
