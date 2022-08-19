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

namespace OrangeHRM\Admin\Controller;

use OrangeHRM\Core\Controller\AbstractVueController;
use OrangeHRM\Core\Vue\Component;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;

class LdapConfigurationController extends AbstractVueController
{
    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('ldap-configuration');
        $this->setComponent($component);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function testConnection(Request $request): Response
    {
        $hostname = $request->request->get('hostname');
        $port = $request->request->getInt('port');
        $encryption = $request->request->get('encryption');
        $distinguishedName = $request->request->get('distinguishedName');
        $distinguishedPassword = $request->request->get('distinguishedPassword');

        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    "message" => "Successfully connected",
                    "hostname" => $hostname,
                    "port" => $port,
                    "encryption" => $encryption,
                    "distinguishedName" => $distinguishedName,
                    "distinguishedPassword" => $distinguishedPassword
                ],
                "meta" => []
            ])
        );
        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
