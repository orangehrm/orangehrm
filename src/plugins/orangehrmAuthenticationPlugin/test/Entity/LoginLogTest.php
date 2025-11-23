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

namespace OrangeHRM\Tests\Authentication\Entity;

use DateTime;
use OrangeHRM\Core\Service\DateTimeHelperService;
use OrangeHRM\Entity\LoginLog;
use OrangeHRM\Framework\Services;
use OrangeHRM\Tests\Util\EntityTestCase;
use OrangeHRM\Tests\Util\TestDataService;

/**
 * @group Authentication
 * @group Entity
 */
class LoginLogTest extends EntityTestCase
{
    protected function setUp(): void
    {
        TestDataService::truncateSpecificTables([LoginLog::class]);
    }

    public function testLoginLogEntity(): void
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
        $loginLog->setUserName('username');
        $loginLog->setUserRoleName('Admin');
        $loginLog->setUserRolePredefined(0);
        $this->persist($loginLog);

        /** @var LoginLog $loginLog */
        $loginLog = $this->getRepository(LoginLog::class)->find(1);
        $this->assertEquals(1, $loginLog->getUserId());
        $this->assertEquals('username', $loginLog->getUserName());
        $this->assertEquals('Admin', $loginLog->getUserRoleName());
        $this->assertEquals(0, $loginLog->getUserRolePredefined());
        $this->assertEquals('2022-07-04 10:56', $loginLog->getLoginTime()->format('Y-m-d H:i'));
    }
}
