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
 * Description of AbstractUserRoleManagerTest
 *
 */
class BasicUserRoleManagerTest extends PHPUnit_Framework_TestCase {
    
    /** @property BasicUserRoleManager $service */
    private $manager;
    
    /**
     * Set up method
     */
    protected function setUp() {        
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/BasicUserRoleManager.yml';
        TestDataService::truncateSpecificTables(array('SystemUser', 'Project', 'JobCandidate', 'JobVacancy', 'JobInterview'));
        TestDataService::populate($this->fixture);
                
        $this->manager = new BasicUserRoleManager();
    }
    
//    public function testGetAccessibleEmployeeIdsExcludeIncludeRoles() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        
//        // Default Admin user  (no employee)
//        $defaultAdmin = $users[5];
//        $this->manager->setUser($defaultAdmin);
//        $result = $this->manager->getAccessibleEntityIds('Employee');
//        $expected = $this->getEmployeeIds($allEmployees);
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);        
//        
//        // Exclude Supervisor Role
//        $result = $this->manager->getAccessibleEntityIds('Employee', null, null, array('Supervisor'));
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);       
//        
//        // Include Admin Role
//        $result = $this->manager->getAccessibleEntityIds('Employee', null, null, array(), array('Admin'));
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);  
//        
//        // Exclude Admin Role
//        $result = $this->manager->getAccessibleEntityIds('Employee', null, null, array('Admin'));
//        $this->assertEquals(0, count($result));       
//               
//        // Admin + supervisor
//        $adminSupervisor = $users[3];
//        $this->manager->setUser($adminSupervisor);
//        $result = $this->manager->getAccessibleEntityIds('Employee');
//        $expected = $this->getEmployeeIds($allEmployees);
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);   
//        
//        // Exclude supervisor role
//        $result = $this->manager->getAccessibleEntityIds('Employee', null, null, array('Supervisor'));
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result); 
//        
//        // Exclude Admin role
//        $result = $this->manager->getAccessibleEntityIds('Employee', null, null, array('Admin'));
//        $expected = array($allEmployees[2]->getEmpNumber());
//        $this->assertEquals(count($expected), count($result));
//        
//        $this->compareArrays($expected, $result);         
//    }
    
    public function testAreEmployeesAccessibleAdmin() {
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $allIds = $this->getEmployeeIds($allEmployees);
        
        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        $this->assertTrue($this->manager->areEntitiesAccessible('Employee', $allIds));
        
        // test with unavailable emp number
        $empIds = array_merge($allIds, array(11));
        
        $this->assertFalse($this->manager->areEntitiesAccessible('Employee', $empIds));
        
        // Admin user 
        $admin = $users[0];
        $this->manager->setUser($admin);
        $this->assertTrue($this->manager->areEntitiesAccessible('Employee', $allIds));
        
        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        $this->assertTrue($this->manager->areEntitiesAccessible('Employee', $allIds));
    }

//    public function testAreEmployeesAccessibleSupervisor() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        $allIds = $this->getEmployeeIds($allEmployees);
//        
//        // Supervisor with one subordinate
//        $supervisor = $users[1];
//        $this->manager->setUser($supervisor);
//        $expected = array($allEmployees[2]->getEmpNumber());
//        $this->assertTrue($this->manager->areEntitiesAccessible('Employee', $expected));
//        $this->assertFalse($this->manager->areEntitiesAccessible('Employee', $allIds));
//        $notAccessible = array_diff($allIds, $expected);
//        $this->assertFalse($this->manager->areEntitiesAccessible('Employee', $notAccessible));
//        $mixed = array_merge($notAccessible, $expected);
//        $this->assertFalse($this->manager->areEntitiesAccessible('Employee', $mixed));
//        
//        // Supervisor with multiple subordinates
//        $supervisor = $users[6];
//        $this->manager->setUser($supervisor);
//        $expected = array($allEmployees[0]->getEmpNumber(), $allEmployees[2]->getEmpNumber(), 
//                          $allEmployees[3]->getEmpNumber(), $allEmployees[4]->getEmpNumber());
//        $this->assertTrue($this->manager->areEntitiesAccessible('Employee', $expected));
//        $this->assertFalse($this->manager->areEntitiesAccessible('Employee', $allIds));
//        $notAccessible = array_diff($allIds, $expected);
//        $this->assertFalse($this->manager->areEntitiesAccessible('Employee', $notAccessible));
//        $mixed = array_merge($notAccessible, $expected);
//        $this->assertFalse($this->manager->areEntitiesAccessible('Employee', $mixed));
//    }
//    
//    public function testAreEmployeesAccessibleESS() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        $allIds = $this->getEmployeeIds($allEmployees);
//        
//        // ESS user
//        $essUser = $users[4];
//        $this->manager->setUser($essUser);
//        $this->assertFalse($this->manager->areEntitiesAccessible('Employee', $allIds));        
//        foreach ($allIds as $id) {
//            $this->assertFalse($this->manager->areEntitiesAccessible('Employee', array($id)));
//        }      
//    }    
    
    public function testIsEmployeeAccessibleAdmin() {
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $allIds = $this->getEmployeeIds($allEmployees);
        
        // Default Admin user  (no employee)
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        foreach ($allIds as $id) {
            $this->assertTrue($this->manager->isEntityAccessible('Employee', $id));
        }
        
        // Admin user 
        $admin = $users[0];
        $this->manager->setUser($admin);
        foreach ($allIds as $id) {
            $this->assertTrue($this->manager->isEntityAccessible('Employee', $id));
        }
        
        // Admin + supervisor
        $adminSupervisor = $users[3];
        $this->manager->setUser($adminSupervisor);
        foreach ($allIds as $id) {
            $this->assertTrue($this->manager->isEntityAccessible('Employee', $id));
        }
    }

