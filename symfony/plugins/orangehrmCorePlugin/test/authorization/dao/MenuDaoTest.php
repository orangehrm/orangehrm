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
 * Description of MenuDaoTest
 * @group Core
 */
class MenuDaoTest extends PHPUnit_Framework_TestCase {
    
    private $menuDao;
    
    /**
     * Set up method
     */
    protected function setUp() {        

        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/MenuDao.yml';
        TestDataService::populate($this->fixture);                
        $this->menuDao = new MenuDao();
        
    }
    
    public function testGetMenuItemListForAdmin() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('Admin');
        
        $menuItemList = $this->menuDao->getMenuItemList($userRoleList);
        
        /* Checking the count */
        $this->assertEquals(18, count($menuItemList));
        
        /* Checking the type */
        $this->assertTrue($menuItemList[0] instanceof MenuItem);
        
        /* Checking order */
        $this->assertEquals('Admin', $menuItemList[0]->getMenuTitle());
        $this->assertEquals('Assign Leave', $menuItemList[17]->getMenuTitle());
        
    }
    
    public function testGetMenuItemListForAdminAndEss() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('Admin');
        $userRoleList[1] = new UserRole();
        $userRoleList[1]->setName('ESS');        
        
        $menuItemList = $this->menuDao->getMenuItemList($userRoleList);
        
        /* Checking the count */
        $this->assertEquals(21, count($menuItemList));
        
        /* Checking the type */
        $this->assertTrue($menuItemList[0] instanceof MenuItem);
        
        /* Checking order */
        $this->assertEquals('Admin', $menuItemList[0]->getMenuTitle());
        $this->assertEquals('Assign Leave', $menuItemList[17]->getMenuTitle());
        
    }
    
    public function testGetMenuItemListForSupervisor() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('Supervisor');
        $userRoleList[1] = new UserRole();
        $userRoleList[1]->setName('ESS');        
        
        $menuItemList = $this->menuDao->getMenuItemList($userRoleList);
        
        /* Checking the count */
        $this->assertEquals(12, count($menuItemList));
        
        /* Checking the type */
        $this->assertTrue($menuItemList[0] instanceof MenuItem);
        
        /* Checking order and eligible items.
         * Note that items with screenId null
         * will be available though they are
         * not permitted.
         */
        $this->assertEquals('Admin', $menuItemList[0]->getMenuTitle());
        $this->assertEquals('Organization', $menuItemList[1]->getMenuTitle());
        $this->assertEquals('Configuration', $menuItemList[2]->getMenuTitle());
        $this->assertEquals('Configure', $menuItemList[3]->getMenuTitle());
        $this->assertEquals('PIM', $menuItemList[4]->getMenuTitle());
        $this->assertEquals('Employee List', $menuItemList[5]->getMenuTitle());
        $this->assertEquals('Leave Summary', $menuItemList[6]->getMenuTitle());
        $this->assertEquals('Leave', $menuItemList[7]->getMenuTitle());        
        $this->assertEquals('Leave List', $menuItemList[8]->getMenuTitle());
        $this->assertEquals('My Info', $menuItemList[9]->getMenuTitle());
        $this->assertEquals('My Leave', $menuItemList[10]->getMenuTitle());
        $this->assertEquals('Apply', $menuItemList[11]->getMenuTitle());
        
    }  
    
    public function testGetMenuItemListForEss() {
        
        $userRoleList[0] = new UserRole();
        $userRoleList[0]->setName('ESS');        
        
        $menuItemList = $this->menuDao->getMenuItemList($userRoleList);
        
        /* Checking the count */
        $this->assertEquals(10, count($menuItemList));
        
        /* Checking the type */
        $this->assertTrue($menuItemList[0] instanceof MenuItem);
        
        /* Checking order and eligible items.
         * Note that items with screenId null
         * will be available though they are
         * not permitted.
         */
        $this->assertEquals('Admin', $menuItemList[0]->getMenuTitle());
        $this->assertEquals('Organization', $menuItemList[1]->getMenuTitle());
        $this->assertEquals('Configuration', $menuItemList[2]->getMenuTitle());
        $this->assertEquals('Configure', $menuItemList[3]->getMenuTitle());
        $this->assertEquals('PIM', $menuItemList[4]->getMenuTitle());
        $this->assertEquals('Leave Summary', $menuItemList[5]->getMenuTitle());
        $this->assertEquals('Leave', $menuItemList[6]->getMenuTitle());        
        $this->assertEquals('My Info', $menuItemList[7]->getMenuTitle());
        $this->assertEquals('My Leave', $menuItemList[8]->getMenuTitle());        
        $this->assertEquals('Apply', $menuItemList[9]->getMenuTitle());
        
    }    
    
    public function testEnableModuleMenuItems() {
        $conn = Doctrine_Manager::connection()->getDbh();
        $statement = $conn->prepare('UPDATE ohrm_menu_item SET status = 0 WHERE parent_id IN (12, 13)');
        $this->assertTrue($statement->execute());
        
        // Items with screen_id NULL are not enabled (because they are not linked to a screen and hense to a module)
        $count = $this->menuDao->enableModuleMenuItems('leave');
        $this->assertEquals(7, $count);

        $statement = $conn->prepare('SELECT count(*) FROM ohrm_menu_item WHERE status = 0 AND parent_id IN (12, 13) AND screen_id IS NOT NULL');
        $this->assertTrue($statement->execute()); 
        
        $count = $statement->fetchColumn();        
        $this->assertEquals(0, $count);
    }
    
    public function testEnableModuleMenuItemsByTitle() {
        $conn = Doctrine_Manager::connection()->getDbh();
        $statement = $conn->prepare('UPDATE ohrm_menu_item SET status = 0 WHERE parent_id IN (12, 13)');
        $this->assertTrue($statement->execute());
        
        // Items with screen_id NULL are not enabled (because they are not linked to a screen and hense to a module)
        $count = $this->menuDao->enableModuleMenuItems('leave', array('Leave Types', 'Leave Summary'));
        $this->assertEquals(2, $count);

        $statement = $conn->prepare('SELECT count(*) FROM ohrm_menu_item WHERE status = 0 AND id IN (15,16)');
        $this->assertTrue($statement->execute()); 
        
        $count = $statement->fetchColumn();        
        $this->assertEquals(0, $count);
    }    

}

