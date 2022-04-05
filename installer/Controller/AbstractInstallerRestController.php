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

use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Installer\Exception\NotImplementedException;

abstract class AbstractInstallerRestController extends AbstractInstallerController
{
    /**
     * @param Request $request
     * @return Response
     * @throws NotImplementedException
     */
    protected function execute(Request $request): Response
    {
        // 'application/json', 'application/x-json'
        if ($request->getContentType() === 'json') {
            if ($request->getContent() !== '') {
                $data = json_decode($request->getContent(), true);
                if (is_array($data)) {
                    $request->request->add($data);
                }
            }
        }

        $response = $this->getResponse();
        $response->headers->set('Content-Type', 'application/json');
        $data = [];
        switch ($request->getMethod()) {
            case Request::METHOD_GET:
                $data = $this->handleGet($request);
                break;

            case Request::METHOD_POST:
                $data = $this->handlePost($request);
                break;

            case Request::METHOD_PUT:
                $data = $this->handlePut($request);
                break;

            case Request::METHOD_DELETE:
                $data = $this->handleDelete($request);
                break;

            default:
                throw new NotImplementedException();
        }
        $response->setContent(json_encode($data));
        return $response;
    }

    /**
     * @param Request $request
     * @return array
     * @throws NotImplementedException
     */
    protected function handleGet(Request $request): array
    {
        throw new NotImplementedException();
    }

    /**
     * @param Request $request
     * @return array
     * @throws NotImplementedException
     */
    protected function handlePost(Request $request): array
    {
        throw new NotImplementedException();
    }

    /**
     * @param Request $request
     * @return array
     * @throws NotImplementedException
     */
    protected function handlePut(Request $request): array
    {
        throw new NotImplementedException();
    }

    /**
     * @param Request $request
     * @return array
     * @throws NotImplementedException
     */
    protected function handleDelete(Request $request): array
    {
        throw new NotImplementedException();
    }
}
