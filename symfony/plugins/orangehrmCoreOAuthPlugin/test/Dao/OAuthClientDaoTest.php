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

namespace OrangeHRM\Tests\OAuth\Dao;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Constant\GrantType;
use OrangeHRM\OAuth\Constant\Scope;
use OrangeHRM\OAuth\Dao\OAuthClientDao;
use OrangeHRM\OAuth\Dto\OAuthClientSearchFilterParams;
use OrangeHRM\OAuth\Service\OAuthService;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OAuth
 * @group Dao
 */
class OAuthClientDaoTest extends TestCase
{
    private OAuthClientDao $authClientDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->authClientDao = new OAuthClientDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmCoreOAuthPlugin/test/fixtures/OAuthClient.yml';
        TestDataService::truncateSpecificTables([OAuthClient::class]);
        TestDataService::populate($this->fixture);
    }

    public function testGetOAuthClients(): void
    {
        $oAuthClientSearchFilterParams = new OAuthClientSearchFilterParams();
        $result = $this->authClientDao->getOAuthClients($oAuthClientSearchFilterParams);
        $this->assertEquals('MobileClientId', $result[0]->getClientId());
        $this->assertEquals('TestClientId', $result[1]->getClientId());
    }

    public function testGetOAuthClientsCount(): void
    {
        $oAuthClientSearchFilterParams = new OAuthClientSearchFilterParams();
        $result = $this->authClientDao->getOAuthClientsCount($oAuthClientSearchFilterParams);
        $this->assertEquals(2, $result);
    }

    public function testGetOAuthClientByClientId_WhenClientAvailable(): void
    {
        $result = $this->authClientDao->getOAuthClientByClientId('MobileClientId');
        $this->assertEquals('MobileClientId', $result->getClientId());
    }

    public function testGetOAuthClientByClientId_WhenClientNotAvailable(): void
    {
        $result = $this->authClientDao->getOAuthClientByClientId('MobileClientIdTest');
        $this->assertNull($result);
    }

    public function testSaveOAuthClient(): void
    {
        $oAuthClient = new OAuthClient();
        $oAuthClient->setClientId('Test1');
        $oAuthClient->setClientSecret('Test1Secret');
        $oAuthClient->setRedirectUri('https://facebook.com');
        $oAuthClient->setGrantTypes('password');
        $oAuthClient->setScope('user');
        $result = $this->authClientDao->saveOAuthClient($oAuthClient);
        $this->assertEquals('Test1', $result->getClientId());
        $this->assertEquals('Test1Secret', $result->getClientSecret());
        $this->assertEquals('https://facebook.com', $result->getRedirectUri());
        $this->assertEquals('password', $result->getGrantTypes());
        $this->assertEquals('user', $result->getScope());
    }

    public function testDeleteOAuthClients(): void
    {
        $oAuthClient = new OAuthClient();
        $oAuthClient->setClientId('Test1ToDelete');
        $oAuthClient->setClientSecret('Test1SecretToDelete');
        $oAuthClient->setRedirectUri('https://facebook.com');
        $oAuthClient->setGrantTypes('password');
        $oAuthClient->setScope('user');
        $this->authClientDao->saveOAuthClient($oAuthClient);
        $toDeleteIds = ['Test1ToDelete'];
        $result = $this->authClientDao->deleteOAuthClients($toDeleteIds);
        $this->assertEquals(1, $result);
    }

    public function testCreateMobileClient(): void
    {
        $result = $this->authClientDao->createMobileClient();
        $this->assertTrue($result instanceof OAuthClient);
        $this->assertEquals(OAuthService::PUBLIC_MOBILE_CLIENT_ID, $result->getClientId());
        $this->assertEquals('', $result->getClientSecret());
        $this->assertEquals(sprintf("%s %s", GrantType::USER_CREDENTIALS, GrantType::REFRESH_TOKEN), $result->getGrantTypes());
        $this->assertEquals(Scope::SCOPE_USER, $result->getScope());
    }
}
