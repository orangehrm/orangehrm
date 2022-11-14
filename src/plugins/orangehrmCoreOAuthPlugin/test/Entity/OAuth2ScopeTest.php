<?php

use OrangeHRM\Entity\OAuth2Scope;
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

class OAuth2ScopeTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([OAuth2Scope::class]);
    }

    public function testOAuth2ScopeEntity(): void
    {
        $oauth2Scope = new OAuth2Scope();
        $oauth2Scope->setScope('Scope 1');
        $oauth2Scope->setIsDefault(true);
        $this->persist($oauth2Scope);

        /** @var OAuth2Scope $oauth2Scope */
        $oauth2Scope = $this->getRepository(OAuth2Scope::class)->find(1);
        $this->assertEquals(1, $oauth2Scope->getIdentifier());
        $this->assertEquals('Scope 1', $oauth2Scope->getScope());
        $this->assertTrue($oauth2Scope->isDefault());
    }
}
