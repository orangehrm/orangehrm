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

namespace OrangeHRM\Tests\Authentication\Controller;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Controller\RequestResetPasswordController;
use OrangeHRM\Authentication\Csrf\CsrfTokenManager;
use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Authentication\Traits\CsrfTokenManagerTrait;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Dao\ConfigDao;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Entity\EmailConfiguration;
use OrangeHRM\Entity\ResetPasswordRequest;
use OrangeHRM\Framework\Http\Request;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\ArrayCsrfTokenStorage;
use OrangeHRM\Tests\Util\TestDataService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * The forgot-password form posted to a host name other than the stored application URL.
 *
 * @group Authentication
 * @group Controller
 */
class RequestResetPasswordControllerTest extends KernelTestCase
{
    use CsrfTokenManagerTrait;

    private const BASE_URL = 'https://hr.example.com/orangehrm/web/index.php';
    private const REQUEST_HOST = 'other.example';
    private const USERNAME = 'Renukshan';
    private const UNKNOWN_USERNAME = 'no.such.user';

    private ?string $body = null;
    private int $sendCount = 0;
    /**
     * What the mail transport does: 'sent', 'failed' (sendEmail returns false) or 'error' (transport exception)
     */
    private string $mailResult = 'sent';

    protected function setUp(): void
    {
        $this->createKernel();
        TestDataService::populate(
            Config::get(Config::PLUGINS_DIR) . '/orangehrmAuthenticationPlugin/test/fixtures/ResetPasswordService.yml'
        );
        $this->setStoredBaseUrl(null);
        $this->body = null;
        $this->sendCount = 0;
        $this->mailResult = 'sent';
    }

    protected function tearDown(): void
    {
        $this->setStoredBaseUrl(null);
        parent::tearDown();
    }

    public function testEmailLinkUsesStoredBaseUrl(): void
    {
        $this->setStoredBaseUrl(self::BASE_URL);
        $before = $this->countResetRequests();

        $response = $this->handle();

        $this->assertSame(1, $this->sendCount);
        $this->assertMatchesRegularExpression(
            '#' . preg_quote(self::BASE_URL, '#') . '/auth/resetPassword/resetCode/[A-Za-z0-9_-]+#',
            $this->body
        );
        $this->assertStringNotContainsString(self::REQUEST_HOST, $this->body);
        $this->assertSame($before + 1, $this->countResetRequests());
        // The redirect goes back to the client that submitted the form
        $this->assertSame(
            'http://' . self::REQUEST_HOST . '/orangehrm/web/index.php/auth/sendPasswordReset',
            $response->getTargetUrl()
        );
    }

    public function testNoEmailWithoutStoredBaseUrl(): void
    {
        $before = $this->countResetRequests();

        $response = $this->handle();

        $this->assertSame(0, $this->sendCount);
        $this->assertNull($this->body);
        $this->assertSame($before, $this->countResetRequests());
        $this->assertSame(
            'http://' . self::REQUEST_HOST . '/orangehrm/web/index.php/auth/sendPasswordResetFailure',
            $response->getTargetUrl()
        );
    }

    /**
     * Without a stored application URL the answer must not tell an existing username from an unknown one.
     */
    public function testUnknownUsernameGetsTheSameAnswerWithoutStoredBaseUrl(): void
    {
        $known = $this->handle();
        $unknown = $this->handle(self::UNKNOWN_USERNAME);

        $this->assertSame(0, $this->sendCount);
        $this->assertSame(
            'http://' . self::REQUEST_HOST . '/orangehrm/web/index.php/auth/sendPasswordResetFailure',
            $unknown->getTargetUrl()
        );
        $this->assertSame($known->getTargetUrl(), $unknown->getTargetUrl());
    }

    public function testUnknownUsernameWithStoredBaseUrl(): void
    {
        $this->setStoredBaseUrl(self::BASE_URL);

        $response = $this->handle(self::UNKNOWN_USERNAME);

        $this->assertSame(0, $this->sendCount);
        $this->assertSame(
            'http://' . self::REQUEST_HOST . '/orangehrm/web/index.php/auth/sendPasswordReset',
            $response->getTargetUrl()
        );
    }

