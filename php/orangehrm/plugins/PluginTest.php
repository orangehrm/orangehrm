<?php
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "PluginTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

//require_once "../build/testConf.php";
require_once "testConf.php";
require_once 'Plugin.php';

/**
 * Plugin test case.
 */
class PluginTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * @var Plugin
	 */
	private $Plugin;
	
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		
		// TODO Auto-generated PluginTest::setUp()
		

		$this->Plugin = new Plugin();
	
	}
	
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("PluginTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
	
	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated PluginTest::tearDown()
		

		$this->Plugin = null;
		
		parent::tearDown ();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Plugin->fetchPlugin()
	 */
	public function testFetchPlugin() {
		// TODO Auto-generated PluginTest->testFetchPlugin()
		
		 
		$xmlPath = Plugin::fetchPlugin("CSVREPORT");
		$this->assertEquals($xmlPath, "plugins/csv/installer.xml", "Returned non existing record");
		 
	
	}
	
	 
}

