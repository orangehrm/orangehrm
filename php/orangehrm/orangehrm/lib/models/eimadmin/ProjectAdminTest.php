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

// Call ProjectAdminTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ProjectAdminTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'ProjectAdmin.php';

/**
 * Test class for ProjectAdmin.
 *
 * NOTE: Simple getters and setters are not checked.
 */
class ProjectAdminTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("ProjectAdminTest");
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
     * @todo Implement testGetName().
     */
    public function testGetName() {

    	$admin = new ProjectAdmin();
    	$this->assertEquals("", $admin->getName());

    	$admin->setLastName("Samarasinghe");
    	$this->assertEquals("Samarasinghe", $admin->getName());

    	$admin->setFirstName("Sam");
    	$this->assertEquals("Sam Samarasinghe", $admin->getName());

    	$admin->setLastName(null);
    	$this->assertEquals("Sam", $admin->getName());
    }
}

// Call ProjectAdminTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ProjectAdminTest::main") {
    ProjectAdminTest::main();
}
?>
