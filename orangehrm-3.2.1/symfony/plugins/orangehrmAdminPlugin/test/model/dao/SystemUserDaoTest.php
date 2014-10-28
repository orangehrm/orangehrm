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
require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

/**
 * @group Admin
 */
class SystemUserDaoTest extends PHPUnit_Framework_TestCase {
    
        private $systemUserDao;
	protected $fixture;

	/**
	 * Set up method
	 */
	protected function setUp() {

		$this->systemUserDao = new SystemUserDao();
		$this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmAdminPlugin/test/fixtures/SystemUser.yml';
        TestDataService::truncateSpecificTables(array('SystemUser'));
		TestDataService::populate($this->fixture);
	}

	public function testSaveSystemUser() {
                $systemUser =   new SystemUser();
                $systemUser->setUserRoleId(1);
                $systemUser->setEmpNumber(2);
                $systemUser->setUserName('orangehrm');
                $systemUser->setUserPassword('orangehrm');
                
                $this->systemUserDao->saveSystemUser( $systemUser );
                
                $saveUser =   Doctrine :: getTable('SystemUser')->find($systemUser->getId());
		$this->assertEquals( $saveUser->getUserName(),'orangehrm');
                $this->assertEquals( $saveUser->getUserPassword(),'orangehrm');
	}
        
        /**
         * @expectedException DaoException
         */
        public function testSaveSystemUserWithExistingUserName() {
                $systemUser =   new SystemUser();
                $systemUser->setUserRoleId(1);
                $systemUser->setEmpNumber(2);
                $systemUser->setUserName('samantha');
                $systemUser->setUserPassword('orangehrm');
                
                $this->systemUserDao->saveSystemUser( $systemUser );
                 
	}
        
        public function testIsExistingSystemUserForNonEsistingUser(){
            $result = $this->systemUserDao->isExistingSystemUser( 'google' );
            $this->assertFalse( $result);
        }
    
        public function testIsExistingSystemUserForEsistingUser(){
            $result = $this->systemUserDao->isExistingSystemUser( 'Samantha' );
            $this->assertTrue( $result instanceof SystemUser);
        }
        
        public function testGetSystemUser(){
           
                
            $result = $this->systemUserDao->getSystemUser( 1 );
           
            $this->assertEquals( $result->getUserName(),'samantha');
            $this->assertEquals( $result->getUserPassword(),'samantha');
        }
        
        
        public function testGetSystemUserForNonExistingId(){
            $result = $this->systemUserDao->getSystemUser( 100 );
            $this->assertFalse( $result);
        }
        
        public function testGetSystemUsers(){
            $result = $this->systemUserDao->getSystemUsers(  );
            $this->assertEquals(3, count(  $result));
        }
        
        public function testDeleteSystemUsers(){
            $this->systemUserDao->deleteSystemUsers( array(1,2,3));
            $result = $this->systemUserDao->getSystemUsers(  );
            $this->assertEquals(0, count(  $result));
        }
        
        public function testGetAssignableUserRoles(){
            $result = $this->systemUserDao->getAssignableUserRoles();
            $this->assertEquals($result[0]->getName(),'Admin');
            $this->assertEquals($result[1]->getName(),'Admin2');
            $this->assertEquals(7, count(  $result));
        }
        
        public function testGetAdminUserCount() {
            
            $this->assertEquals(1, $this->systemUserDao->getAdminUserCount());
            $this->assertEquals(2, $this->systemUserDao->getAdminUserCount(false));
            $this->assertEquals(2, $this->systemUserDao->getAdminUserCount(true, false));
            $this->assertEquals(3, $this->systemUserDao->getAdminUserCount(false, false));
            
        }
        
        public function testUpdatePassword() {
            
            $this->assertEquals(1, $this->systemUserDao->updatePassword(1, 'samantha2'));
            
            $userObject = TestDataService::fetchObject('SystemUser', 1);
            
            $this->assertEquals('samantha2', $userObject->getUserPassword());
            
        }
        
    public function testGetSystemUserIdList() {
        
        $result = $this->systemUserDao->getSystemUserIdList();
        
        $this->assertEquals(3, count($result));
        $this->assertEquals(array(1, 2, 3), $result);
    }
    
    public function testGetSystemUserIdListForOneActiveUser() {
        
        $q = Doctrine_Query::create()
                             ->update('SystemUser')
                             ->set('deleted', 1)
                             ->whereIn('id', array(2, 3));        
        $q->execute();

        $result = $this->systemUserDao->getSystemUserIdList();
        
        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]);
        
    }
    
    /**
     *
     * @covers SystemUserDao::getNonPredefinedUserRoles
     */
    public function testGetNonPredefinedUserRoles() {
        $userRoleNames = array('Admin2', 'TestAdmin', 'UserRole1', 'UserRole2', 'UserRole3');

        $useRoles = $this->systemUserDao->getNonPredefinedUserRoles();
        $this->assertEquals(count($userRoleNames), count($useRoles));
        for ($i = 0; $i < count($userRoleNames); $i++) {
            $userRole = $useRoles[$i];
            $this->assertTrue($userRole instanceof UserRole);
            $this->assertEquals($userRoleNames[$i], $userRole->getName());
        }
    }
    
    public function testGetEmployeesByUserRole() {
        
        // default
        $employees = $this->systemUserDao->getEmployeesByUserRole('Admin');        
        $this->assertEquals(2, count($employees));
        
        // with terminated employees
        $employees = $this->systemUserDao->getEmployeesByUserRole('Admin', false, true);        
        $this->assertEquals(2, count($employees));

        $employees = $this->systemUserDao->getEmployeesByUserRole('Ess', false, true);        
        $this->assertEquals(1, count($employees));
        
        $employees = $this->systemUserDao->getEmployeesByUserRole('TestAdmin', false, true);        
        $this->assertEquals(0, count($employees));
        
    }
    
    
}