//    public function testIsEmployeeAccessibleSupervisor() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        $allIds = $this->getEmployeeIds($allEmployees);
//        
//        // Supervisor with one subordinate
//        $supervisor = $users[1];
//        $this->manager->setUser($supervisor);
//        $expected = array($allEmployees[2]->getEmpNumber());
//        foreach ($allIds as $id) {
//            if (in_array($id, $expected)) {
//                $this->assertTrue($this->manager->isEntityAccessible('Employee', $id));
//            } else {
//                $this->assertFalse($this->manager->isEntityAccessible('Employee', $id));
//            }
//        }
//        
//        // Supervisor with multiple subordinates
//        $supervisor = $users[6];
//        $this->manager->setUser($supervisor);
//        $expected = array($allEmployees[0]->getEmpNumber(), $allEmployees[2]->getEmpNumber(), 
//                          $allEmployees[3]->getEmpNumber(), $allEmployees[4]->getEmpNumber());
//        foreach ($allIds as $id) {
//            if (in_array($id, $expected)) {
//                $this->assertTrue($this->manager->isEntityAccessible('Employee', $id));
//            } else {
//                $this->assertFalse($this->manager->isEntityAccessible('Employee', $id));
//            }
//        }
//    }
    
    public function testIsEmployeeAccessibleESS() {
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
        $allIds = $this->getEmployeeIds($allEmployees);
        
        // ESS user
        $essUser = $users[4];
        $this->manager->setUser($essUser);
        foreach ($allIds as $id) {
            $this->assertFalse($this->manager->isEntityAccessible('Employee', $id));
        }      
    }    
    
//    public function testGetAccessibleEmployeesAdmin() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        
//        // Default Admin user  (no employee)
//        $defaultAdmin = $users[5];
//        $this->manager->setUser($defaultAdmin);
//        $result = $this->manager->getAccessibleEntities('Employee');
//        $expected = $this->getEmployeeIds($allEmployees);
//        $this->assertEquals(count($expected), count($result));
//        
//        $this->checkEmployees($expected, $result);        
//        
//        // Admin user 
//        $admin = $users[0];
//        $this->manager->setUser($admin);
//        $result = $this->manager->getAccessibleEntities('Employee');
//        $expected = $this->getEmployeeIds($allEmployees);
//        $this->assertEquals(count($expected), count($result));
//        $this->checkEmployees($expected, $result);
//        
//        // Admin + supervisor
//        $adminSupervisor = $users[3];
//        $this->manager->setUser($adminSupervisor);
//        $result = $this->manager->getAccessibleEntities('Employee');
//        $expected = $this->getEmployeeIds($allEmployees);
//        $this->assertEquals(count($expected), count($result));
//        $this->checkEmployees($expected, $result);      
//    }

//    public function testGetAccessibleEmployeesSupervisor() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        
//        // Supervisor with one subordinate
//        $supervisor = $users[1];
//        $this->manager->setUser($supervisor);
//        $expected = array($allEmployees[2]->getEmpNumber());
//        
//        $result = $this->manager->getAccessibleEntities('Employee');
//        $this->assertEquals(count($expected), count($result));
//        $this->checkEmployees($expected, $result);
//        
//        
//        // Supervisor with multiple subordinates
//        $supervisor = $users[6];
//        $this->manager->setUser($supervisor);
//        $expected = array($allEmployees[0]->getEmpNumber(), $allEmployees[2]->getEmpNumber(), 
//                          $allEmployees[3]->getEmpNumber(), $allEmployees[4]->getEmpNumber());
//        
//        $result = $this->manager->getAccessibleEntities('Employee');
//        $this->assertEquals(count($expected), count($result));
//        $this->checkEmployees($expected, $result);         
//    }
//    
//    public function testGetAccessibleEmployeesESS() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        
//        // Supervisor with one subordinate
//        $essUser = $users[4];
//        $this->manager->setUser($essUser);
//        
//        $result = $this->manager->getAccessibleEntities('Employee');
//        $this->assertEquals(0, count($result));        
//    }    
    
