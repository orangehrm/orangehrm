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
 * Test class for ohrmCellFilter abstract class
 * @group Core
 * @group ListComponent
 */
class EnumCellFilterTest extends PHPUnit_Framework_TestCase {

    /**
     * @var filter
     */
    protected $filter;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->filter = new EnumCellFilter();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }
    
    /**
     * Test the filter() method.
     */
    public function testFilter() {
        
        // Without enum. Should return default 'default': ''
        $value = 4;
        $this->assertEquals('', $this->filter->filter($value));
        
        // With enum, but without that value
        $this->filter->setEnum(array(1 => "Xyz", 2 => "basic"));        
        $this->assertEquals('', $this->filter->filter($value));
        
        // With enum, without that value, with default defined.
        $default = "-";
        $this->filter->setDefault($default);
        $this->assertEquals($default, $this->filter->filter($value));
        
        // With enum which includes given value
        $this->filter->setEnum(array(1 => "Xyz", 2 => "basic", 4 => 'OK', 5 => 'NOK'));
        $this->assertEquals('OK', $this->filter->filter($value));
        
    }
    
    /**
     * Test the get/set methods
     */
    public function testGetSetMethods() {
        $filter = array('2' => "test", "4" => "xyz");
        $this->filter->setEnum($filter);
        $this->assertEquals($filter, $this->filter->getEnum());
        
        $default = 'Z1';
        $this->filter->setDefault($default);
        $this->assertEquals($default, $this->filter->getDefault());        
    }

}





