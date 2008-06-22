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

// Call ExtractorJobTitleTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ExtractorJobTitleTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once ROOT_PATH."/lib/extractor/eimadmin/EXTRACTOR_JobTitle.php";

/**
 * Test class for EXTRACTOR_ViewList.php
 */
class ExtractorJobTitleTest extends PHPUnit_Framework_TestCase {

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("ExtractorJobTitleTest");
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
	 * test the parseAddData function
	 */
	public function testParseAddData() {

		$extractor = new EXTRACTOR_JobTitle();
        
        // Add specifying a job spec
        $postArr = array('txtJobTitleName'=>'Test job title', 'txtJobTitleDesc'=> 'Just a description',
                        'txtJobTitleComments'=>'Some comments', 'cmbPayGrade'=>'SAL002', 'cmbJobSpecId'=>'2');
        $jobTitle = $extractor->parseAddData($postArr);
        
        $jobId = $jobTitle->getJobId();
        $this->assertTrue(!isset($jobId));
        $this->assertEquals($postArr['txtJobTitleName'], $jobTitle->getJobName());
        $this->assertEquals($postArr['txtJobTitleDesc'], $jobTitle->getJobDesc());
        $this->assertEquals($postArr['txtJobTitleComments'], $jobTitle->getJobComm());
        $this->assertEquals($postArr['cmbPayGrade'], $jobTitle->getJobSalGrd());
        $this->assertEquals($postArr['cmbJobSpecId'], $jobTitle->getJobSpecId());
        
        // Add without specifying a job spec
        $postArr = array('txtJobTitleName'=>'Test job title', 'txtJobTitleDesc'=> 'Just a description',
                        'txtJobTitleComments'=>'Some comments', 'cmbPayGrade'=>'SAL002', 'cmbJobSpecId'=>'-1');
        $jobTitle = $extractor->parseAddData($postArr);
        
        $jobId = $jobTitle->getJobId();
        $this->assertTrue(!isset($jobId));
        $this->assertEquals($postArr['txtJobTitleName'], $jobTitle->getJobName());
        $this->assertEquals($postArr['txtJobTitleDesc'], $jobTitle->getJobDesc());
        $this->assertEquals($postArr['txtJobTitleComments'], $jobTitle->getJobComm());
        $this->assertEquals($postArr['cmbPayGrade'], $jobTitle->getJobSalGrd());
        $jobSpecId = $jobTitle->getJobSpecId();
        $this->assertTrue(!isset($jobSpecId));                
	}

	/**
	 * test the parseEditData function
	 */
	public function testParseEditData() {

		$extractor = new EXTRACTOR_JobTitle();

        // Add specifying a job spec
        $postArr = array('txtJobTitleID'=> 'JOB201', 'txtJobTitleName'=>'Test job title', 'txtJobTitleDesc'=> 'Just a description',
                        'txtJobTitleComments'=>'Some comments', 'cmbPayGrade'=>'SAL002', 'cmbJobSpecId'=>'2');
        $jobTitle = $extractor->parseEditData($postArr);
        
        $this->assertEquals($postArr['txtJobTitleID'], $jobTitle->getJobId());        
        $this->assertEquals($postArr['txtJobTitleName'], $jobTitle->getJobName());
        $this->assertEquals($postArr['txtJobTitleDesc'], $jobTitle->getJobDesc());
        $this->assertEquals($postArr['txtJobTitleComments'], $jobTitle->getJobComm());
        $this->assertEquals($postArr['cmbPayGrade'], $jobTitle->getJobSalGrd());
        $this->assertEquals($postArr['cmbJobSpecId'], $jobTitle->getJobSpecId());
        
        // Add without specifying a job spec
        $postArr = array('txtJobTitleID'=> 'JOB201', 'txtJobTitleName'=>'Test job title', 'txtJobTitleDesc'=> 'Just a description',
                        'txtJobTitleComments'=>'Some comments', 'cmbPayGrade'=>'SAL002', 'cmbJobSpecId'=>'-1');
        $jobTitle = $extractor->parseEditData($postArr);
        
        $this->assertEquals($postArr['txtJobTitleID'], $jobTitle->getJobId());
        $this->assertEquals($postArr['txtJobTitleName'], $jobTitle->getJobName());
        $this->assertEquals($postArr['txtJobTitleDesc'], $jobTitle->getJobDesc());
        $this->assertEquals($postArr['txtJobTitleComments'], $jobTitle->getJobComm());
        $this->assertEquals($postArr['cmbPayGrade'], $jobTitle->getJobSalGrd());
        $jobSpecId = $jobTitle->getJobSpecId();
        $this->assertTrue(!isset($jobSpecId));
        
	}

}
// Call ExtractorJobTitleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ExtractorJobTitleTest::main") {
    ExtractorJobTitleTest::main();
}
?>
