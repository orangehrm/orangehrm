<?php
// Call ViewControllerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "ViewControllerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";

// Required by View Controller
$_SESSION['ldap'] = "disabled";

require_once ROOT_PATH . '/lib/controllers/ViewController.php';

require_once ROOT_PATH . '/lib/common/UniqueIDGenerator.php';
require_once ROOT_PATH . "/lib/confs/Conf.php";

/**
 * Test class for ViewController.
 */
class ViewControllerTest extends PHPUnit_Framework_TestCase {
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("ViewControllerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
    	$_SESSION['empID'] = "010";

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
     * Tests getJobSpecForJob method.
     */
    public function testGetJobSpecForJob() {
		$viewController = new ViewController();
        
        // invalid job title id
        $spec = $viewController->getJobSpecForJob('JOB010');
        $this->assertNull($spec);        
        
        // job title with no job spec assigned
        $spec = $viewController->getJobSpecForJob('JOB001');
        $this->assertNull($spec);
        
        // job id with job spec assigned
        $spec = $viewController->getJobSpecForJob('JOB002');
        $this->assertNotNull($spec);
        
        $expected = new JobSpec();
        $expected->setId(1);
        $expected->setName('Spec 1');
        $expected->setDesc('Desc 1');
        $expected->setDuties('duties 1');
        
        $this->assertEquals($expected, $spec);
	}
    
    /**
     * Run given sql query, checking the return value
     */
    private function _runQuery($sql) {
        $this->assertTrue(mysql_query($sql), mysql_error());
    }
    
}

// Call ViewControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ViewControllerTest::main") {
    ViewControllerTest::main();
}
?>
