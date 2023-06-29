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
use OrangeHRM\Core\Traits\ORM\EntityManagerTrait;
use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\OAuth\Dao\OAuthClientDao;
use OrangeHRM\OAuth\Dto\OAuthClientSearchFilterParams;
use OrangeHRM\Tests\Util\TestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OAuth
 * @group Dao
 */
class OAuthClientDaoTest extends TestCase
{
    use EntityManagerTrait;

    private OAuthClientDao $oAuthClientDao;
    protected string $fixture;

    protected function setUp(): void
    {
        $this->oAuthClientDao = new OAuthClientDao();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmCoreOAuthPlugin/test/fixtures/OAuthClient.yml';
        TestDataService::populate($this->fixture);
    }

    public function testSaveOAuthClient(): void
    {
        $oAuthClient = new OAuthClient();
        $oAuthClient->setName("Test Client");
        $oAuthClient->setClientId("Client ID");
        $oAuthClient->setClientSecret("Client Secret");
        $oAuthClient->setRedirectUri("https://www.testclient.com");
        $oAuthClient->setEnabled(false);
        $oAuthClient->setConfidential(true);

        $result = $this->oAuthClientDao->saveOAuthClient($oAuthClient);
        $this->assertEquals("Test Client", $result->getName());
        $this->assertEquals("Client ID", $result->getClientId());
        $this->assertEquals("Client Secret", $result->getClientSecret());
        $this->assertEquals("https://www.testclient.com", $result->getRedirectUri());
        $this->assertFalse($result->isEnabled());
        $this->assertTrue($result->isConfidential());
    }

    public function testGetOAuthClientList(): void
    {
        $oAuthClientSearchFilterParams = new OAuthClientSearchFilterParams();
        $clientList = $this->oAuthClientDao->getOAuthClientList($oAuthClientSearchFilterParams);

        $this->assertCount(2, $clientList);

        $this->assertEquals(1, $clientList[0]->getId());
        $this->assertEquals("ohrm-mobile-updated", $clientList[0]->getName());
        $this->assertEquals("85c5ce5fe84ee8dc2035378d9b35f04dfabf9e8e0aa7eb636cb0d90ed5c7f906", $clientList[0]->getClientId());
        $this->assertEquals("01793f6d4a0751806d14f8e5c3efa3dd6d3893d3f19c9f9509070493275941d9", $clientList[0]->getClientSecret());
        $this->assertEquals("https://www.test.com", $clientList[0]->getRedirectUri());
        $this->assertFalse($clientList[0]->isConfidential());
        $this->assertTrue($clientList[0]->isEnabled());

        $this->assertEquals(2, $clientList[1]->getId());
        $this->assertEquals("ohrm-mobile-2", $clientList[1]->getName());
        $this->assertEquals("256697adeead32faf700cf8dd9f53d49a3583491d2cff9605168220b4a276967", $clientList[1]->getClientId());
        $this->assertEquals("cd6f1f80eb19bf463a5e21056b9bba4b9e5d4fe6dac4838922cee701e3c17ab8", $clientList[1]->getClientSecret());
        $this->assertEquals("https://www.mobile.com", $clientList[1]->getRedirectUri());
        $this->assertTrue($clientList[1]->isConfidential());
        $this->assertFalse($clientList[1]->isEnabled());
    }

    public function testGetOAuthClientCount(): void
    {
        $oAuthClientSearchFilterParams = new OAuthClientSearchFilterParams();
        $count = $this->oAuthClientDao->getOAuthClientCount($oAuthClientSearchFilterParams);

        $this->assertEquals(2, $count);
    }

    public function testGetOAuthClientById(): void
    {
        $client = $this->oAuthClientDao->getOAuthClientById(1);

        $this->assertEquals("ohrm-mobile-updated", $client->getName());
        $this->assertEquals("85c5ce5fe84ee8dc2035378d9b35f04dfabf9e8e0aa7eb636cb0d90ed5c7f906", $client->getClientId());

        $client = $this->oAuthClientDao->getOAuthClientById(100);
        $this->assertNull($client);
    }

    public function testGetOAuthClientByClientId(): void
    {
        $client = $this->oAuthClientDao->getOAuthClientByClientId('85c5ce5fe84ee8dc2035378d9b35f04dfabf9e8e0aa7eb636cb0d90ed5c7f906');

        $this->assertEquals("ohrm-mobile-updated", $client->getName());
        $this->assertEquals(1, $client->getId());

        $client = $this->oAuthClientDao->getOAuthClientByClientId('invalid-id');
        $this->assertNull($client);
    }

    public function testDeleteOAuthClients(): void
    {
        $this->oAuthClientDao->deleteOAuthClients([1]);

        /** @var OAuthClient[] $clients */
        $clients = $this->getEntityManager()->getRepository(OAuthClient::class)->findAll();
        $this->assertCount(1, $clients);
        $this->assertEquals(2, $clients[0]->getId());
        $this->assertEquals("ohrm-mobile-2", $clients[0]->getName());
    }
}
