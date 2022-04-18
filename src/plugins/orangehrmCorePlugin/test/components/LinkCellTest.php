<?php



/**
 * Test class for LinkCell.
 * @group Core
 * @group ListComponent
 */
class LinkCellTest extends PHPUnit_Framework_TestCase {

    /**
     * @var LinkCell
     */
    protected $linkCell;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->linkCell = new LinkCell;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    /**
     * @todo Implement test__toString().
     */
    public function test__toString() {
        $dataObject = new LinkCellTestDataObject();

        $this->linkCell->setDataObject($dataObject);
        $this->linkCell->setProperties(array(
            'labelGetter' => 'getLabel',
            'placeholderGetters' => array(
                'id' => 'getId',
                'status' => 'getCurrentState',
            ),
            'urlPattern' => 'index.php?id={id}&amp;status={status}'
        ));

        $expectedLink = '<a href="http://' . $_SERVER['PHP_SELF'] . '/index.php?id=1&amp;status=active">Label</a>';
        $this->assertEquals($expectedLink, $this->linkCell->__toString());
    }

    public function testPopulateFromArray() {
        $properties = array(
            'dataObject' => new stdClass(),
        );

        $this->linkCell->populateFromArray($properties);

        $this->assertEquals($properties['dataObject'], $this->linkCell->getDataObject());
    }
    
    public function testGetProperties() {
        $properties = array(
            'dataObject' => new stdClass(),
        );
        $this->linkCell->setProperties($properties);
        $this->assertEquals($properties, $this->linkCell->getProperties());
    }
    
    public function testSetProperties() {
        $properties = array(
            'dataObject' => new stdClass(),
        );
        $this->linkCell->setProperties($properties);
        $this->assertEquals($properties, $this->linkCell->getProperties());        
    }
    
    public function testUnlinkableCells() {
        $dataObject = new LinkCellTestDataObject();

        $this->linkCell->setDataObject($dataObject);
        $this->linkCell->setProperties(array(
            'labelGetter' => 'getLabel',
            'linkable' => false,
            'placeholderGetters' => array(
                'id' => 'getId',
                'status' => 'getCurrentState',
            ),
            'urlPattern' => 'index.php?id={id}&amp;status={status}'
        ));

        $expectedLink = 'Label';
        $this->assertEquals($expectedLink, $this->linkCell->__toString());
    }

        public function testConditionalLinkableCells() {
        $dataObject = new LinkCellTestDataObject();
        $conditionalParams = new sfOutputEscaperArrayDecorator('htmlentities', array(3));
        
        $this->linkCell->setDataObject($dataObject);
        $this->linkCell->setProperties(array(
            'labelGetter' => 'getLabel',
            'linkable' => array('isEven', $conditionalParams),
            'placeholderGetters' => array(
                'id' => 'getId',
                'status' => 'getCurrentState',
            ),
            'urlPattern' => 'index.php?id={id}&amp;status={status}'
        ));

        $expectedLink = 'Label';
        $this->assertEquals($expectedLink, $this->linkCell->__toString());
        
        $conditionalParams = new sfOutputEscaperArrayDecorator('htmlentities', array(2));
        $this->linkCell->setProperties(array(
            'labelGetter' => 'getLabel',
            'linkable' => array('isEven', $conditionalParams),
            'placeholderGetters' => array(
                'id' => 'getId',
                'status' => 'getCurrentState',
            ),
            'urlPattern' => 'index.php?id={id}&amp;status={status}'
        ));
        $expectedLink = '<a href="http://' . $_SERVER['PHP_SELF'] . '/index.php?id=1&amp;status=active">Label</a>';
        $this->assertEquals($expectedLink, $this->linkCell->__toString());
    }

}

class LinkCellTestDataObject {

    public function getLabel() {
        return 'Label';
    }

    public function getId() {
        return 1;
    }

    public function getCurrentState() {
        return 'active';
    }
    
    public function isEven($number) {
        return ($number % 2 === 0);
    }

}

?>
