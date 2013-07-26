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
    
    public function testGetParsedPropertyValuePlain() {
        $name = 'id';
        $value = 'ohrmList_chkSelectRecord';
        $properties = array($name => $value);
        $this->cell->setProperties($properties);
        $result = $this->cell->getParsedPropertyValue($name);
        $this->assertEquals($value, $result);        
    }
    
    public function testGetParsedPropertyValueWithGetterDataObject() {
        $name = 'id';
        $value = 'ohrmList_chkSelectRecord';
        $properties = array('idGetter' => 'getId');
        
        $dataObject = new TestDataObject();
        $dataObject->setId($value);
        
        $this->cell->setProperties($properties);        
        $this->cell->setDataObject($dataObject);
        
        $result = $this->cell->getParsedPropertyValue($name);
        $this->assertEquals($value, $result);         
        
    }
    
    public function testGetParsedPropertyValueWithGetterPropertyDataObject() {
        $name = 'id';
        $value = 'ohrmList_chkSelectRecord';
        $properties = array('idGetter' => 'id');
        
        $dataObject = new stdClass();
        $dataObject->id = $value;
        
        $this->cell->setProperties($properties);        
        $this->cell->setDataObject($dataObject);
        
        $result = $this->cell->getParsedPropertyValue($name);
        $this->assertEquals($value, $result);                 
    }    
    
    public function testGetParsedPropertyValueWithGetterDataArray() {
        $name = 'id';
        $value = 'ohrmList_chkSelectRecord';
        $properties = array('idGetter' => 'test_id');
        
        $dataObject = array('test_id' => $value);
        
        $this->cell->setProperties($properties);        
        $this->cell->setDataObject($dataObject);
        
        $result = $this->cell->getParsedPropertyValue($name);
        $this->assertEquals($value, $result);                 
    }
    
    public function testGetParsedPropertyValueWithPlaceHolders() {

        $properties = array(
            'id' => 'ohrmList_chkSelectRecord_{id}',
            'placeholderGetters' => array('id' => 'id')
            );
        
        $dataObject = new stdClass();
        $dataObject->id = 'xyz';
        
        $this->cell->setDataObject($dataObject);
        
        $this->cell->setProperties($properties);
        $result = $this->cell->getParsedPropertyValue('id');
        $this->assertEquals('ohrmList_chkSelectRecord_xyz', $result);             
    }    
    
    public function testGetParsedPropertyValueWithManyPlaceHolders() {

        $properties = array(
            'id' => 'ohrmList_{age}{mf}SelectRecord_{id}',
            'placeholderGetters' => array(
                'id' => 'getId',
                'age' => 'getAge',
                'mf' => 'getMf')
        );

        $dataObject = new TestDataObject();
        $dataObject->setId('abcd');
        $dataObject->setAge(33);
        $dataObject->setMf('M');
        
        $expected = 'ohrmList_33MSelectRecord_abcd';
        
        $this->cell->setDataObject($dataObject);        
        $this->cell->setProperties($properties);
        
        $result = $this->cell->getParsedPropertyValue('id');
        
        $this->assertEquals($expected, $result);             
    }    
    
    public function testGetParsedPropertyValueMultipleValues() {

        $properties = array(
            'id' => 'ohrmList_chkSelectRecord_{id}',
            'name' => 'chkSelectRow[]',
            'valueGetter' => 'getId',
            'label' => 'Enable',
            'placeholderGetters' => array('id' => 'getId'),
        );

        $dataObject = new TestDataObject();
        $dataObject->setId(2);
        
        $this->cell->setDataObject($dataObject);        
        $this->cell->setProperties($properties);        

        $id = $this->cell->getParsedPropertyValue('id');
        $this->assertEquals('ohrmList_chkSelectRecord_2', $id);
        
        $name = $this->cell->getParsedPropertyValue('name');
        $this->assertEquals('chkSelectRow[]', $name);
        
        $value = $this->cell->getParsedPropertyValue('value');
        $this->assertEquals('2', $value);
        
        $labelName = $this->cell->getParsedPropertyValue('label');
        $this->assertEquals('Enable', $labelName);
        
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

class TestDataObject {
    
    protected $id;
    protected $age;
    protected $mf;
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }
    public function getAge() {
        return $this->age;
    }

    public function setAge($age) {
        $this->age = $age;
    }

    public function getMf() {
        return $this->mf;
    }

    public function setMf($mf) {
        $this->mf = $mf;
    }

}
