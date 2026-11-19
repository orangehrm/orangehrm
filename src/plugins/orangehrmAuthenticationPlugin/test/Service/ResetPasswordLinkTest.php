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

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Entity\EmailConfiguration;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ResetPasswordRequest;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * The password reset link comes from the configured application base URL, never from the request.
 *
 * @group Authentication
 * @group Service
 */
class ResetPasswordLinkTest extends KernelTestCase
{
    private const BASE_URL = 'https://hr.example.com/orangehrm/web/index.php';
    private const REQUEST_HOST = 'other.example';
    private const RESET_CODE = 'UmVudWtzaGFuI1NFUEFSQVRPUiP-random';

    private UrlGenerator $urlGenerator;

    protected function setUp(): void
    {
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAuthenticationPlugin/test/fixtures/ResetPasswordService.yml'
        );
        $this->setStoredBaseUrl(null);

        // Same wiring as Framework::configureRouter(), for a request whose Host header differs from the stored URL
        $request = Request::create(
            'http://' . self::REQUEST_HOST . '/orangehrm/web/index.php/auth/requestResetPassword',
            'POST',
            [],
            [],
            [],
            ['SCRIPT_NAME' => '/orangehrm/web/index.php', 'SCRIPT_FILENAME' => '/var/www/orangehrm/web/index.php']
        );
        $context = new RequestContext();
        $context->fromRequest($request);
        $routes = new RouteCollection();
        $routes->add('auth_reset_code', new Route('/auth/resetPassword/resetCode/{resetCode}'));
        $routes->add('auth_login', new Route('/auth/login'));
        $this->urlGenerator = new UrlGenerator($routes, $context);

        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => new ConfigService(),
            Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            Services::USER_SERVICE => new UserService(),
            Services::URL_GENERATOR => $this->urlGenerator,
        ]);
    }

    protected function tearDown(): void
    {
        $this->setStoredBaseUrl(null);
        parent::tearDown();
    }

    public function testResetLinkUsesConfiguredBaseUrl(): void
    {
        $this->setStoredBaseUrl(self::BASE_URL);

        $link = (new ResetPasswordService())->getPasswordResetLink(self::RESET_CODE);

        $this->assertSame(self::BASE_URL . '/auth/resetPassword/resetCode/' . self::RESET_CODE, $link);
        $this->assertStringNotContainsString(self::REQUEST_HOST, $link);
    }

    public function testResetLinkUsesNormalizedBaseUrl(): void
    {
        $this->setStoredBaseUrl('  HTTP://HR.example.com:8080/  ');

        $this->assertSame(
            'http://hr.example.com:8080/auth/resetPassword/resetCode/' . self::RESET_CODE,
            (new ResetPasswordService())->getPasswordResetLink(self::RESET_CODE)
        );
    }

    /**
     * @dataProvider unusableBaseUrlDataProvider
     */
    public function testResetLinkIsNullWithoutUsableBaseUrl(?string $storedValue): void
    {
        $this->setStoredBaseUrl($storedValue);

        $this->assertNull((new ResetPasswordService())->getPasswordResetLink(self::RESET_CODE));
    }

    public function unusableBaseUrlDataProvider(): array
    {
        return [
            'not set' => [null],
            'empty' => [''],
            'host only' => [self::REQUEST_HOST],
            'not http(s)' => ['ftp://hr.example.com/web/index.php'],
            'with query' => ['https://hr.example.com/web/index.php?x=1'],
        ];
    }

    public function testSharedRequestContextIsNotChanged(): void
    {
        $this->setStoredBaseUrl(self::BASE_URL);

        (new ResetPasswordService())->getPasswordResetLink(self::RESET_CODE);

        // Other absolute URLs (redirects to the same client) keep using the request context
        $this->assertSame(self::REQUEST_HOST, $this->urlGenerator->getContext()->getHost());
        $this->assertSame(
            'http://' . self::REQUEST_HOST . '/orangehrm/web/index.php/auth/login',
            $this->urlGenerator->generate('auth_login', [], UrlGenerator::ABSOLUTE_URL)
        );
    }

    public function testEmailBodyCarriesConfiguredLink(): void
    {
        $this->setStoredBaseUrl(self::BASE_URL);
        $body = null;
        $emailService = $this->getEmailServiceMock(true, $body);

        $sent = $this->getServiceWithEmailService($emailService)
            ->sendPasswordResetCodeEmail($this->getEmployee(), self::RESET_CODE, 'Renukshan');

        $this->assertTrue($sent);
        $this->assertStringContainsString(
            self::BASE_URL . '/auth/resetPassword/resetCode/' . self::RESET_CODE,
            $body
        );
        $this->assertStringNotContainsString(self::REQUEST_HOST, $body);
    }

    public function testEmailNotSentWithoutBaseUrl(): void
    {
        $body = null;
        $emailService = $this->getEmailServiceMock(false, $body);

        $sent = $this->getServiceWithEmailService($emailService)
            ->sendPasswordResetCodeEmail($this->getEmployee(), self::RESET_CODE, 'Renukshan');

        $this->assertFalse($sent);
        $this->assertNull($body);
    }

    public function testResetRequestNotSavedWithoutBaseUrl(): void
    {
        $body = null;
        $emailService = $this->getEmailServiceMock(false, $body);
        $user = $this->getEntityManager()->getRepository(User::class)->find(3);
        $before = $this->getEntityManager()->getRepository(ResetPasswordRequest::class)->count([]);

        $saved = $this->getServiceWithEmailService($emailService)->logPasswordResetRequest($user);

        $this->assertFalse($saved);
        $this->assertSame(
            $before,
            $this->getEntityManager()->getRepository(ResetPasswordRequest::class)->count([])
        );
    }

    public function testResetRequestSavedWithBaseUrl(): void
    {
        $this->setStoredBaseUrl(self::BASE_URL);
        $body = null;
        $emailService = $this->getEmailServiceMock(true, $body);
        $user = $this->getEntityManager()->getRepository(User::class)->find(3);
        $before = $this->getEntityManager()->getRepository(ResetPasswordRequest::class)->count([]);

        $saved = $this->getServiceWithEmailService($emailService)->logPasswordResetRequest($user);

        $this->assertTrue($saved);
        $this->assertSame(
            $before + 1,
            $this->getEntityManager()->getRepository(ResetPasswordRequest::class)->count([])
        );
        $this->assertStringContainsString(self::BASE_URL . '/auth/resetPassword/resetCode/', $body);
    }

    /**
     * @param bool $expectSend
     * @param string|null $body receives the message body when one is set
     * @return EmailService
     */
    private function getEmailServiceMock(bool $expectSend, ?string &$body): EmailService
    {
        $emailConfig = new EmailConfiguration();
        $emailConfig->setSentAs('hr-noreply@example.com');
        $emailService = $this->getMockBuilder(EmailService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                ['getEmailConfig', 'setMessageTo', 'setMessageFrom', 'setMessageSubject', 'setMessageBody', 'sendEmail']
            )
            ->getMock();
        $emailService->method('getEmailConfig')->willReturn($emailConfig);
        $emailService->method('setMessageBody')->willReturnCallback(
            static function ($messageBody) use (&$body) {
                $body = $messageBody;
            }
        );
        $emailService->expects($expectSend ? $this->once() : $this->never())
            ->method('sendEmail')
            ->willReturn(true);
        return $emailService;
    }

    private function getServiceWithEmailService(EmailService $emailService): ResetPasswordService
    {
        $service = $this->getMockBuilder(ResetPasswordService::class)
            ->onlyMethods(['getEmailService'])
            ->getMock();
        $service->method('getEmailService')->willReturn($emailService);
        return $service;
    }

    private function getEmployee(): Employee
    {
        return $this->getEntityManager()->getRepository(Employee::class)->find(3);
    }

    private function setStoredBaseUrl(?string $value): void
    {
        $this->getEntityManager()->getConnection()
            ->delete('hs_hr_config', ['name' => ConfigService::KEY_INSTANCE_BASE_URL]);
        if ($value !== null) {
            (new ConfigDao())->setValue(ConfigService::KEY_INSTANCE_BASE_URL, $value);
        }
    }
}
