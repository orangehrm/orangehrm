<?php



/**
 * Test class for LabelCell.
 * @group Core
 * @group ListComponent
 */
class LabelCellTest extends PHPUnit_Framework_TestCase {

    /**
     * @var LabelCell
     */
    protected $labelCell;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->labelCell = new LabelCell;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    public function test__toString() {
        $dataObject = new LabelCellTestDataObject();
        $dataObject->getName = 'Kayla Abbey';

        $this->labelCell->setDataObject($dataObject);
        $this->labelCell->setProperties(array('getter' => 'getDescription'));

        $this->assertEquals('Sample class', $this->labelCell->__toString());
    }

    public function testToValue() {
        $dataObject = new LabelCellTestDataObject();

        $this->labelCell->setDataObject($dataObject);
        $this->labelCell->setProperties(array('getter' => 'getDescription'));

        $this->assertEquals('Sample class', $this->labelCell->toValue());
    }

    public function testGetterChain() {
        $dataObject = new LabelCellTestDataObject();

        $this->labelCell->setDataObject($dataObject);
        $this->labelCell->setProperties(array('getter' => array('getObject', 'getDescription')));

        $this->assertEquals('Sample class', $this->labelCell->toValue());
    }

    public function testCellshHiddenField() {
        $dataObject = new LabelCellTestDataObject();

        $this->labelCell->setDataObject($dataObject);
        $this->labelCell->setProperties(array(
            'getter' => 'getDescription',
            'hasHiddenField' => true,
            'hiddenFieldName' => 'hdnTest',
            'hiddenFieldId' => 'hdnTest[]',
            'hiddenFieldValueGetter' => array('getObject', 'getDescription'),
        ));

        $this->assertEquals('Sample class<input type="hidden" name="hdnTest" id="hdnTest[]" class="" value="Sample class" />', $this->labelCell->__toString());
    }

    public function testCellshPlaceholderGetters() {
        $dataObject = new LabelCellTestDataObject();

        $this->labelCell->setDataObject($dataObject);
        $this->labelCell->setProperties(array(
            'getter' => 'getDescription',
            'hasHiddenField' => true,
            'placeholderGetters' => array('id' => 'getId'),
            'hiddenFieldName' => 'hdnTest',
            'hiddenFieldId' => 'hdnTest[{id}]',
            'hiddenFieldValueGetter' => array('getObject', 'getDescription'),
        ));

        $xpectedOutput = 'Sample class<input type="hidden" name="hdnTest" id="hdnTest[1]" class="" value="Sample class" />';
        $this->assertEquals($xpectedOutput, $this->labelCell->__toString());
    }

    public function testToStringWithArrayDataSource() {
        $dataSource = array('name' => 'Kayla Abbey', 'age' => 25);

        $this->labelCell->setDataObject($dataSource);

        $this->labelCell->setProperties(array('getter' => 'name'));
        $this->assertEquals('Kayla Abbey', $this->labelCell->__toString());

        $this->labelCell->setProperties(array('getter' => 'age'));
        $this->assertEquals('25', $this->labelCell->__toString());

        $dataSource = array('Kayla Abbey', 25);

        $this->labelCell->setDataObject($dataSource);

        $this->labelCell->setProperties(array('getter' => 0));
        $this->assertEquals('Kayla Abbey', $this->labelCell->__toString());

        $this->labelCell->setProperties(array('getter' => 1));
        $this->assertEquals('25', $this->labelCell->__toString());
    }

    public function testToStringWithDecoratedArrayDataSource() {
        $dataSource = new sfOutputEscaperArrayDecorator('htmlentities', array('name' => 'Kayla Abbey', 'age' => 25));

        $this->labelCell->setDataObject($dataSource);

        $this->labelCell->setProperties(array('getter' => 'name'));
        $this->assertEquals('Kayla Abbey', $this->labelCell->__toString());

        $this->labelCell->setProperties(array('getter' => 'age'));
        $this->assertEquals('25', $this->labelCell->__toString());

        $dataSource = new sfOutputEscaperArrayDecorator('htmlentities', array('Kayla Abbey', 25));

        $this->labelCell->setDataObject($dataSource);

        $this->labelCell->setProperties(array('getter' => 0));
        $this->assertEquals('Kayla Abbey', $this->labelCell->__toString());

        $this->labelCell->setProperties(array('getter' => 1));
        $this->assertEquals('25', $this->labelCell->__toString());
    }

}

class LabelCellTestDataObject {

    public function getId() {
        return 1;
    }

    public function getDescription() {
        return 'Sample class';
    }

    public function getObject() {
        return new self;
    }

}

?>
