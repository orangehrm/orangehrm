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
 * Description of ScreenDaoTest
 * @group Core
 */
class ScreenDaoTest extends PHPUnit_Framework_TestCase {
    
    /** @property ScreenPermissionDao $dao */
    private $dao;
    
    /**
     * Set up method
     */
    protected function setUp() {        
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/ScreenDao.yml';
        TestDataService::truncateSpecificTables(array('SystemUser'));
        TestDataService::populate($this->fixture);
                
        $this->dao = new ScreenDao();
    }
    
    public function testGetScreen() {
        
        $screen = $this->dao->getScreen('pim', 'viewEmployeeList');
        $this->assertNotNull($screen);
        $this->assertEquals(1, $screen->getId());
        $this->assertEquals('employee list', $screen->getName());
        $this->assertEquals(3, $screen->getModuleId());
        $this->assertEquals('viewEmployeeList', $screen->getActionUrl()); 
        
        // non existing action
        $screen = $this->dao->getScreen('pim', 'viewNoneNone');
        $this->assertFalse($screen);               
    }

}


