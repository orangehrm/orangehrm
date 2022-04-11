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

use OrangeHRM\Authentication\Dao\ResetPasswordDao;
use OrangeHRM\Authentication\Service\ResetPasswordService;
use OrangeHRM\Config\Config;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Core\Traits\ORM\EntityManagerHelperTrait;
use OrangeHRM\Core\Traits\Service\DateTimeHelperTrait;
use OrangeHRM\Entity\ResetPasswordRequest;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Authentication
 * @group Dao
 */
class ResetPasswordDaoTest extends KernelTestCase
{
    use DateTimeHelperTrait;
    use EntityManagerHelperTrait;
    private ResetPasswordDao $resetPasswordDao;

    protected function setUp(): void
    {
        $this->resetPasswordDao = new ResetPasswordDao();
        $this->resetPasswordService = new ResetPasswordService();
        $this->fixture = Config::get(
            Config::PLUGINS_DIR
        ) . '/orangehrmAuthenticationPlugin/test/fixtures/ResetPasswordService.yml';
        TestDataService::populate($this->fixture);
        $this->createKernelWithMockServices([
          Services::DATETIME_HELPER_SERVICE=>new DateTimeHelperService()
        ]);
    }

    public function testSaveResetPassword(): void
    {
        $resetPassword=$this->getRepository(ResetPasswordRequest::class)->find('1');
        $resetPassword=$this->resetPasswordDao->saveResetPasswordRequest($resetPassword);
        $this->assertEquals('haran@orangehrm.live.com', $resetPassword->getResetEmail());
        $this->assertEquals('YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q', $resetPassword->getResetCode());
    }

    public function testGetResetPasswordLogByResetCode(): void
    {
        $resetCode=$this->resetPasswordDao->getResetPasswordLogByResetCode('YWRtaW4jU0VQQVJBVE9SI-xpEY5IF4lNPp8bfWQzz2Q');
        $this->assertEquals('haran@orangehrm.live.com', $resetCode->getResetEmail());
    }
}
