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
 * @group Pim
 *
 */
class PIMLeftMenuServiceTest extends PHPUnit_Framework_TestCase {
    
    protected $leftMenuService;
    
    protected function setUp() {
        $this->leftMenuService = new TestPIMLeftMenuService();
    }
    
    public function testGetMenuItemsAllMenusAccessible() {
        $empNumber = 12;
        $self = false;
        $permission = new ResourcePermission(true, true, true, true);
        
        $allMenuItems = $this->leftMenuService->getAvailableActionsPublic();
        $cache = array($empNumber => $allMenuItems);
        
        $getAttributeMap = array(
            array(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED, true),            
            array(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array(), array()),
            array(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array(), array()),
        );
        
        $mocksfUser = $this->getMock('MockSfUser');
        $mocksfUser->expects($this->exactly(3))
             ->method('getAttribute')
             ->will($this->returnValueMap($getAttributeMap));
        $mocksfUser->expects($this->once())
             ->method('setAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        $mocksfUser->expects($this->once())
             ->method('hasAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
             ->will($this->returnValue(true));
        
        $mockUserRoleManager = $this->getMock('BasicUserRoleManager');
        $mockUserRoleManager->expects($this->any())
             ->method('getDataGroupPermissions')
             ->will($this->returnValue($permission));
        
        $this->leftMenuService->setUser($mocksfUser);        
        $this->leftMenuService->setUserRoleManager($mockUserRoleManager);
        
        $menu = $this->leftMenuService->getMenuItems($empNumber, $self);
        

        $this->assertEquals(array_keys($allMenuItems), array_keys($menu));
    }
    
    public function testGetMenuItemsOnlyDependentsAccessible() {
        $empNumber = 12;
        $self = false;
        
        $allMenuItems = $this->leftMenuService->getAvailableActionsPublic();
        $expected['viewDependents'] = $allMenuItems['viewDependents'];
        
        $cache = array($empNumber => $expected);
        
        $getAttributeMap = array(
            array(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED, true),            
            array(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array(), array()),
            array(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array(), array()),
        );
        
        $mocksfUser = $this->getMock('MockSfUser');
        $mocksfUser->expects($this->exactly(3))
             ->method('getAttribute')
             ->will($this->returnValueMap($getAttributeMap));
        $mocksfUser->expects($this->once())
             ->method('setAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        $mocksfUser->expects($this->once())
             ->method('hasAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
             ->will($this->returnValue(true));
        
        $dataGroupClosure = function($dataGroups, $rolesToExclude, $rolesToInclude, $self, $entities) {
            if (in_array('dependents', $dataGroups)) {
                $permission = new ResourcePermission(true, true, true, true);
            } else {
                $permission = new ResourcePermission(false, false, false, false);
            }
            
            return $permission;
        };
        
        $mockUserRoleManager = $this->getMock('BasicUserRoleManager');
        $mockUserRoleManager->expects($this->any())
             ->method('getDataGroupPermissions')
             ->will($this->returnCallback($dataGroupClosure));
        
        $this->leftMenuService->setUser($mocksfUser);        
        $this->leftMenuService->setUserRoleManager($mockUserRoleManager);
        
        $menu = $this->leftMenuService->getMenuItems($empNumber, $self);
        
        $this->assertEquals(1, count($menu));
        $this->assertTrue(isset($menu['viewDependents']));
    }    
    
    public function testClearCachedMenuOneEmployee() {
        $empNumber = 42;
        
        $allMenuItems = $this->leftMenuService->getAvailableActionsPublic();                
        $cache = array($empNumber => $allMenuItems,
            34 => $allMenuItems,
            55 => $allMenuItems);
        
        $clearedCache = $cache;
        unset($clearedCache[$empNumber]);        
        $this->assertEquals(count($clearedCache) + 1, count($cache));
        
        $mocksfUser = $this->getMock('MockSfUser');
        $mocksfUser->expects($this->once())
             ->method('getAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array())
             ->will($this->returnValue($cache));
        $mocksfUser->expects($this->once())
             ->method('setAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $clearedCache);
        
        $this->leftMenuService->setUser($mocksfUser);
        $this->leftMenuService->clearCachedMenu($empNumber);
    }
    
    public function testClearCachedMenuOneEmployeeNotCached() {
        $empNumber = 42;
        
        $allMenuItems = $this->leftMenuService->getAvailableActionsPublic();                
        $cache = array(34 => $allMenuItems, 55 => $allMenuItems);

        
        $mocksfUser = $this->getMock('MockSfUser');
        $mocksfUser->expects($this->once())
             ->method('getAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array())
             ->will($this->returnValue($cache));
        $mocksfUser->expects($this->once())
             ->method('setAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        
        $this->leftMenuService->setUser($mocksfUser);
        $this->leftMenuService->clearCachedMenu($empNumber);
    }    
    
    public function testClearCachedMenuAll() {

        $allMenuItems = $this->leftMenuService->getAvailableActionsPublic();                
        $cache = array(34 => $allMenuItems, 55 => $allMenuItems, 101 => $allMenuItems);

        
        $mocksfUser = $this->getMock('MockSfUser');
        $mocksfUser->expects($this->once())
             ->method('getAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array())
             ->will($this->returnValue($cache));
        $mocksfUser->expects($this->once())
             ->method('setAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array());
        
        $this->leftMenuService->setUser($mocksfUser);
        $this->leftMenuService->clearCachedMenu();
    }    
        
    public function testIsPimAccessibleTrue() {
        $permission = new ResourcePermission(true, true, true, true);
        
        $allMenuItems = $this->leftMenuService->getAvailableActionsPublic();
        $cache = array('default' => $allMenuItems);
        
        $getAttributeMap = array(
            array(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED, true),            
            array(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array(), array()),
            array(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array(), array()),
        );
        
        $mocksfUser = $this->getMock('MockSfUser');
        $mocksfUser->expects($this->exactly(3))
             ->method('getAttribute')
             ->will($this->returnValueMap($getAttributeMap));
        $mocksfUser->expects($this->once())
             ->method('setAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        $mocksfUser->expects($this->once())
             ->method('hasAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
             ->will($this->returnValue(true));
        
        $mockUserRoleManager = $this->getMock('BasicUserRoleManager');
        $mockUserRoleManager->expects($this->any())
             ->method('getDataGroupPermissions')
             ->will($this->returnValue($permission));
        
        $this->leftMenuService->setUser($mocksfUser);        
        $this->leftMenuService->setUserRoleManager($mockUserRoleManager);
        
        $accessible = $this->leftMenuService->isPimAccessible(null, false);
        $this->assertTrue($accessible);
    }
    
    public function testIsPimAccessibleFalse() {
        $permission = new ResourcePermission(false, false, false, false);
        
        $cache = array('default' => array());
        
        $getAttributeMap = array(
            array(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED, true),            
            array(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array(), array()),
            array(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, array(), array()),
        );
        
        $mocksfUser = $this->getMock('MockSfUser');
        $mocksfUser->expects($this->exactly(3))
             ->method('getAttribute')
             ->will($this->returnValueMap($getAttributeMap));
        $mocksfUser->expects($this->once())
             ->method('setAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_SESSION_KEY, $cache);
        $mocksfUser->expects($this->once())
             ->method('hasAttribute')
             ->with(PIMLeftMenuService::PIM_LEFTMENU_TAXMENU_ENABLED)
             ->will($this->returnValue(true));
        
        $mockUserRoleManager = $this->getMock('BasicUserRoleManager');
        $mockUserRoleManager->expects($this->any())
             ->method('getDataGroupPermissions')
             ->will($this->returnValue($permission));
        
        $this->leftMenuService->setUser($mocksfUser);        
        $this->leftMenuService->setUserRoleManager($mockUserRoleManager);
        
        $accessible = $this->leftMenuService->isPimAccessible(null, false);
        $this->assertTrue(!$accessible);
    }    
}

/* 
 * Test implementation created to expose some protected methods of
 * PIMLeftMenuService.
 */
class TestPIMLeftMenuService extends PIMLeftMenuService {
    
    public function getAvailableActionsPublic() {
        return parent::getAvailableActions();
    }
}

class MockSfUser extends sfUser {
    function __construct() {
        
    }
}

