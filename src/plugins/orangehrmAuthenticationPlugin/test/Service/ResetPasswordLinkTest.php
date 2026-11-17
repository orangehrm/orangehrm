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

namespace OrangeHRM\Tests\Authentication\Service;

use DateTime;
use OrangeHRM\Authentication\Service\LoginService;
use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\LoginLog;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Http\RequestStack;
use OrangeHRM\Framework\Logger\Logger;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use ReflectionMethod;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * The password reset email links only to an address that an Admin signed in on recently, never to the host of
 * the request that asked for the reset. Without such an address it carries the reset code and no link.
 *
 * @group Authentication
 * @group Service
 */
class ResetPasswordLinkTest extends KernelTestCase
{
    private const NOW = '2026-11-17 10:00:00';
    private const CODE = 'YWRtaW4jU0VQQVJBVE9SI6kK4PL4sB8AtJa2y5WNP-Y';
    private const SITE = 'https://hr.example.com/ohrm/web/index.php';
    private const INTERNAL = 'http://10.0.0.5/ohrm/web/index.php';
    private const EVIL = 'https://evil.example/ohrm/web/index.php';

    private array $warnings = [];

    protected function setUp(): void
    {
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAuthenticationPlugin/test/fixtures/ResetPasswordService.yml'
        );
        TestDataService::truncateSpecificTables([LoginLog::class]);
    }

    /**
     * @param string|null $requestBaseUrl base URL the reset was requested on; null outside a web request
     */
    private function mockServices(?string $requestBaseUrl): void
    {
        $requestStack = new RequestStack();
        $context = new RequestContext();
        if ($requestBaseUrl !== null) {
            $request = $this->createRequest($requestBaseUrl . '/auth/requestResetPassword');
            $requestStack->push($request);
            // As in production, the router's context follows the request, including a forged Host
            $context->fromRequest($request);
        }
        $routes = new RouteCollection();
        $routes->add('auth_reset_code', new Route('/auth/resetPassword/resetCode/{resetCode}'));

        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->method('getNow')->willReturnCallback(fn () => new DateTime(self::NOW));

        $this->warnings = [];
        $logger = $this->getMockBuilder(Logger::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['warning'])
            ->getMock();
        $logger->method('warning')->willReturnCallback(function ($message) {
            $this->warnings[] = $message;
        });

        $this->createKernelWithMockServices([
            Services::REQUEST_STACK => $requestStack,
            Services::URL_GENERATOR => new UrlGenerator($routes, $context),
            Services::DATETIME_HELPER_SERVICE => $dateTimeHelper,
            Services::LOGGER => $logger,
        ]);
    }

    /**
     * A request on the given URL, served by the front controller /ohrm/web/index.php
     */
    private function createRequest(string $url): Request
    {
        return Request::create($url, 'POST', [], [], [], [
            'SCRIPT_NAME' => '/ohrm/web/index.php',
            'SCRIPT_FILENAME' => '/var/www/ohrm/web/index.php',
        ]);
    }

    private function addSignIn(string $baseUrl, string $loginTime, string $role = 'Admin', int $predefined = 1): void
    {
        $loginLog = new LoginLog();
        $loginLog->setUserId(6);
        $loginLog->setUserName('Admin');
        $loginLog->setUserRoleName($role);
        $loginLog->setUserRolePredefined($predefined);
        $loginLog->setBaseUrl($baseUrl);
        $loginLog->setLoginTime(new DateTime($loginTime));
        $this->getEntityManager()->persist($loginLog);
        $this->getEntityManager()->flush();
    }

    private function getEmailBody(): string
    {
        $method = new ReflectionMethod(ResetPasswordService::class, 'generatePasswordResetEmailBody');
        $method->setAccessible(true);
        /** @var Employee $employee */
        $employee = $this->getEntityManager()->getRepository(Employee::class)->find(1);
        return $method->invoke(new ResetPasswordService(), $employee, self::CODE, 'Admin');
    }

    public function testNoAdminSignInSendsCodeWithoutLink(): void
    {
        $this->mockServices(self::EVIL);

        $this->assertNull((new ResetPasswordService())->getPasswordResetBaseUrl());
        $body = $this->getEmailBody();
        $this->assertStringContainsString(self::CODE, $body);
        $this->assertStringNotContainsString('http', $body);
        $this->assertStringNotContainsString('evil.example', $body);
        $this->assertCount(1, $this->warnings);
        $this->assertStringContainsString('no Admin has signed in', $this->warnings[0]);
    }

    public function testRequestOnAnAddressAnAdminSignedInOnUsesThatAddress(): void
    {
        $this->mockServices(self::INTERNAL);
        $this->addSignIn(self::INTERNAL, '2026-11-10 09:00:00');
        $this->addSignIn(self::SITE, '2026-11-16 09:00:00');

        $this->assertSame(self::INTERNAL, (new ResetPasswordService())->getPasswordResetBaseUrl());
        $this->assertStringContainsString(
            self::INTERNAL . '/auth/resetPassword/resetCode/' . self::CODE,
            $this->getEmailBody()
        );
    }

    /**
     * The reported attack: the reset is requested with a forged Host header
     */
    public function testForgedHostFallsBackToTheLatestAdminAddress(): void
    {
        $this->mockServices(self::EVIL);
        $this->addSignIn(self::INTERNAL, '2026-11-10 09:00:00');
        $this->addSignIn(self::SITE, '2026-11-16 09:00:00');

        $this->assertSame(self::SITE, (new ResetPasswordService())->getPasswordResetBaseUrl());
        $body = $this->getEmailBody();
        $this->assertStringContainsString(self::SITE . '/auth/resetPassword/resetCode/' . self::CODE, $body);
        $this->assertStringNotContainsString('evil.example', $body);
        $this->assertSame([], $this->warnings);
    }

    /**
     * An employee who signs in with a forged Host header does not make that host trusted
     */
    public function testSignInsOfOtherRolesAreNotTrusted(): void
    {
        $this->mockServices(self::EVIL);
        $this->addSignIn(self::SITE, '2026-11-01 09:00:00');
        $this->addSignIn(self::EVIL, '2026-11-16 09:00:00', 'ESS');
        $this->addSignIn(self::EVIL, '2026-11-16 09:30:00', 'Admin', 0);

        $this->assertSame(self::SITE, (new ResetPasswordService())->getPasswordResetBaseUrl());
        $this->assertStringNotContainsString('evil.example', $this->getEmailBody());
    }

    /**
     * An address no Admin has used recently (e.g. a domain the organization gave up) is not trusted
     */
    public function testOldAdminSignInsAreNotTrusted(): void
    {
        $this->mockServices(self::EVIL);
        $this->addSignIn(self::EVIL, '2026-10-10 09:00:00');

        $this->assertNull((new ResetPasswordService())->getPasswordResetBaseUrl());
        $this->assertStringNotContainsString('evil.example', $this->getEmailBody());
    }

    public function testResetOutsideAWebRequestUsesTheLatestAdminAddress(): void
    {
        $this->mockServices(null);
        $this->addSignIn(self::SITE, '2026-11-16 09:00:00');

        $this->assertSame(self::SITE, (new ResetPasswordService())->getPasswordResetBaseUrl());
    }

    public function testLinkIsEscapedForTheHtmlEmail(): void
    {
        $this->mockServices(null);
        $this->addSignIn('https://hr.example.com/a&b/web/index.php', '2026-11-16 09:00:00');

        $body = $this->getEmailBody();
        $this->assertStringContainsString('https://hr.example.com/a&amp;b/web/index.php/auth/resetPassword/', $body);
        $this->assertStringNotContainsString('a&b', $body);
    }

    public function testSignInRecordsTheBaseUrlOfTheRequest(): void
    {
        $this->mockServices('https://hr.example.com:8443/ohrm/web/index.php');
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->find(6);

        $loginLog = (new LoginService())->addOIDCLogin($user);

        $this->assertSame('https://hr.example.com:8443/ohrm/web/index.php', $loginLog->getBaseUrl());
    }

    public function testCurrentBaseUrl(): void
    {
        $this->mockServices(null);
        $this->assertNull((new LoginService())->getCurrentBaseUrl(), 'no web request');

        /** @var RequestStack $requestStack */
        $requestStack = $this->getContainer()->get(Services::REQUEST_STACK);
        $requestStack->push($this->createRequest('https://HR.Example.COM:443/ohrm/web/index.php/auth/validate'));
        $this->assertSame('https://hr.example.com/ohrm/web/index.php', (new LoginService())->getCurrentBaseUrl());

        $request = $this->createRequest('https://hr.example.com/ohrm/web/index.php/auth/validate');
        $request->headers->set('HOST', 'bad host/name');
        $requestStack->pop();
        $requestStack->push($request);
        $this->assertNull((new LoginService())->getCurrentBaseUrl(), 'Host header that is not a host name');
    }
}
