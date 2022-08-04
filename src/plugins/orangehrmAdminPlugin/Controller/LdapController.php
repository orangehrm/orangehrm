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
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Exception\LdapException;

class LdapController extends AbstractVueController
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

        try {
            $ldap = Ldap::create('ext_ldap', [
                'host' => $hostname,
                'port' => $port,
                'encryption' => $encryption ?: 'none',
            ]);

            $ldap->bind($distinguishedName, $distinguishedPassword);

            $response->setContent(
                json_encode([
                    "data" => [
                        "message" => "Successfully connected"
                    ],
                    "meta" => []
                ])
            );
        } catch (LdapException $e) {
            $response->setContent(
                json_encode([
                    "error" => [
                        "error" => true,
                        "message" => $e->getMessage()
                    ],
                ])
            );
        } catch (\Exception $e) {
            // die(var_dump($e));
            $response->setContent(
                json_encode([
                    "error" => [
                        "error" => true,
                        "message" => $e->getMessage()
                    ],
                ])
            );
        }

        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
