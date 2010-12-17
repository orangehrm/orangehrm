<?php

require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';
require_once 'PHPUnit/Util/Log/JSON.php';

class sfPhpunitActions extends sfActions
{
	public function preExecute()
	{
		$this->setLayout(false);
	}
	
	public function executeIndex()
	{   
    $loader = new sfPhpunitProjectTestLoader('units/*');
    $loader->load();
    
	  $tree = array();
	  $added_tests = array();
    foreach ($this->_getTests($loader->suite()) as $test) {
      $reflection = new ReflectionClass($test);
      
      $test = $reflection->getFileName();
      $test = substr($test, strpos($test, 'phpunit/') + 8, strlen($test));
      
      $path = dirname($reflection->getFileName());
      $path = substr($path, strpos($path, 'phpunit/') + 8, strlen($path));
      $path = implode('"]["', explode('/', $path));

      if (!in_array($test, $added_tests)) {
        eval("\$tree[\"{$path}\"][] = \$test;");
        $added_tests[] = $test;
      }
    }
        
    $this->tree = $tree;
	}
	
	public function executeRun()
	{ 
    set_time_limit(0);
		
		$buffer = tempnam(sys_get_temp_dir(), 'phpunit');
    $listener = new PHPUnit_Util_Log_JSON($buffer);
    
    $testResult = new PHPUnit_Framework_TestResult();
    $testResult->addListener($listener);
    
    $path = str_replace('-', '/', $this->getRequestParameter('test'));
    $loader = new sfPhpunitProjectTestLoader($path);
    $loader->load();
    $loader->suite()->run($testResult);

    $result = '['.str_replace('}{', '},{', file_get_contents($buffer)).']';
    
    $tests = array();
    foreach (json_decode($result) as $test) {
    	if ('suiteStart' == $test->event) continue;
    	
    	if (!isset($tests[$test->suite])) {
    		$tests[$test->suite]['methods'] = array();
    		$tests[$test->suite]['status'] = 'pass';
    	}
    	
      $tests[$test->suite]['methods'][] = $test;
      if ('pass' != $test->status) {
        $tests[$test->suite]['status'] = 'fail';
      }
    }
    
    $this->result = $testResult; 
    $this->tests = $tests;
    $this->path = $path;
	}
	
	protected function _getTests(PHPUnit_Framework_TestSuite $suite)
	{
		$tests = array();
		foreach ($suite->tests() as $test) {
			if ($test instanceof PHPUnit_Framework_TestSuite) {
				$tests = array_merge($tests, $this->_getTests($test));
			} else {
				$tests[] = $test;
			}
		}
		
		return $tests;
	} 
}