<?php



require_once ROOT_PATH . '/symfony/lib/vendor/symfony/lib/helper/TagHelper.php';

/**
 * Test class for Button.
 * @group Core
 * @group ListComponent
 */
class ButtonTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Button
     */
    protected $button;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->button = new Button;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    public function test__toString_WithoutProperties() {
        $this->button->setIdentifier('Save');
        $expectedAttributes = array(
            'type="button"',
            'id="btnSave"',
            'name="btnSave"',
            'value="Save"',
        );
        $html = $this->button->__toString();

        foreach ($expectedAttributes as $attribute) {
            $this->assertRegExp("/{$attribute}/", $html);
        }
    }

    public function test__toString_WithProperties() {
        $this->button->setIdentifier('Search_Button');
        $this->button->setProperties(array(
            'label' => 'Search',
            'id' => 'cmdSearch',
            'class' => 'longbtn',
            'type' => 'submit',
            'name' => '_search',
        ));
        $expectedAttributes = array(
            'type="submit"',
            'id="cmdSearch"',
            'name="_search"',
            'value="Search"',
        );
        $html = $this->button->__toString();

        foreach ($expectedAttributes as $attribute) {
            $this->assertRegExp("/{$attribute}/", $html);
        }
    }
    
    public function testPopulateFromArray() {
        $properties = array(
            'identifier' => 'btnSave',
        );

        $this->button->populateFromArray($properties);

        $this->assertEquals($properties['identifier'], $this->button->getIdentifier());
    }
    
    public function testGetProperties() {
        $properties = array(
            'identifier' => 'btnSave',
        );
        $this->button->setProperties($properties);
        $this->assertEquals($properties, $this->button->getProperties());
    }
    
    public function testSetProperties() {
        $properties = array(
            'identifier' => 'btnSave',
        );
        $this->button->setProperties($properties);
        $this->assertEquals($properties, $this->button->getProperties());        
    }

}

?>
