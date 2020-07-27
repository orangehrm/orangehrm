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

require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group CoreOAuth
 */
class OAuthClientDaoTest extends PHPUnit\Framework\TestCase
{
    private $oauthClientDao;
    protected $fixture;

    /**
     * Set up method
     */
    protected function setUp()
    {
        $this->oauthClientDao = new OAuthClientDao();
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCoreOAuthPlugin/test/fixtures/OAuthClient.yml';
        TestDataService::populate($this->fixture);
    }

    public function testGetOAuthClient()
    {
        $client = $this->oauthClientDao->getOAuthClient('TestClientId');
        $this->assertTrue($client instanceof OAuthClient);
        $this->assertEquals('client_credentials', $client->getGrantTypes());
        $this->assertEquals('admin', $client->getScope());
        $this->assertEquals('TestClientSecret', $client->getClientSecret());
    }

    public function testDeleteOAuthClient()
    {
        $result = $this->oauthClientDao->deleteOAuthClient('TestClientId');
        $this->assertTrue($result == 1);
        $client = $this->oauthClientDao->getOAuthClient('TestClientId');
        $this->assertFalse($client);
    }

    public function testListOAuthClients()
    {
        $result = $this->oauthClientDao->listOAuthClients();
        $this->assertEquals(1, count($result));
    }

    public function testCreateMobileClient()
    {
        $this->oauthClientDao->createMobileClient();
        $client = $this->oauthClientDao->getOAuthClient(OAuthClientDao::PUBLIC_MOBILE_CLIENT_ID);
        $this->assertTrue($client instanceof OAuthClient);
        $this->assertEquals('password refresh_token', $client->getGrantTypes());
        $this->assertEquals('user', $client->getScope());
        $this->assertEquals('', $client->getClientSecret());
    }
}
