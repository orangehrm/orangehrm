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
 * Boston, MA 02110-1301, USA
 */

/**
 * Class MarketplaceServiceTest
 */
class MarketplaceServiceTest extends PHPUnit_Framework_TestCase
{
    private $marketplaceService;

    public function setUp()
    {
        $this->marketplaceService = new MarketplaceService();

    }

    public function testAddonPrerequisitesVerifyWhenAddonTypeIsPaid()
    {

        $addon =
            [
                'type' => "paid",
                'prerequisites' =>
                    [
                        [
                            "type" => "php_extension",
                            "params" =>
                                [
                                    "extension" => "ldap"
                                ]
                        ],
                        [
                            "type" => "os_version",
                            "params" =>
                                [
                                    "constraint" => ">=4.3"
                                ]
                        ]
                    ]
            ];
        $result = $this->marketplaceService->addonPrerequisitesVerify($addon);
        $preRequisiteNotMetCount = 2;
        $prerequisites = ["ldap","ionCube Loader"];
        foreach($prerequisites as $prerequisite) {
            if(extension_loaded($prerequisite)) {
                $preRequisiteNotMetCount--;
            }
        }

        $this->assertEquals($preRequisiteNotMetCount, count($result));

    }

    public function testAddonPrerequisitesVerifyWhenAddonTypeIsFree()
    {

        $addon =
            [
                'type' => "free",
                'prerequisites' =>
                    [
                        [
                            "type" => "php_extension",
                            "params" =>
                                [
                                    "extension" => "toggl"
                                ]
                        ],
                        [
                            "type" => "os_version",
                            "params" =>
                                [
                                    "constraint" => ">=4.3"
                                ]
                        ]
                    ]
            ];
        $result = $this->marketplaceService->addonPrerequisitesVerify($addon);
        if (!extension_loaded("toggl")) {
            $this->assertEquals(1, count($result));
        } else {
            $this->assertEquals(0, count($result));
        }

    }

    public function testAddonPrerequisitesVerifyWhenHavingNotInstalledPrerequisites()
    {

        $addon =
            [
                'type' => "paid",
                'prerequisites' =>
                    [
                        [
                            "type" => "php_extension",
                            "params" =>
                                [
                                    "extension" => "toggl,soap,image"
                                ]
                        ],
                        [
                            "type" => "os_version",
                            "params" =>
                                [
                                    "constraint" => ">=4.3"
                                ]
                        ]
                    ]
            ];


        $result = $this->marketplaceService->addonPrerequisitesVerify($addon);

        $preRequisiteNotMetCount = 4;

        $prerequisites = ["toggl","soap","image","ionCube Loader"];
        foreach($prerequisites as $prerequisite) {
            if(extension_loaded($prerequisite)) {
                $preRequisiteNotMetCount--;

            }
        }

        $this->assertEquals($preRequisiteNotMetCount, count($result));
    }

}
