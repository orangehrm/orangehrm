<?php

/**
 * Symfony task that provide more flexable way to run tests.
 *
 * @package    sfPhpunitPlugin
 * @subpackage task

 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitRuntestTask extends sfBaseTask
{
  /**
   * 
   * @var array
   */
  protected $options = array();
  
  /**
   * 
   * @var array
   */
  protected $arguments = array();
  
  /**
   * 
   * @var sfBasePhpunitTestSuite
   */
  protected $suite;
  
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('path', sfCommandArgument::OPTIONAL, 'Name of test to run', '')));
    
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'The envirement name', 'test'),
      new sfCommandOption('and-plugins', null, sfCommandOption::PARAMETER_NONE, 'Run project and plugins tests'),
      new sfCommandOption('and-plugin', null, sfCommandOption::PARAMETER_OPTIONAL, 'Run project and concret plugin tests'),      
      new sfCommandOption('only-plugins', null, sfCommandOption::PARAMETER_NONE, 'Run only plugins tests'),
      new sfCommandOption('only-plugin', null, sfCommandOption::PARAMETER_OPTIONAL, 'Run only concret plugin tests')));
    
    $this->namespace        = 'phpunit';
    $this->name             = 'runtest';
    $this->aliases          = array('phpunit');
    $this->briefDescription = 'Runs PHPUnit tests';
    $this->detailedDescription = <<<EOF
The [phpunit:runtest|INFO] Allow you to run simple test or test from the directory or ofcourse all tests.

  [php symfony phpunit:runtest foo|INFO] - run all test in the phpunit directory and all subdirectories.
  
  [php symfony phpunit:runtest unit |INFO] - run all test in the /phpunit/unit directory.
  
  [php symfony phpunit:runtest unit/* |INFO] - run all test in the /phpunit/unit directory and all subdirectories.
  
  [php symfony phpunit:runtest unit/models/UserTest.php |INFO] - run a single UserTest.php test case.

EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $this->arguments = $arguments;
    $this->options = $options;
    
    $this->executeInitTask();
    
    $suite = new sfBasePhpunitTestSuite();
    $this->handlePluginsTests($suite);
    $this->handleProjectTests($suite);
    
  	$this->runTests($suite);
  }
  
  protected function handlePluginsTests($suite)
  {
    $rootDir = sfConfig::get('sf_root_dir');

    $andPlugins = $this->options['and-plugins'];
    $andPlugin = $this->options['and-plugin'];
    $onlyPlugins = $this->options['only-plugins'];
    $onlyPlugin = $this->options['only-plugin'];
    if (!($andPlugins || $andPlugin || $onlyPlugins || $onlyPlugin)) return;
    
    if ($andPlugin) {
      if (is_dir("{$rootDir}/plugins/{$andPlugin}")) {
        $plugins = array("{$rootDir}/plugins/{$andPlugin}"); 
      } else {
        throw new Exception('You want run project tests and the plugin ones. But the the plugin with name `'.$andPlugin.'`');      
      }
    }
    
    if ($onlyPlugin) {
        if (is_dir("{$rootDir}/plugins/{$onlyPlugin}")) {
        $plugins = array("{$rootDir}/plugins/{$onlyPlugin}"); 
      } else {
        throw new Exception('You want run only the plugin ones. But the the plugin with name `'.$onlyPlugin.'`');      
      }
    }
    
    isset($plugins) || $plugins = $this->configuration->getPluginPaths(); 

    $suite->addTestSuite(
      sfPhpunitPluginTestLoader::factory($plugins)->suite()); 
  }
  
  protected function handleProjectTests($suite)
  {
    if ($this->options['only-plugins'] || $this->options['only-plugin']) return; 
    
    $suite->addTestSuite(
     sfPhpunitProjectTestLoader::factory($this->arguments['path'])->suite());
  }
  
  protected function runTests($suite)
  {
    $runner = new PHPUnit_TextUI_TestRunner();
    $runner->doRun($suite);
  }
  
  protected function executeInitTask()
  {
    $rootDir = sfConfig::get('sf_root_dir');
    passthru("{$rootDir}/symfony phpunit:init");
  }
}
