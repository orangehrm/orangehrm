<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HspSummaryTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";

require_once 'PropertyReader.php';
require_once 'FileLogger.php';

class FileLoggerTest extends PHPUnit_Framework_TestCase {
	private $logger1;
	private $logger2;
	private $logger3;

	public function setUp() {
		$propertyReader = new PropertyReader(ROOT_PATH . '/lib/logger/testdata/logger.properties');
		$this->logger1 = new FileLogger('logger1', $propertyReader);
		$this->logger2 = new FileLogger('logger2', $propertyReader);
		$this->logger3 = new FileLogger('logger3', $propertyReader);

	}

	public function testInfo() {
		$this->assertTrue($this->logger1->info("Testing file logger - test info 1"));
		$this->assertFalse($this->logger2->info("Testing file logger - test info 2"));
		$this->assertTrue($this->logger3->info("Testing file logger - test info 3"));
	}

	public function testDebug() {
		$this->assertTrue($this->logger1->debug("Testing file logger - test debug 1"));
		$this->assertFalse($this->logger2->debug("Testing file logger - test debug 2"));
		$this->assertTrue($this->logger3->debug("Testing file logger - test debug 3"));
	}

	public function testError() {
		$this->assertTrue($this->logger1->error("Testing file logger - test error 1"));
		$this->assertTrue($this->logger2->error("Testing file logger - test error 2"));
		$this->assertTrue($this->logger3->error("Testing file logger - test error 3"));
	}

	public static function main() {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('FileLoggerTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
}
?>
