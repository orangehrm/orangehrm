<?php



/**
 * Test class for HeaderCell.
 * @group Core
 * @group ListComponent
 */
class HeaderCellTest extends PHPUnit_Framework_TestCase {

    /**
     * @var HeaderCell
     */
    protected $headerCell;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->headerCell = new HeaderCell;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    public function test__toString() {
        $this->assertEquals('<span class="headerCell">Heading</span>', $this->headerCell->__toString());

        $this->headerCell->setProperties(array('label' => 'First Name'));
        $this->assertEquals('<span class="headerCell">First Name</span>', $this->headerCell->__toString());
    }

}

?>
