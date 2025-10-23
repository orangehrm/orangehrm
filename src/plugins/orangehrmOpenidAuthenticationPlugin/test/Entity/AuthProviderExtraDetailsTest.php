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

namespace OrangeHRM\Tests\OpenidAuthentication\Entity;

use OrangeHRM\Config\Config;
use OrangeHRM\Entity\AuthProviderExtraDetails;
use OrangeHRM\Entity\OpenIdProvider;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OpenidAuthentication
 * @group Entity
 */
class AuthProviderExtraDetailsTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmOpenidAuthenticationPlugin/test/fixtures/AuthProviderExtraDetails.yml';
        TestDataService::populate($fixture);
    }

    public function testEntity(): void
    {
        $authProviderExtraDetails = new AuthProviderExtraDetails();
        $authProviderExtraDetails->setOpenIdProvider($this->getReference(OpenIdProvider::class, 1));
        $authProviderExtraDetails->setClientId('123.google.com');
        $authProviderExtraDetails->setClientSecret('Np3pLf0CvBpDPqXEK');

        $this->assertEquals('Google', $authProviderExtraDetails->getOpenIdProvider()->getProviderName());
        $this->assertEquals('https://accounts.google.com', $authProviderExtraDetails->getOpenIdProvider()->getProviderUrl());
        $this->assertEquals('123.google.com', $authProviderExtraDetails->getClientId());
        $this->assertEquals('Np3pLf0CvBpDPqXEK', $authProviderExtraDetails->getClientSecret());
    }
}
