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


// Call InstallUtilTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "InstallUtilTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";

require_once "installUtil.php";

require_once ROOT_PATH."/lib/confs/Conf.php";

/**
 * Test class for the functions in installUtil.php.
 */
class InstallUtilTest extends PHPUnit_Framework_TestCase {

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("InstallUtilTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

	/**
	 * Test method for checkPHPVersion() in installUtil.php
	 */
    public function testCheckPHPVersion() {
		$minVersion = '5.1.2';
		$supportedVersions = array (
			'5.0.1', '5.0.2', '5.0.3', '5.0.4',
			'5.1.0', '5.1.1', '5.1.2',
			'5.1.4', '5.1.5', '5.1.6', '5.1.7',
			'5.2.0', '5.2.1', '5.2.2'
		);
		$invalidVersions = array('5.0.0', '5.0.5', '5.5.0');

		// Less than minVersion
		$version = "4.8.1";
		$this->assertEquals(checkPHPVersion($minVersion, $supportedVersions, $invalidVersions, $version),
							INSTALLUTIL_VERSION_INVALID, "Should return INSTALLUTIL_VERSION_INVALID");

		// Less than minVersion but in supported versions
		$version = "5.1.1";
		$this->assertEquals(checkPHPVersion($minVersion, $supportedVersions, $invalidVersions, $version),
							INSTALLUTIL_VERSION_SUPPORTED, "Should return INSTALLUTIL_VERSION_SUPPORTED");

		// Less than minVersion and in invalid versions
		$version = "5.0.0";
		$this->assertEquals(checkPHPVersion($minVersion, $supportedVersions, $invalidVersions, $version),
							INSTALLUTIL_VERSION_INVALID, "Should return INSTALLUTIL_VERSION_INVALID");

		// Greather than minVersion and not in supported or invalid versions
		$version = "5.1.3";
		$this->assertEquals(checkPHPVersion($minVersion, $supportedVersions, $invalidVersions, $version),
							INSTALLUTIL_VERSION_UNSUPPORTED, "Should return INSTALLUTIL_VERSION_UNSUPPORTED");

		// Not in supported versions or invalid versions but greater than the highest version in
		// supportedVersions
		$version = "5.2.3";
		$this->assertEquals(checkPHPVersion($minVersion, $supportedVersions, $invalidVersions, $version),
							INSTALLUTIL_VERSION_SUPPORTED, "Should return INSTALLUTIL_VERSION_SUPPORTED");

		// Greater than minVersion but in invalid versions
		$version = "5.5.0";
		$this->assertEquals(checkPHPVersion($minVersion, $supportedVersions, $invalidVersions, $version),
							INSTALLUTIL_VERSION_INVALID, "Should return INSTALLUTIL_VERSION_INVALID");


    }

	/**
	 * Test method for checkPHPMemory() in installUtil.php
	 */
    public function testCheckPHPMemory() {

		$memory = "24M";
		$this->assertEquals(checkPHPMemory(5, 10, $memory), INSTALLUTIL_MEMORY_OK,
							'Should return INSTALLUTIL_MEMORY_OK');

		$memory = "12M";
		$this->assertEquals(checkPHPMemory(5, 16, $memory), INSTALLUTIL_MEMORY_SOFT_LIMIT_FAIL,
							'Should return INSTALLUTIL_MEMORY_SOFT_LIMIT_FAIL');

		$memory = "4M";
		$this->assertEquals(checkPHPMemory(5, 16, $memory), INSTALLUTIL_MEMORY_HARD_LIMIT_FAIL,
							'Should return INSTALLUTIL_MEMORY_HARD_LIMIT_FAIL');

		$memory = "-1";
		$this->assertEquals(checkPHPMemory(5, 16, $memory), INSTALLUTIL_MEMORY_UNLIMITED,
							'Should return INSTALLUTIL_MEMORY_UNLIMITED');

		$memory = "";
		$this->assertEquals(checkPHPMemory(5, 16, $memory), INSTALLUTIL_MEMORY_NO_LIMIT,
							'Should return INSTALLUTIL_MEMORY_NO_LIMIT');

		$actualMaxMem = ini_get('memory_limit');
		$actualMemInt = (int) $actualMaxMem;

		if ($actualMemInt > 0) {
			$softLimit = $actualMemInt + 1;
			$hardLimit = $actualMemInt - 1;
			$this->assertEquals(checkPHPMemory($hardLimit, $softLimit), INSTALLUTIL_MEMORY_SOFT_LIMIT_FAIL,
								'Should return INSTALLUTIL_MEMORY_OK');

			$memory = null;
			$this->assertEquals(checkPHPMemory($hardLimit, $softLimit, $memory), INSTALLUTIL_MEMORY_SOFT_LIMIT_FAIL,
								'Should return INSTALLUTIL_MEMORY_OK');

			$this->assertEquals($memory, $actualMaxMem, "Should return memory value");
		}

    }

    public function testIsPHP4() {

		$this->assertTrue(isAtleastPHP4(), 'Should return true if no version given, since development is on PHP5');
		$this->assertTrue(isAtleastPHP4('4.5.0'));
		$this->assertFalse(isAtleastPHP4('3.2'));
    }
}

// Call InstallUtilTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "InstallUtilTest::main") {
    InstallUtilTest::main();
}
?>
