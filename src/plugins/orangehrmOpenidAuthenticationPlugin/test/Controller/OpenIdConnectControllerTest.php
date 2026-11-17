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

namespace OrangeHRM\Tests\OpenidAuthentication\Controller;

use DateTime;
use Jumbojett\OpenIDConnectClientException;
use OrangeHRM\Authentication\Auth\User as AuthUser;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Authentication\Service\LoginService;
use RuntimeException;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Authorization\Service\HomePageService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\LoginLog;
use OrangeHRM\Framework\Http\RedirectResponse;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\Session\MemorySessionStorage;
use OrangeHRM\Framework\Http\Session\Session;
use OrangeHRM\Framework\Logger\Logger;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\OpenidAuthentication\Controller\OpenIdConnectLoginController;
use OrangeHRM\OpenidAuthentication\Controller\OpenIdConnectRedirectController;
use OrangeHRM\OpenidAuthentication\Exception\OpenIdConnectAuthenticationException;
use OrangeHRM\OpenidAuthentication\OpenID\OpenIDConnectClient;
use OrangeHRM\OpenidAuthentication\Service\SocialMediaAuthenticationService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * OIDC start (/openidauth/openIdCredentials/{providerId}) and callback (/openidauth/openIdCredentials).
 * The identity provider is replaced by a mocked OIDC client that returns the given claims.
 *
 * @group OpenId
 * @group Controller
 */
class OpenIdConnectControllerTest extends KernelTestCase
{
    private const SUB = 'b18df208-5cf2-4a8c-877e-2a9c77267cb2';
    private const ADMIN = 'vera.admin@orangehrm.us.com';
    private const ESS = 'manul@orangehrm.us.com';
    private const LOGIN_URL = 'http://orangehrm.test/web/index.php/auth/login';

    private Session $session;
    private array $warnings = [];

