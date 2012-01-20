<?php



/**
 * Test class for SortableHeaderCell.
 * @group Core
 * @group ListComponent
 */
class SortableHeaderCellTest extends PHPUnit_Framework_TestCase {

    /**
     * @var SortableHeaderCell
     */
    protected $sortableHeaderCell;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->sortableHeaderCell = new SortableHeaderCell;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    public function test__toString() {
        $expectedHtml = '<a href="#" class="null">Heading</a>';
        $this->assertEquals($expectedHtml, $this->sortableHeaderCell->__toString());

        $this->sortableHeaderCell->setProperties(array(
            'label' => 'Column 1',
            'sortUrl' => 'index.php?sort=ASC',
            'currentSortOrder' => 'DESC',
        ));
        $expectedHtml = '<a href="index.php?sort=ASC" class="DESC">Column 1</a>';
        $this->assertEquals($expectedHtml, $this->sortableHeaderCell->__toString());

    }

}

?>
