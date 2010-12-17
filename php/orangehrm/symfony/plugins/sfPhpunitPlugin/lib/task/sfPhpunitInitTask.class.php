<?php

/**
 * Symfony task that prepare standart dirs and files for phpunit.
 *
 * @package    sfPhpunitPlugin
 * @subpackage task

 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitInitTask extends sfBasePhpunitCreateTask
{
	public function __construct(sfEventDispatcher $dispatcher = null, sfFormatter $formatter = null)
	{
		is_null($dispatcher) && $dispatcher = new sfEventDispatcher;
		is_null($formatter) && $formatter = new sfFormatter;
		
		return parent::__construct($dispatcher, $formatter);
	}
	
  protected function configure()
  {  	
    $this->namespace        = 'phpunit';
    $this->name             = 'init';
    $this->briefDescription = 'Prepare files and dirs needed for phpunit';
    $this->detailedDescription = <<<EOF
EOF;

    parent::configure();
  }

  protected function execute($arguments = array(), $options = array())
  {
    parent::execute($arguments,$options);
    
  	$this->_initRequiredDirs();
  	$this->_initBaseClasses();
  	$this->_initFixturesDirs();
  	$this->_initAppsTestIndex();
  }
  
  protected function _initRequiredDirs()
  {
    if (!$this->_isAllowed('required_dirs')) return;   
    
    $phpunit_dir = ProjectConfiguration::guessRootDir().'/test/phpunit';
    
    $this->_createDir($phpunit_dir);
    $this->_createDir($phpunit_dir.'/unit');
    $this->_createDir($phpunit_dir.'/functional');
    $this->_createDir($phpunit_dir.'/unit/model');
    $this->_createDir($phpunit_dir.'/fixtures');
    $this->_createDir($phpunit_dir.'/fixtures/unit');
    $this->_createDir($phpunit_dir.'/fixtures/functional');
    $this->_createDir($phpunit_dir.'/fixtures/unit/model');
  }
  
  protected function _initBaseClasses()
  {
    if (!$this->_isAllowed('base_classes')) return;   
    
    $this->_createSuiteClass(
      '', 'unit/BasePhpunitTestSuite.tpl', array(
        'className' => 'BasePhpunitTestSuite', 
        'application' => $this->getFirstApplication()));
   
    $this->_createFile('AllTests.php', 'unit/AllTests.tpl');
  }
  
  protected function _initFixturesDirs(PHPUnit_Framework_TestSuite $suite = null)
  { 
    if (!$this->_isAllowed('test_fixture_dirs')) return;   
    
    if (null === $suite) $suite = sfPhpunitProjectTestLoader::factory()->suite();
    
    foreach ($suite->tests() as $test) {
      //don't create fixtures directories for default created suite.
      if ('sfBasePhpunitTestSuite' === get_class($test)) {
        $this->_initFixturesDirs($test);
        continue;
      }      
      
      if ($test instanceof sfPhpunitFixtureAggregator) {
        $this->_createDir($test->getCommonFixtureDir());
        $this->_createDir($test->getPackageFixtureDir());
        $this->_createDir($test->getOwnFixtureDir());
      }
      
      if ($test instanceof PHPUnit_Framework_TestSuite) {
        $this->_initFixturesDirs($test);
      }
    }    
  }
  
  protected function _initAppsTestIndex()
  {   
    if (!$this->_isAllowed('apps_test_index')) return;   
    
    $webDir = sfConfig::get('sf_web_dir');
    
    $appsDir = sfFinder::type('dir')
      ->name('*')
      ->maxdepth(0)
      ->in(sfConfig::get('sf_root_dir').'/apps');   
    foreach ($appsDir as $appDir) {
      $appName = pathinfo($appDir, PATHINFO_FILENAME);
      
      $this->_createFileAbsolutePath(
        "{$webDir}/{$appName}_test_selenium.php", 
        'templates/selenium_index.tpl', 
        array('application' => $appName));
    }
  }
  
  protected function _isAllowed($key)
  {
    $initOptions = sfConfig::get('sf_phpunit_init');
    
    return $initOptions[$key];
  }
}