    protected function setUp(): void
    {
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmOpenidAuthenticationPlugin/test/fixtures/OpenIdConnectController.yml'
        );
        $this->getEntityManager()->createQueryBuilder()->delete(LoginLog::class, 'l')->getQuery()->execute();
    }

    protected function tearDown(): void
    {
        $connection = $this->getEntityManager()->getConnection();
        while ($connection->isTransactionActive()) {
            $connection->rollBack();
        }
        $this->getEntityManager()->createQueryBuilder()->delete(LoginLog::class, 'l')->getQuery()->execute();
        parent::tearDown();
    }

    /**
     * @param OpenIDConnectClient|null $client
     */
    private function mockServices(?OpenIDConnectClient $client): void
    {
        $this->session = new Session(new MemorySessionStorage());

        $service = $this->getMockBuilder(SocialMediaAuthenticationService::class)
            ->onlyMethods(['initiateAuthentication', 'getRedirectURL'])
            ->getMock();
        $service->method('getRedirectURL')->willReturn('http://orangehrm.test/web/index.php/openidauth/openIdCredentials');
        if ($client !== null) {
            $service->method('initiateAuthentication')->willReturn($client);
        } else {
            $service->expects($this->never())->method('initiateAuthentication');
        }

        $urlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->onlyMethods(['generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $urlGenerator->method('generate')->willReturn(self::LOGIN_URL);

        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')->willReturn(new DateTime('2026-11-16 09:00:00'));

        $this->warnings = [];
        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['warning'])
            ->getMock();
        $logger->method('warning')->willReturnCallback(function ($message) {
            $this->warnings[] = $message;
        });

        $this->createKernelWithMockServices([
            Services::SESSION => $this->session,
            Services::AUTH_USER => AuthUser::getInstance(),
            Services::SOCIAL_MEDIA_AUTH_SERVICE => $service,
            Services::AUTHENTICATION_SERVICE => new AuthenticationService(),
            Services::URL_GENERATOR => $urlGenerator,
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::LOGGER => $logger,
        ]);
    }

    private function client(array $idTokenClaims, array $userInfo): OpenIDConnectClient
    {
        $client = $this->getMockBuilder(OpenIDConnectClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['authenticate', 'getVerifiedClaims', 'requestUserInfo'])
            ->getMock();
        $client->method('authenticate')->willReturn(true);
        $client->method('getVerifiedClaims')->willReturn((object)$idTokenClaims);
        $client->method('requestUserInfo')->willReturn((object)$userInfo);
        return $client;
    }

    /**
     * @param LoginService|null $loginService
     * @param bool $realTimeoutRedirect use the real SessionHandlingTrait::handleSessionTimeoutRedirect()
     */
    private function callbackController(
        ?LoginService $loginService = null,
        bool $realTimeoutRedirect = false
    ): OpenIdConnectRedirectController {
        return new class ($loginService, $realTimeoutRedirect) extends OpenIdConnectRedirectController {
            private bool $realTimeoutRedirect;

            public function __construct(?LoginService $loginService, bool $realTimeoutRedirect)
            {
                parent::__construct();
                $this->loginService = $loginService;
                $this->realTimeoutRedirect = $realTimeoutRedirect;
            }

            public function getHomePageService(): HomePageService
            {
                return new class () extends HomePageService {
                    public function getHomePagePath(): ?string
                    {
                        return 'dashboard/index';
                    }
                };
            }

            public function handleSessionTimeoutRedirect(): ?string
            {
                return $this->realTimeoutRedirect ? parent::handleSessionTimeoutRedirect() : null;
            }

            protected function redirect(string $path): RedirectResponse
            {
                return new RedirectResponse('http://orangehrm.test/web/index.php/' . $path);
            }
        };
    }

    /**
     * @return array{0: RedirectResponse, 1: bool, 2: string|null}
     *         [response, authenticated, flashed login error]
     */
    private function runCallback(int $sessionProviderId, array $idTokenClaims, array $userInfo): array
    {
        $this->mockServices($this->client($idTokenClaims, $userInfo));
        $this->session->set(AuthUser::OPENID_PROVIDER_ID, $sessionProviderId);
        $response = $this->callbackController()->handle(new Request());
        $flash = $this->session->getFlashBag()->get(AuthUser::FLASH_LOGIN_ERROR);
        return [
            $response,
            AuthUser::getInstance()->isAuthenticated(),
            $flash[0]['error'] ?? null,
        ];
    }

    private function loginLogUsers(): array
    {
        return array_map(
            fn (LoginLog $log) => $log->getUserName(),
            $this->getEntityManager()->getRepository(LoginLog::class)->findAll()
        );
    }

    private function assertRefused(array $result): void
    {
        [$response, $authenticated, $error] = $result;
        $this->assertFalse($authenticated, 'session must not be authenticated');
        $this->assertSame(self::LOGIN_URL, $response->getTargetUrl());
        $this->assertNotNull($error);
        $this->assertSame([], $this->loginLogUsers());
        $this->assertFalse($this->getEntityManager()->getConnection()->isTransactionActive());
    }

    public function testVerifiedEmailSignsInAndIsLogged(): void
    {
        [$response, $authenticated, $error] = $this->runCallback(
            1,
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true],
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true]
        );
        $this->assertTrue($authenticated);
        $this->assertNull($error);
        $this->assertSame('http://orangehrm.test/web/index.php/dashboard/index', $response->getTargetUrl());
        $this->assertSame(2, AuthUser::getInstance()->getUserId());
        // committed, not left open
        $this->assertFalse($this->getEntityManager()->getConnection()->isTransactionActive());
        $this->getEntityManager()->clear();
        $this->assertSame([self::ESS], $this->loginLogUsers());
    }

    public function testFailedSignInLogLeavesNoUserInSession(): void
    {
        $this->mockServices($this->client(
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true],
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true]
        ));
        $this->session->set(AuthUser::OPENID_PROVIDER_ID, 1);
        $loginService = $this->getMockBuilder(LoginService::class)
            ->onlyMethods(['addOIDCLogin'])
            ->getMock();
        $loginService->method('addOIDCLogin')->willThrowException(new RuntimeException('database gone'));

        $response = $this->callbackController($loginService)->handle(new Request());
        $this->assertSame(self::LOGIN_URL, $response->getTargetUrl());
        $this->assertFalse(AuthUser::getInstance()->isAuthenticated());
        $this->assertNull(AuthUser::getInstance()->getUserId());
        $this->assertFalse($this->getEntityManager()->getConnection()->isTransactionActive());
    }

    public function testSignInReturnsToPageStoredAtSessionTimeout(): void
    {
        $this->mockServices($this->client(
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true],
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true]
        ));
        $this->session->set(AuthUser::OPENID_PROVIDER_ID, 1);
        $timedOutPage = 'http://orangehrm.test/web/index.php/leave/viewMyLeaveList';
        AuthUser::getInstance()->setAttribute(AuthUser::SESSION_TIMEOUT_REDIRECT_URL, $timedOutPage);

        $response = $this->callbackController(null, true)->handle(new Request());
        $this->assertSame($timedOutPage, $response->getTargetUrl());
        // the session is recreated after sign-in and keeps the signed-in user
        $this->assertTrue(AuthUser::getInstance()->isAuthenticated());
        $this->assertSame(2, AuthUser::getInstance()->getUserId());
        $this->assertFalse(AuthUser::getInstance()->hasAttribute(AuthUser::SESSION_TIMEOUT_REDIRECT_URL));
        $this->assertFalse($this->getEntityManager()->getConnection()->isTransactionActive());
        $this->getEntityManager()->clear();
        $this->assertSame([self::ESS], $this->loginLogUsers());
    }

    public function testFailureWhileRedirectingAfterSignInReturnsToLoginPage(): void
    {
        $this->mockServices($this->client(
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true],
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true]
        ));
        $this->session->set(AuthUser::OPENID_PROVIDER_ID, 1);
        $controller = new class () extends OpenIdConnectRedirectController {
            public function handleSessionTimeoutRedirect(): ?string
            {
                return null;
            }

            public function getHomePageService(): HomePageService
            {
                throw new RuntimeException('database gone');
            }
        };

        $response = $controller->handle(new Request());
        $flash = $this->session->getFlashBag()->get(AuthUser::FLASH_LOGIN_ERROR);
        $this->assertSame(self::LOGIN_URL, $response->getTargetUrl());
        $this->assertSame('unexpected_error', $flash[0]['error'] ?? null);
        $this->assertFalse($this->getEntityManager()->getConnection()->isTransactionActive());
    }

    public function testProviderIdIsRemovedFromSessionOnCallback(): void
    {
        $this->runCallback(
            1,
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true],
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true]
        );
        $this->assertFalse($this->session->has(AuthUser::OPENID_PROVIDER_ID));
    }

    public function testUnverifiedEmailOfAdminIsRefused(): void
    {
        $result = $this->runCallback(
            1,
            ['sub' => self::SUB, 'email' => self::ADMIN, 'email_verified' => false],
            ['sub' => self::SUB, 'email' => self::ADMIN, 'email_verified' => false]
        );
        $this->assertRefused($result);
        $this->assertSame(OpenIdConnectAuthenticationException::EMAIL_NOT_VERIFIED, $result[2]);
        $this->assertNull(AuthUser::getInstance()->getUserId());
    }

    /**
     * The login page names the provider the user clicked, translated where a translation exists.
     */
    public function testRefusalMessageNamesTheProvider(): void
    {
        $this->mockServices($this->client(
            ['sub' => self::SUB],
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => false]
        ));
        $this->session->set(AuthUser::OPENID_PROVIDER_ID, 1);
        $this->callbackController()->handle(new Request());
        $flash = $this->session->getFlashBag()->get(AuthUser::FLASH_LOGIN_ERROR);
        $this->assertSame(
            'Your email address is not verified by Corp IdP. Please verify it with Corp IdP or contact your administrator.',
            $flash[0]['message'] ?? null
        );
    }

    public function testEmailWithoutVerifiedClaimIsRefused(): void
    {
        $result = $this->runCallback(
            1,
            ['sub' => self::SUB, 'email' => self::ADMIN],
            ['sub' => self::SUB, 'email' => self::ADMIN]
        );
        $this->assertRefused($result);
        $this->assertSame(OpenIdConnectAuthenticationException::EMAIL_NOT_VERIFIED, $result[2]);
    }

    /**
     * A provider that existed before 5.9.1 has both checks off and signs in as before the upgrade.
     */
    public function testProviderWithChecksOffSignsInAsBefore(): void
    {
        [, $authenticated, $error] = $this->runCallback(
            3,
            ['sub' => self::SUB],
            ['sub' => 'another-subject', 'email' => self::ESS, 'email_verified' => false]
        );
        $this->assertNull($error, implode("\n", $this->warnings));
        $this->assertTrue($authenticated);
        $this->assertSame(2, AuthUser::getInstance()->getUserId());
    }

    /**
     * Refusals after the user lookup share one message, so the response does not tell whether
     * an account exists for the email. The reason is logged for the administrator.
     *
     * @dataProvider accountRefusalProvider
     */
    public function testAccountRefusalsShareOneMessage(string $email, string $loggedReason): void
    {
        $result = $this->runCallback(
            1,
            ['sub' => self::SUB, 'email' => $email, 'email_verified' => true],
            ['sub' => self::SUB, 'email' => $email, 'email_verified' => true]
        );
        $this->assertRefused($result);
        $this->assertSame(OpenIdConnectAuthenticationException::ACCOUNT_NOT_AVAILABLE, $result[2]);
        $this->assertCount(1, $this->warnings);
        $this->assertStringContainsString($loggedReason, $this->warnings[0]);
    }

    public function accountRefusalProvider(): array
    {
        return [
            'no account for the email' => ['nobody@orangehrm.us.com', 'No User Found'],
            'disabled account' => ['dora.disabled@orangehrm.us.com', 'Account disabled'],
        ];
    }

    public function testProviderErrorIsReportedAsSignInFailed(): void
    {
        $client = $this->getMockBuilder(OpenIDConnectClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['authenticate', 'getVerifiedClaims', 'requestUserInfo'])
            ->getMock();
        // e.g. the user cancelled at the provider: ?error=access_denied
        $client->method('authenticate')->willThrowException(new OpenIDConnectClientException('Error: access_denied'));
        $this->mockServices($client);
        $this->session->set(AuthUser::OPENID_PROVIDER_ID, 1);

        $response = $this->callbackController()->handle(new Request());
        $flash = $this->session->getFlashBag()->get(AuthUser::FLASH_LOGIN_ERROR);
        $this->assertSame(self::LOGIN_URL, $response->getTargetUrl());
        $this->assertSame(OpenIdConnectAuthenticationException::SIGN_IN_FAILED, $flash[0]['error'] ?? null);
        $this->assertFalse(AuthUser::getInstance()->isAuthenticated());
        $this->assertStringContainsString('access_denied', $this->warnings[0]);
    }

    public function testUserinfoForAnotherSubjectIsRefused(): void
    {
        $result = $this->runCallback(
            1,
            ['sub' => self::SUB],
            ['sub' => 'another-subject', 'email' => self::ESS, 'email_verified' => true]
        );
        $this->assertRefused($result);
        $this->assertSame(OpenIdConnectAuthenticationException::INCONSISTENT_USER_DETAILS, $result[2]);
    }

    public function testDisabledProviderCallbackIsRefused(): void
    {
        $result = $this->runCallback(
            2,
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true],
            ['sub' => self::SUB, 'email' => self::ESS, 'email_verified' => true]
        );
        $this->assertRefused($result);
        $this->assertSame(OpenIdConnectAuthenticationException::PROVIDER_NOT_AVAILABLE, $result[2]);
    }

    public function testCallbackWithoutStartedFlowIsRefused(): void
    {
        $this->mockServices(null);
        $response = $this->callbackController()->handle(new Request());
        $this->assertSame(self::LOGIN_URL, $response->getTargetUrl());
        $this->assertFalse(AuthUser::getInstance()->isAuthenticated());
        $this->assertFalse($this->getEntityManager()->getConnection()->isTransactionActive());
    }

    public function testCallbackWithoutCodeIsRefused(): void
    {
        $client = $this->getMockBuilder(OpenIDConnectClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['authenticate', 'getVerifiedClaims', 'requestUserInfo'])
            ->getMock();
        // no ?code= : the library starts a new authorization request and returns false
        $client->method('authenticate')->willReturn(false);
        $client->expects($this->never())->method('requestUserInfo');
        $this->mockServices($client);
        $this->session->set(AuthUser::OPENID_PROVIDER_ID, 1);
        $response = $this->callbackController()->handle(new Request());
        $this->assertSame(self::LOGIN_URL, $response->getTargetUrl());
        $this->assertFalse(AuthUser::getInstance()->isAuthenticated());
    }

    public function testStartWithEnabledProviderStoresProviderId(): void
    {
        $client = $this->getMockBuilder(OpenIDConnectClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['authenticate', 'getGeneratedAuthUrl'])
            ->getMock();
        $client->method('authenticate')->willReturn(false);
        $client->method('getGeneratedAuthUrl')->willReturn('https://idp.example/auth?state=s&nonce=n');
        $this->mockServices($client);

        $request = new Request();
        $request->attributes->set('providerId', 1);
        $response = (new OpenIdConnectLoginController())->handle($request);
        $this->assertSame('https://idp.example/auth?state=s&nonce=n&prompt=consent', $response->getTargetUrl());
        $this->assertSame(1, $this->session->get(AuthUser::OPENID_PROVIDER_ID));
    }

    /**
     * @dataProvider unavailableProviderIdProvider
     */
    public function testStartWithUnavailableProviderReturnsToLogin(int $providerId): void
    {
        $this->mockServices(null);
        $request = new Request();
        $request->attributes->set('providerId', $providerId);
        $response = (new OpenIdConnectLoginController())->handle($request);
        $flash = $this->session->getFlashBag()->get(AuthUser::FLASH_LOGIN_ERROR);
        $this->assertSame(self::LOGIN_URL, $response->getTargetUrl());
        $this->assertSame(OpenIdConnectAuthenticationException::PROVIDER_NOT_AVAILABLE, $flash[0]['error'] ?? null);
        $this->assertFalse($this->session->has(AuthUser::OPENID_PROVIDER_ID));
    }

    public function unavailableProviderIdProvider(): array
    {
        return ['disabled provider' => [2], 'unknown provider' => [99]];
    }

    public function testStartWithUnreachableProviderReturnsToLogin(): void
    {
        $client = $this->getMockBuilder(OpenIDConnectClient::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['authenticate'])
            ->getMock();
        $client->method('authenticate')->willThrowException(
            new OpenIDConnectClientException('Unable to get provider configuration data')
        );
        $this->mockServices($client);
        $request = new Request();
        $request->attributes->set('providerId', 1);

        $response = (new OpenIdConnectLoginController())->handle($request);
        $flash = $this->session->getFlashBag()->get(AuthUser::FLASH_LOGIN_ERROR);
        $this->assertSame(self::LOGIN_URL, $response->getTargetUrl());
        $this->assertSame(OpenIdConnectAuthenticationException::SIGN_IN_FAILED, $flash[0]['error'] ?? null);
    }
}
