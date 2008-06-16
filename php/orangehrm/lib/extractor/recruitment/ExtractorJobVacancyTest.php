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

// Call ExtractorJobVacancyTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ExtractorJobVacancyTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";
require_once ROOT_PATH."/lib/confs/sysConf.php";
require_once ROOT_PATH."/lib/models/recruitment/JobVacancy.php";
require_once ROOT_PATH."/lib/extractor/recruitment/EXTRACTOR_JobVacancy.php";

/**
 * Test class for EXTRACTOR_JobVacancy.php
 */
class ExtractorJobVacancyTest extends PHPUnit_Framework_TestCase {

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("ExtractorJobVacancyTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     * @access protected
     */
    protected function setUp() {

    }

    /**
     * Tears down the fixture
     * @access protected
     */
    protected function tearDown() {
    }

	/**
	 * test the parseData function
	 */
	public function testParseData() {

		$extractor = new EXTRACTOR_JobVacancy();

		// No parameters - default settings
		$post = array();
		$vacancy = $extractor->parseData($post);

		$expected = new JobVacancy();
		$this->assertEquals($expected, $vacancy);

		// All parameters
		$post = array('txtId'=>'2', 'cmbJobTitle'=>'3', 'cmbHiringManager'=>'2', 'txtDesc'=>'XYZ', 'active' => '1');
		$vacancy = $extractor->parseData($post);

		$expected = new JobVacancy();
		$expected->setId(2);
		$expected->setJobTitleCode(3);
		$expected->setManagerId(2);
		$expected->setDescription('XYZ');
		$expected->setActive(true);
		$this->assertEquals($expected, $vacancy);

		// Without ID
		$post = array('cmbJobTitle'=>'3', 'cmbHiringManager'=>'2', 'txtDesc'=>'XYZ', 'active' => '1');
		$vacancy = $extractor->parseData($post);

		$expected = new JobVacancy();
		$expected->setJobTitleCode(3);
		$expected->setManagerId(2);
		$expected->setDescription('XYZ');
		$expected->setActive(true);
		$this->assertEquals($expected, $vacancy);
	}
}

// Call ExtractorJobVacancyTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ExtractorJobVacancyTest::main") {
    ExtractorJobVacancyTest::main();
}
?>
