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

use OrangeHRM\Admin\Dao\UserDao;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Dao\ResetPasswordDao;
use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\EmailService;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\Employee;
use OrangeHRM\Entity\ResetPassword;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

class ResetPasswordServiceTest extends KernelTestCase
{
    use DateTimeHelperTrait;

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
            Services::CONFIG_SERVICE=>new ConfigService()
        ]);
    }

    public function testGetEmailService(): void
    {
        $emailService=$this->resetPasswordService->getEmailService();
        $this->assertInstanceOf(EmailService::class, $emailService);
    }

    public function testUserService(): void
    {
        $userService=$this->resetPasswordService->getUserService();
        $this->assertInstanceOf(UserService::class, $userService);
    }

    public function testResetPasswordDao(): void
    {
        $resetPasswordDao=$this->resetPasswordService->getResetPasswordDao();
        $this->assertInstanceOf(ResetPasswordDao::class, $resetPasswordDao);
    }

    public function testUserDao(): void
    {
        $userDao=$this->resetPasswordService->getUserDao();
        $this->assertInstanceOf(UserDao::class, $userDao);
    }

    public function testHasPasswordResetRequestNotExpired(): void
    {
        $resetPassword=$this->getEntityManager()->getRepository(ResetPassword::class)->findOneBy(['resetCode'=>'YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q']);
        $exp=$this->resetPasswordService->hasPasswordResetRequestNotExpired($resetPassword);
        $this->assertGreaterThan(2, $exp);
    }

    public function testExtractPasswordResetMetaData(): void
    {
        $metaData=$this->resetPasswordService->extractPasswordResetMetaData('YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q');
        $this->assertCount(1, $metaData);

        $metaData=$this->resetPasswordService->extractPasswordResetMetaData('xpEY5IF4lNPp8bfWQzz2Q');
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
        $receiver=$this->getEntityManager()->getRepository(Employee::class)->findOneBy(['empNumber'=>'1']);
        $replacements = [
            $receiver->getFirstName(),
            $receiver->getLastName(),
            $receiver->getMiddleName(),
            $receiver->getWorkEmail(),
            'samantha',
            'http://localhost/orangeHrm/orangehrm/web/index.php/auth/resetPassword/resetCode/YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q',
            'YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q',
        ];

        $body=$this->resetPasswordService->generateEmailBody('password-reset-request.txt', $placeholders, $replacements);
        $this->assertIsString($body, '');
    }

    public function testSearchForUserRecord(): void
    {
        $user=$this->resetPasswordService->searchForUserRecord('samantha');
        $this->assertEquals(null, $user);

        $user=$this->resetPasswordService->searchForUserRecord('Renukshan');
        $this->assertInstanceOf(User::class, $user);

//        $user=$this->resetPasswordService->searchForUserRecord('Renukshan');
//        $this->assertInstanceOf(User::class,$user);
    }
}
