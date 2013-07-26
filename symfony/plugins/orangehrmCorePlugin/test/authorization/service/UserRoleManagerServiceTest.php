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
 * Description of UserRoleManagerFactoryTest
 *
 * @group Core
 */
class UserRoleManagerServiceTest extends PHPUnit_Framework_TestCase {
    
    /** @property UserRoleManagerService $service */
    private $service;
    
    /**
     * Set up method
     */
    protected function setUp() {
        $this->service = new UserRoleManagerService();
    }
    
    /**
     * Test the getConfigDao() and setConfigDao() method
     */
    public function testGetUserRoleManagerClassName() {
        $configDao = $this->getMock('ConfigDao', array('getValue'));
        $configDao->expects($this->once())
                ->method('getValue')
                ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
                ->will($this->returnValue('TestUserRoleManager'));
        
        $this->service->setConfigDao($configDao);
        $class = $this->service->getUserRoleManagerClassName();
        $this->assertEquals('TestUserRoleManager', $class);
    }
    
    public function testGetUserRoleManagerExistingClass() {
        $configDao = $this->getMock('ConfigDao', array('getValue'));
        $configDao->expects($this->once())
                ->method('getValue')
                ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
                ->will($this->returnValue('UnitTestUserRoleManager'));
        
        $authenticationService = $this->getMock('AuthenticationService', array('getLoggedInUserId'));
        $authenticationService->expects($this->once())
                              ->method('getLoggedInUserId')
                              ->will($this->returnValue(211));
        
        $systemUser = new SystemUser();
        $systemUser->setId(211);
        
        $systemUserService = $this->getMock('SystemUserService', array('getSystemUser'));
        $systemUserService->expects($this->once())
                          ->method('getSystemUser')
                          ->will($this->returnValue($systemUser));
        
        $this->service->setConfigDao($configDao);
        $this->service->setAuthenticationService($authenticationService);
        $this->service->setSystemUserService($systemUserService);
        
        $manager = $this->service->getUserRoleManager();
        $this->assertNotNull($manager);
        $this->assertTrue($manager instanceof AbstractUserRoleManager);
        $this->assertTrue($manager instanceof UnitTestUserRoleManager);
        $user = $manager->getUser();
        $this->assertEquals($systemUser, $user);
    }
    
    public function testGetUserRoleManagerInvalidClass() {
        $configDao = $this->getMock('ConfigDao', array('getValue'));
        $configDao->expects($this->once())
                ->method('getValue')
                ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
                ->will($this->returnValue('InvalidUserRoleManager'));
        
        $this->service->setConfigDao($configDao);
        
        try {
            $manager = $this->service->getUserRoleManager();
            $this->fail("Should throw exception if user role manager is invalid");
        } catch (ServiceException $e) {
            // expected
        }
    } 
    
    public function testGetUserRoleManagerNonExistingClass() {
        $configDao = $this->getMock('ConfigDao', array('getValue'));
        $configDao->expects($this->once())
                ->method('getValue')
                ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
                ->will($this->returnValue('xasdfasfdskfdaManager'));
        
        $this->service->setConfigDao($configDao);
        
        try {
            $manager = $this->service->getUserRoleManager();
            $this->fail("Should throw exception if user role manager class does not exist.");
        } catch (ServiceException $e) {
            // expected
        }
    }
    
    public function testGetUserRoleManagerNoLoggedInUser() {
        $configDao = $this->getMock('ConfigDao', array('getValue'));
        $configDao->expects($this->once())
                ->method('getValue')
                ->with(UserRoleManagerService::KEY_USER_ROLE_MANAGER_CLASS)
                ->will($this->returnValue('UnitTestUserRoleManager'));
        

        $authenticationService = $this->getMock('AuthenticationService', array('getLoggedInUserId'));
        $authenticationService->expects($this->once())
                              ->method('getLoggedInUserId')
                              ->will($this->returnValue(NULL));
        
        
        $systemUserService = $this->getMock('SystemUserService', array('getSystemUser'));
        $systemUserService->expects($this->once())
                          ->method('getSystemUser')
                          ->with(NULL)
                          ->will($this->returnValue(NULL));
        
        $this->service->setConfigDao($configDao);
        $this->service->setAuthenticationService($authenticationService);
        $this->service->setSystemUserService($systemUserService);        
            
        $manager = $this->service->getUserRoleManager();
        $this->assertNull($manager->getUser(), "user should be null when no logged in user ");
            
    }    
    
}

class InvalidUserRoleManager {
   
}

class UnitTestUserRoleManager extends AbstractUserRoleManager {
    public function getAccessibleEntities($entityType, $operation = null, $returnType = null,
            $rolesToExclude = array(), $rolesToInclude = array(), $requestedPermissions = array()) {
        
    }    
    
    public function getAccessibleModules() {
        
    }
    
    public function getAccessibleMenuItemDetails() {
        
    }    
    
    public function isModuleAccessible($module) {
        
    }
    
    public function isScreenAccessible($module, $screen, $field) {
        
    }
    
    public function isFieldAccessible($module, $screen, $field) {
        
    }
    
    protected function getUserRoles(SystemUser $user) {
        
    }    
    public function getScreenPermissions($module, $screen) {
        
    }

    public function areEntitiesAccessible($entityType, $entityIds, $operation = null, 
            $rolesToExclude = array(), $rolesToInclude = array(), $requiredPermissions = array()) {
        
    }

    public function isEntityAccessible($entityType, $entityId, $operation = null, 
            $rolesToExclude = array(), $rolesToInclude = array(), $requiredPermissions = array()) {
        
    }

    public function getAccessibleEntityIds($entityType, $operation = null, 
            $returnType = null, $rolesToExclude = array(), 
            $rolesToInclude = array(), $requiredPermissions = array()) {
        
    }

    protected function getAllowedActions($workFlowId, $state, $rolesToExclude = array(), $rolesToInclude = array(), $entities = array()) {
        
    }

    protected function isActionAllowed($workFlowId, $state, $action, $rolesToExclude = array(), $rolesToInclude = array(), $entities = array()) {
        
    }
    
    public function getActionableStates($workflow, $actions, $rolesToExclude = array(), $rolesToInclude = array(), $entities = array()) {
        
    }

    public function getAccessibleEntityProperties($entityType, $properties = array(), $orderField = null, $orderBy = null, $rolesToExclude = array(), $rolesToInclude = array(), $requiredPermissions = array()) {
        
    }

    public function getEmployeesWithRole($roleName, $entities = array()) {
        
    }

    public function getHomePage() {
        
    }

    public function getModuleDefaultPage($module) {
        
    }
}
