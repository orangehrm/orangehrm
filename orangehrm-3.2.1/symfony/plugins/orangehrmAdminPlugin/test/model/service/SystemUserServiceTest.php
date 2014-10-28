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
 * Description of SystemUserServiceTest
 *
 */
class SystemUserServiceTest extends PHPUnit_Framework_TestCase {
    
    /** @property SystemUserService $systemUserService */
    private $systemUserService;
    
    /**
     * Set up method
     */
    protected function setUp() {
        $this->systemUserService = new SystemUserService();
    }
    
    /**
     * @covers SystemUserService::getNonPredefinedUserRoles
     */  
    public function testGetNonPredefinedUserRoles() {
        $userRoles = new Doctrine_Collection('UserRole');

        for ($i = 0; $i < 2; $i++) {
            $userRole = new UserRole();
            $userRole->setId($i+1);
            $userRole->setName("test name".$i+1);
            $userRole->setIsAssignable(1);
            $userRole->setIsPredefined(0);
            $userRoles->add($userRole);
        }
        
        $dao = $this->getMock('SystemUserDao');
        
        $dao->expects($this->once())
             ->method('getNonPredefinedUserRoles')
             ->will($this->returnValue($userRoles));
        
        $this->systemUserService->setSystemUserDao($dao);
        $result = $this->systemUserService->getNonPredefinedUserRoles();
        
         $this->assertEquals($userRoles, $result);
    }
    
    public function testSaveSystemUserNoPasswordChange() {
        
        $password = 'y28#$!!';
        
        $user = new SystemUser();
        $user->setId(1);
        $user->setUserRoleId(1);
        $user->setUserName('admin_user');
        $user->setUserPassword($password);                
        
        $dao = $this->getMock('SystemUserDao');
        
        $dao->expects($this->once())
             ->method('saveSystemUser')
             ->will($this->returnArgument(0));      
        
        $this->systemUserService->setSystemUserDao($dao);
        $result = $this->systemUserService->saveSystemUser($user);
        
        $this->assertEquals($user, $result);
    }
    
    public function testSaveSystemUserWithPasswordChange() {
        $password = 'y28#$!!';

        $user = new SystemUser();
        $user->setId(1);
        $user->setUserRoleId(1);
        $user->setUserName('admin_user');
        $user->setUserPassword($password);

        $dao = $this->getMock('SystemUserDao');

        $dao->expects($this->once())
                ->method('saveSystemUser')
                ->will($this->returnArgument(0));

        $this->systemUserService->setSystemUserDao($dao);
        
        $result = $this->systemUserService->saveSystemUser($user, true);
        
        // check password is hashed before saving
        $this->assertNotEquals($password, $result->getUserPassword());
        
        // verify correct hash
        $hashedPassword = $result->getUserPassword();
        $hasher = new PasswordHash(12, false);
        $this->assertTrue($hasher->CheckPassword($password, $hashedPassword));
    }
    
    public function testIsCurrentPasswordUserNotFound() {
        
        $userId = 5;
        $password = 'sadffas';
        
        $dao = $this->getMock('SystemUserDao', array('getSystemUser'));

        $dao->expects($this->once())
                ->method('getSystemUser')
                ->will($this->returnValue(false));

        $this->systemUserService->setSystemUserDao($dao);
        
        $result = $this->systemUserService->isCurrentPassword($userId, $password);
        $this->assertFalse($result);
    }
    
    public function testIsCurrentPasswordTrue() {
        $userId = 5;
        $password = 'y28#$!!';
        
        $hasher = new PasswordHash(12, false);
        $hashedPassword = $hasher->HashPassword($password);
        
        $user = new SystemUser();
        $user->setId($userId);
        $user->setUserRoleId(1);
        $user->setUserName('admin_user');
        $user->setUserPassword($hashedPassword);

        
        $dao = $this->getMock('SystemUserDao', array('getSystemUser'));

        $dao->expects($this->once())
                ->method('getSystemUser')
                ->will($this->returnValue($user));

        $this->systemUserService->setSystemUserDao($dao);
        
        $result = $this->systemUserService->isCurrentPassword($userId, $password);
        $this->assertTrue($result);        
    }    
    
    public function testIsCurrentPasswordOldHash() {
        $userId = 5;
        $password = 'y28#$!!';
        
        $hashedPassword = md5($password);
        
        $user = new SystemUser();
        $user->setId($userId);
        $user->setUserRoleId(1);
        $user->setUserName('admin_user');
        $user->setUserPassword($hashedPassword);

        
        $dao = $this->getMock('SystemUserDao', array('getSystemUser'));

        $dao->expects($this->once())
                ->method('getSystemUser')
                ->will($this->returnValue($user));

        $this->systemUserService->setSystemUserDao($dao);
        
        $result = $this->systemUserService->isCurrentPassword($userId, $password);
        $this->assertTrue($result);          
    }          
    
    public function testIsCurrentPasswordFalse() {
        $userId = 5;
        $password = 'y28#$!!';
        
        $hashedPassword = 'asdfasfda';
        
        $user = new SystemUser();
        $user->setId($userId);
        $user->setUserRoleId(1);
        $user->setUserName('admin_user');
        $user->setUserPassword($hashedPassword);

        
        $dao = $this->getMock('SystemUserDao', array('getSystemUser'));

        $dao->expects($this->once())
                ->method('getSystemUser')
                ->will($this->returnValue($user));

        $this->systemUserService->setSystemUserDao($dao);
        
        $result = $this->systemUserService->isCurrentPassword($userId, $password);
        $this->assertFalse($result);         
    }
    
