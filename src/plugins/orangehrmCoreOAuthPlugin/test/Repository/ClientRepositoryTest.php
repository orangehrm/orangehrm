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

namespace OrangeHRM\Tests\OAuth\Repository;

use OrangeHRM\Config\Config;
use OrangeHRM\OAuth\Repository\ClientRepository;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OAuth
 * @group Dao
 */
class ClientRepositoryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR)
            . '/orangehrmCoreOAuthPlugin/test/fixtures/ClientRepositoryTest.yaml';
        TestDataService::populate($fixture);
    }

    public function testGetClientEntity(): void
    {
        $clientRepository = new ClientRepository();
        $clientEntity = $clientRepository->getClientEntity('orangehrm_mobile_app');
        $this->assertEquals(1, $clientEntity->getIdentifier());
        $this->assertEquals('orangehrm_mobile_app', $clientEntity->getName());
        $this->assertEquals('com.orangehrm.opensource://oauthredirect', $clientEntity->getRedirectUri());
        $this->assertFalse($clientEntity->isConfidential());
        $this->assertEquals('OrangeHRM Mobile App', $clientEntity->getDisplayName());

        $clientEntity = $clientRepository->getClientEntity('fe0740c8920a98414fc85233b42bb293');
        $this->assertEquals(2, $clientEntity->getIdentifier());
        $this->assertEquals('fe0740c8920a98414fc85233b42bb293', $clientEntity->getName());
        $this->assertEquals('https://example.com/oauth/callback', $clientEntity->getRedirectUri());
        $this->assertTrue($clientEntity->isConfidential());
        $this->assertEquals('OAuth Client 1', $clientEntity->getDisplayName());

        $clientEntity = $clientRepository->getClientEntity('ad99b8d5b9de9e474a31452e20f68897');
        $this->assertNull($clientEntity); // disabled client

        $clientEntity = $clientRepository->getClientEntity('non-existence-client-id');
        $this->assertNull($clientEntity); // non existence client
    }

    public function testValidateClient(): void
    {
        $clientRepository = new ClientRepository();

        $valid = $clientRepository->validateClient('non-existence-client-id', '', 'authorization_code');
        $this->assertFalse($valid); // non existence client

        $valid = $clientRepository->validateClient('fe0740c8920a98414fc85233b42bb293', '--', 'authorization_code');
        $this->assertFalse($valid); // invalid client secret

        $valid = $clientRepository->validateClient(
            'fe0740c8920a98414fc85233b42bb293',
            'Im2eYEIIWp+sT+W9xVYYwzBlYXpC0pfHWA0GthvjrII=',
            'authorization_code'
        );
        $this->assertTrue($valid); // valid client secret

        $valid = $clientRepository->validateClient(
            'ad99b8d5b9de9e474a31452e20f68897',
            'nuyyxrXh4cOJGMfup4m6RLXDMspIrRuapeesiv2k8cU=',
            'authorization_code'
        );
        $this->assertFalse($valid); // valid client secret, but disabled client

        $valid = $clientRepository->validateClient('45349b9c3676b30a3d4bce76d68c50b0', '-', 'authorization_code');
        $this->assertFalse($valid); // confidential client, but not defined client secret

        $valid = $clientRepository->validateClient('orangehrm_mobile_app', '', 'refresh_token');
        $this->assertTrue($valid); // non-confidential client with `refresh_token` is valid everytime

        $valid = $clientRepository->validateClient('orangehrm_mobile_app', '', 'authorization_code');
        $this->assertFalse($valid); // fallback to invalid for non-confidential (secret=NULL) clients
    }
}
