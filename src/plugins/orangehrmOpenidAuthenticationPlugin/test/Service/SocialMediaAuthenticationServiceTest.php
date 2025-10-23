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

namespace OrangeHRM\Tests\OpenidAuthentication\Service;

use OrangeHRM\Authentication\Auth\User;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\OpenidAuthentication\Dao\AuthProviderDao;
use OrangeHRM\OpenidAuthentication\Service\SocialMediaAuthenticationService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group OpenIDAuth
 * @group Service
 */
class SocialMediaAuthenticationServiceTest extends KernelTestCase
{
    private SocialMediaAuthenticationService $socialMediaAuthenticationService;

    protected function setUp(): void
    {
        $this->socialMediaAuthenticationService = new SocialMediaAuthenticationService();
        $this->fixture = Config::get(Config::PLUGINS_DIR) . '/orangehrmOpenidAuthenticationPlugin/test/fixtures/AuthProviderExtraDetails.yml';
        TestDataService::populate($this->fixture);
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

    public function testGetRedirectURL(): void
    {
        $urlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->onlyMethods(['generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(
                'https://orangehrm.com/orangehrm5/web/index.php/openidauth/openIdCredentials'
            );
        $this->createKernelWithMockServices(
            [Services::URL_GENERATOR => $urlGenerator, Services::CONFIG_SERVICE => new ConfigService()]
        );

        $url = $this->socialMediaAuthenticationService->getRedirectURL();
        $this->assertEquals('https://orangehrm.com/orangehrm5/web/index.php/openidauth/openIdCredentials', $url);
    }

    public function testGetScope(): void
    {
        $scope = $this->socialMediaAuthenticationService->getScope();
        $this->assertEquals('email', $scope);
        $this->assertIsString($scope);
    }

    public function testGetUserForAuthenticate(): void
    {
        $userCredential = new UserCredential();
        $userCredential->setUsername('admin@orangehrm.us.com');

        $user = $this->socialMediaAuthenticationService->getUserForAuthenticate($userCredential);
        $this->assertTrue($user instanceof \OrangeHRM\Entity\User);
        $this->assertEquals('1', $user->getId());

        $userCredential->setUsername('manul@orangehrm.us.com');
        $user = $this->socialMediaAuthenticationService->getUserForAuthenticate($userCredential);
        $this->assertEquals('2', $user->getId());
        $this->assertFalse($user->isDeleted());
    }

    public function testSetOIDCUserIdentity(): void
    {
        $userCredential = new UserCredential();
        $userCredential->setUsername('manul@orangehrm.us.com');

        $user = $this->socialMediaAuthenticationService->getUserForAuthenticate($userCredential);
        $provider = $this->socialMediaAuthenticationService->getAuthProviderDao()->getAuthProviderById(1);

        $userIdentity = $this->socialMediaAuthenticationService->setOIDCUserIdentity($user, $provider);
        $this->assertEquals('2', $userIdentity->getUser()->getId());
        $this->assertEquals('Google', $userIdentity->getOpenIdProvider()->getProviderName());
    }

    public function testHandleOIDCAuthentication(): void
    {
        $session = $this->getMockBuilder(Session::class)
            ->onlyMethods(['set'])
            ->getMock();
        $session->expects($this->exactly(5))
            ->method('set');

        $this->createKernelWithMockServices(
            [
                Services::AUTH_USER => User::getInstance(),
                Services::SESSION => $session,
                Services::AUTHENTICATION_SERVICE => new AuthenticationService(),
            ]
        );

        $userCredential = new UserCredential();
        $userCredential->setUsername('manul@orangehrm.us.com');

        $user = $this->socialMediaAuthenticationService->getUserForAuthenticate($userCredential);
        $success = $this->socialMediaAuthenticationService->handleOIDCAuthentication($user);

        $this->assertIsBool($success);
        $this->assertTrue($success);
    }
}
