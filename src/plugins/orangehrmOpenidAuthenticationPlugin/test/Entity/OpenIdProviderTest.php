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

use OrangeHRM\Entity\OpenIdProvider;
use OrangeHRM\Tests\Util\EntityTestCase;

/**
 * @group OpenidAuthentication
 * @group Entity
 */
class OpenIdProviderTest extends EntityTestCase
{
    public function testEntity(): void
    {
        $openIdProvider = new OpenIdProvider();
        $openIdProvider->setProviderName('google');
        $openIdProvider->setProviderUrl('https://www.google.com/');
        $openIdProvider->setStatus(1);

        $this->assertEquals('google', $openIdProvider->getProviderName());
        $this->assertEquals('https://www.google.com/', $openIdProvider->getProviderUrl());
        $this->assertEquals(1, $openIdProvider->getStatus());
    }

    public function testNewProviderHasBothSignInChecksOn(): void
    {
        $openIdProvider = new OpenIdProvider();
        $this->assertTrue($openIdProvider->isRequireVerifiedEmail());
        $this->assertTrue($openIdProvider->isRequireMatchingSubject());
    }

    public function testSignInChecksArePersisted(): void
    {
        $openIdProvider = new OpenIdProvider();
        $openIdProvider->setProviderName('Keycloak');
        $openIdProvider->setProviderUrl('https://keycloak.example/realms/corp');
        $openIdProvider->setRequireVerifiedEmail(false);
        $openIdProvider->setRequireMatchingSubject(true);
        $this->persist($openIdProvider);

        $this->getEntityManager()->clear();
        /** @var OpenIdProvider $saved */
        $saved = $this->getRepository(OpenIdProvider::class)->find($openIdProvider->getId());
        $this->assertFalse($saved->isRequireVerifiedEmail());
        $this->assertTrue($saved->isRequireMatchingSubject());
    }
}
