<?php
/**
 * OrangeHRM Enterprise is a closed sourced comprehensive Human Resource Management (HRM)
 * System that captures all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM Inc is the owner of the patent, copyright, trade secrets, trademarks and any
 * other intellectual property rights which subsist in the Licensed Materials. OrangeHRM Inc
 * is the owner of the media / downloaded OrangeHRM Enterprise software files on which the
 * Licensed Materials are received. Title to the Licensed Materials and media shall remain
 * vested in OrangeHRM Inc. For the avoidance of doubt title and all intellectual property
 * rights to any design, new software, new protocol, new interface, enhancement, update,
 * derivative works, revised screen text or any other items that OrangeHRM Inc creates for
 * Customer shall remain vested in OrangeHRM Inc. Any rights not expressly granted herein are
 * reserved to OrangeHRM Inc.
 *
 * Please refer http://www.orangehrm.com/Files/OrangeHRM_Commercial_License.pdf for the license which includes terms and conditions on using this software.
 *
 */

/**
 * Created by PhpStorm.
 * User: administrator
 * Date: 4/4/19
 * Time: 1:10 PM
 */

/**
 * @group SecurityAuthentication
 */
class SecurityAuthenticationDaoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SecurityAuthenticationDao
     */
    private $dao;
    private $changedTable;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->dao = new SecurityAuthenticationDao;
        $this->changedTable = '';
    }

//    public function testIsEmailConfigurationSetup() {
//        $emailConfig=new EmailConfiguration();
//
//        $result=$this->dao->isEmailConfigurationSetup();
//        $this->assertTrue($result instanceof EmailConfiguration);
//    }

    public function testSaveResetPasswordLog_Correct() {
        $resetPasswordLog = new ResetPasswordLog();
        //$resetPasswordLog->setId(2);
        $resetPasswordLog->setResetEmail('someone@example.com');
        $resetPasswordLog->setResetRequestDate(date('Y-m-d H:i:s'));
        $resetPasswordLog->setResetCode('test_user_1234567890');
        $resetPasswordLog->setStatus(SecurityAuthenticationService::STATE_NOT_USED);
        $this->assertTrue($this->dao->saveResetPasswordLog($resetPasswordLog));
    }


    public function testGetResetPasswordLogByEmail_Correct() {
        $result = $this->dao->getResetPasswordLogByEmail('someone@example.com');
        $this->assertNotNull($result);
        $this->assertTrue($result instanceof ResetPasswordLog);
    }


    public function testGetSaveResetPasswordLog_Correct() {
        $resetPasswordLog = new ResetPasswordLog();
        //$resetPasswordLog->setId(2);
        $resetPasswordLog->setResetEmail('someone@example.com');
        $resetPasswordLog->setResetRequestDate(date('Y-m-d H:i:s'));
        $resetPasswordLog->setResetCode('test_user_1234567890');
        $resetPasswordLog->setStatus(SecurityAuthenticationService::STATE_NOT_USED);
        $this->assertTrue($this->dao->saveResetPasswordLog($resetPasswordLog));
    }

//    public function testSaveNewPassword_Correct() {
//
////        $user = new SystemUser();
////        $user->setEmpNumber(2);
////        $user->setUserName('testq_user');
////        $user->setUserPassword('old_password');
////        $user->setUserRoleId(999);
////        $user->save();
//        $userService = new SystemUserService();
//
//        $user = $userService->searchSystemUsers(array(
//            'userName' => 'test_user',
//            'offset' => 0,
//            'limit' => 1,
//            'status' => 1
//        ))->getFirst();
//
//        $oldPassword = 'old_password';
//        $this->assertEquals($oldPassword, $user->getUserPassword());
//
//        $newPassword = 'new_password';
//        $this->dao->saveNewPrimaryPassword('test_user', $newPassword);
//
//        $user = $userService->searchSystemUsers(array(
//            'userName' => 'test_user',
//            'offset' => 0,
//            'limit' => 1,
//            'status' => 1
//        ))->getFirst();
//        $this->assertEquals($newPassword, $user->getUserPassword());
//    }
    /**
     * @test testDeletePasswordResetRequestsByEmail
     */
    public function testDeletePasswordResetRequestsByEmail_Correct() {
        $resetPasswordLog = new ResetPasswordLog();
        $resetPasswordLog->setResetEmail('someone@example.com');
        $resetPasswordLog->setResetRequestDate(date('Y-m-d H:i:s'));
        $resetPasswordLog->setResetCode('test_user_1234567890');
        $this->dao->saveResetPasswordLog($resetPasswordLog);
        $this->assertTrue($this->dao->getResetPasswordLogByEmail('someone@example.com') instanceof ResetPasswordLog);
        $this->assertEquals(3, $this->dao->deletePasswordResetRequestsByEmail('someone@example.com'));

    }

}