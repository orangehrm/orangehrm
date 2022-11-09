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

namespace OrangeHRM\Installer\Controller;

use InvalidArgumentException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Traits\ControllerTrait;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

abstract class AbstractInstallerController
{
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
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function handle(Request $request)
    {
        $ignoredPaths = ['/upgrader/complete', '/installer/complete', '/'];
        if (Config::isInstalled() && !in_array($request->getPathInfo(), $ignoredPaths)) {
            $this->getResponse()->setStatusCode(Response::HTTP_BAD_GATEWAY);
            return $this->getResponse();
        }
        return $this->execute($request);
    }

    /**
     * @param Request $request
     * @return Response|RedirectResponse
     */
    abstract protected function execute(Request $request);
}
