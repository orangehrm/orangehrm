<?php



/**
 * Test class for ListHeader.
 * @group Core
 * @group ListComponent
 */
class ListHeaderTest extends PHPUnit_Framework_TestCase {

    /**
     * @var ListHeader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ListHeader;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    public function testGetName() {
        $name = 'Name';
        $this->object->setName($name);
        $this->assertEquals($name, $this->object->getName());

        $name = 'Date (yyyy-mm-dd)';
        $this->object->setName($name);
        $this->assertEquals($name, $this->object->getName());

        $name = 'Sort Order';
        $this->object->setName($name);
        $this->assertEquals($name, $this->object->getName());
    }

    public function testSetName() {
        $name = 'Name';
        $this->object->setName($name);
        $this->assertEquals($name, $this->object->getName());
    }

    public function testIsSortable() {
        $this->assertFalse($this->object->isSortable());

        $this->object->isSortable(true);
        $this->assertTrue($this->object->isSortable());

        $this->object->isSortable(1);
        $this->assertTrue($this->object->isSortable());

        $this->object->isSortable('1');
        $this->assertTrue($this->object->isSortable());

        $this->object->isSortable('abc');
        $this->assertTrue($this->object->isSortable());

        $this->object->isSortable(0);
        $this->assertFalse($this->object->isSortable());

        $this->object->isSortable('');
        $this->assertFalse($this->object->isSortable());
    }

    public function testGetSortOrder() {
        $sortOrder = 'ASC';
        $this->object->setSortOrder($sortOrder);
        $this->assertEquals($sortOrder, $this->object->getSortOrder());
    }

    public function testSetSortOrder_Default() {
        $sortOrder = 'ASC';
        $this->object->setSortOrder($sortOrder);
        $this->assertEquals($sortOrder, $this->object->getSortOrder());

        $sortOrder = 'DESC';
        $this->object->setSortOrder($sortOrder);
        $this->assertEquals($sortOrder, $this->object->getSortOrder());
    }

    /**
     * @expectedException ListHeaderException
     */
    public function testSetSortOrder_Invalid() {
        $sortOrder = 'abc';
        $this->object->setSortOrder($sortOrder);
    }
    
    public function testGetSortField() {
        $sortField = 'id';
        $this->object->setSortField($sortField);
        $this->assertEquals($sortField, $this->object->getSortField());
    }
    
    public function testSetSortField_Default() {
        $sortField = 'id';
        $this->object->setSortField($sortField);
        $this->assertEquals($sortField, $this->object->getSortField());
    }
    
    /**
     * @expectedException ListHeaderException
     */
    public function testSetSortField_Numeric() {
        $this->object->setSortField(123.12);
    }

    /**
     * @expectedException ListHeaderException
     */
    public function testSetSortField_IvalidString() {
        $this->object->setSortField(123);
    }

    public function testGetElementType() {
        $this->assertEquals('label', $this->object->getElementType());

        $elementType = 'textbox';
        $this->object->setElementType($elementType);
        $this->assertEquals($elementType, $this->object->getElementType());
    }

    public function testSetElementType_Default() {
        $elementType = 'textbox';
        $this->object->setElementType($elementType);
        $this->assertEquals($elementType, $this->object->getElementType());
    }

    /**
     * @expectedException ListHeaderException
     */
    public function testSetElementType_UnsupportedType() {
        $elementType = 'circle';
        $this->object->setElementType($elementType);
    }

    public function testGetElementProperty() {
        $elementProperty = 'John Smith';
        $this->object->setElementProperty($elementProperty);
        $this->assertEquals($elementProperty, $this->object->getElementProperty());
    }

    public function testSetElementProperty() {
        $elementProperty = null;
        $this->object->setElementProperty($elementProperty);
        $this->assertEquals($elementProperty, $this->object->getElementProperty());

        $elementProperty = '';
        $this->object->setElementProperty($elementProperty);
        $this->assertEquals($elementProperty, $this->object->getElementProperty());

        $elementProperty = 'John Smith';
        $this->object->setElementProperty($elementProperty);
        $this->assertEquals($elementProperty, $this->object->getElementProperty());

        $elementProperty = array('John Smith', 'Mary Johns', 'Bob Parker');
        $this->object->setElementProperty($elementProperty);
        $this->assertEquals($elementProperty, $this->object->getElementProperty());

        $elementProperty = new stdClass();
        $this->object->setElementProperty($elementProperty);
        $this->assertEquals($elementProperty, $this->object->getElementProperty());

        $elementProperty = new stdClass();
        $elementProperty->name = 'John Smith';
        $elementProperty->age = 45;
        $this->object->setElementProperty($elementProperty);
        $this->assertEquals($elementProperty, $this->object->getElementProperty());

        $object1 = new stdClass();
        $object1->name = 'John Smith';
        $object1->age = 45;

        $object2 = new stdClass();
        $object2->name = 'Mary Johns';
        $object2->age = 45;

        $elementProperty = array($object1, $object2);
        $this->object->setElementProperty($elementProperty);
        $this->assertEquals($elementProperty, $this->object->getElementProperty());
    }

    public function testGetWidth() {
        $width = 400;
        $this->object->setWidth($width);
        $this->assertEquals($width, $this->object->getWidth());
    }

    public function testSetWidth_Default() {
        $width = 400;
        $this->object->setWidth($width);
        $this->assertEquals($width, $this->object->getWidth());

        $width = '200';
        $this->object->setWidth($width);
        $this->assertEquals($width, $this->object->getWidth());

        $width = '10%';
        $this->object->setWidth($width);
        $this->assertEquals($width, $this->object->getWidth());
    }

