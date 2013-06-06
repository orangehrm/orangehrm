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
 * Description of DataGroupDaoTest
 * @group Core
 */
class DataGroupDaoTest extends PHPUnit_Framework_TestCase {
    
    /** @property ScreenPermissionDao $dao */
    private $dao;
    
    /**
     * Set up method
     */
    protected function setUp() {        
        $this->fixture = sfConfig::get('sf_plugins_dir') . '/orangehrmCorePlugin/test/fixtures/DataGroupDao.yml';
        TestDataService::truncateSpecificTables(array('SystemUser'));
        TestDataService::populate($this->fixture);
                
        $this->dao = new DataGroupDao();
    }
    
    
    public function testGetDataGroupPermission(){ 
        $permissions = $this->dao->getDataGroupPermission('pim_1',1);
        $this->assertEquals(1, $permissions->count());
        $this->assertEquals(1,$permissions[0]->getCanRead());
    
    }
    
    public function testGetDataGroups(){
        $this->assertEquals(4,sizeof($this->dao->getDataGroups()));    
    }    
    
    public function testGetDataGroupsNoneDefined(){
        $pdo = Doctrine_Manager::connection()->getDbh();
        $pdo->exec('DELETE FROM ohrm_data_group');
        $this->assertEquals(0, sizeof($this->dao->getDataGroups()));    
    }    
    

    public function testGetDataGroup() {
        $dataGroup1 = $this->dao->getDataGroup('pim_1');
        $this->assertTrue($dataGroup1 instanceof DataGroup);
        $this->assertEquals(1, $dataGroup1->getId());
        
        $dataGroup2 = $this->dao->getDataGroup('pim_2');
        $this->assertTrue($dataGroup2 instanceof DataGroup);
        $this->assertEquals(2, $dataGroup2->getId());
        
    }
    
    public function testGetDataGroupInvalid() {
        $dataGroup = $this->dao->getDataGroup('xyz_not_exist');
        $this->assertTrue($dataGroup === false);
    }
}


