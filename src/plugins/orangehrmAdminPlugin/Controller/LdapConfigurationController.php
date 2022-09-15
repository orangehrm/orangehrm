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
use OrangeHRM\Core\Vue\Prop;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Response;
use OrangeHRM\Core\Traits\Service\ConfigServiceTrait;
use OrangeHRM\LDAP\Dto\LDAPSetting;

class LdapConfigurationController extends AbstractVueController
{
    use ConfigServiceTrait;

    /**
     * @inheritDoc
     */
    public function preRender(Request $request): void
    {
        $component = new Component('ldap-configuration');
        $ldapSettings = $this->getConfigService()->getLDAPSetting();
        $showSync = $ldapSettings instanceof LDAPSetting && $ldapSettings->isEnable();
        $component->addProp(new Prop('show-sync', Prop::TYPE_BOOLEAN, $showSync));
        $this->setComponent($component);
    }

    /**
     * @todo should remove
     * @param Request $request
     * @return Response
     */
    public function testConnection(Request $request): Response
    {
        $distinguishedName = $request->request->get('distinguishedName');
        $baseDistinguishedName = $request->request->get('baseDistinguishedName');

        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    [
                        "category" => "Login",
                        "checks" => [
                            [
                                "label" => "Authentication",
                                "value" => [
                                    "message" => "Ok (Connected in 5ms)",
                                    "status" => 1
                                ]
                            ],
                            [
                                "label" => "Base DN",
                                "value" => [
                                    "message" => $baseDistinguishedName,
                                    "status" => 1
                                ]
                            ],
                            [
                                "label" => "User DN",
                                "value" => [
                                    "message" => $distinguishedName,
                                    "status" => 1
                                ]
                            ]
                        ]
                    ],
                    [
                        "category" => "Lookup",
                        "checks" => [
                            [
                                "label" => "User lookup",
                                "value" => [
                                    "message" => "Ok",
                                    "status" => 1
                                ]
                            ],
                            [
                                "label" => "User groups",
                                "value" => [
                                    "message" => "2 Found",
                                    "status" => 1
                                ]
                            ],
                            [
                                "label" => "Users",
                                "value" => [
                                    "message" => '150 Found',
                                    "status" => 1
                                ]
                            ]
                        ]
                    ],
                ],
                "meta" => []
            ])
        );
        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }

    /**
     * @todo should remove
     * @param Request $request
     * @return Response
     */
    public function syncConnection(): Response
    {
        $response = new Response();
        $response->setContent(
            json_encode([
                "data" => [
                    "timestamp" => time()
                ],
                "meta" => []
            ])
        );
        $response->setStatusCode(Response::HTTP_OK);
        return $response->send();
    }
}
