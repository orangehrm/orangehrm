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
class SecurityAuthenticationServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SecurityAuthenticationService
     */
    protected $secuirtyAuthService;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->secuirtyAuthService = new SecurityAuthenticationService;
    }

    public function testGetState() {
        $this->assertEquals(StateAccessibleByExecutionFilters::EMPTY_STATE, $this->secuirtyAuthService->getState());
    }

    /**
     * @test testGetSecurityAuthenticationConfigService().
     */
    public function testGetSecurityAuthenticationConfigService() {
        $this->assertTrue($this->secuirtyAuthService->getSecurityAuthenticationConfigService() instanceof SecurityAuthenticationConfigService);

        $securityAuthConfigService = new SecurityAuthenticationConfigService();
        $this->secuirtyAuthService->setSecurityAuthenticationConfigService($securityAuthConfigService);
        $this->assertEquals($securityAuthConfigService, $this->secuirtyAuthService->getSecurityAuthenticationConfigService());
    }

    /**
     * @test testSetSecurityAuthenticationConfigService().
     */
    public function testSetSecurityAuthenticationConfigService() {
        $securityAuthConfigService = new SecurityAuthenticationConfigService();
        $this->secuirtyAuthService->setSecurityAuthenticationConfigService($securityAuthConfigService);
        $this->assertEquals($securityAuthConfigService, $this->secuirtyAuthService->getSecurityAuthenticationConfigService());
    }

    public function testGetAuthenticationService() {
        $this->assertTrue($this->secuirtyAuthService->getAuthenticationService() instanceof AuthenticationService);

        $authService = new AuthenticationService();
        $this->secuirtyAuthService->setAuthenticationService($authService);
        $this->assertEquals($authService, $this->secuirtyAuthService->getAuthenticationService());
    }

    /**
     * @test testSetAuthenticationService().
     */
    public function testSetAuthenticationService() {
        $authService = new AuthenticationService();
        $this->secuirtyAuthService->setAuthenticationService($authService);
        $this->assertEquals($authService, $this->secuirtyAuthService->getAuthenticationService());
    }

    /**
     * @test getSystemUserService().
     */
    public function testGetSystemUserService() {
        $this->assertTrue($this->secuirtyAuthService->getSystemUserService() instanceof SystemUserService);

        $systemUserService = new SystemUserService();
        $this->secuirtyAuthService->setSystemUserService($systemUserService);
        $this->assertEquals($systemUserService, $this->secuirtyAuthService->getSystemUserService());
    }

    /**
     * @test setSystemUserService().
     */
    public function testSetUserService() {
        $systemUserService = new SystemUserService();
        $this->secuirtyAuthService->setSystemUserService($systemUserService);
        $this->assertEquals($systemUserService, $this->secuirtyAuthService->getSystemUserService());
    }


    /**
     * @test testGetEmployeeService().
     */
    public function testGetEmployeeService() {
        $this->assertTrue($this->secuirtyAuthService->getEmployeeService() instanceof EmployeeService);

        $employeeService = new EmployeeService();
        $this->secuirtyAuthService->setEmployeeService($employeeService);
        $this->assertEquals($employeeService, $this->secuirtyAuthService->getEmployeeService());
    }

    /**
     * @test testSetEmployeeService().
     */
    public function testSetEmployeeService() {
        $employeeService = new EmployeeService();
        $this->secuirtyAuthService->setEmployeeService($employeeService);
        $this->assertEquals($employeeService, $this->secuirtyAuthService->getEmployeeService());
    }

    /**
     * @test setSecurityAuthenticationDao().
     */
    public function testSetSecurityAuthenticationDao() {
        $securityAuthenticationDao = new SecurityAuthenticationDao();
        $this->secuirtyAuthService->setSecurityAuthenticationDao($securityAuthenticationDao);

        $this->assertEquals($securityAuthenticationDao, $this->secuirtyAuthService->getSecurityAuthenticationDao());
    }

    /**
     * @test getSecurityAuthenticationDao().
     */
    public function testGetSecurityAuthenticationDao() {
        $this->assertTrue($this->secuirtyAuthService->getSecurityAuthenticationDao() instanceof SecurityAuthenticationDao);

        $securityAuthenticationDao = new SecurityAuthenticationDao();
        $this->secuirtyAuthService->setSecurityAuthenticationDao($securityAuthenticationDao);
        $this->assertEquals($securityAuthenticationDao, $this->secuirtyAuthService->getSecurityAuthenticationDao());
    }

    /**
     * @test testGetEmailService().
     */
    public function testGetEmailService() {
        $this->assertTrue($this->secuirtyAuthService->getEmailService() instanceof EmailService);

        $emailService = new EmailService();
        $this->secuirtyAuthService->setEmailService($emailService);
        $this->assertEquals($emailService, $this->secuirtyAuthService->getEmailService());
    }

    /**
     * @test testSetEmailService().
     */
    public function testSetEmailService() {
        $emailService = new EmailService();
        $this->secuirtyAuthService->setEmailService($emailService);
        $this->assertEquals($emailService, $this->secuirtyAuthService->getEmailService());
    }
    /**
     * @test testGeneratePasswordResetCode().
     */
    public function testGeneratePasswordResetCode() {
        $identfier = 'test_username';
        $resetCode = $this->secuirtyAuthService->generatePasswordResetCode($identfier);
        $decodedResetCode = base64_decode($resetCode);

        $this->assertNotNull($resetCode);
        $this->assertRegExp("/[^{$identfier}]/", $resetCode);
        $this->assertRegExp("/^{$identfier}#SEPARATOR#.{8,}/", $decodedResetCode);
    }

    /**
     * @test testSaveResetPasswordLog().
     */
    public function testSaveResetPasswordLog_Success() {
        $resetPasswordLog = new ResetPasswordLog();
        $resetPasswordLog->setResetEmail(1);
        $securityAuthDao = $this->getMockBuilder('SecurityAuthenticationDao')
            ->setMethods( array('saveResetPasswordLog'))
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('saveResetPasswordLog')
            ->with($this->equalTo($resetPasswordLog))
            ->will($this->returnValue(true));

        $this->secuirtyAuthService->setSecurityAuthenticationDao($securityAuthDao);
        $this->assertTrue($this->secuirtyAuthService->saveResetPasswordLog($resetPasswordLog));
    }

    /**
     * @test testSaveResetPasswordLog().
     * @expectedException ServiceException
     */
    public function testSaveResetPasswordLog_Failure() {
        $resetPasswordLog = new ResetPasswordLog();
        $securityAuthDao = $this->getMockBuilder('SecurityAuthenticationDao')
            ->setMethods( array('saveResetPasswordLog'))
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('saveResetPasswordLog')
            ->with($this->equalTo($resetPasswordLog))
            ->will($this->throwException(new DaoException()));

        $this->secuirtyAuthService->setSecurityAuthenticationDao($securityAuthDao);
        $this->secuirtyAuthService->saveResetPasswordLog($resetPasswordLog);
    }

    /**
     * @test testGetResetPasswordLogByEmail().
     */
    public function testGetResetPasswordLogByEmail_Success() {
        $email = 'user@example.com';
        $securityAuthDao = $this->getMockBuilder('SecurityAuthenticationDao')
            ->setMethods( array('getResetPasswordLogByEmail'))
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('getResetPasswordLogByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue(new ResetPasswordLog()));

        $this->secuirtyAuthService->setSecurityAuthenticationDao($securityAuthDao);
        $result=$this->secuirtyAuthService->getResetPasswordLogByEmail($email);
        $this->assertEquals($result,$this->secuirtyAuthService->getResetPasswordLogByEmail($email));
    }

//    /**
//     * @test getResetPasswordLogByEmail().
//     * @expectedException ServiceException
//     */
//    public function testGetResetPasswordLogByEmail_Failure() {
//        $email = 'user123@example.com';
//        $securityAuthDao = $this->getMockBuilder('SecurityAuthenticationDao')
//            ->setMethods( array('getResetPasswordLogByEmail'))
//            ->getMock();
//        $securityAuthDao->expects($this->once())
//            ->method('getResetPasswordLogByEmail')
//            ->with($this->equalTo($email))
//            ->will($this->throwException(new DaoException()));
//
//        $this->secuirtyAuthService->setSecurityAuthenticationDao($securityAuthDao);
//        $this->secuirtyAuthService->getResetPasswordLogByEmail($email);
//    }
    /**
     * @test testHasPasswordResetRequest().
     */
    public function testHasPasswordResetRequest() {
        $this->assertFalse($this->secuirtyAuthService->hasPasswordResetRequest('user@example.com'));

        $securityAuthDao = $this->getMockBuilder('SecurityAuthenticationDao')
            ->setMethods( array('getResetPasswordLogByEmail'))
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('getResetPasswordLogByEmail')
            ->with($this->equalTo('user@example.com'))
            ->will($this->returnValue(new ResetPasswordLog));

        $securityAuthDao = $this->getMockBuilder('SecurityAuthenticationService')
            ->setMethods( array('hasPasswordResetRequestNotExpired'))
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('hasPasswordResetRequestNotExpired')
            ->with($this->equalTo('user@example.com'))
            ->will($this->returnValue(true));

        $this->secuirtyAuthService->setSecurityAuthenticationDao($securityAuthDao);
        $this->assertEquals(false,$this->secuirtyAuthService->hasPasswordResetRequest('user@example.com'));
       // $this->assertTrue($this->secuirtyAuthService->hasPasswordResetRequest('user@example.com'));
    }

    /**
     * @test testHasPasswordResetRequestNotExpired().
     */
    public function testHasPasswordResetRequestNotExpired() {
        $resetPasswordLog = new ResetPasswordLog();
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
    public function testLogPasswordResetRequest_Success() {
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
            ->setMethods( array('sendEmail'))
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
    public function testLogPasswordResetRequest_EmailFailure() {
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
            ->setMethods( array('sendEmail'))
            ->getMock();
        $emailServiceMock->expects($this->once())
            ->method('sendEmail')
            ->will($this->returnValue(false));

        $resetOption = 'exampleOption';

        $this->secuirtyAuthService->setEmailService($emailServiceMock);
        $this->secuirtyAuthService->logPasswordResetRequest($user, $resetOption);
    }

    /**
     * @todo Implement testSendPasswordResetCodeEmail().
     */
    public function testSendPasswordResetCodeEmail() {
        $employee = new Employee();
        $employee->setFirstName('John');
        $employee->setLastName('Smith');
        $employee->setEmpWorkEmail('john.s@example.com');
        $employee->setEmpOthEmail('john@example.com');

        $emailServiceMock = $this->getMockBuilder('EmailService')
            ->setMethods( array('sendEmail'))
            ->getMock();
        $emailServiceMock->expects($this->once())
            ->method('sendEmail')
            ->will($this->returnValue(true));

        $this->secuirtyAuthService->setEmailService($emailServiceMock);
        $this->assertTrue($this->secuirtyAuthService->sendPasswordResetCodeEmail($employee, 'test_user_1234567890'));
    }


//    /**
//     * @test testSearchForUserRecord()
//     */
//    public function testSearchForUserRecord_MatchByUsername() {
//        $employee = new Employee();
//        $employee->setEmpNumber(1);
//        $employee->setFirstName('John');
//        $employee->setLastName('Smith');
//        $employee->setEmpWorkEmail('john.s@example.com');
//        $employee->setEmpOthEmail('john@example.com');
//
//        $user = new SystemUser();
//        //$user->setId(1);
//        $user->setUserName('test_username');
//        $user->setEmployee($employee);
//
//        $userCollection = new Doctrine_Collection('SystemUser');
//        $userCollection->add($user);
//
//        $userServiceMock = $this->getMockBuilder('SystemUserService')
//            ->setMethods( array('searchSystemUsers'))
//            ->getMock();
//        $userServiceMock->expects($this->once())
//            ->method('searchSystemUsers')
//            ->will($this->returnValue($userCollection));
//
//        $securityAuthDaoMock = $this->getMockBuilder('SecurityAuthenticationDao')
//            ->setMethods( array('getResetPasswordLogByEmail'))
//            ->getMock();
//        $securityAuthDaoMock->expects($this->once())
//            ->method('getResetPasswordLogByEmail')
//            ->with($this->equalTo('john.s@example.com'))
//            ->will($this->returnValue(null));
//
//        $this->secuirtyAuthService->setSystemUserService($userServiceMock);
//        $this->secuirtyAuthService->setSecurityAuthenticationDao($securityAuthDaoMock);
//        list($returnedUser, $matchedByField, $matchedValue) = $this->secuirtyAuthService->searchForUserRecord('test_username', 'john.s@example.com');
//        $this->assertTrue($returnedUser instanceof SystemUser);
//        $this->assertEquals('Username', $matchedByField);
//        $this->assertEquals('test_username', $matchedValue);
//    }

    /**
     * @test testSearchForUserRecord()
     * @expectedException ServiceException
     */
    public function testSearchForUserRecord_UserIsNotAssociatedWithAnEmployee() {
        $user = new SystemUser();
        $user->setId(1);
        $user->setUserName('test_username');
        $user->setEmployee(null);

        $userServiceMock = $this->getMockBuilder('UserService')
            ->setMethods( array('getUserByUserName'))
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
    public function testSearchForUserRecord_WhoHasAlreadySentAPasswordResetRequest() {
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
            ->setMethods( array('getUserByUserName'))
            ->getMock();
        $userServiceMock->expects($this->any())
            ->method('getUserByUserName')
            ->with($this->equalTo('test_username'))
            ->will($this->returnValue($user));

        $securityAuthDao = $this->getMockBuilder('SecurityAuthenticationDao')
            ->setMethods( array('getResetPasswordLogByEmail'))
            ->getMock();
        $securityAuthDao->expects($this->any())
            ->method('getResetPasswordLogByEmail')
            ->with($this->equalTo('john.s@example.com'))
            ->will($this->returnValue(new ResetPasswordLog));

        $this->secuirtyAuthService->setSecurityAuthenticationDao($securityAuthDao);
        $this->secuirtyAuthService->setSystemUserService($userServiceMock);
        $this->secuirtyAuthService->searchForUserRecord('test_username', 'john.s@example.com');
    }

    /**
     * @test testSearchForUserRecord()
     * @expectedException ServiceException
     */
    public function testSearchForUserRecord_UnmatchedRecord() {

        $userServiceMock = $this->getMockBuilder('UserService')
            ->setMethods( array('getUserByUserName'))
            ->getMock();
        $userServiceMock->expects($this->any())
            ->method('getUserByUserName')
            ->with($this->equalTo('test_username'))
            ->will($this->returnValue(null));

        $this->secuirtyAuthService->setSystemUserService($userServiceMock);
        $this->secuirtyAuthService->searchForUserRecord('test_username', 'john.s@example.com');
    }

//    public function testCheckPasswordExpirationNullExpirationDate() {
//        $userName = 'johnm1';
//
//        $mockRequest = $this->getMockBuilder('MockUnitTestRequest')
//            ->setMethods(array('getParameter'))
//            ->getMock();
//        $mockRequest->expects($this->once())
//            ->method('getParameter')
//            ->with('txtUsername')
//            ->will($this->returnValue($userName));
//
//        $mockUser = $this->getMockBuilder('SystemUser')
//            ->setMethods(array('getTextStatus'))
//            ->getMock();
//
//        $mockService = $this->getMockBuilder('SystemUserService')
//            ->setMethods( array('fetchSystemUserByUsername', 'isPasswordExpired'))
//            ->getMock();
//        $mockService->expects($this->once())
//            ->method('fetchSystemUserByUsername')
//            ->with($userName)
//            ->will($this->returnValue($mockUser));
//        $mockService->expects($this->once())
//            ->method('isPasswordExpired')
//            ->with($userName)
//            ->will($this->returnValue(false));
//
//        $this->secuirtyAuthService->setSystemUserService($mockService);
//        $result = $this->secuirtyAuthService->checkPasswordExpiration($mockRequest);
//
//        $this->assertNull($result);
//    }

//    public function testCheckPasswordExpirationNotExpired() {
//        $userName = 'johnm1';
//
//        $mockRequest = $this->getMockBuilder('MockUnitTestRequest')
//            ->setMethods(array('getParameter'))
//            ->getMock();
//        $mockRequest->expects($this->once())
//            ->method('getParameter')
//            ->with('txtUsername')
//            ->will($this->returnValue($userName));
//
//        $mockUser = $this->getMockBuilder('SystemUser')
//            ->setMethods(array('getTextStatus'))
//            ->getMock();
//
//        $mockService = $this->getMockBuilder('SystemUserService')
//            ->setMethods( array('fetchSystemUserByUsername', 'isPasswordExpired'))
//            ->getMock();
//        $mockService->expects($this->once())
//            ->method('fetchSystemUserByUsername')
//            ->with($userName)
//            ->will($this->returnValue($mockUser));
//        $mockService->expects($this->once())
//            ->method('isPasswordExpired')
//            ->with($userName)
//            ->will($this->returnValue(false));
//
//        $this->secuirtyAuthService->setSystemUserService($mockService);
//        $result = $this->secuirtyAuthService->checkPasswordExpiration($mockRequest);
//
//        $this->assertNull($result);
//    }

//    public function testCheckPasswordExpirationExpired() {
//        $userName = 'johnm1';
//
//        $mockRequest = $this->getMockBuilder('MockUnitTestRequest')
//            ->setMethods(array('getParameter'))
//            ->getMock();
//        $mockRequest->expects($this->once())
//            ->method('getParameter')
//            ->with('txtUsername')
//            ->will($this->returnValue($userName));
//
//        $mockUser = $this->getMockBuilder('SystemUser')
//            ->setMethods(array('getTextStatus'))
//            ->getMock();
//
//
//        $mockService = $this->getMockBuilder('SystemUserService')
//            ->setMethods( array('fetchSystemUserByUsername', 'isPasswordExpired'))
//            ->getMock();
//        $mockService->expects($this->once())
//            ->method('fetchSystemUserByUsername')
//            ->with($userName)
//            ->will($this->returnValue($mockUser));
//        $mockService->expects($this->once())
//            ->method('isPasswordExpired')
//            ->with($userName)
//            ->will($this->returnValue(true));
//
//        $mockAuthenticationService = $this->getMockBuilder('AuthenticationService')
//            ->setMethods( array('setCredentialsForUser'))
//            ->getMock();
//        $mockAuthenticationService->expects($this->once())
//            ->method('setCredentialsForUser')
//            ->with($mockUser, array());
//
//        $this->secuirtyAuthService->setSystemUserService($mockService);
//        $this->secuirtyAuthService->setAuthenticationService($mockAuthenticationService);
//
//        $result = $this->secuirtyAuthService->checkPasswordExpiration($mockRequest);
//
//        $this->assertEquals('admin/changeUserPassword', $result);
//    }

    /**
     * @test testExtractUsername().
     */
    public function testExtractUsername() {
        $identfier = 'testUsername';
        $seperator= 'SEPARATOR';
        $resetCode = $this->secuirtyAuthService->generatePasswordResetCode($identfier);

        $this->assertEquals(array($identfier,$seperator), $this->secuirtyAuthService->extractPasswordResetMetaData($resetCode));
    }

}