//    public function testGetAccessibleEmployeeIdsAdmin() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        
//        // Default Admin user  (no employee)
//        $defaultAdmin = $users[5];
//        $this->manager->setUser($defaultAdmin);
//        $result = $this->manager->getAccessibleEntityIds('Employee');
//        $expected = $this->getEmployeeIds($allEmployees);
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);        
//        
//        // Admin user 
//        $admin = $users[0];
//        $this->manager->setUser($admin);
//        $result = $this->manager->getAccessibleEntityIds('Employee');
//        $expected = $this->getEmployeeIds($allEmployees);
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);
//        
//        // Admin + supervisor
//        $adminSupervisor = $users[3];
//        $this->manager->setUser($adminSupervisor);
//        $result = $this->manager->getAccessibleEntityIds('Employee');
//        $expected = $this->getEmployeeIds($allEmployees);
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);      
//    }
//
//    public function testGetAccessibleEmployeeIdsSupervisor() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        
//        // Supervisor with one subordinate
//        $supervisor = $users[1];
//        $this->manager->setUser($supervisor);
//        $expected = array($allEmployees[2]->getEmpNumber());
//        
//        $result = $this->manager->getAccessibleEntityIds('Employee');
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);
//        
//        
//        // Supervisor with multiple subordinates
//        $supervisor = $users[6];
//        $this->manager->setUser($supervisor);
//        $expected = array($allEmployees[0]->getEmpNumber(), $allEmployees[2]->getEmpNumber(), 
//                          $allEmployees[3]->getEmpNumber(), $allEmployees[4]->getEmpNumber());
//        
//        $result = $this->manager->getAccessibleEntityIds('Employee');
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);         
//    }
//    
//    public function testGetAccessibleEmployeeIdsESS() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        
//        // Supervisor with one subordinate
//        $essUser = $users[4];
//        $this->manager->setUser($essUser);
//        
//        $result = $this->manager->getAccessibleEntityIds('Employee');
//        $this->assertEquals(0, count($result));        
//    }    
//    
//    public function testGetAccessibleSystemUsersAdmin() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $expected = $this->getObjectIds($users);
//
//        // Default Admin user  (no employee)
//        $defaultAdmin = $users[5];
//        $this->manager->setUser($defaultAdmin);
//        $result = $this->manager->getAccessibleEntityIds('SystemUser');
//        
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);        
//        
//        // Admin user 
//        $admin = $users[0];
//        $this->manager->setUser($admin);
//        $result = $this->manager->getAccessibleEntityIds('SystemUser');
//        
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);   
//        
//        // Admin + supervisor
//        $adminSupervisor = $users[3];
//        $this->manager->setUser($adminSupervisor);
//        $result = $this->manager->getAccessibleEntityIds('SystemUser');
//        
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);       
//    }    
//    
//    public function testGetAccessibleSystemUsersESSSupervisor() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        
//        // Supervisor with one subordinate
//        $supervisor = $users[1];
//        $this->manager->setUser($supervisor);
//        
//        $result = $this->manager->getAccessibleEntityIds('SystemUser');        
//        $this->assertEquals(0, count($result));  
//                
//        // Supervisor with multiple subordinates
//        $supervisor = $users[6];
//        $this->manager->setUser($supervisor);
//        
//        $result = $this->manager->getAccessibleEntityIds('SystemUser');        
//        $this->assertEquals(0, count($result));    
//        
//        // ESS user
//        $essUser = $users[4];
//        $this->manager->setUser($essUser);
//        
//        $result = $this->manager->getAccessibleEntityIds('SystemUser');        
//        $this->assertEquals(0, count($result));           
//    }  
//    
//    public function testGetAccessibleOperationalCountriesAdmin() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser'); 
//        $operationalCountries = TestDataService::loadObjectList('OperationalCountry', $this->fixture, 'OperationalCountry'); 
//        
//        
//        $expected = $this->getObjectIds($operationalCountries);
//
//        // Default Admin user  (no employee)
//        $defaultAdmin = $users[5];
//        $this->manager->setUser($defaultAdmin);
//        $result = $this->manager->getAccessibleEntityIds('OperationalCountry');
//        
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);        
//        
//        // Admin user 
//        $admin = $users[0];
//        $this->manager->setUser($admin);
//        $result = $this->manager->getAccessibleEntityIds('OperationalCountry');
//        
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);   
//        
//        // Admin + supervisor
//        $adminSupervisor = $users[3];
//        $this->manager->setUser($adminSupervisor);
//        $result = $this->manager->getAccessibleEntityIds('OperationalCountry');
//        
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);       
//    }    
//    
//    public function xtestGetAccessibleOperationalCountriesESSSupervisor() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        
//        // Supervisor with one subordinate
//        $supervisor = $users[1];
//        $this->manager->setUser($supervisor);
//        
//        $result = $this->manager->getAccessibleEntityIds('SystemUser');        
//        $this->assertEquals(0, count($result));  
//                
//        // Supervisor with multiple subordinates
//        $supervisor = $users[6];
//        $this->manager->setUser($supervisor);
//        
//        $result = $this->manager->getAccessibleEntityIds('SystemUser');        
//        $this->assertEquals(0, count($result));    
//        
//        // ESS user
//        $essUser = $users[4];
//        $this->manager->setUser($essUser);
//        
//        $result = $this->manager->getAccessibleEntityIds('SystemUser');        
//        $this->assertEquals(0, count($result));           
//    } 
//    
//    public function testGetAccessibleUserRolesAdmin() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser'); 
//        $userRoles = TestDataService::loadObjectList('UserRole', $this->fixture, 'UserRole'); 
//        
//        $expected = array();
//        foreach ($userRoles as $role) {
//            if ($role->is_assignable == 1) {
//                $expected[] = $role->getId();
//            }
//        }        
//
//        // Default Admin user  (no employee)
//        $defaultAdmin = $users[5];
//        $this->manager->setUser($defaultAdmin);
//        $result = $this->manager->getAccessibleEntityIds('UserRole');
//        
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);        
//        
//        // Admin user 
//        $admin = $users[0];
//        $this->manager->setUser($admin);
//        $result = $this->manager->getAccessibleEntityIds('UserRole');
//        
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);   
//        
//        // Admin + supervisor
//        $adminSupervisor = $users[3];
//        $this->manager->setUser($adminSupervisor);
//        $result = $this->manager->getAccessibleEntityIds('UserRole');
//        
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);       
//    } 
//    
//    public function testGetAccessibleUserRolesESSSupervisor() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        
//        // Supervisor with one subordinate
//        $supervisor = $users[1];
//        $this->manager->setUser($supervisor);
//        
//        $result = $this->manager->getAccessibleEntityIds('UserRole');        
//        $this->assertEquals(0, count($result));  
//                
//        // Supervisor with multiple subordinates
//        $supervisor = $users[6];
//        $this->manager->setUser($supervisor);
//        
//        $result = $this->manager->getAccessibleEntityIds('UserRole');        
//        $this->assertEquals(0, count($result));    
//        
//        // ESS user
//        $essUser = $users[4];
//        $this->manager->setUser($essUser);
//        
//        $result = $this->manager->getAccessibleEntityIds('UserRole');        
//        $this->assertEquals(0, count($result));           
//    } 
//         
//    public function testGetAccessibleLocationIdsAdmin() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $locations = TestDataService::loadObjectList('Location', $this->fixture, 'Location');
//        $expected = $this->getObjectIds($locations);
//        
//        // Default Admin user  (no employee)
//        $defaultAdmin = $users[5];
//        $this->manager->setUser($defaultAdmin);
//        $result = $this->manager->getAccessibleEntityIds('Location');
//
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);        
//        
//        // Admin user 
//        $admin = $users[0];
//        $this->manager->setUser($admin);
//        $result = $this->manager->getAccessibleEntityIds('Location');
//
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);
//        
//        // Admin + supervisor
//        $adminSupervisor = $users[3];
//        $this->manager->setUser($adminSupervisor);
//        $result = $this->manager->getAccessibleEntityIds('Location');
//
//        $this->assertEquals(count($expected), count($result));
//        $this->compareArrays($expected, $result);      
//    }
//
//    public function testGetAccessibleLocationIdsSupervisor() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        
//        // Supervisor with one subordinate
//        $supervisor = $users[1];
//        $this->manager->setUser($supervisor);
//        
//        $result = $this->manager->getAccessibleEntityIds('Location');
//        $this->assertEquals(0, count($result));
//        
//        
//        // Supervisor with multiple subordinates
//        $supervisor = $users[6];
//        $this->manager->setUser($supervisor);
//        
//        $result = $this->manager->getAccessibleEntityIds('Location');
//        $this->assertEquals(0, count($result));       
//    }
//    
//    public function testGetAccessibleLocationIdsESS() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        
//        // Supervisor with one subordinate
//        $essUser = $users[4];
//        $this->manager->setUser($essUser);
//        
//        $result = $this->manager->getAccessibleEntityIds('Location');
//        $this->assertEquals(0, count($result));        
//    }  
    
    public function testGetUserRoles() {
        $this->manager = new TestBasicUserRoleManager();
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');
        
        // id is not preserved in loadObjectList()
        $users[0]->id = 11;
        
        // 0 - Admin (also ESS?)        
        $roles = $this->manager->getUserRolesPublic($users[0]);
        $this->compareUserRoles(array('Admin', 'ESS'), $roles);
        
        // 1 - ESS, Supervisor   
        $users[1]->id = 12;
        $roles = $this->manager->getUserRolesPublic($users[1]);
        $this->compareUserRoles(array('ESS', 'Supervisor'), $roles);
        
        // 2 - ESS        
        $users[2]->id = 13;             
        $roles = $this->manager->getUserRolesPublic($users[2]);
        $this->compareUserRoles(array('ESS'), $roles);
        
        // 3 - Admin, Supervisor
        $users[0]->id = 14;
        $roles = $this->manager->getUserRolesPublic($users[3]);
        $this->compareUserRoles(array('Admin', 'Supervisor', 'ESS'), $roles);
        
        // 4 - ESS
        $users[3]->id = 15;
        $roles = $this->manager->getUserRolesPublic($users[4]);
        $this->compareUserRoles(array('ESS'), $roles);
        
        // 5 - Admin (Default admin) - does not have ESS role
        $users[4]->id = 16;
        $roles = $this->manager->getUserRolesPublic($users[5]);
        $this->compareUserRoles(array('Admin'), $roles);       
    }
    
    public function testGetScreenPermissions() {
        
        $userRole = new UserRole();
        $userRole->setId(1);
        $userRole->setName('Admin');
        
        $user = new SystemUser();
        $user->setId(1);
        $user->setEmpNumber(NULL);
        $user->setUserRole($userRole);
        
        $systemUserService = $this->getMock('SystemUserService', array('getSystemUser'));
        
        $systemUserService->expects($this->once())
                ->method('getSystemUser')
                ->with($user->getId())
                ->will($this->returnValue($user));
        
        $this->manager->setSystemUserService($systemUserService);
        $this->manager->setUser($user);       
        
        $mockScreenPermissionService = $this->getMock('ScreenPermissionService', array('getScreenPermissions'));
        $permission = new ResourcePermission(true, false, true, false);
        
        $module = 'admin';
        $action = 'testAction';
        $roles = array($userRole);
        
        $mockScreenPermissionService->expects($this->once())
                ->method('getScreenPermissions')
                ->with($module, $action, $roles)
                ->will($this->returnValue($permission));
        
        $this->manager->setScreenPermissionService($mockScreenPermissionService);   
        
        $result = $this->manager->getScreenPermissions($module, $action);        
        
        $this->assertEquals($permission, $result);
        
    }
    
