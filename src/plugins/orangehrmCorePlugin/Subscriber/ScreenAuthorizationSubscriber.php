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

use OrangeHRM\Authentication\Controller\ForbiddenController;
use OrangeHRM\Authentication\Exception\ForbiddenException;
use OrangeHRM\Core\Authorization\Controller\CapableViewController;
use OrangeHRM\Core\Authorization\Dto\ResourcePermission;
use OrangeHRM\Core\Controller\AbstractViewController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Traits\ControllerTrait;
use OrangeHRM\Core\Traits\ModuleScreenHelperTrait;
use OrangeHRM\Core\Traits\Service\TextHelperTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ScreenAuthorizationSubscriber extends AbstractEventSubscriber
{
    use ServiceContainerTrait;
    use UserRoleManagerTrait;
    use TextHelperTrait;
    use ModuleScreenHelperTrait;
    use ControllerTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onControllerEvent', 80000],
            ],
            KernelEvents::EXCEPTION => [
                ['onExceptionEvent', 0],
            ],
        ];
    }

    /**
     * @param ControllerEvent $event
     */
    public function onControllerEvent(ControllerEvent $event)
    {
        if ($this->getControllerInstance($event) instanceof PublicControllerInterface) {
            return;
        }

        $module = $this->getCurrentModuleAndScreen()->getModule();
        $screen = $this->getCurrentModuleAndScreen()->getScreen();

        if ($module === 'auth' && $screen == 'logout') {
            return;
        }

        if (($controller = $this->getControllerInstance($event)) instanceof AbstractViewController) {
            $permissions = $this->getUserRoleManager()->getScreenPermissions($module, $screen);

            if (!$permissions instanceof ResourcePermission || !$permissions->canRead()) {
                throw new ForbiddenException();
            }

            if ($controller instanceof CapableViewController) {
                if (!$controller->isCapable($event->getRequest())) {
                    throw new ForbiddenException();
                }
            }
        }
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onExceptionEvent(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ForbiddenException) {
            $response = $this->forward(ForbiddenController::class . '::handle');
            $event->setResponse($response);
            $event->stopPropagation();
        }
    }

    /**
     * @param ControllerEvent $event
     * @return mixed
     */
    private function getControllerInstance(ControllerEvent $event)
    {
        return $event->getController()[0];
    }
}
