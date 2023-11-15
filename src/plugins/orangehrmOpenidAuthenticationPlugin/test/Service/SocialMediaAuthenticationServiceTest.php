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

namespace OrangeHRM\Tests\OpenidAuthentication\Service;

use OrangeHRM\OpenidAuthentication\Dao\AuthProviderDao;
use OrangeHRM\OpenidAuthentication\Service\SocialMediaAuthenticationService;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group OpenidAuthentication
 * @group Service
 */
class SocialMediaAuthenticationServiceTest extends TestCase
{
    private SocialMediaAuthenticationService $socialMediaAuthenticationService;

    protected function setUp(): void
    {
        $this->socialMediaAuthenticationService = new SocialMediaAuthenticationService();
    }

    public function testGetAuthProviderDao(): void
    {
        $this->assertTrue(
            $this->socialMediaAuthenticationService->getAuthProviderDao() instanceof AuthProviderDao
        );
    }

    public function testInitiateAuthentication(): void
    {
        $provider = $this->socialMediaAuthenticationService->getAuthProviderDao()->getAuthProviderDetailsByProviderId(1);
        $scope = 'email';
        $redirectUrl = 'https://accounts.google.com/auth';

        $oidcClient = $this->socialMediaAuthenticationService->initiateAuthentication($provider, $scope, $redirectUrl);
        $this->assertEquals('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', $oidcClient->getClientSecret());
        $this->assertEquals('445659888050-a0n4aisrubg8l4gsb35si9gni9l6t0hn.apps.googleusercontent.com', $oidcClient->getClientID());
        $scopes = $oidcClient->getScopes();
        $this->assertIsArray($scopes);
        $this->assertEquals('email', $scopes[0]);
        $this->assertEquals('https://accounts.google.com/auth', $oidcClient->getRedirectURL());
    }
}
