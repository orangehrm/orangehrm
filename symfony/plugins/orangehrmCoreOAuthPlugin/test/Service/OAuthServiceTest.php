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

namespace OrangeHRM\Tests\OAuth\Service;

use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Constant\GrantType;
use OrangeHRM\OAuth\Constant\Scope;
use OrangeHRM\OAuth\Dao\OAuthClientDao;
use OrangeHRM\OAuth\Service\OAuthService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group OAuth
 * @group Service
 */
class OAuthServiceTest extends TestCase
{
    private OAuthService $oAuthService;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->oAuthService = new OAuthService();
    }

    public function testGetOAuthClientDao(): void
    {
        $oAuthClientDao = $this->oAuthService->getOAuthClientDao();
        $this->assertTrue($oAuthClientDao instanceof OAuthClientDao);
    }

    public function testGetOAuthClientByClientId(): void
    {
        $clientId = 'Test';
        $oauthClient = new OAuthClient();
        $oAuthClientDao = $this->getMockBuilder(OAuthClientDao::class)->getMock();
        $oAuthClientDao->expects($this->once())
            ->method('getOAuthClientByClientId')
            ->with($clientId)
            ->will($this->returnValue($oauthClient));

        $this->oAuthService->setOAuthClientDao($oAuthClientDao);
        $authClient = $this->oAuthService->getOAuthClientByClientId($clientId);
        $this->assertEquals($oauthClient, $authClient);
    }


    public function testDeleteOAuthClients(): void
    {
        $clientIdArray = ['Test'];
        $oAuthClientDao = $this->getMockBuilder(OAuthClientDao::class)->getMock();
        $oAuthClientDao->expects($this->once())
            ->method('deleteOAuthClients')
            ->with($clientIdArray)
            ->will($this->returnValue(1));

        $this->oAuthService->setOAuthClientDao($oAuthClientDao);
        $deletedCount = $this->oAuthService->deleteOAuthClients($clientIdArray);
        $this->assertEquals(1, $deletedCount);
    }


    public function testSaveOAuthClient(): void
    {
        $oauthClient = new OAuthClient();
        $oauthClient->setClientId('Test1');
        $oauthClient->setClientSecret('Test1Secret');
        $oauthClient->setRedirectUri('');
        $oauthClient->setGrantTypes('password');
        $oauthClient->setScope('user');


        $oAuthClientDao = $this->getMockBuilder(OAuthClientDao::class)->getMock();
        $oAuthClientDao->expects($this->once())
            ->method('saveOAuthClient')
            ->with($oauthClient)
            ->will($this->returnValue($oauthClient));

        $this->oAuthService->setOAuthClientDao($oAuthClientDao);
        $savedItem = $this->oAuthService->saveOAuthClient($oauthClient);
        $this->assertEquals($oauthClient, $savedItem);
    }

    public function testCreateMobileClient(): void
    {
        $client = new OAuthClient();
        $client->setClientId(OAuthService::PUBLIC_MOBILE_CLIENT_ID);
        $client->setClientSecret('');
        $client->setRedirectUri('');
        $client->setGrantTypes(sprintf("%s %s", GrantType::USER_CREDENTIALS, GrantType::REFRESH_TOKEN));
        $client->setScope(Scope::SCOPE_USER);

        $oAuthClientDao = $this->getMockBuilder(OAuthClientDao::class)->getMock();
        $oAuthClientDao->expects($this->once())
            ->method('createMobileClient')
            ->will($this->returnValue($client));

        $this->oAuthService->setOAuthClientDao($oAuthClientDao);
        $savedItem = $this->oAuthService->createMobileClient();
        $this->assertEquals($client, $savedItem);
    }
}
