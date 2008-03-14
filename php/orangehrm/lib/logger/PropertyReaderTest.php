<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'HspSummaryTest::main');
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once "testConf.php";

require_once 'PropertyReader.php';
require_once ROOT_PATH . '/lib/logger/Logger.php';


class PropertyReaderTest extends PHPUnit_Framework_TestCase {
	private $propertyReader;
	private $filePath;

	public function setUp() {
		$this->filePath ='testdata/mypro.properties';
		$this -> propertyReader = new PropertyReader($this->filePath);
	}

	public function testGetProperty() {
		$testkey1 = $this->propertyReader->getProperty('key1');
		$this->assertEquals("test1", $testkey1);
		$testKey2 = $this->propertyReader->getProperty('key2.key');
		$this->assertEquals("test.test2", $testKey2);

		try {
			$properties = new PropertyReader("nofile");
			$this->fail("PropertyReaderException expected");
		}catch(PropertyReaderException $e) {

		}
	}

	public function testGetPropertyArray() {
		$key = $this->propertyReader->getPropertyArray('name');

		$this->assertTrue(is_array($key));
		$this->assertEquals(3, count($key));
		$this->assertEquals('dimuthu', $key[0]);
		$this->assertEquals('kasun', $key[2]);
	}

	public function testLog() {
		$log = Logger::getInstance();
		$log->info("test message");
	}


	public static function main() {
        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('PropertyReaderTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
}
?>
