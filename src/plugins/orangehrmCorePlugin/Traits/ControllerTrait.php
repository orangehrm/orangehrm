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

namespace OrangeHRM\Core\Traits;

use OrangeHRM\Framework\Framework;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\RequestStack;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Framework\Services;

trait ControllerTrait
{
    use ServiceContainerTrait;

    /**
     * Forwards the request to another controller.
     *
     * @param string $controller The controller name (a string like OrangeHRM\Controller\PostController::handle)
     * @param array $attributes
     * @param array $query
     * @return Response
     */
    protected function forward(string $controller, array $attributes = [], array $query = []): Response
    {
        $request = $this->getCurrentRequest();
        $attributes['_controller'] = $controller;
        $subRequest = $request->duplicate($query, null, $attributes);

        /** @var Framework $kernel */
        $kernel = $this->getContainer()->get(Services::HTTP_KERNEL);
        return $kernel->handle($subRequest, Framework::SUB_REQUEST);
    }

    /**
     * @return Request|null
     */
    protected function getCurrentRequest(): ?Request
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->getContainer()->get(Services::REQUEST_STACK);
        return $requestStack->getCurrentRequest();
    }

    /**
     * @param string $path
     * @return RedirectResponse
     */
    protected function redirect(string $path): RedirectResponse
    {
        $request = $this->getCurrentRequest();
        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBaseUrl();
        if (substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }
        return new RedirectResponse($baseUrl . $path);
    }
}
