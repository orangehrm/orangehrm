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

use Exception;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Exception\SessionExpiredException;
use OrangeHRM\Authentication\Exception\UnauthorizedException;
use OrangeHRM\Core\Controller\AbstractModuleController;
use OrangeHRM\Core\Controller\AbstractViewController;
use OrangeHRM\Core\Controller\PublicControllerInterface;
use OrangeHRM\Core\Controller\Rest\V2\AbstractRestController;
use OrangeHRM\Core\Traits\Auth\AuthUserTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthenticationSubscriber extends AbstractEventSubscriber
{
    use ServiceContainerTrait;
    use AuthUserTrait;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onRequestEvent', 97000]],
            KernelEvents::CONTROLLER => [['onControllerEvent', 100000]],
            KernelEvents::EXCEPTION => [['onExceptionEvent', 0]],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onRequestEvent(RequestEvent $event): void
    {
        if (!$this->getAuthUser()->isAuthenticated()) {
            // Stop KernelEvents::REQUEST event propagation and let it throw an exception from AuthenticationSubscriber::onControllerEvent
            $event->stopPropagation();
        }
    }

    /**
     * @param ControllerEvent $event
     * @throws Exception
     */
    public function onControllerEvent(ControllerEvent $event): void
    {
        if ($this->getAuthUser()->isAuthenticated()) {
            return;
        }

        if ($this->getControllerInstance($event) instanceof PublicControllerInterface) {
            return;
        }

        if (
            $this->getControllerInstance($event) instanceof AbstractViewController ||
            $this->getControllerInstance($event) instanceof AbstractModuleController
        ) {
            /** @var UrlHelper $urlHelper */
            $urlHelper = $this->getContainer()->get(Services::URL_HELPER);
            $requestUri = $event->getRequest()->getRequestUri();
            $redirectUri = $urlHelper->getAbsoluteUrl($requestUri);
            $this->getAuthUser()->setAttribute(AuthUser::SESSION_TIMEOUT_REDIRECT_URL, $redirectUri);
            throw new SessionExpiredException();
        }

        if ($this->getControllerInstance($event) instanceof AbstractRestController) {
            $response = new Response();
            $message = 'Session expired';
            $response->setContent(
                \OrangeHRM\Core\Api\V2\Response::formatError(
                    ['error' => ['status' => Response::HTTP_UNAUTHORIZED, 'message' => $message]]
                )
            );
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $response->headers->set(
                \OrangeHRM\Core\Api\V2\Response::CONTENT_TYPE_KEY,
                \OrangeHRM\Core\Api\V2\Response::CONTENT_TYPE_JSON
            );
            throw new UnauthorizedException($response, $message);
        }

        // Fallback
        throw new SessionExpiredException();
    }

    /**
     * @param ExceptionEvent $event
     * @throws Exception
     */
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof SessionExpiredException) {
            /** @var UrlGenerator $urlGenerator */
            $urlGenerator = $this->getContainer()->get(Services::URL_GENERATOR);

            $loginUrl = $urlGenerator->generate('auth_login', [], UrlGenerator::ABSOLUTE_URL);
            $response = new RedirectResponse($loginUrl);

            $event->setResponse($response);
            $event->stopPropagation();
        } elseif ($exception instanceof UnauthorizedException) {
            $event->setResponse($exception->getResponse());
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
