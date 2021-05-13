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

namespace OrangeHRM\Authentication\Subscriber;

use Exception;
use OrangeHRM\Core\Controller\AbstractViewController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Controller\Rest\V2\AbstractRestController;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\ServiceContainer;
use OrangeHRM\Framework\Services;
use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Authentication\Exception\SessionExpiredException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onControllerEvent', 0],
            ],
            KernelEvents::EXCEPTION => [
                ['onExceptionEvent', 0],
            ],
        ];
    }

    /**
     * @param ControllerEvent $event
     * @throws Exception
     */
    public function onControllerEvent(ControllerEvent $event)
    {
        if (User::getInstance()->isAuthenticated()) {
            return;
        }

        if ($this->getControllerInstance($event) instanceof PublicControllerInterface) {
            return;
        }

        /** @var UrlGenerator $urlGenerator */
        $urlGenerator = ServiceContainer::getContainer()->get(Services::URL_GENERATOR);

        if ($this->getControllerInstance($event) instanceof AbstractViewController) {
            throw new SessionExpiredException();
        }

        if ($this->getControllerInstance($event) instanceof AbstractRestController) {
            throw new UnauthorizedHttpException($urlGenerator->generate('auth_login'), 'Session expired');
        }

        // Fallback
        throw new SessionExpiredException();
    }

    /**
     * @param ExceptionEvent $event
     * @throws Exception
     */
    public function onExceptionEvent(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof SessionExpiredException) {
            /** @var UrlGenerator $urlGenerator */
            $urlGenerator = ServiceContainer::getContainer()->get(Services::URL_GENERATOR);

            $loginUrl = $urlGenerator->generate('auth_login', [], UrlGenerator::ABSOLUTE_URL);
            $response = new RedirectResponse($loginUrl);

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