    public function testUpdatePassword() {
        $userId = 3;
        $password = 'sadf&^#@!';
        $hashedPassword = '939adfiasdfasdfas';
        
        $mockHasher = $this->getMockBuilder('PasswordHash')
                     ->disableOriginalConstructor()
                     ->setMethods(array('HashPassword'))
                     ->getMock();
        $mockHasher->expects($this->once())
                ->method('HashPassword')
                ->with($password)
                ->will($this->returnValue($hashedPassword));
        
        $mockDao = $this->getMock('SystemUserDao', array('updatePassword'));
        $mockDao->expects($this->once())
                ->method('updatePassword')
                ->with($userId, $hashedPassword)
                ->will($this->returnValue(true));

        $this->systemUserService->setSystemUserDao($mockDao);
        $this->systemUserService->setPasswordHasher($mockHasher);
        $result = $this->systemUserService->updatePassword($userId, $password);
        
        $this->assertTrue($result);
    }
    
    
    public function testGetCredentialsUserNotFound() {
        $userName = 'adminUser1';
        $password = 'isd#@!';
        
        $mockDao = $this->getMock('SystemUserDao', array('isExistingSystemUser'));
        $mockDao->expects($this->once())
                ->method('isExistingSystemUser')
                ->with($userName)
                ->will($this->returnValue(false));        
        $this->systemUserService->setSystemUserDao($mockDao);
        $result = $this->systemUserService->getCredentials($userName, $password);
        
        $this->assertFalse($result);        
    }
    
    public function testGetCredentialsValidPassword() {
        $userId = 3838;
        $userName = 'adminUser1';
        $password = 'isd#@!';
        
        $hashedPassword = 'asdfasfda';
        
        $user = new SystemUser();
        $user->setId($userId);
        $user->setUserRoleId(1);
        $user->setUserName($userName);
        $user->setUserPassword($hashedPassword);        
        
        $mockHasher = $this->getMockBuilder('PasswordHash')
                     ->disableOriginalConstructor()
                     ->setMethods(array('CheckPassword'))
                     ->getMock();
        $mockHasher->expects($this->once())
                ->method('CheckPassword')
                ->with($password, $hashedPassword)
                ->will($this->returnValue(true));        
        
        $mockDao = $this->getMock('SystemUserDao', array('isExistingSystemUser'));
        $mockDao->expects($this->once())
                ->method('isExistingSystemUser')
                ->with($userName)
                ->will($this->returnValue($user));        
        
        $this->systemUserService->setSystemUserDao($mockDao);
        $this->systemUserService->setPasswordHasher($mockHasher);
        $result = $this->systemUserService->getCredentials($userName, $password);

        $this->assertTrue($result instanceof SystemUser);
        $this->assertEquals($user, $result);
    }
    
    public function testGetCredentialsOldHash() {
        $userId = 3838;
        $userName = 'adminUser1';
        $password = 'isd#@!';
        
        $hashedPassword = md5($password);
        
        $user = new SystemUser();
        $user->setId($userId);
        $user->setUserRoleId(1);
        $user->setUserName($userName);
        $user->setUserPassword($hashedPassword);        
      
        
        $mockDao = $this->getMock('SystemUserDao', array('isExistingSystemUser', 'saveSystemUser'));
        $mockDao->expects($this->once())
                ->method('isExistingSystemUser')
                ->with($userName)
                ->will($this->returnValue($user));        
        
        $mockDao->expects($this->once())
                ->method('saveSystemUser')
                ->will($this->returnArgument(0));  
        
        $this->systemUserService->setSystemUserDao($mockDao);
        $result = $this->systemUserService->getCredentials($userName, $password);
        
        $this->assertTrue($result instanceof SystemUser);
        
        // Check password correctly hashed
        $hasher = new PasswordHash(12, false);        
        $this->assertTrue($hasher->CheckPassword($password, $result->getUserPassword()));        
    }
    
    public function testGetCredentialsInvalidPassword() {
        $userId = 3838;
        $userName = 'adminUser1';
        $password = 'isd#@!';
        
        $hashedPassword = 'asdfasfe##';
        
        $user = new SystemUser();
        $user->setId($userId);
        $user->setUserRoleId(1);
        $user->setUserName($userName);
        $user->setUserPassword($hashedPassword);        
      
        
        $mockDao = $this->getMock('SystemUserDao', array('isExistingSystemUser'));
        $mockDao->expects($this->once())
                ->method('isExistingSystemUser')
                ->with($userName)
                ->will($this->returnValue($user));        
        
        $this->systemUserService->setSystemUserDao($mockDao);
        $result = $this->systemUserService->getCredentials($userName, $password);
        
        $this->assertFalse($result);        
    }   
    
    public function testHashPassword() {
        $password = 'sadf&^#@!';
        $hashedPassword = '939adfiasdfasdfas';
        
        $mockHasher = $this->getMockBuilder('PasswordHash')
                     ->disableOriginalConstructor()
                     ->setMethods(array('HashPassword'))
                     ->getMock();
        $mockHasher->expects($this->once())
                ->method('HashPassword')
                ->with($password)
                ->will($this->returnValue($hashedPassword));
        
        $this->systemUserService->setPasswordHasher($mockHasher);
        $result = $this->systemUserService->hashPassword($password);  
        $this->assertEquals($hashedPassword, $result);
    }
    
    public function testCheckPasswordHashValid() {
        
    }
    
    public function testCheckPasswordHashInvalid() {
        
    }
    
    public function testForOldHashValid() {
        
    }
    
    public function testForOldHashInvalid() {
        
    }
}

