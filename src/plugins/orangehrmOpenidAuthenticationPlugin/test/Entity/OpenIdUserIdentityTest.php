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
use OrangeHRM\Entity\OpenIdProvider;
use OrangeHRM\Entity\OpenIdUserIdentity;
use OrangeHRM\Entity\User;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OpenidAuthentication
 * @group Entity
 */
class OpenIdUserIdentityTest extends EntityTestCase
{
    protected function setUp(): void
    {
        $fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmOpenidAuthenticationPlugin/test/fixtures/OpenIdUserIdentity.yml';
        TestDataService::populate($fixture);
    }

    public function testEntity(): void
    {
        $openIdUserIdentity = new OpenIdUserIdentity();
        $openIdUserIdentity->setUser($this->getReference(User::class, 1));
        $openIdUserIdentity->setOpenIdProvider($this->getReference(OpenIdProvider::class, 1));
        $openIdUserIdentity->setUserIdentity('sample');

        $this->assertEquals('admin', $openIdUserIdentity->getUser()->getUserName());
        $this->assertEquals('Google', $openIdUserIdentity->getOpenIdProvider()->getProviderName());
        $this->assertEquals('https://google.com/o/8/', $openIdUserIdentity->getOpenIdProvider()->getProviderUrl());
        $this->assertEquals('sample', $openIdUserIdentity->getUserIdentity());
    }
}
