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
 *
 */

/**
 * Description of DataGroupServiceTest
 * @group Core
 */
class DataGroupServiceTest extends PHPUnit_Framework_TestCase {

    private $service;
    
    /**
     * Set up method
     */
    protected function setUp() {
        $this->service = new DataGroupService();        
    }
    
    public function testGetDataGroupPermission() {
        $dataGroupPermission = new DataGroupPermission();
        $dataGroupPermission->fromArray(array('id' => 2, 'user_role_id' => 1, 'data_group_id' => 1,
            'can_read' => 1, 'can_create' => 1, 'can_update' => 1, 'can_delete' => 1, 'self' => 1));

        $dao = $this->getMock('DataGroupDao', array('getDataGroupPermission'));
        $dao->expects($this->once())
                    ->method('getDataGroupPermission')
                    ->with('test', 2, true)
                    ->will($this->returnValue($dataGroupPermission));
        
        $this->service->setDao($dao);
        $result = $this->service->getDataGroupPermission('test', 2, true);
        $this->assertEquals($dataGroupPermission, $result);
        
    }
    
    public function testGetDataGroups() {
        $expected = new Doctrine_Collection('DataGroup');        

        $dao = $this->getMock('DataGroupDao', array('getDataGroups'));
        $dao->expects($this->once())
                    ->method('getDataGroups')
                    ->will($this->returnValue($expected));
        
        $this->service->setDao($dao);
        $result = $this->service->getDataGroups();
        $this->assertEquals($expected, $result);
    }    
    
    public function testGetDataGroup() {
        $expected = new DataGroup();
        $expected->fromArray(array('id' => 2, 'can_read' => 1, 'can_create' => 1, 'can_update' => 1, 'can_delete' => 1));

        $dao = $this->getMock('DataGroupDao', array('getDataGroup'));
        $dao->expects($this->once())
                    ->method('getDataGroup')
                    ->with('xyz')
                    ->will($this->returnValue($expected));
        
        $this->service->setDao($dao);
        $result = $this->service->getDataGroup('xyz');
        $this->assertEquals($expected, $result);        
    }

}
