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

namespace OrangeHRM\Core\Controller;

use InvalidArgumentException;
use OrangeHRM\Core\Traits\ControllerTrait;
use OrangeHRM\Core\Traits\ServiceContainerTrait;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Response;

abstract class AbstractController
{
    use ServiceContainerTrait;
    use ControllerTrait;

    /**
     * @var Response|RedirectResponse|null
     */
    protected $response = null;

    /**
     * @return Response
     */
    protected function getNewResponse(): Response
    {
        return new Response();
    }

    /**
     * @return Response|RedirectResponse
     */
    protected function getResponse()
    {
        if (!($this->response instanceof Response || $this->response instanceof RedirectResponse)) {
            $this->response = $this->getNewResponse();
        }
        return $this->response;
    }

    /**
     * @param RedirectResponse|Response|null $response
     */
    protected function setResponse($response): void
    {
        if (!($response instanceof Response ||
            $response instanceof RedirectResponse ||
            is_null($response))
        ) {
            throw new InvalidArgumentException(
                'Only allowed null, ' . Response::class . ', ' . RedirectResponse::class
            );
        }

        $this->response = $response;
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
