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

// Call ExtractorViewListTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ExtractorViewListTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";
require_once ROOT_PATH."/lib/confs/sysConf.php";
require_once ROOT_PATH."/lib/common/SearchObject.php";
require_once ROOT_PATH."/lib/extractor/recruitment/EXTRACTOR_ViewList.php";

/**
 * Test class for EXTRACTOR_ViewList.php
 */
class ExtractorViewListTest extends PHPUnit_Framework_TestCase {

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("ExtractorViewListTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, making sure table is empty and creating database
     * entries needed during test.
     *
     * @access protected
     */
    protected function setUp() {

    }

    /**
     * Tears down the fixture, removed database entries created during test.
     *
     * @access protected
     */
    protected function tearDown() {
    }

	/**
	 * test the parseSearchData function
	 */
	public function testParseSearchData() {

		$extractor = new EXTRACTOR_ViewList();

		// No parameters - default settings
		$get = array();
		$post = array();
		$searchObj = $extractor->parseSearchData($post, $get);

		$expected = $this->_getDefaultSearchObject();
		$this->assertEquals($expected, $searchObj);

		// With search parameters
		$post = array('pageNO'=>'2', 'captureState'=>'SearchMode', 'loc_code'=>'2', 'loc_name'=>'XYZ');
		$searchObj = $extractor->parseSearchData($post, $get);

		$expected = $this->_getDefaultSearchObject();
		$expected->setPageNumber(2);
		$expected->setSearchField(2);
		$expected->setSearchString('XYZ');

		$this->assertEquals($expected, $searchObj);

		// with sort parameters
		$post = array('pageNO'=>'3');
		$get = array('sortField'=>2, 'sortOrder2'=>'DESC');
		$searchObj = $extractor->parseSearchData($post, $get);

		$expected = $this->_getDefaultSearchObject();
		$expected->setPageNumber(3);
		$expected->setSortField(2);
		$expected->setSortOrder(SearchObject::SORT_ORDER_DESC);

		$this->assertEquals($expected, $searchObj);

		// with search and sort parameters
		$post = array('pageNO'=>'2', 'captureState'=>'SearchMode', 'loc_code'=>'2', 'loc_name'=>'XYZ');
		$get = array('sortField'=>2, 'sortOrder2'=>'DESC');
		$searchObj = $extractor->parseSearchData($post, $get);

		$expected = $this->_getDefaultSearchObject();
		$expected->setPageNumber(2);
		$expected->setSearchField(2);
		$expected->setSearchString('XYZ');
		$expected->setSortField(2);
		$expected->setSortOrder(SearchObject::SORT_ORDER_DESC);

		$this->assertEquals($expected, $searchObj);
	}

	private function _getDefaultSearchObject() {
		$searchObj = new SearchObject();
		$searchObj->setPageNumber(1);
		$searchObj->setSearchField(SearchObject::SEARCH_FIELD_NONE);
		$searchObj->setSearchString('');
		$searchObj->setSortField(0);
		$searchObj->setSortOrder(SearchObject::SORT_ORDER_ASC);
	    return $searchObj;
	}
}

// Call ExtractorViewListTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ExtractorViewListTest::main") {
    ExtractorViewListTest::main();
}
?>
