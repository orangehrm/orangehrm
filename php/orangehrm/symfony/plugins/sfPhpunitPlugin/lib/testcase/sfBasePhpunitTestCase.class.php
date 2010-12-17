<?php

/**
 * sfBasePhpunitUnitTestCase is the super class for all unit
 * tests using PHPUnit.
 *
 * @package    sfPhpunitPlugin
 * @subpackage testcase
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfBasePhpunitTestCase 
  extends PHPUnit_Framework_TestCase
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
	 * Please do not touch this method and use _start directly!
	 */
	public function setUp()
	{
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
	
  public function getPackageFixtureDir()
  {
    $sep = DIRECTORY_SEPARATOR;
    
    $reflection = new ReflectionClass($this);
    $path = dirname($reflection->getFileName());
    
    return str_replace(
      "{$sep}test{$sep}phpunit", 
      "{$sep}test{$sep}phpunit{$sep}fixtures", 
      $path);
  }
	
	public function getOwnFixtureDir()
	{
	  $sep = DIRECTORY_SEPARATOR;
	  
	  $reflection = new ReflectionClass($this);
    $path = str_replace('.php', '', $reflection->getFileName());
    
    return str_replace(
      "{$sep}test{$sep}phpunit", 
      "{$sep}test{$sep}phpunit{$sep}fixtures", 
      $path);
	}
	
	public function getCommonFixtureDir()
	{
	  $path = array(sfConfig::get('sf_test_dir'), 'phpunit', 'fixtures', 'common');
	  
	  return implode(DIRECTORY_SEPARATOR,$path);
	}
	
  public function getSymfonyFixtureDir()
  {
    $path = array(sfConfig::get('sf_data_dir'), 'fixtures');
    
    return implode(DIRECTORY_SEPARATOR, $path);
  }
  
  /**
   * All stubed methods should be called at least one time.
   */
  public function getStubStrict($originalClassName, $stubedMethods = array(), $arguments = array(), $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true)
  {
    return $this->getStub(
      $originalClassName, 
      $stubedMethods, 
      $arguments, 
      $mockClassName, 
      $callOriginalConstructor, 
      $callOriginalClone, 
      $callAutoload, 
      true); 
  }
  
  public function getStub($originalClassName, $stubedMethods = array(), $arguments = array(), $mockClassName = '', $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $strict = false)
  {
    $stubLatter = get_class($this->stubLater());
    
    $mock = $this->getMock(
      $originalClassName, 
      array_keys($stubedMethods), 
      $arguments,
      $mockClassName,
      $callOriginalConstructor,
      $callOriginalClone,
      $callAutoload);
      
    $expects = $strict ? $this->atLeastOnce() : $this->any();

    foreach ($stubedMethods as $method => $stub) {
      
      if ($stub instanceof $stubLatter) continue;
      
      $stubedMethod = $mock->expects($expects)->method($method);
      if ($stub instanceof sfCallable) {
        $stubedMethod->will($this->returnCallback($stub->getCallable()));
      } else if ($stub instanceof Exception) { 
        $stubedMethod->will($this->throwException($stub));
      } else {
        $stubedMethod->will($this->returnValue($stub));
      }
    }
    
    return $mock;
  }
  
  /**
   * 
   * @return sfPhpunitStubLatter
   */
  public function stubLater()
  {
    return new sfPhpunitStubLatter();
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