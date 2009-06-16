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

// Call ExtractorJobApplicationTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ExtractorJobApplicationTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/confs/Conf.php";
require_once ROOT_PATH."/lib/confs/sysConf.php";
require_once ROOT_PATH."/lib/models/recruitment/JobApplication.php";
require_once ROOT_PATH."/lib/extractor/recruitment/EXTRACTOR_JobApplication.php";

/**
 * Test class for EXTRACTOR_JobApplication.php
 */
class ExtractorJobApplicationTest extends PHPUnit_Framework_TestCase {

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("ExtractorJobApplicationTest");
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
		
		$_FILES['txtResume']['size'] = 0;

		$extractor = new EXTRACTOR_JobApplication();

		// No parameters - default settings
		$post = array();
		$application = $extractor->parseData($post);

		$expected = new JobApplication();
		$this->assertEquals($expected, $application);

		// Without ID
		$post = array('txtVacancyId' => '1', 'txtFirstName' => 'John',
						'txtMiddleName'=>'K', 'txtLastName'=>'Salgado', 'txtStreet1'=>'111 Main St',
						'txtStreet2' => 'Apt 1111', 'txtCity'=> 'Colombo', 'txtCountry'=>'Sri Lanka',
						'txtProvince'=>'Central', 'txtZip'=> '10000', 'txtMobile' => '0772828282',
						'txtPhone' => '1119191991', 'txtEmail' => 'abc@example.com', 'txtQualifications'=>'sdf sadf sfsd');
		$application = $extractor->parseData($post);

		$expected = new JobApplication();
		$expected->setVacancyId(1);
		$expected->setFirstName('John');
		$expected->setMiddleName('K');
		$expected->setLastName('Salgado');
		$expected->setStreet1('111 Main St');
		$expected->setStreet2('Apt 1111');
		$expected->setCity('Colombo');
		$expected->setCountry('Sri Lanka');
		$expected->setProvince('Central');
		$expected->setZip('10000');
		$expected->setPhone('1119191991');
		$expected->setMobile('0772828282');
		$expected->setEmail('abc@example.com');
		$expected->setQualifications('sdf sadf sfsd');
		$this->assertEquals($expected, $application);

		// All parameters
		$post = array('txtId' => '121', 'txtVacancyId' => '1', 'txtFirstName' => 'John',
						'txtMiddleName'=>'K', 'txtLastName'=>'Salgado', 'txtStreet1'=>'111 Main St',
						'txtStreet2' => 'Apt 1111', 'txtCity'=> 'Colombo', 'txtCountry'=>'Sri Lanka',
						'txtProvince'=>'Central', 'txtZip'=> '10000', 'txtMobile' => '0772828282',
						'txtPhone' => '1119191991', 'txtEmail' => 'abc@example.com', 'txtQualifications'=>'sdf sadf sfsd');
		$application = $extractor->parseData($post);
		$expected->setId(121);
		$this->assertEquals($expected, $application);
	}
}

// Call ExtractorJobApplicationTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ExtractorJobApplicationTest::main") {
    ExtractorJobApplicationTest::main();
}
?>
