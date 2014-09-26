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
 * Description of ScreenPermissionDaoTest
 * @group Core
 */
class ScreenPermissionDaoTest  extends PHPUnit_Framework_TestCase {
    
    /** @property ScreenPermissionDao $dao */
    private $dao;
    
    /**
     * Set up method
     */
    protected function setUp() {        
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/ScreenPermissionDao.yml';
        TestDataService::truncateSpecificTables(array('SystemUser'));
        TestDataService::populate($this->fixture);
                
        $this->dao = new ScreenPermissionDao();
    }
    
    public function testGetScreenPermission() {
        $permissions = $this->dao->getScreenPermissions('pim', 'viewEmployeeList', array('Admin'));
        $this->assertNotNull($permissions);
        $this->assertEquals(1, count($permissions));
        $this->verifyPermissions($permissions[0], true, true, true, true);
       
        
        $permissions = $this->dao->getScreenPermissions('pim', 'viewEmployeeList', array('ESS'));
        $this->assertNotNull($permissions);
        $this->assertEquals(1, count($permissions));
        $this->verifyPermissions($permissions[0], false, false, false, false);
        
        $permissions = $this->dao->getScreenPermissions('pim', 'viewEmployeeList', array('Supervisor'));
        $this->assertNotNull($permissions);
        $this->assertEquals(1, count($permissions));
        $this->verifyPermissions($permissions[0], true, false, true, false);
        
        $permissions = $this->dao->getScreenPermissions('pim', 'viewEmployeeList', array('Admin', 'Supervisor', 'ESS'));
        $this->assertNotNull($permissions);
        $this->assertEquals(3, count($permissions));
        
        foreach($permissions as $permission) {
            $roleId = $permission->getUserRoleId();
            if ($roleId == 1) {
                // Admin
                $this->verifyPermissions($permission, true, true, true, true);
            } else if ($roleId == 2) {
                // Supervisor
                $this->verifyPermissions($permission, false, false, false, false);            
            } else if ($roleId == 3) {
                // ESS
                $this->verifyPermissions($permission, true, false, true, false);    
            } else {
                $this->fail("Unexpected roleId=" . $roleId);
            }
        }
        
        $permissions = $this->dao->getScreenPermissions('pim', 'viewEmployeeListNoneExisting', array('Admin', 'Supervisor', 'ESS'));
        $this->assertTrue($permissions instanceof Doctrine_Collection);
        $this->assertEquals(0, count($permissions));
        
    }
    
    protected function verifyPermissions($permission, $read, $create, $update, $delete) {
        $this->assertEquals($read, $permission->can_read);
        $this->assertEquals($create, $permission->can_create);
        $this->assertEquals($update, $permission->can_update);
        $this->assertEquals($delete, $permission->can_delete);        
    }
}

