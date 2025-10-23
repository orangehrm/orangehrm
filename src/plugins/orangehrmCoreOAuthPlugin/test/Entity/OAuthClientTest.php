<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software: you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with OrangeHRM.
 * If not, see <https://www.gnu.org/licenses/>.
 */

namespace OrangeHRM\Tests\OAuth\Entity;

use OrangeHRM\Entity\OAuthClient;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OAuth
 * @group Entity
 */
class OAuthClientTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([OAuthClient::class]);
    }

    public function testOAuthClientEntity(): void
    {
        $oauthClient = new OAuthClient();
        $oauthClient->setName('OAuth Client');
        $oauthClient->setClientId('TestOAuth');
        $oauthClient->setClientSecret('TestOAuthSecret');
        $oauthClient->setRedirectUri('https://facebook.com');
        $oauthClient->setConfidential(false);
        $oauthClient->setEnabled(true);
        $this->persist($oauthClient);

        /** @var OAuthClient[] $oauthClientObjects */
        $oauthClientObjects = $this->getRepository(OAuthClient::class)->findBy(['clientId' => 'TestOAuth']);
        $oauthClientObj = $oauthClientObjects[0];
        $this->assertEquals('OAuth Client', $oauthClientObj->getName());
        $this->assertEquals('TestOAuth', $oauthClientObj->getClientId());
        $this->assertEquals('TestOAuthSecret', $oauthClientObj->getClientSecret());
        $this->assertEquals('https://facebook.com', $oauthClientObj->getRedirectUri());
        $this->assertFalse($oauthClientObj->isConfidential());
        $this->assertTrue($oauthClientObj->isEnabled());
    }
}
