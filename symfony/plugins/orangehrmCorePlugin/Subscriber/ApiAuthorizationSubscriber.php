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

use OrangeHRM\Core\Api\V2\Exception\ForbiddenException;
use OrangeHRM\Core\Controller\Rest\V2\AbstractRestController;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Core\Traits\UserRoleManagerTrait;
use OrangeHRM\Framework\Event\AbstractEventSubscriber;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiAuthorizationSubscriber extends AbstractEventSubscriber
{
    use ServiceContainerTrait;
    use UserRoleManagerTrait;

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
        if (!$this->getControllerInstance($event) instanceof AbstractRestController) {
            return;
        }

        $apiClass = $event->getRequest()->attributes->get('_api');
        if (is_null($apiClass)) {
            throw new ForbiddenException('`_api` parameter not defined in API routes');
        }
        $permissions = $this->getUserRoleManager()->getApiPermissions($apiClass);

        $permissionGetter = $this->getPermissionGetterMethod($event->getRequest()->getMethod());
        if (is_null($permissionGetter) || !$permissions->$permissionGetter()) {
            throw new ForbiddenException('Unauthorized');
        }
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onExceptionEvent(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof ForbiddenException) {
            $response = new Response();
            $message = 'Unauthorized';
            $code = Response::HTTP_FORBIDDEN;
            $response->setContent(
                \OrangeHRM\Core\Api\V2\Response::formatError(
                    ['error' => ['status' => $code, 'message' => $message]]
                )
            );
            $response->headers->set(
                \OrangeHRM\Core\Api\V2\Response::CONTENT_TYPE_KEY,
                \OrangeHRM\Core\Api\V2\Response::CONTENT_TYPE_JSON
            );
            $response->setStatusCode($code);
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

    /**
     * @param string $method
     * @return string|null
     */
    private function getPermissionGetterMethod(string $method): ?string
    {
        switch ($method) {
            case Request::METHOD_GET:
                return 'canRead';

            case Request::METHOD_POST:
                return 'canCreate';

            case Request::METHOD_PUT:
                return 'canUpdate';

            case Request::METHOD_DELETE:
                return 'canDelete';

            default:
                return null;
        }
    }
}
