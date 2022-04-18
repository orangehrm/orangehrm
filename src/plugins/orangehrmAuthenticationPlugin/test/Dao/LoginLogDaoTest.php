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

namespace OrangeHRM\Tests\Authentication\Dao;

use OrangeHRM\Authentication\Dao\LoginLogDao;
use OrangeHRM\Entity\LoginLog;
use OrangeHRM\Tests\Util\TestCase;

/**
 * @group Authentication
 * @group Dao
 */
class LoginLogDaoTest extends TestCase
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
    }
}
