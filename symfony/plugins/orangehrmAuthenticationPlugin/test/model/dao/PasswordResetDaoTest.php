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

/**
 * @group SecurityAuthentication
 */
class PasswordResetDaoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PasswordResetDao
     */
    private $dao;
    private $changedTable;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->dao = new PasswordResetDao();
        $this->changedTable = '';
    }

    public function testSaveResetPasswordLog_Correct()
    {
        $resetPasswordLog = new ResetPassword();
        $resetPasswordLog->setResetEmail('someone@example.com');
        $resetPasswordLog->setResetRequestDate(date('Y-m-d H:i:s'));
        $resetPasswordLog->setResetCode('test_user_1234567890');
        $this->assertTrue($this->dao->saveResetPasswordLog($resetPasswordLog));
    }


    public function testGetResetPasswordLogByEmail_Correct()
    {
        $result = $this->dao->getResetPasswordLogByEmail('someone@example.com');
        $this->assertNotNull($result);
        $this->assertTrue($result instanceof ResetPassword);
    }


    public function testGetSaveResetPasswordLog_Correct()
    {
        $resetPasswordLog = new ResetPassword();
        $resetPasswordLog->setResetEmail('someone@example.com');
        $resetPasswordLog->setResetRequestDate(date('Y-m-d H:i:s'));
        $resetPasswordLog->setResetCode('test_user_1234567890');
        $this->assertTrue($this->dao->saveResetPasswordLog($resetPasswordLog));
    }

    /**
     * @test testDeletePasswordResetRequestsByEmail
     */
    public function testDeletePasswordResetRequestsByEmail_Correct()
    {
        $resetPasswordLog = new ResetPassword();
        $resetPasswordLog->setResetEmail('someone@example.com');
        $resetPasswordLog->setResetRequestDate(date('Y-m-d H:i:s'));
        $resetPasswordLog->setResetCode('test_user_1234567890');
        $this->dao->saveResetPasswordLog($resetPasswordLog);
        $this->assertTrue($this->dao->getResetPasswordLogByEmail('someone@example.com') instanceof ResetPassword);
        $this->assertEquals(3, $this->dao->deletePasswordResetRequestsByEmail('someone@example.com'));
    }
}