    /**
     * When the email cannot be sent, the answer must not tell an existing username from an unknown one.
     *
     * @dataProvider mailFailureDataProvider
     */
    public function testFailedEmailGetsTheSameAnswerAsAnUnknownUsername(string $mailResult): void
    {
        $this->setStoredBaseUrl(self::BASE_URL);
        $this->mailResult = $mailResult;
        $before = $this->countResetRequests();

        $known = $this->handle();
        $unknown = $this->handle(self::UNKNOWN_USERNAME);

        $this->assertSame(1, $this->sendCount, 'Sending was attempted for the existing username only');
        $this->assertSame($before, $this->countResetRequests(), 'No reset request is saved when sending fails');
        $this->assertSame(
            'http://' . self::REQUEST_HOST . '/orangehrm/web/index.php/auth/sendPasswordReset',
            $known->getTargetUrl()
        );
        $this->assertSame($unknown->getTargetUrl(), $known->getTargetUrl());
    }

    /**
     * @return array<string, array{string}>
     */
    public function mailFailureDataProvider(): array
    {
        return [
            'sendEmail returns false' => ['failed'],
            'mail transport error' => ['error'],
        ];
    }

    /**
     * POST /auth/requestResetPassword with Host: other.example, through the controller.
     */
    private function handle(string $username = self::USERNAME)
    {
        $request = Request::create(
            'http://' . self::REQUEST_HOST . '/orangehrm/web/index.php/auth/requestResetPassword',
            'POST',
            [],
            [],
            [],
            ['SCRIPT_NAME' => '/orangehrm/web/index.php', 'SCRIPT_FILENAME' => '/var/www/orangehrm/web/index.php']
        );
        $requestStack = new RequestStack();
        $requestStack->push($request);

        // Same wiring as Framework::configureRouter(): the shared URL generator uses the request context
        $context = new RequestContext();
        $context->fromRequest($request);
        $routes = new RouteCollection();
        $routes->add('auth_reset_code', new Route('/auth/resetPassword/resetCode/{resetCode}'));
        $urlGenerator = new UrlGenerator($routes, $context);

        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => new ConfigService(),
            Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            Services::USER_SERVICE => new UserService(),
            Services::URL_GENERATOR => $urlGenerator,
            Services::REQUEST_STACK => $requestStack,
        ]);
        $this->getContainer()->register(Services::CSRF_TOKEN_STORAGE, ArrayCsrfTokenStorage::class);
        $this->getContainer()->register(Services::CSRF_TOKEN_MANAGER, CsrfTokenManager::class);

        $request->request->set('_token', $this->getCsrfTokenManager()->getToken('request-reset-password')->getValue());
        $request->request->set('username', $username);

        $service = $this->getMockBuilder(ResetPasswordService::class)
            ->onlyMethods(['getEmailService'])
            ->getMock();
        $service->method('getEmailService')->willReturn($this->getEmailServiceMock());
        $controller = $this->getMockBuilder(RequestResetPasswordController::class)
            ->onlyMethods(['getResetPasswordService'])
            ->getMock();
        $controller->method('getResetPasswordService')->willReturn($service);

        return $controller->handle($request);
    }

    private function getEmailServiceMock(): EmailService
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
        $emailService->method('setMessageBody')->willReturnCallback(function ($messageBody) {
            $this->body = $messageBody;
        });
        $emailService->method('sendEmail')->willReturnCallback(function () {
            $this->sendCount++;
            if ($this->mailResult === 'error') {
                throw new TransportException('Connection could not be established with host "smtp.example.test"');
            }
            return $this->mailResult === 'sent';
        });
        return $emailService;
    }

    private function countResetRequests(): int
    {
        return $this->getEntityManager()->getRepository(ResetPasswordRequest::class)->count([]);
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
