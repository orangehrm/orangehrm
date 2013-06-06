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
 * Description of ScreenPermissionServiceTest
 * @group Core
 */
class ScreenPermissionServiceTest extends PHPUnit_Framework_TestCase {
    
    /** @property ScreenPermissionService $service */
    private $service;
    
    /**
     * Set up method
     */
    protected function setUp() {
        $this->service = new ScreenPermissionService();
    }    
    
    /**
     * Test case for when no permissions are defined for given user role(s).
     * Behavior is to allow access if the screen is not defined, unless prohibited through a rule in the database.
     * This allows to progressively update the rules in code. 
     */
    public function testGetScreenPermissionsNoneWithNoScreen() {
        $module = 'xim';
        $action = 'doThis';
        $roles = '';
        
        $permissionDao = $this->getMock('ScreenPermissionDao', array('getScreenPermissions'));
        $emptyDoctrineCollection = new Doctrine_Collection('ScreenPermission');
        
        $permissionDao->expects($this->once())
                ->method('getScreenPermissions')
                ->with($module, $action, $roles)
                ->will($this->returnValue($emptyDoctrineCollection));
        
        $this->service->setScreenPermissionDao($permissionDao);
        
        $screenDao = $this->getMock('ScreenDao', array('getScreen'));
        $screenDao->expects($this->once())
                ->method('getScreen')
                ->with($module, $action)
                ->will($this->returnValue(false));        
        
        $this->service->setScreenDao($screenDao);
        
        $permissions = $this->service->getScreenPermissions($module, $action, $roles);
        
        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, true, true, true, true);

    }
    
    public function testGetScreenPermissionsNoneWithScreenDefined() {
        $module = 'xim';
        $action = 'doThis';
        $roles = '';
        
        $mockDao = $this->getMock('ScreenPermissionDao', array('getScreenPermissions'));
        $emptyDoctrineCollection = new Doctrine_Collection('ScreenPermission');
        
        $mockDao->expects($this->once())
                ->method('getScreenPermissions')
                ->with($module, $action, $roles)
                ->will($this->returnValue($emptyDoctrineCollection));
        
        $this->service->setScreenPermissionDao($mockDao);
        
        $screen = new Screen();
        $screen->setName('abc');
        
        $screenDao = $this->getMock('ScreenDao', array('getScreen'));
        $screenDao->expects($this->once())
                ->method('getScreen')
                ->with($module, $action)
                ->will($this->returnValue($screen));        
        
        $this->service->setScreenDao($screenDao);        
        
        $permissions = $this->service->getScreenPermissions($module, $action, $roles);
        
        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, false, false, false, false);

    }
    
    public function testGetScreenPermissionsOne() {
        $module = 'xim';
        $action = 'doThis';
        $roles = array('Admin');
        

        $doctrineCollection = new Doctrine_Collection('ScreenPermission');
        $screenPermission1 = new ScreenPermission();
        $screenPermission1->fromArray(array('id' => 1, 'user_role_id' => 1, 'screen_id' => 1, 
                                           'can_read' => 1, 'can_create' => 0, 
                                           'can_update'=> 0, 'can_delete'=> 1));
        $screenPermission2 = new ScreenPermission();
        $screenPermission2->fromArray(array('id' => 1, 'user_role_id' => 1, 'screen_id' => 1, 
                                           'can_read' => 0, 'can_create' => 1, 
                                           'can_update'=> 0, 'can_delete'=> 1));
        
        $screenPermissions = array($screenPermission1, $screenPermission2);
        $doctrineCollection->setData($screenPermissions);
        
        $mockDao = $this->getMock('ScreenPermissionDao', array('getScreenPermissions'));        
        $mockDao->expects($this->once())
                ->method('getScreenPermissions')
                ->with($module, $action, $roles)
                ->will($this->returnValue($doctrineCollection));
        
        $this->service->setScreenPermissionDao($mockDao);
        
        $permissions = $this->service->getScreenPermissions($module, $action, $roles);
        
        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, true, true, false, true);        
    }
    
    public function testGetScreenPermissionsTwo() {
        $module = 'xim';
        $action = 'doThis';
        $roles = array('Admin', 'ESS');
        

        $doctrineCollection = new Doctrine_Collection('ScreenPermission');
        $screenPermission1 = new ScreenPermission();
        $screenPermission1->fromArray(array('id' => 1, 'user_role_id' => 1, 'screen_id' => 1, 
                                           'can_read' => 1, 'can_create' => 0, 
                                           'can_update'=> 0, 'can_delete'=> 1));
        $screenPermission2 = new ScreenPermission();
        $screenPermission2->fromArray(array('id' => 1, 'user_role_id' => 1, 'screen_id' => 1, 
                                           'can_read' => 0, 'can_create' => 1, 
                                           'can_update'=> 0, 'can_delete'=> 1));
        
        $screenPermissions = array($screenPermission1, $screenPermission2);
        $doctrineCollection->setData($screenPermissions);
        
        $mockDao = $this->getMock('ScreenPermissionDao', array('getScreenPermissions'));        
        $mockDao->expects($this->once())
                ->method('getScreenPermissions')
                ->with($module, $action, $roles)
                ->will($this->returnValue($doctrineCollection));
        
        $this->service->setScreenPermissionDao($mockDao);
        
        $permissions = $this->service->getScreenPermissions($module, $action, $roles);
        
        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, true, true, false, true);        
    }
    
    public function testGetScreenPermissionsMany() {
        $module = 'xim';
        $action = 'doThis';
        $roles = array('Admin', 'ESS', 'Supervisor');
        
        
        $doctrineCollection = new Doctrine_Collection('ScreenPermission');
        $screenPermission1 = new ScreenPermission();
        $screenPermission1->fromArray(array('id' => 1, 'user_role_id' => 1, 'screen_id' => 1, 
                                           'can_read' => 0, 'can_create' => 0, 
                                           'can_update'=> 0, 'can_delete'=> 0));
        $screenPermission2 = new ScreenPermission();
        $screenPermission2->fromArray(array('id' => 1, 'user_role_id' => 1, 'screen_id' => 1, 
                                           'can_read' => 0, 'can_create' => 1, 
                                           'can_update'=> 0, 'can_delete'=> 0));
        
        $screenPermission3 = new ScreenPermission();
        $screenPermission3->fromArray(array('id' => 1, 'user_role_id' => 1, 'screen_id' => 1, 
                                           'can_read' => 0, 'can_create' => 1, 
                                           'can_update'=> 0, 'can_delete'=> 1));
        
        $screenPermissions = array($screenPermission1, $screenPermission2, $screenPermission3);
        $doctrineCollection->setData($screenPermissions);
        
        $mockDao = $this->getMock('ScreenPermissionDao', array('getScreenPermissions'));        
        $mockDao->expects($this->once())
                ->method('getScreenPermissions')
                ->with($module, $action, $roles)
                ->will($this->returnValue($doctrineCollection));
        
        $this->service->setScreenPermissionDao($mockDao);
        
        $permissions = $this->service->getScreenPermissions($module, $action, $roles);
        
        $this->assertTrue($permissions instanceof ResourcePermission);
        $this->verifyPermissions($permissions, false, true, false, true);         
    }
    
    public function testGetScreen() {
        
        $module = 'xim';
        $action = 'doThis';
        $expected = new Screen();
        $expected->setId(2);
        $expected->setName('test');
        $expected->setModuleId(33);
        $expected->setActionUrl($action);

    
        $screenDao = $this->getMock('ScreenDao', array('getScreen'));
        $screenDao->expects($this->once())
                ->method('getScreen')
                ->with($module, $action)
                ->will($this->returnValue($expected));        
        
        $this->service->setScreenDao($screenDao);        
        
        $result = $this->service->getScreen($module, $action);
        $this->assertEquals($expected, $result);
    }
    
    protected function verifyPermissions(ResourcePermission $permission, $read, $create, $update, $delete) {
        $this->assertEquals($read, $permission->canRead());
        $this->assertEquals($create, $permission->canCreate());
        $this->assertEquals($update, $permission->canUpdate());
        $this->assertEquals($delete, $permission->canDelete());        
    }    
}

