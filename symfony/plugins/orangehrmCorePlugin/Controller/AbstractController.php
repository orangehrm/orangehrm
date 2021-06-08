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

namespace OrangeHRM\Core\Controller;

use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\RequestStack;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Framework\Services;

abstract class AbstractController
{
    use ServiceContainerTrait;

    /**
     * @var Response|null
     */
    protected ?Response $response = null;

    /**
     * @return Response
     */
    protected function getNewResponse(): Response
    {
        return new Response();
    }

    /**
     * @return Response
     */
    protected function getResponse(): Response
    {
        if (!$this->response instanceof Response) {
            $this->response = $this->getNewResponse();
        }
        return $this->response;
    }

    /**
     * @param string $path
     * @return RedirectResponse
     */
    protected function redirect(string $path): RedirectResponse
    {
        $request = $this->getCurrentRequest();
        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBaseUrl();
        if (substr($path, 0, 1) !== "/") {
            $path = "/" . $path;
        }
        return new RedirectResponse($baseUrl . $path);
    }

    /**
     * @return Request|null
     */
    private function getCurrentRequest(): ?Request
    {
        /** @var RequestStack $requestStack */
        $requestStack = $this->getContainer()->get(Services::REQUEST_STACK);
        return $requestStack->getCurrentRequest();
    }

    /**
     * @param Response|null $response
     * @return Response
     */
    protected function handleBadRequest(?Response $response = null): Response
    {
        if (is_null($response)) {
            $response = $this->getResponse();
        }

        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
        return $response;
    }
}
