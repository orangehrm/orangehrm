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

namespace OrangeHRM\Tests\Authentication\Dao;

use DateTime;
use OrangeHRM\Authentication\Dao\LoginLogDao;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\LoginLog;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\KernelTestCase;

/**
 * @group Authentication
 * @group Dao
 */
class LoginLogDaoTest extends KernelTestCase
{
    /**
     * @var LoginLogDao
     */
    private LoginLogDao $loginLogDao;

    protected function setUp(): void
    {
        $this->loginLogDao = new LoginLogDao();
    }

    public function testSaveLoginLog(): void
    {
        $dateTimeHelper = $this->getMockBuilder(DateTimeHelperService::class)
            ->onlyMethods(['getNow'])
            ->getMock();
        $dateTimeHelper->expects($this->atLeastOnce())
            ->method('getNow')
            ->willReturn(new DateTime('2022-07-04 10:56:56'));
        $this->getContainer()->set(Services::DATETIME_HELPER_SERVICE, $dateTimeHelper);

        $loginLog = new LoginLog();
        $loginLog->setUserId(1);
        $loginLog->setUserName('Admin');
        $loginLog->setUserRoleName('Admin');
        $loginLog->setUserRolePredefined(1);
        $result = $this->loginLogDao->saveLoginLog($loginLog);

        $this->assertTrue($result instanceof LoginLog);
        $this->assertEquals(1, $result->getUserId());
        $this->assertEquals('Admin', $result->getUserName());
        $this->assertEquals('Admin', $result->getUserRoleName());
        $this->assertEquals(1, $result->getUserRolePredefined());
        $this->assertEquals('2022-07-04 10:56', $result->getLoginTime()->format('Y-m-d H:i'));
    }
}
