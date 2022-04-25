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

namespace OrangeHRM\Tests\Authentication\Service;

use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Dao\ResetPasswordDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ResetPasswordRequest;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Routing\UrlGenerator;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Authentication
 * @group Service
 */
class ResetPasswordServiceTest extends KernelTestCase
{
    private string $fixture;
    private ResetPasswordService $resetPasswordService;

    protected function setUp(): void
    {
        $this->resetPasswordService = new ResetPasswordService();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmAuthenticationPlugin/test/fixtures/ResetPasswordService.yml';
        TestDataService::populate($this->fixture);
        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => new ConfigService(),
            Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            Services::USER_SERVICE => new UserService(),
        ]);
    }

    public function testGetEmailService(): void
    {
        $emailService = $this->resetPasswordService->getEmailService();
        $this->assertInstanceOf(EmailService::class, $emailService);
    }


    public function testResetPasswordDao(): void
    {
        $resetPasswordDao = $this->resetPasswordService->getResetPasswordDao();
        $this->assertInstanceOf(ResetPasswordDao::class, $resetPasswordDao);
    }


    public function testHasPasswordResetRequestNotExpired(): void
    {
        $resetPassword = $this->getEntityManager()->getRepository(ResetPasswordRequest::class)->findOneBy(
            ['resetCode' => 'YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q']
        );
        $exp = $this->resetPasswordService->hasPasswordResetRequestNotExpired($resetPassword);
        $this->assertGreaterThan(2, $exp);
    }

    public function testExtractPasswordResetMetaData(): void
    {
        $metaData = $this->resetPasswordService->extractPasswordResetMetaData(
            'YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q'
        );
        $this->assertCount(1, $metaData);

        $metaData = $this->resetPasswordService->extractPasswordResetMetaData('xpEY5IF4lNPp8bfWQzz2Q');
        $this->assertCount(0, $metaData);
    }

    public function testGenerateEmailBody(): void
    {
        $placeholders = [
            'firstName',
            'lastName',
            'middleName',
            'workEmail',
            'userName',
            'passwordResetLink',
            'code',
            'passwordResetCodeLink'
        ];
        $receiver = $this->getEntityManager()->getRepository(Employee::class)->findOneBy(['empNumber' => '1']);
        $replacements = [
            $receiver->getFirstName(),
            $receiver->getLastName(),
            $receiver->getMiddleName(),
            $receiver->getWorkEmail(),
            'samantha',
            'http://localhost/orangeHrm/orangehrm/web/index.php/auth/resetPassword/resetCode/YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q',
            'YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q',
        ];

        $body = $this->resetPasswordService->generateEmailBody(
            'password-reset-request.txt',
            $placeholders,
            $replacements
        );
        $this->assertIsString($body, '');
    }

    public function testSearchForUserRecord(): void
    {
        $user = $this->resetPasswordService->searchForUserRecord('samantha');
        $this->assertEquals(null, $user);

        $user = $this->resetPasswordService->searchForUserRecord('yashika');
        $this->assertEquals(null, $user);

        $user = $this->resetPasswordService->searchForUserRecord('user');
        $this->assertEquals(null, $user);

        $user = $this->resetPasswordService->searchForUserRecord('user1');
        $this->assertEquals(null, $user);

        $user = $this->resetPasswordService->searchForUserRecord('Renukshan');
        $this->assertInstanceOf(User::class, $user);
    }

    public function testSendPasswordResetCodeEmail(): void
    {
        $urlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->onlyMethods(['generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(
                'http://localhost/orangeHrm/orangehrm/web/index.php/auth/resetPassword/resetCode/YWRtaW4jU0VQQVJBVE9SI6kK4PL4sB8AtJa2y5WNP-Y'
            );
        $this->createKernelWithMockServices(
            [Services::URL_GENERATOR => $urlGenerator, Services::CONFIG_SERVICE => new ConfigService()]
        );
        $employee = $this->getEntityManager()->getRepository(Employee::class)->findOneBy(['empNumber' => '1']);

        $isSend = $this->resetPasswordService->sendPasswordResetCodeEmail(
            $employee,
            'YWRtaW4jU0VQQVJBVE9SI6kK4PL4sB8AtJa2y5WNP-Y',
            'testUser'
        );
        $this->assertEquals(false, $isSend);
    }

    public function testGeneratePasswordResetCode(): void
    {
        $result = $this->resetPasswordService->generatePasswordResetCode('Renukshan');
        $metaData = $this->resetPasswordService->extractPasswordResetMetaData($result);
        $this->assertEquals('Renukshan', $metaData[0]);
    }

    public function testSaveResetPassword(): void
    {
        $credential = new UserCredential('Renukshan', 'Admin1234%');
        $isSave = $this->resetPasswordService->saveResetPassword($credential);
        $this->assertEquals(true, $isSave);

        $credential->setUsername('Rensdukshan');
        $credential->setPassword('Admin1234%');
        $isSave = $this->resetPasswordService->saveResetPassword($credential);
        $this->assertEquals(false, $isSave);
    }

    public function testLogPasswordResetRequest(): void
    {
        $urlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->onlyMethods(['generate'])
            ->disableOriginalConstructor()
            ->getMock();
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(
                'http://localhost/orangeHrm/orangehrm/web/index.php/auth/resetPassword/resetCode/YWRtaW4jU0VQQVJBVE9SI6kK4PL4sB8AtJa2y5WNP-Y'
            );
        $this->createKernelWithMockServices(
            [
                Services::URL_GENERATOR => $urlGenerator,
                Services::CONFIG_SERVICE => new ConfigService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['id' => '3']);
        $isSave = $this->resetPasswordService->logPasswordResetRequest($user);
        $this->assertEquals(false, $isSave);

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['id' => '3']);
        $service = $this->getMockBuilder(ResetPasswordService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['logPasswordResetRequest'])
            ->getMock();
        $service->expects($this->once())
            ->method('logPasswordResetRequest')
            ->with($user)
            ->willReturn(true);
        $this->assertEquals(true, $service->logPasswordResetRequest($user));

        $service = $this->getMockBuilder(ResetPasswordService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['logPasswordResetRequest'])
            ->getMock();
        $service->expects($this->once())
            ->method('logPasswordResetRequest')
            ->with($user)
            ->willReturn(false);
        $this->assertEquals(false, $service->logPasswordResetRequest($user));
    }

    public function testValidateUrl(): void
    {
        $user = $this->resetPasswordService->validateUrl('UmVudWtzaGFuI1NFUEFSQVRPUiMGoPNCPf-4W2qC9iUGPl2n');
        $this->assertEquals(null, $user);

        $user = $this->resetPasswordService->validateUrl('YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q');
        $this->assertEquals(null, $user);

        $user = $this->resetPasswordService->validateUrl('YWRtaWsdsd4jU0VQQVJBsdsdsdVE9SI-xpEY5IF4lNPp8bfWQzz2Q');
        $this->assertEquals(null, $user);

        $user = $this->resetPasswordService->validateUrl('QWRtaW4jU0VQQVJBVE9SI02O9C_ScxCx_t5U1tKybS0');
        $this->assertEquals(null, $user);
    }

    public function testValidateUser(): void
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['id' => '3']);
        $user = $this->resetPasswordService->validateUser($user);
        $this->assertInstanceOf(User::class, $user);

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['id' => '1']);
        $user = $this->resetPasswordService->validateUser($user);
        $this->assertEquals(null, $user);

        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['id' => '2']);
        $user = $this->resetPasswordService->validateUser($user);
        $this->assertInstanceOf(User::class, $user);
    }
}
