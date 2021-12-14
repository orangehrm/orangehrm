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
class PasswordResetServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PasswordResetService
     */
    protected $secuirtyAuthService = null;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->secuirtyAuthService = new PasswordResetService();
    }


    /**
     * @test testGetSecurityAuthenticationConfigService().
     */
    public function testGetSecurityAuthenticationConfigService()
    {
        $this->assertTrue($this->secuirtyAuthService->getSecurityAuthenticationConfigService() instanceof SecurityAuthenticationConfigService);

        $securityAuthConfigService = new SecurityAuthenticationConfigService();
        $this->secuirtyAuthService->setSecurityAuthenticationConfigService($securityAuthConfigService);
        $this->assertEquals($securityAuthConfigService, $this->secuirtyAuthService->getSecurityAuthenticationConfigService());
    }

    /**
     * @test testSetSecurityAuthenticationConfigService().
     */
    public function testSetSecurityAuthenticationConfigService()
    {
        $securityAuthConfigService = new SecurityAuthenticationConfigService();
        $this->secuirtyAuthService->setSecurityAuthenticationConfigService($securityAuthConfigService);
        $this->assertEquals($securityAuthConfigService, $this->secuirtyAuthService->getSecurityAuthenticationConfigService());
    }

    public function testGetAuthenticationService()
    {
        $this->assertTrue($this->secuirtyAuthService->getAuthenticationService() instanceof AuthenticationService);

        $authService = new AuthenticationService();
        $this->secuirtyAuthService->setAuthenticationService($authService);
        $this->assertEquals($authService, $this->secuirtyAuthService->getAuthenticationService());
    }

    /**
     * @test testSetAuthenticationService().
     */
    public function testSetAuthenticationService()
    {
        $authService = new AuthenticationService();
        $this->secuirtyAuthService->setAuthenticationService($authService);
        $this->assertEquals($authService, $this->secuirtyAuthService->getAuthenticationService());
    }

    /**
     * @test getSystemUserService().
     */
    public function testGetSystemUserService()
    {
        $this->assertTrue($this->secuirtyAuthService->getSystemUserService() instanceof SystemUserService);

        $systemUserService = new SystemUserService();
        $this->secuirtyAuthService->setSystemUserService($systemUserService);
        $this->assertEquals($systemUserService, $this->secuirtyAuthService->getSystemUserService());
    }

    /**
     * @test setSystemUserService().
     */
    public function testSetUserService()
    {
        $systemUserService = new SystemUserService();
        $this->secuirtyAuthService->setSystemUserService($systemUserService);
        $this->assertEquals($systemUserService, $this->secuirtyAuthService->getSystemUserService());
    }


    /**
     * @test testGetEmployeeService().
     */
    public function testGetEmployeeService()
    {
        $this->assertTrue($this->secuirtyAuthService->getEmployeeService() instanceof EmployeeService);

        $employeeService = new EmployeeService();
        $this->secuirtyAuthService->setEmployeeService($employeeService);
        $this->assertEquals($employeeService, $this->secuirtyAuthService->getEmployeeService());
    }

    /**
     * @test testSetEmployeeService().
     */
    public function testSetEmployeeService()
    {
        $employeeService = new EmployeeService();
        $this->secuirtyAuthService->setEmployeeService($employeeService);
        $this->assertEquals($employeeService, $this->secuirtyAuthService->getEmployeeService());
    }

    /**
     * @test setPasswordResetDao().
     */
    public function testSetSecurityAuthenticationDao()
    {
        $securityAuthenticationDao = new PasswordResetDao();
        $this->secuirtyAuthService->setPasswordResetDao($securityAuthenticationDao);

        $this->assertEquals($securityAuthenticationDao, $this->secuirtyAuthService->getPasswordResetDao());
    }

    /**
     * @test getPasswordResetDao().
     */
    public function testGetSecurityAuthenticationDao()
    {
        $this->assertTrue($this->secuirtyAuthService->getPasswordResetDao() instanceof PasswordResetDao);

        $securityAuthenticationDao = new PasswordResetDao();
        $this->secuirtyAuthService->setPasswordResetDao($securityAuthenticationDao);
        $this->assertEquals($securityAuthenticationDao, $this->secuirtyAuthService->getPasswordResetDao());
    }

    /**
     * @test testGetEmailService().
     */
    public function testGetEmailService()
    {
        $this->assertTrue($this->secuirtyAuthService->getEmailService() instanceof EmailService);

        $emailService = new EmailService();
        $this->secuirtyAuthService->setEmailService($emailService);
        $this->assertEquals($emailService, $this->secuirtyAuthService->getEmailService());
    }

    /**
     * @test testSetEmailService().
     */
    public function testSetEmailService()
    {
        $emailService = new EmailService();
        $this->secuirtyAuthService->setEmailService($emailService);
        $this->assertEquals($emailService, $this->secuirtyAuthService->getEmailService());
    }
    /**
     * @test testGeneratePasswordResetCode().
     */
    public function testGeneratePasswordResetCode()
    {
        $identifier = 'test_username';
        $resetCode = $this->secuirtyAuthService->generatePasswordResetCode($identifier);
        $decodedResetCode = Base64Url::decode($resetCode);

        $this->assertNotNull($resetCode);
        $this->assertRegExp("/[^{$identifier}]/", $resetCode);
        $this->assertRegExp("/^{$identifier}#SEPARATOR#.{8,}/", $decodedResetCode);

        $parts = explode('#SEPARATOR#', $decodedResetCode);
        $this->assertCount(2, $parts);
        $this->assertEquals(PasswordResetService::RESET_PASSWORD_TOKEN_RANDOM_BYTES_LENGTH, strlen($parts[1]));
    }

    /**
     * @test testSaveResetPasswordLog().
     */
    public function testSaveResetPasswordLog_Success()
    {
        $resetPasswordLog = new ResetPassword();
        $resetPasswordLog->setResetEmail(1);
        $securityAuthDao = $this->getMockBuilder('PasswordResetDao')
            ->setMethods(['saveResetPasswordLog'])
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('saveResetPasswordLog')
            ->with($this->equalTo($resetPasswordLog))
            ->will($this->returnValue(true));

        $this->secuirtyAuthService->setPasswordResetDao($securityAuthDao);
        $this->assertTrue($this->secuirtyAuthService->saveResetPasswordLog($resetPasswordLog));
    }

    /**
     * @test testSaveResetPasswordLog().
     * @expectedException ServiceException
     */
    public function testSaveResetPasswordLog_Failure()
    {
        $resetPasswordLog = new ResetPassword();
        $securityAuthDao = $this->getMockBuilder('PasswordResetDao')
            ->setMethods(['saveResetPasswordLog'])
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('saveResetPasswordLog')
            ->with($this->equalTo($resetPasswordLog))
            ->will($this->throwException(new DaoException()));

        $this->secuirtyAuthService->setPasswordResetDao($securityAuthDao);
        $this->secuirtyAuthService->saveResetPasswordLog($resetPasswordLog);
    }

    /**
     * @test testGetResetPasswordLogByEmail().
     */
    public function testGetResetPasswordLogByEmail_Success()
    {
        $email = 'user@example.com';
        $securityAuthDao = $this->getMockBuilder('PasswordResetDao')
            ->setMethods(['getResetPasswordLogByEmail'])
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('getResetPasswordLogByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue(new ResetPassword()));

        $this->secuirtyAuthService->setPasswordResetDao($securityAuthDao);
        $result=$this->secuirtyAuthService->getResetPasswordLogByEmail($email);
        $this->assertEquals($result, $this->secuirtyAuthService->getResetPasswordLogByEmail($email));
    }

    /**
     * @test testHasPasswordResetRequest().
     */
    public function testHasPasswordResetRequest()
    {
        $this->assertFalse($this->secuirtyAuthService->hasPasswordResetRequest('user@example.com'));

        $securityAuthDao = $this->getMockBuilder('PasswordResetDao')
            ->setMethods(['getResetPasswordLogByEmail'])
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('getResetPasswordLogByEmail')
            ->with($this->equalTo('user@example.com'))
            ->will($this->returnValue(new ResetPassword()));

        $securityAuthDao = $this->getMockBuilder('PasswordResetService')
            ->setMethods(['hasPasswordResetRequestNotExpired'])
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('hasPasswordResetRequestNotExpired')
            ->with($this->equalTo('user@example.com'))
            ->will($this->returnValue(true));

        $this->secuirtyAuthService->setPasswordResetDao($securityAuthDao);
        $this->assertEquals(false, $this->secuirtyAuthService->hasPasswordResetRequest('user@example.com'));
    }

    /**
     * @test testHasPasswordResetRequestNotExpired().
     */
    public function testHasPasswordResetRequestNotExpired()
    {
        $resetPasswordLog = new ResetPassword();
        $today = date('Y-m-d H:i:s');
        $fourDaysBefore = date('Y-m-d H:i:s', strtotime('-4 days', time()));
        $threeDaysBefore = date('Y-m-d H:i:s', strtotime('-3 days', time()));
        $threeDaysAfter = date('Y-m-d H:i:s', strtotime('+3 days', time()));

        $resetPasswordLog->setResetRequestDate($fourDaysBefore);
        $this->assertFalse($this->secuirtyAuthService->hasPasswordResetRequestNotExpired($resetPasswordLog));

        $resetPasswordLog->setResetRequestDate($threeDaysBefore);
        $this->assertFalse($this->secuirtyAuthService->hasPasswordResetRequestNotExpired($resetPasswordLog));

        $resetPasswordLog->setResetRequestDate($today);
        $this->assertTrue($this->secuirtyAuthService->hasPasswordResetRequestNotExpired($resetPasswordLog));

        $resetPasswordLog->setResetRequestDate($threeDaysAfter);
        $this->assertTrue($this->secuirtyAuthService->hasPasswordResetRequestNotExpired($resetPasswordLog));
    }

    /**
     * @test testLogPasswordResetRequest().
     */
    public function testLogPasswordResetRequest_Success()
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

        $employee = new Employee();
        $employee->setFirstName('John');
        $employee->setLastName('Smith');
        $employee->setEmpWorkEmail('john.s@example.com');
        $employee->setEmpOthEmail('john@example.com');

        $user = new SystemUser();
        $user->setId(1);
        $user->setUserName('test_username');
        $user->setEmployee($employee);

        $emailServiceMock = $this->getMockBuilder('EmailService')
            ->setMethods(['sendEmail'])
            ->getMock();
        $emailServiceMock->expects($this->once())
            ->method('sendEmail')
            ->will($this->returnValue(true));

        $resetOption = 'example-option';

        $this->secuirtyAuthService->setEmailService($emailServiceMock);
        $this->assertTrue($this->secuirtyAuthService->logPasswordResetRequest($user, $resetOption));
    }

    /**
     * @test testLogPasswordResetRequest().
     * @expectedException ServiceException
     */
    public function testLogPasswordResetRequest_EmailFailure()
    {
        sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

        $employee = new Employee();
        $employee->setFirstName('John');
        $employee->setLastName('Smith');
        $employee->setEmpWorkEmail('john.s@example.com');
        $employee->setEmpOthEmail('john@example.com');

        $user = new SystemUser();
        $user->setId(1);
        $user->setUserName('test_username');
        $user->setEmployee($employee);

        $emailServiceMock = $this->getMockBuilder('EmailService')
            ->setMethods(['sendEmail'])
            ->getMock();
        $emailServiceMock->expects($this->once())
            ->method('sendEmail')
            ->will($this->returnValue(false));

        $resetOption = 'exampleOption';

        $this->secuirtyAuthService->setEmailService($emailServiceMock);
        $this->secuirtyAuthService->logPasswordResetRequest($user, $resetOption);
    }

    /**
     * @test testSendPasswordResetCodeEmail().
     */
    public function testSendPasswordResetCodeEmail()
    {
        $employee = new Employee();
        $employee->setFirstName('John');
        $employee->setLastName('Smith');
        $employee->setEmpWorkEmail('john.s@example.com');
        $employee->setEmpOthEmail('john@example.com');

        $emailServiceMock = $this->getMockBuilder('EmailService')
            ->setMethods(['sendEmail'])
            ->getMock();
        $emailServiceMock->expects($this->once())
            ->method('sendEmail')
            ->will($this->returnValue(true));

        $this->secuirtyAuthService->setEmailService($emailServiceMock);
        $this->assertTrue($this->secuirtyAuthService->sendPasswordResetCodeEmail($employee, 'test_user_1234567890'));
    }

    /**
     * @test testSearchForUserRecord()
     * @expectedException ServiceException
     */
    public function testSearchForUserRecord_UserIsNotAssociatedWithAnEmployee()
    {
        $user = new SystemUser();
        $user->setId(1);
        $user->setUserName('test_username');
        $user->setEmployee(null);

        $userServiceMock = $this->getMockBuilder('UserService')
            ->setMethods(['getUserByUserName'])
            ->getMock();
        $userServiceMock->expects($this->any())
            ->method('getUserByUserName')
            ->with($this->equalTo('test_username'))
            ->will($this->returnValue($user));

        $this->secuirtyAuthService->setSystemUserService($userServiceMock);
        $this->secuirtyAuthService->searchForUserRecord('test_username', 'john.s@example.com');
    }

    /**
     * @test testSearchForUserRecord()
     * @expectedException ServiceException
     */
    public function testSearchForUserRecord_WhoHasAlreadySentAPasswordResetRequest()
    {
        $employee = new Employee();
        $employee->setFirstName('John');
        $employee->setLastName('Smith');

        $user = new SystemUser();
        $user->setId(1);
        $user->setUserName('test_username');
        $user->setEmployee($employee);
        $employee->setEmpWorkEmail('john.s@example.com');
        $employee->setEmpOthEmail('john@example.com');

        $userServiceMock = $this->getMockBuilder('UserService')
            ->setMethods(['getUserByUserName'])
            ->getMock();
        $userServiceMock->expects($this->any())
            ->method('getUserByUserName')
            ->with($this->equalTo('test_username'))
            ->will($this->returnValue($user));

        $securityAuthDao = $this->getMockBuilder('PasswordResetDao')
            ->setMethods(['getResetPasswordLogByEmail'])
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('getResetPasswordLogByEmail')
            ->with($this->equalTo('john.s@example.com'))
            ->will($this->returnValue(new ResetPassword()));

        $this->secuirtyAuthService->setPasswordResetDao($securityAuthDao);
        $this->secuirtyAuthService->setSystemUserService($userServiceMock);
        $this->secuirtyAuthService->searchForUserRecord('test_username', 'john.s@example.com');
    }

    /**
     * @test testSearchForUserRecord()
     * @expectedException ServiceException
     */
    public function testSearchForUserRecord_UnmatchedRecord()
    {
        $userServiceMock = $this->getMockBuilder('UserService')
            ->setMethods(['getUserByUserName'])
            ->getMock();
        $userServiceMock->expects($this->any())
            ->method('getUserByUserName')
            ->with($this->equalTo('test_username'))
            ->will($this->returnValue(null));

        $this->secuirtyAuthService->setSystemUserService($userServiceMock);
        $this->secuirtyAuthService->searchForUserRecord('test_username', 'john.s@example.com');
    }

    /**
     * @test testExtractUsername().
     */
    public function testExtractUsername()
    {
        $identifier = 'testUsername';
        $resetCode = $this->secuirtyAuthService->generatePasswordResetCode($identifier);

        $this->assertEquals([$identifier], $this->secuirtyAuthService->extractPasswordResetMetaData($resetCode));
    }
}
