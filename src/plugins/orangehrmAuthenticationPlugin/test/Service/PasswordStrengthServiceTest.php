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

use DateTime;
use OrangeHRM\Admin\Service\UserService;
use OrangeHRM\Authentication\Dao\EnforcePasswordDao;
use OrangeHRM\Authentication\Dto\UserCredential;
use OrangeHRM\Authentication\Service\PasswordStrengthService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Helper\ClassHelper;
use OrangeHRM\Core\Service\ConfigService;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\EnforcePasswordRequest;
use OrangeHRM\Entity\User;
use OrangeHRM\Framework\Services;
use OrangeHRM\I18N\Service\I18NService;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\Mock\MockCacheService;
use OrangeHRM\Tests\Util\Mock\MockUserRoleManager;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Authentication
 * @group Service
 */
class PasswordStrengthServiceTest extends KernelTestCase
{
    private string $fixture;

    private PasswordStrengthService $passwordStrengthService;

    protected function setUp(): void
    {
        $this->passwordStrengthService = new PasswordStrengthService();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmAuthenticationPlugin/test/fixtures/PasswordStrengthValidation.yml';
        TestDataService::populate($this->fixture);

        $this->createKernelWithMockServices([
            Services::CONFIG_SERVICE => new ConfigService(),
            Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            Services::USER_SERVICE => new UserService(),
            Services::I18N_SERVICE => new I18NService(),
            Services::CACHE => MockCacheService::getCache(),
        ]);
    }

    public function testGetEnforcePasswordDao(): void
    {
        $enforcePasswordDao = $this->passwordStrengthService->getEnforcePasswordDao();
        $this->assertInstanceOf(EnforcePasswordDao::class, $enforcePasswordDao);
    }

    public function testCheckPasswordPolicies(): void
    {
        $credential = new UserCredential('admin', 'Admin123');
        $messages = $this->passwordStrengthService->checkPasswordPolicies($credential, 2);
        $this->assertEquals($messages[0], 'Should have at least 8 characters');

        $credential = new UserCredential('admin', 'Admin@OHRM123');
        $messages = $this->passwordStrengthService->checkPasswordPolicies($credential, 2);
        $this->assertEquals($messages[0], 'Your password meets the minimum requirements, but it could be guessable');
    }

    public function testIsValidPassword(): void
    {
        $credential = new UserCredential('admin', 'Admin@OHRM123');
        $isValid = $this->passwordStrengthService->isValidPassword($credential, 4);
        $this->assertEquals(true, $isValid);

        $credential = new UserCredential('Adalwin', 'Admin@OHRM123');
        $isValid = $this->passwordStrengthService->isValidPassword($credential, 2);
        $this->assertEquals(false, $isValid);
    }

    public function testLogPasswordEnforceRequest(): void
    {
        $this->createKernelWithMockServices([Services::CLASS_HELPER => new ClassHelper()]);
        $userRoleManager = $this->getMockBuilder(MockUserRoleManager::class)
            ->onlyMethods(['getUser'])
            ->getMock();
        $userRoleManager->method('getUser')
            ->willReturn($this->getEntityReference(User::class, 1));

        $this->createKernelWithMockServices(
            [
                Services::USER_ROLE_MANAGER => $userRoleManager,
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        $enforcePasswordRequest = new EnforcePasswordRequest();
        $enforcePasswordRequest->setId(1);
        $enforcePasswordRequest->setUser($userRoleManager->getUser());
        $enforcePasswordRequest->setResetRequestDate(new DateTime('2023-02-14'));
        $enforcePasswordRequest->setExpired(false);

        $result = $this->passwordStrengthService->logPasswordEnforceRequest();
        $this->assertIsString($result);
    }

    public function testValidateUrl(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturn(new DateTime('2023-05-25 09:40:00'));
        $this->getContainer()->set(Services::DATETIME_HELPER_SERVICE, $dateTimeHelper);

        $isValid = $this->passwordStrengthService->validateUrl('k08QAjOHBZWdi-JNPMyFRw');
        $this->assertEquals(true, $isValid);
    }

    public function testGetUserNameByResetCode(): void
    {
        $username = $this->passwordStrengthService->getUserNameByResetCode('k08QAjOHBZWdi-JNPMyFRw');
        $this->assertEquals('admin', $username);

        $username = $this->passwordStrengthService->getUserNameByResetCode('FIhTJCX-rSW_XjNKHTPScg');
        $this->assertEquals('Adalwin', $username);
    }

    public function testSaveEnforcedPassword(): void
    {
        $this->createKernelWithMockServices(
            [
                Services::USER_SERVICE =>  new UserService(),
                Services::DATETIME_HELPER_SERVICE => new DateTimeHelperService(),
            ]
        );

        $username = 'admin';
        $password = 'admin@OHRM123';
        $credentials = new UserCredential($username, $password);

        $result = $this->passwordStrengthService->saveEnforcedPassword($credentials);
        $this->assertEquals(true, $result);

        $username = 'admin123';
        $credentials = new UserCredential($username, $password);

        $result = $this->passwordStrengthService->saveEnforcedPassword($credentials);
        $this->assertEquals(false, $result);
    }
}
