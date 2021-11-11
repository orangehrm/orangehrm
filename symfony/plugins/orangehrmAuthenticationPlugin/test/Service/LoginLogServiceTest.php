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
use OrangeHRM\Authentication\Dao\LoginLogDao;
use OrangeHRM\Authentication\Service\AuthenticationService;
use OrangeHRM\Authentication\Service\LoginService;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Authentication
 * @group Service
 */
class LoginLogServiceTest extends KernelTestCase
{
    /**
     * @var LoginService
     */
    private LoginService $loginService;

    protected function setUp(): void
    {
        $this->loginService = new LoginService();
    }

    public function testGetSystemUserService(): void
    {
        $this->createKernelWithMockServices([Services::USER_SERVICE => new UserService()]);
        $authenticationService = new AuthenticationService();
        $this->assertTrue($authenticationService->getSystemUserService() instanceof UserService);
    }

    public function testGetLoginLogDao(): void
    {
        $this->assertTrue($this->loginService->getLoginLogDao() instanceof LoginLogDao);
    }
}
