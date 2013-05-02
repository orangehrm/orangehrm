<?php
/*
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
 * Description of MenuServiceTest
 * @group Core
 */
class MenuServiceTest extends PHPUnit_Framework_TestCase {
    
    private $menuService;
    
    /**
     * Set up method
     */
    protected function setUp() {        

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/MenuDao.yml';
        TestDataService::populate($this->fixture);                
        $this->menuService = new MenuService();
        $this->_clearDoctrineResults();
        
    }
    
    private function _clearDoctrineResults() {
        
        $manager = Doctrine_Manager::getInstance();
        $connection = $manager->getCurrentConnection();
        $table = $connection->getTable('MenuItem');        
        $table->clear();        
        
    }    
    
    public function testGetMenuItemCollectionForAdmin() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('Admin');            
        
        $menuArray = $this->menuService->getMenuItemCollection($userRoleList);
       
        /* Checking the count of level-1 menu items */
        $this->assertEquals(3, count($menuArray));
        
        /* Checking the type */
        foreach ($menuArray as $menuItem) {
            $this->assertTrue($menuItem instanceof MenuItem);
        }
        
        /* Checking order and eligible items */
        $this->assertEquals('Admin', $menuArray[1]->getMenuTitle());
        $this->assertEquals('PIM', $menuArray[7]->getMenuTitle());
        $this->assertEquals('Leave', $menuArray[12]->getMenuTitle());
        
    }
    
    public function testGetMenuItemCollectionForEss() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('ESS');  
        
        $menuArray = $this->menuService->getMenuItemCollection($userRoleList);
        
        /* Checking the count of level-1 menu items */
        $this->assertEquals(2, count($menuArray));
        
        /* Checking the type */
        foreach ($menuArray as $menuItem) { //echo $menuItem->getMenuTitle() . "\n";
            $this->assertTrue($menuItem instanceof MenuItem);
        }
        
        /* Checking order and eligible items */
        $this->assertEquals('Leave', $menuArray[12]->getMenuTitle());
        $this->assertEquals('My Info', $menuArray[21]->getMenuTitle());        
        
    }    
    
    public function testGetMenuItemDetailsForAdmin() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('Admin');        
        
        $menuDetails = $this->menuService->getMenuItemDetails($userRoleList);
        $menuArray = $menuDetails['menuItemArray'];
        
        /* Checking the count of level-1 menu items */
        $this->assertEquals(3, count($menuArray));     
        
        /* Checking order and eligible items */
        $this->assertEquals('Admin', $menuArray[0]['menuTitle']);
        $this->assertEquals('PIM', $menuArray[1]['menuTitle']);
        $this->assertEquals('Leave', $menuArray[2]['menuTitle']);  
        
        $organizationMenu = $menuArray[0]['subMenuItems'];
        $this->assertEquals('Organization', $organizationMenu[0]['menuTitle']);
        $this->assertEquals('', $organizationMenu[0]['module']);
        $this->assertEquals('', $organizationMenu[0]['action']);
        $this->assertEquals(2, $organizationMenu[0]['level']);
        
        $LocationsMenu = $organizationMenu[0]['subMenuItems'];
        $this->assertEquals('Locations', $LocationsMenu[1]['menuTitle']);
        $this->assertEquals('admin', $LocationsMenu[1]['module']);
        $this->assertEquals('viewLocations', $LocationsMenu[1]['action']);        
        $this->assertEquals(3, $LocationsMenu[1]['level']);
        
    }
    
    public function testGetMenuItemDetailsForEss() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('ESS');        
        
        $menuDetails = $this->menuService->getMenuItemDetails($userRoleList);
        $menuArray = $menuDetails['menuItemArray'];
        
        /* Checking the count of level-1 menu items */
        $this->assertEquals(2, count($menuArray));     
        
        /* Checking order and eligible items */
        $this->assertEquals('Leave', $menuArray[0]['menuTitle']);
        $this->assertEquals('My Info', $menuArray[1]['menuTitle']);
        
    }
    
    public function testGetMenuItemDetailsForSupervisor() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('ESS');
        $userRoleList[1] = new UserRole();
        $userRoleList[1]->setName('Supervisor');         
        
        $menuDetails = $this->menuService->getMenuItemDetails($userRoleList);
        $menuArray = $menuDetails['menuItemArray'];
        
        /* Checking the count of level-1 menu items */
        $this->assertEquals(3, count($menuArray));     
        
        /* Checking order and eligible items */
        $this->assertEquals('PIM', $menuArray[0]['menuTitle']);
        $this->assertEquals('Leave', $menuArray[1]['menuTitle']);
        $this->assertEquals('My Info', $menuArray[2]['menuTitle']);
        
        $pimSubMenus = $menuArray[0]['subMenuItems'];
        $this->assertEquals('Employee List', $pimSubMenus[0]['menuTitle']);
        
    }   
    
    public function testGetMenuItemsDetailsForAdminAndEss() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('ESS');
        $userRoleList[1] = new UserRole();
        $userRoleList[1]->setName('Admin');         
        
        $menuDetails = $this->menuService->getMenuItemDetails($userRoleList);
        $menuArray = $menuDetails['menuItemArray'];
        
        /* Checking the count of level-1 menu items */
        $this->assertEquals(4, count($menuArray));     
        
        /* Checking order and eligible items */
        $this->assertEquals('Admin', $menuArray[0]['menuTitle']);
        $this->assertEquals('PIM', $menuArray[1]['menuTitle']);
        $this->assertEquals('Leave', $menuArray[2]['menuTitle']);
        $this->assertEquals('My Info', $menuArray[3]['menuTitle']);
        
        $adminSubMenus = $menuArray[0]['subMenuItems'];
        $this->assertEquals('Organization', $adminSubMenus[0]['menuTitle']);
        
    }    
 
}