    /**
     * @expectedException ListHeaderException
     */
    public function testSetWidth_Invalid_Chars() {
        $width = 'abcd';
        $this->object->setWidth($width);
    }

    /**
     * @expectedException ListHeaderException
     */
    public function testSetWidth_Invalid_MixedAlphaNumeric() {
        $width = '10cd';
        $this->object->setWidth($width);
    }

    /**
     * @expectedException ListHeaderException
     */
    public function testSetWidth_Invalid_PercentageFormat() {
        $width = '10&';
        $this->object->setWidth($width);
    }

    public function testGetElementTypes() {
        $elementTypes = $this->object->getElementTypes();
        $this->assertTrue(is_array($elementTypes));
        $this->assertFalse(empty($elementTypes));
        $this->assertContains('label', $elementTypes);
        $this->assertContains('link', $elementTypes);
    }

    public function testPopulateFromArray() {
        $properties = array(
            'name' => 'Full Name',
            'isSortable' => true,
            'sortOrder' => 'DESC',
            'elementType' => 'link',
            'elementProperty' => 'getFullName',
            'width' => 100,
        );

        $this->object->populateFromArray($properties);

        $this->assertEquals($properties['name'], $this->object->getName());
        $this->assertEquals($properties['isSortable'], $this->object->isSortable());
        $this->assertEquals($properties['elementType'], $this->object->getElementType());
        $this->assertEquals($properties['elementProperty'], $this->object->getElementProperty());
        $this->assertEquals($properties['width'], $this->object->getWidth());
    }
    
    public function testIsExportable() {
        $this->assertTrue($this->object->isExportable());

        $this->object->isExportable(true);
        $this->assertTrue($this->object->isExportable());

        $this->object->isExportable(1);
        $this->assertTrue($this->object->isExportable());

        $this->object->isExportable('1');
        $this->assertTrue($this->object->isExportable());

        $this->object->isExportable('abc');
        $this->assertTrue($this->object->isExportable());

        $this->object->isExportable(0);
        $this->assertFalse($this->object->isExportable());

        $this->object->isExportable('');
        $this->assertFalse($this->object->isExportable());
    }

    public function testGetTextAlignmentStyle() {
        $textAlignmentStyle = 'center';
        $this->object->setTextAlignmentStyle($textAlignmentStyle);
        $this->assertEquals($textAlignmentStyle, $this->object->getTextAlignmentStyle());
    }
 
    public function testSetTextAlignmentStyle() {
        $textAlignmentStyle = 'left';
        $this->object->setTextAlignmentStyle($textAlignmentStyle);
        $this->assertEquals($textAlignmentStyle, $this->object->getTextAlignmentStyle());
        
        $textAlignmentStyle = 'right';
        $this->object->setTextAlignmentStyle($textAlignmentStyle);
        $this->assertEquals($textAlignmentStyle, $this->object->getTextAlignmentStyle());

        $textAlignmentStyle = 'center';
        $this->object->setTextAlignmentStyle($textAlignmentStyle);
        $this->assertEquals($textAlignmentStyle, $this->object->getTextAlignmentStyle());
    }

    /**
     * @expectedException ListHeaderException
     */
    public function testSetTextAlignmentStyle_UnsupportedType() {
        $textAlignmentStyle = 'bottom';
        $this->object->setTextAlignmentStyle($textAlignmentStyle);
    }
    
    public function testSetFilters() {
        
        // set invalid value, check that it is not set        
        $this->object->setFilters("test");
        $this->assertEquals(array(), $this->object->getFilters());
        
        $this->object->setFilters(null);
        $this->assertEquals(array(), $this->object->getFilters());

        $filters = array('EnumCellFilter' => array(), 'I18nCellFilter' => array());
        
        $this->object->setFilters($filters);
        $this->assertEquals($filters, $this->object->getFilters());
        
        // verify that setFilters overwrites existing filters:
        $this->object->setFilters(null);
        $this->assertEquals(array(), $this->object->getFilters());
        
    }
    
    public function testFilterValue() {
        
        // No filters
        $srcValue = 'test value';
        
        $this->assertEquals($srcValue, $this->object->filterValue($srcValue));  
        
        $this->object->setFilters(array());        
        $this->assertEquals($srcValue, $this->object->filterValue($srcValue));        
                        
        $x = new ucTestCellFilter;
        
        // one filter        
        $this->object->setFilters(array('ucTestCellFilter' => array()));
        $this->assertEquals('TEST VALUE', $this->object->filterValue($srcValue));
                
        // two filters
        $this->object->setFilters(array('ucTestCellFilter' => array(), 
                                        'reverseTestCellFilter' => array()));
        $this->assertEquals('EULAV TSET', $this->object->filterValue($srcValue));        
        
        // three filters, one using properties
        $this->object->setFilters(array('ucTestCellFilter' => array(), 
                                        'constTestCellFilter' => array('const' => 'XyZ'),
                                        'reverseTestCellFilter' => array()));
        $this->assertEquals('ZyXEULAV TSET', $this->object->filterValue($srcValue)); 
        
    }

}

class ucTestCellFilter extends ohrmCellFilter {
    
    public function filter($value) {
        return strtoupper($value);
    }
    
}

class reverseTestCellFilter extends ohrmCellFilter {
    
    public function filter($value) {
        
        return strrev($value);
    }
    
}

class constTestCellFilter extends ohrmCellFilter {
    
    private $const = "";
    
    public function filter($value) {
        
        return $value . $this->const;
    }
    
    public function setConst($const) {
        $this->const = $const;
    }
    
}