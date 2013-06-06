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
 * Test class for Cell.
 * @group Core
 * @group ListComponent
 */
class CellTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ListHeader
     */
    protected $cell;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->cell = new TestConcreteCell();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    /** 
     * Tests the setHeader and getHeader methods.
     */
    public function testSetHeader() {
        
        // Simple object
        $header = new ListHeader();
        
        $this->cell->setHeader($header);       
        $this->assertEquals($header, $this->cell->getHeader());
        
        // decorated with sfOutputEscaperObjectDecorator
        $header2 = new ListHeader();
        $header2->setName("Test Header");
        $decoratedHeader = new sfOutputEscaperObjectDecorator(null, $header2);
        
        $this->cell->setHeader($decoratedHeader);
        $this->assertEquals($header2, $this->cell->getHeader());        

    }

    /**
     * Test the filterValue() method.
     */
    public function testFilterValue() {
        
        $value = "Test Value";
        $filteredValue = "XYZ Test";
        
        $mockHeader = $this->getMock('ListHeader', array('filterValue'));
        $mockHeader->expects($this->once())
                     ->method('filterValue')
                     ->with($value)                
                     ->will($this->returnValue($filteredValue));
        
        $this->cell->setHeader($mockHeader); 
        $this->assertEquals($filteredValue, $this->cell->publicFilter($value));         
    }
    
}

class TestConcreteCell extends Cell {
    
    /**
     * Expose the filterValue method for testing
     */
    public function publicFilter($value) {
        return $this->filterValue($value);
    }
    
}

