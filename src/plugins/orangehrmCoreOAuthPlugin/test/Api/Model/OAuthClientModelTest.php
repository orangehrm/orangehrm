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

namespace OrangeHRM\Tests\OAuth\Api\Model;

use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Api\Model\OAuthClientModel;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group OAuth
 * @group Model
 */
class OAuthClientModelTest extends TestCase
{
    public function testToArray()
    {
        $resultArray = [
            "id" => 1,
            "name" => "ohrm-client",
            "clientId" => "85c5ce5fe84ee8dc2035378d9b35f04dfabf9e8e0aa7eb636cb0d90ed5c7f906",
            "redirectUri" => "https://www.test.com",
            "enabled" => true,
            "confidential" => true,
        ];


        $oauthClient = new OAuthClient();
        $oauthClient->setId(1);
        $oauthClient->setName("ohrm-client");
        $oauthClient->setClientId('85c5ce5fe84ee8dc2035378d9b35f04dfabf9e8e0aa7eb636cb0d90ed5c7f906');
        $oauthClient->setClientSecret('01793f6d4a0751806d14f8e5c3efa3dd6d3893d3f19c9f9509070493275941d9');
        $oauthClient->setRedirectUri("https://www.test.com");
        $oauthClient->setEnabled(true);
        $oauthClient->setConfidential(true);

        $oauthClientModel = new OAuthClientModel($oauthClient);
        $this->assertEquals($resultArray, $oauthClientModel->toArray());
    }
}
