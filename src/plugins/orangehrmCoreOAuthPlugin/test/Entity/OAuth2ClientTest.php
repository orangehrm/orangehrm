<?php

use OrangeHRM\Entity\OAuth2Client;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

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

class OAuth2ClientTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([OAuth2Client::class]);
    }

    public function testOAuth2ClientEntity(): void
    {
        $oauth2Client = new OAuth2Client();
        $oauth2Client->setName('Client Name');
        $oauth2Client->setClientSecret('Client Secret');
        $oauth2Client->setRedirectUri('redirect.com');
        $oauth2Client->setIsConfidential(false);
        $this->persist($oauth2Client);

        /** @var OAuth2Client $oauth2Client */
        $oauth2Client = $this->getRepository(OAuth2Client::class)->find(1);
        $this->assertEquals(1, $oauth2Client->getIdentifier());
        $this->assertEquals('Client Name', $oauth2Client->getName());
        $this->assertEquals('Client Secret', $oauth2Client->getClientSecret());
        $this->assertEquals('redirect.com', $oauth2Client->getRedirectUri());
        $this->assertFalse($oauth2Client->isConfidential());

        $oauth2Client = new OAuth2Client();
        $oauth2Client->setName('Client 2');
        $oauth2Client->setClientSecret('Client Secret 2');
        $oauth2Client->setRedirectUri('redirect.com/redirect');
        $oauth2Client->setIsConfidential(true);
        $this->persist($oauth2Client);

        /** @var OAuth2Client $oauth2Client */
        $oauth2Client = $this->getRepository(OAuth2Client::class)->find(2);
        $this->assertEquals(2, $oauth2Client->getIdentifier());
        $this->assertEquals('Client 2', $oauth2Client->getName());
        $this->assertEquals('Client Secret 2', $oauth2Client->getClientSecret());
        $this->assertEquals('redirect.com/redirect', $oauth2Client->getRedirectUri());
        $this->assertTrue($oauth2Client->isConfidential());
    }
}
