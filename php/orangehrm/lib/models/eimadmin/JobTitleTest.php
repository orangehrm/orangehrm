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

// Call JobTitleTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "JobTitleTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";
require_once 'JobTitle.php';
require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';

/**
 * Test class for JobTitle.
 */
class JobTitleTest extends PHPUnit_Framework_TestCase {
    
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
	public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("JobTitleTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
	protected function setUp() {

    	$conf = new Conf();

    	mysql_connect($conf->dbhost.":".$conf->dbport, $conf->dbuser, $conf->dbpass);
        mysql_select_db($conf->dbname);
        $this->_deleteTables();

        $this->_runQuery("INSERT INTO hs_hr_job_spec(jobspec_id, jobspec_name, jobspec_desc, jobspec_duties) " .
                           "VALUES(1, 'Spec 1', 'Desc 1', 'duties 1')");
        $this->_runQuery("INSERT INTO hs_hr_job_spec(jobspec_id, jobspec_name, jobspec_desc, jobspec_duties) " .
                           "VALUES(2, 'Spec 2', 'Desc 2', 'duties 2')");
        $this->_runQuery("INSERT INTO hs_pr_salary_grade(sal_grd_code, sal_grd_name) " .
                           "VALUES('SAL001', 'Director grade')");
        $this->_runQuery("INSERT INTO hs_pr_salary_grade(sal_grd_code, sal_grd_name) " .
                           "VALUES('SAL002', 'Other grade')");
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc,jobtit_comm, " .
                "sal_grd_code, jobspec_id) " .
                "VALUES('JOB001', 'Driver', 'Driver Desc', 'Driver comments', 'SAL002', null)");
        $this->_runQuery("INSERT INTO hs_hr_job_title(jobtit_code, jobtit_name, jobtit_desc,jobtit_comm, " .
                "sal_grd_code, jobspec_id) " .
                "VALUES('JOB002', 'Typist', 'Typist Desc', 'Typist comments', 'SAL002', 1)");
        
		UniqueIDGenerator::getInstance()->initTable();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
	protected function tearDown() {
        $this->_deleteTables();
		UniqueIDGenerator::getInstance()->initTable();
    }

    private function _deleteTables() {
        $this->_runQuery("TRUNCATE TABLE `hs_hr_job_title`");
        $this->_runQuery("TRUNCATE TABLE `hs_pr_salary_grade`");
        $this->_runQuery("TRUNCATE TABLE `hs_hr_job_spec`");        
    }
    
    /**
     * Test case for addJobTitles() and updateJobTitles()
     */    
    public function testAddUpdateJobTitles() {
        
        $before = $this->_getCount();
        
        // Add job title without spec
        $jobTitle = new JobTitle();
        $jobTitle->setJobName('Director');
        $jobTitle->setJobDesc('Director Description');
        $jobTitle->setJobComm('Director comments');
        $jobTitle->setJobSalGrd('SAL001');
        $jobTitle->addJobTitles();
        $id = $jobTitle->getJobId();
        
        $this->assertEquals($before + 1, $this->_getCount());
        $this->assertEquals(1, $this->_getCount("jobtit_code='$id' AND jobtit_name='Director'" .
                "AND jobtit_desc = 'Director Description' AND jobtit_comm = 'Director comments' AND " .
                "sal_grd_code = 'SAL001' AND jobspec_id IS NULL"));

        // Update job title values (specify job spec id)
        $jobTitle->setJobSpecId('1');
        $jobTitle->updateJobTitles();
        
        $this->assertEquals($before + 1, $this->_getCount());
        $this->assertEquals(1, $this->_getCount("jobtit_code='$id' AND jobtit_name='Director'" .
                "AND jobtit_desc = 'Director Description' AND jobtit_comm = 'Director comments' AND " .
                "sal_grd_code = 'SAL001' AND jobspec_id = 1"));
                        
        // Add job title with spec
        $jobTitle = new JobTitle();
        $jobTitle->setJobName('Manager');
        $jobTitle->setJobDesc('Manager Description');
        $jobTitle->setJobComm('Manager comments');
        $jobTitle->setJobSalGrd('SAL001');
        $jobTitle->setJobSpecId('1');        
        $jobTitle->addJobTitles();
        $id = $jobTitle->getJobId();
        $this->assertEquals($before + 2, $this->_getCount());
        $this->assertEquals(1, $this->_getCount("jobtit_code='$id' AND jobtit_name='Manager'" .
                "AND jobtit_desc = 'Manager Description' AND jobtit_comm = 'Manager comments' AND " .
                "sal_grd_code = 'SAL001' AND jobspec_id = 1"));
                            
        // Update job title values (clear job spec id)
        $jobTitle->setJobSpecId(null);        
        $jobTitle->updateJobTitles();
        $this->assertEquals($before + 2, $this->_getCount());
        $this->assertEquals(1, $this->_getCount("jobtit_code='$id' AND jobtit_name='Manager'" .
                "AND jobtit_desc = 'Manager Description' AND jobtit_comm = 'Manager comments' AND " .
                "sal_grd_code = 'SAL001' AND jobspec_id IS NULL"));                       
    }

    /**
     * Test case for filterJobTitles()
     */
    public function testFilterJobTitles() {
        
        // retrieve job title with job spec defined
        $jobTitle = new JobTitle();
        $result = $jobTitle->filterJobTitles('JOB002');
        $this->_validateJobTitle($result, 'JOB002', 'Typist', 'Typist Desc', 'Typist comments', 'SAL002', 1);        

        // retrieve job title without job spec defined
        $result = $jobTitle->filterJobTitles('JOB001');
        $this->_validateJobTitle($result, 'JOB001', 'Driver', 'Driver Desc', 'Driver comments', 'SAL002', null);
    }
    
    /**
     * Validate job title
     * asserts if not valid 
     */
    private function _validateJobTitle($result, $id, $name, $desc, $comments, $salaryCode, $jobSpecId) {
        $this->assertEquals(1, count($result));
        $this->assertEquals(6, count($result[0]));
        $this->assertEquals($result[0][0], $id);
        $this->assertEquals($result[0][1], $name);
        $this->assertEquals($result[0][2], $desc);
        $this->assertEquals($result[0][3], $comments);
        $this->assertEquals($result[0][4], $salaryCode);
        $this->assertEquals($result[0][5], $jobSpecId);        
    }
    
	/**
	 * Return the count from job title table (with given where statement)
	 */
	private function _getCount($whereClause = null) {

        $sql = 'SELECT COUNT(*) FROM hs_hr_job_title';
        
        if (!empty($whereClause)) {
            $sql .= ' WHERE ' . $whereClause;
        }

		$result = mysql_query($sql);
		$this->assertTrue($result !== false);
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$count = $row[0];
		return $count;
	}

    /**
     * Run given sql query, checking the return value
     */
    private function _runQuery($sql) {
        $this->assertTrue(mysql_query($sql), mysql_error());
    }

}

// Call JobTitleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "JobTitleTest::main") {
    JobTitleTest::main();
}
?>