//    public function testGetAccessibleEntityPropertiesAdmin() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        
//        // Default Admin user  (no employee)
//        $defaultAdmin = $users[5];
//        $this->manager->setUser($defaultAdmin);
//        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
//        $result = $this->manager->getAccessibleEntityProperties('Employee', $properties, 'lastName', 'ASC');
//        $expected = $this->getEmployeePropertyList($allEmployees, $properties);
//        $this->assertEquals(count($expected), count($result));
//        $this->assertEquals($expected[1]['empNumber'], $result[1]['empNumber']);
//        $this->assertEquals($expected[2]['firstName'], $result[2]['firstName']);
//        $this->assertEquals($expected[3]['middleName'], $result[3]['middleName']);
//        $this->assertEquals($expected[4]['lastName'], $result[4]['lastName']);
//        $this->assertEquals($expected[5]['termination_id'], $result[5]['termination_id']);
//        
//        // Admin user 
//        $admin = $users[0];
//        $this->manager->setUser($admin);
//        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
//        $result = $this->manager->getAccessibleEntityProperties('Employee', $properties, 'lastName', 'ASC');
//        $expected = $this->getEmployeePropertyList($allEmployees, $properties);
//        $this->assertEquals(count($expected), count($result));
//        $this->assertEquals($expected[1]['empNumber'], $result[1]['empNumber']);
//        $this->assertEquals($expected[2]['firstName'], $result[2]['firstName']);
//        $this->assertEquals($expected[3]['middleName'], $result[3]['middleName']);
//        $this->assertEquals($expected[4]['lastName'], $result[4]['lastName']);
//        $this->assertEquals($expected[5]['termination_id'], $result[5]['termination_id']);
//        
//        // Admin + supervisor
//        $adminSupervisor = $users[3];
//        $this->manager->setUser($adminSupervisor);
//        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
//        $result = $this->manager->getAccessibleEntityProperties('Employee', $properties, 'lastName', 'ASC');
//        $expected = $this->getEmployeePropertyList($allEmployees, $properties);
//        $this->assertEquals(count($expected), count($result));
//        $this->assertEquals($expected[1]['empNumber'], $result[1]['empNumber']);
//        $this->assertEquals($expected[2]['firstName'], $result[2]['firstName']);
//        $this->assertEquals($expected[3]['middleName'], $result[3]['middleName']);
//        $this->assertEquals($expected[4]['lastName'], $result[4]['lastName']);
//        $this->assertEquals($expected[5]['termination_id'], $result[5]['termination_id']);
//    }
//    
//    public function testGetAccessibleEntityPropertiesSupervisor() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        $allEmployees = TestDataService::loadObjectList('Employee', $this->fixture, 'Employee');
//        
//        // Supervisor with one subordinate
//        $supervisor = $users[1];
//        $this->manager->setUser($supervisor);
//        
//        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
//        $result = $this->manager->getAccessibleEntityProperties('Employee', $properties, 'lastName', 'ASC');
//        $allPropertyList = $this->getEmployeePropertyList($allEmployees, $properties);
//        $expected = $allPropertyList[3];
//        $this->assertEquals(1, count($result));
//        $this->assertEquals($expected['empNumber'], $result[3]['empNumber']);
//        $this->assertEquals($expected['firstName'], $result[3]['firstName']);
//        $this->assertEquals($expected['middleName'], $result[3]['middleName']);
//        $this->assertEquals($expected['lastName'], $result[3]['lastName']);
//        $this->assertEquals($expected['termination_id'], $result[3]['termination_id']);
//        
//        
//        // Supervisor with multiple subordinates
//        $supervisor = $users[6];
//        $this->manager->setUser($supervisor);
//        $expectedEmployees = array($allEmployees[0], $allEmployees[2], 
//                          $allEmployees[3], $allEmployees[4]);
//        
//        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
//        $result = $this->manager->getAccessibleEntityProperties('Employee', $properties, 'lastName', 'ASC');
//        $expectedResults = $this->getEmployeePropertyList($expectedEmployees, $properties);
//        $this->assertEquals(count($expectedResults), count($result));
//        $this->assertEquals($expectedResults[1]['empNumber'], $result[1]['empNumber']);
//        $this->assertEquals($expectedResults[3]['firstName'], $result[3]['firstName']);
//        $this->assertEquals($expectedResults[4]['middleName'], $result[4]['middleName']);
//        $this->assertEquals($expectedResults[5]['lastName'], $result[5]['lastName']);
//        $this->assertEquals($expectedResults[1]['termination_id'], $result[1]['termination_id']);
//    }
//    
//    public function testGetAccessibleEntityPropertiesESS() {
//        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');   
//        
//        // Supervisor with one subordinate
//        $essUser = $users[4];
//        $this->manager->setUser($essUser);
//        
//        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
//        $result = $this->manager->getAccessibleEntityProperties('Employee', $properties, 'lastName', 'ASC');
//        $this->assertEquals(0, count($result));
//    }
    
    public function testFilterRoles() {
        
        $testManager = new TestBasicUserRoleManager();
        
        $userRoles = $this->__convertRoleNamesToObjects(array('Supervisor', 'Admin', 'RegionalAdmin'));;
        
        $rolesToExclude = array();
        $rolesToInclude = array();
        
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);        
        $this->assertEquals($userRoles, $roles);
        
        $rolesToExclude =  array('Admin');
        $rolesToInclude = array();        
        
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);        
        $this->assertEquals(array($userRoles[0], $userRoles[2]), $roles);
        
        $rolesToExclude = array();
        $rolesToInclude =  array('Supervisor','RegionalAdmin');        
        
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);        
        $this->assertEquals(array($userRoles[0], $userRoles[2]), $roles);
        
        $rolesToExclude = array('Admin', 'Supervisor','RegionalAdmin');   
        $rolesToInclude = array();      
        
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);     
        $this->assertEquals(0, count($roles));
        
        $rolesToExclude = array('NewRole');   
        $rolesToInclude = array();      
        
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);     
        $this->assertEquals($userRoles, $roles);      
    }
    
 public function testFilterRolesSupervisorForEmployee() {
        
        $testManager = new TestBasicUserRoleManager();
        
        $userRoles = $this->__convertRoleNamesToObjects(array('Supervisor', 'Admin', 'RegionalAdmin'));
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude);        
        $this->assertEquals($userRoles, $roles);        
        
        $rolesToExclude = array();
        $rolesToInclude = array();
        
        $user = new SystemUser();
        $user->setId(11);        
        $user->setEmpNumber(9);
        
        
        $systemUserService = $this->getMock('SystemUserService', array('getSystemUser'));
        $systemUserService->expects($this->once())
                 ->method('getSystemUser')
                 ->will($this->returnValue($user));
        
        $testManager->setSystemUserService($systemUserService);
        $testManager->setUser($user);
        
        $employeeIds = array(1, 2, 3);
        
        $employeeService = $this->getMock('EmployeeService', array('getSubordinateIdListBySupervisorId'));
        $employeeService->expects($this->once())
                 ->method('getSubordinateIdListBySupervisorId')
                ->with($user->getEmpNumber())
                 ->will($this->returnValue($employeeIds));
       
        $testManager->setEmployeeService($employeeService);
        
        // Test that supervisor role is returned for Employee who is a subordinate 
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude, array('Employee' => 3));        
        $this->assertEquals($userRoles, $roles);
        
        // Test that supervisor role is not returned for Employee who is not a subordinate
        $roles = $testManager->filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude, array('Employee' => 13));        
        $this->assertEquals(array($userRoles[1], $userRoles[2]), $roles);
    }
    
    public function testGetHomePage() {
        $userRoleIds = array(1, 2, 3);
        
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');        
        $adminUserSupervisor = $users[3];
        $this->manager->setUser($adminUserSupervisor);
        
        $homePage1 = new HomePage();
        $homePage1->fromArray(array('id' => 4, "user_role_id" => 1, "action" => 'pim/viewEmployeeTimesheets', 
            "enable_class" => 'TestEnableClass', "priority" => 50));
        
        $homePage2 = new HomePage();
        $homePage2->fromArray(array('id' => 5, "user_role_id" => 1, "action" => 'pim/viewEmployeeList', "priority" => 30));
        
        $homePage3 = new HomePage();
        $homePage3->fromArray(array('id' => 3, "user_role_id" => 1, "action" => 'pim/viewSystemUsers', "priority" => 30));
                
        $homePage4 = new HomePage();
        $homePage4->fromArray(array('id' => 1, "user_role_id" => 1, "action" => 'pim/viewEmployeeList2', "priority" => 10));
        
        $homePage5 = new HomePage();
        $homePage5->fromArray(array('id' => 2, "user_role_id" => 1, "action" => 'pim/viewMyDetails', "priority" => 0));
              
        $homePages = array(
            $homePage1, $homePage2, $homePage3, $homePage4, $homePage5
        );
        $mockDao = $this->getMock('HomePageDao', array('getHomePagesInPriorityOrder'));
        $mockDao->expects($this->once())
                 ->method('getHomePagesInPriorityOrder')
                ->with($userRoleIds)
                ->will($this->returnValue($homePages));
       

        
        $this->manager->setHomePageDao($mockDao);
        $homePage = $this->manager->getHomePage();
        
        $this->assertEquals('pim/viewEmployeeList', $homePage);
    }
    
    public function testGetModuleDefaultPage() {
        $userRoleIds = array(1, 2, 3);
        
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');        
        $adminUserSupervisor = $users[3];
        $this->manager->setUser($adminUserSupervisor);
        $module = "time";
        
        $defaultPage1 = new HomePage();
        $defaultPage1->fromArray(array('id' => 4, "module_id" => 5, "user_role_id" => 1, "action" => 'pim/viewEmployeeTimesheets', 
            "enable_class" => 'TestEnableClass', "priority" => 50));
        
        $defaultPage2 = new HomePage();
        $defaultPage2->fromArray(array('id' => 5, "module_id" => 5, "user_role_id" => 1, "action" => 'pim/viewEmployeeList', "priority" => 30));
        
        $defaultPage3 = new HomePage();
        $defaultPage3->fromArray(array('id' => 3, "module_id" => 5, "user_role_id" => 1, "action" => 'pim/viewSystemUsers', "priority" => 30));
                
        $defaultPage4 = new HomePage();
        $defaultPage4->fromArray(array('id' => 1, "module_id" => 5, "user_role_id" => 1, "action" => 'pim/viewEmployeeList2', "priority" => 10));
        
        $defaultPage5 = new HomePage();
        $defaultPage5->fromArray(array('id' => 2, "module_id" => 5, "user_role_id" => 1, "action" => 'pim/viewMyDetails', "priority" => 0));
              
        $defaultPages = array(
            $defaultPage1, $defaultPage2, $defaultPage3, $defaultPage4, $defaultPage5
        );
        $mockDao = $this->getMock('HomePageDao', array('getModuleDefaultPagesInPriorityOrder'));
        $mockDao->expects($this->once())
                 ->method('getModuleDefaultPagesInPriorityOrder')
                ->with($module, $userRoleIds)
                ->will($this->returnValue($defaultPages));
               
        $this->manager->setHomePageDao($mockDao);
        $homePage = $this->manager->getModuleDefaultPage($module);
        
        $this->assertEquals('pim/viewEmployeeList', $homePage);
    }
    
    private function __convertRoleNamesToObjects(array $roleNames) {
        $roles = array();
        
        foreach ($roleNames as $name) {
            $userRole = new UserRole();
            $userRole->setName($name);
            
            $roles[] = $userRole;
        }
        
        return $roles;
    }
    

    protected function compareUserRoles($expected, $actual) {
        $this->assertEquals(count($expected), count($actual));
        foreach($expected as $role) {
            $found = false;
            
            foreach($actual as $roleObject) {
                
                if ($roleObject->name == $role) {
                    $found = true;
                    break;
                }
            }
            
            $this->assertTrue($found, 'Expected Role ' . $role . ' not found');
        }
    }
    
    protected function compareEmployees($expected, $actual) {
        $this->assertEquals(count($expected), count($actual));
        
        foreach($expected as $expectedEmployee) {
            $found = false;
            
            foreach($actual as $employee) {
                
                if ($employee->getEmpNumber() == $expectedEmployee->getEmpNumber()) {
                    $found = true;
                    break;
                }
            }
            
            $this->assertTrue($found, 'Expected Employee (id = ' . $expectedEmployee->getEmpNumber() . ' not found');
        }        
    }
    
    protected function compareArrays($expected, $actual) {
        $this->assertEquals(count($expected), count($actual));
        
        $diff = array_diff($expected, $actual);
        $this->assertEquals(0, count($diff), $diff);       
    }    
    
    protected function checkEmployees($expected, $actual) {
        $this->assertEquals(count($expected), count($actual));
        
//        foreach($actual as $id=>$ac) {
//            echo "$id => " . $ac->getFullName() . "\n";
//        }
        foreach ($expected as $id) {
            $this->assertTrue(isset($actual[$id]));
        }
    }
    
    protected function getEmployeeIds($employees) {
        $ids = array();
        
        foreach ($employees as $employee) {
            $ids[] = $employee->getEmpNumber();
        }
        
        return $ids;
    }
    
    protected function getEmployeePropertyList($employees, $properties) {
        $propertyList = array();
        
        foreach ($employees as $employee) {
            $propertyValueArray = array();
            foreach ($properties as $property) {
                $propertyValueArray[$property] = $employee["$property"];
            }
            $propertyList[$propertyValueArray['empNumber']] = $propertyValueArray;
        }
        
        return $propertyList;
    }
    
    protected function getObjectIds($users) {
        $ids = array();
        
        foreach ($users as $user) {
            $ids[] = $user->getId();
        }
        
        return $ids;
    }
    
    public function testGetAllowedActionsForAdminUserRole() {
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');
        $expected = array(14, 15);
        
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        
        $workflow = 3;
        $state = 'ACTIVE';
        $result = $this->manager->getAllowedActions($workflow, $state);
        
        $this->assertEquals(2, count($result));
        foreach ($expected as $expectedId) {
            $found = false;
            foreach($result as $workflowItem) {
                if ($workflowItem->getId() == $expectedId) {
                    $found = true;
                    break;
                }
            }
            $this->assertTrue($found);
        }
        
    }
    
    public function testIsActionAllowedForAdminAddEmployee() {
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');
        
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        
        $workflow = 3;
        $state = 'NOT EXIST';
        $action = '1';
        $isAllowed = $this->manager->isActionAllowed($workflow, $state, $action);
        
        $this->assertTrue($isAllowed);
    }
    
    public function testIsActionAllowedForAdminAddActiveEmployee() {
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');
        
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        
        $workflow = 3;
        $state = 'ACTIVE';
        $action = '1';
        $isAllowed = $this->manager->isActionAllowed($workflow, $state, $action);
        
        $this->assertTrue(!$isAllowed);
    }
    
    public function testGetEmployeesWithRole() {
        $employees = $this->manager->getEmployeesWithRole('Admin');
        $this->assertEquals(2, count($employees));
        
        $expected = array(1, 4);
        foreach ($employees as $employee) {
            $id = array_search($employee->getEmpNumber(), $expected);
            $this->assertTrue($id !== false);
            unset($expected[$id]);
        }
        $this->assertEquals(0, count($expected));
    }
    
    public function testGetActionableStatesValidActionsAndRole() {
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');
        
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        
        $workflow = 3;
        $actions = array('1', '2', '3');
        $expected = array('ACTIVE', 'NOT EXIST');

        $states = $this->manager->getActionableStates($workflow, $actions);
        $this->assertTrue(is_array($states));
        
        sort($states);
        $this->assertEquals($expected, $states);
    }   
    
    public function testGetActionableStatesInvalidAction() {
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');
        
        $defaultAdmin = $users[5];
        $this->manager->setUser($defaultAdmin);
        
        $workflow = 3;
        $actions = array('11');
        $expected = array();

        $states = $this->manager->getActionableStates($workflow, $actions);
        $this->assertTrue(is_array($states));

        $this->assertEquals(0, count($states));
    }    
    
    public function testGetActionableStatesUserRoleWithNoWorkflowAccess() {
        $users = TestDataService::loadObjectList('SystemUser', $this->fixture, 'SystemUser');
        
        $ess = $users[1];
        $this->manager->setUser($ess);
        
        $workflow = 3;
        $actions = array('1', '2', '3');
        $expected = array('ACTIVE', 'NOT EXIST');

        $states = $this->manager->getActionableStates($workflow, $actions);
        $this->assertTrue(is_array($states));

        $this->assertEquals(0, count($states));
    }    
}

/* Extend class to get access to protected method */
class TestBasicUserRoleManager extends BasicUserRoleManager {
    public function getUserRolesPublic($user) {
        return $this->getUserRoles($user);
    }
    
    public function filterUserRolesPublic($userRoles, $rolesToExclude, $rolesToInclude, $entities = array()) {
        return $this->filterRoles($userRoles, $rolesToExclude, $rolesToInclude, $entities);
    }
}

class TestEnableClass implements HomePageEnablerInterface {
    public function isEnabled($systemUser) {
        
        return false;
    }    
}

