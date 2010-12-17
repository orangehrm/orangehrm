<?php

/**
 * sfBasePhpunitUnitTestSuite is the super class for all unit
 * suites using PHPUnit.
 *
 * @package    sfPhpunitPlugin
 * @subpackage lib
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfBasePhpunitTestSuite 
  extends PHPUnit_Framework_TestSuite
{  
  /**
   * 
   * @var sfPhpunitFixture
   */
  protected $_fixture;
  
  /**
   * 
   * @var array
   */
  protected $_backupSfConfig = array();
  
  /**
   * Dev hook for custom "setUp" stuff
   * Overwrite it in your test class, if you have to execute stuff before a test is called.
   */
  protected function _start()
  {
  }

  /**
   * Dev hook for custom "tearDown" stuff
   * Overwrite it in your test class, if you have to execute stuff after a test is called.
   */
  protected function _end()
  {
  }

  /**
   * Please do not touch this method and use _start directly!
   */
  public function setUp()
  {
    if ($this instanceof sfPhpunitContextInitilizerInterface) {
      $this->setupContext();
    }
    
    $this->_backupSfConfig();
    
  	$this->_start();
  }

  /**
   * Please do not touch this method and use _end directly!
   */
  public function tearDown()
  {
    $this->_end();
    $this->_restoreSfConfig();
  }
  
  protected function _backupSfConfig()
  {
    $this->_backupSfConfig = sfConfig::getAll();
  }
  
  protected function _restoreSfConfig()
  {
    sfConfig::clear();
    sfConfig::add($this->_backupSfConfig);
  }
  
  /**
   * Recreate your database structure.
   * 
   * @return void
   */
  protected function _setupDatabaseSchema()
  {
    $this->setupContext();
  	
  	$env = sfContext::getInstance()->getConfiguration()->getEnvironment();
  	chdir(sfConfig::get('sf_root_dir'));
    
    $cmd = 'symfony propel:insert-sql --no-confirmation --env='.$env;
    shell_exec($cmd);
  }
  
  /**
   * build new sfContext
   * 
   * @return void
   */
  public function setupContext()
  {
    if (!$this instanceof sfPhpunitContextInitilizerInterface) {
      throw new Exception('You should implement `sfPhpunitContextInitilizerInterface` before initialazing context');
    }
    
    $app = $this->getApplication();
    $env = $this->getEnvironment();
    $name = $app.'-'.$env; 
    
  	if (!sfContext::hasInstance($name)) {
  	  sfContext::createInstance(
  	    ProjectConfiguration::getApplicationConfiguration($app, $env, true), $name);
  	}
  	
  	sfContext::switchTo($name);
  }
  
  /**
   * This methods has to be init by restriction of `sfPhpunitContextInitilizerInterface 
   * And used in @method setupContext
   * 
   * This is default implementation. You can redefine it in child classes.
   * 
   * @return string
   */
  public function getEnvironment()
  {
    return sfConfig::get('sf_environment', 'test');
  }
  
  public function getPackageFixtureDir()
  {
    return dirname($this->getOwnFixtureDir());
  }
  
  public function getOwnFixtureDir()
  {
    $reflection = new ReflectionClass($this);
    $path = str_replace('.php', '', $reflection->getFileName());
    $path = substr_replace($path, 'fixtures/', strpos($path, 'phpunit' . DIRECTORY_SEPARATOR) + 8, 0);
    
    return $path;
  }
  
  public function getCommonFixtureDir()
  {
    return sfConfig::get('sf_phpunit_dir').'/fixtures/common';
  }
  
  public function getSymfonyFixtureDir()
  {
    return sfConfig::get('sf_data_dir').'/fixtures';
  }
  
  /**
   * 
   * 
   * @return sfPhpunitFixture|mixed
   */
  protected function fixture($id = null)
  {
    if (!$this->_fixture) $this->_initFixture();

    return is_null($id) ? $this->_fixture : $this->_fixture->get($id); 
  }
  
  protected function _initFixture(array $options = array())
  {
    $this->_fixture = sfPhpunitFixture::build($this, $options);
  }
}