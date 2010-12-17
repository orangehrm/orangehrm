<?php

/**
 *
 * Handle file fixtures.
 *
 * @package    sfPhpunitPlugin
 * @subpackage fixture
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitFixtureFile extends sfPhpunitFixture
{
  protected $_requiredOptions = array();
  
  public function  load($file = null, $fixture_type = self::OWN)
  {
    $this->_throw(__METHOD__);
  }
  
  public function clean()
  {
    $this->_throw(__METHOD__);
  }
  
  public function get($id)
  {
    $this->_throw(__METHOD__);
  }
  
  public function doSnapshot($name)
  {
    $this->_throw(__METHOD__);
  }
  
  public function cleanSnapshots()
  {
    $this->_throw(__METHOD__);
  }
  
  public function loadSnapshot($name)
  {
    $this->_throw(__METHOD__);
  }
  
  public function pdo()
  {
    $this->_throw(__METHOD__);
  }
  
  protected function _pdo()
  {
    $this->_throw(__METHOD__);
  }
  
  protected function _throw($methodName)
  {    
    throw new Exception('The method `'.$methodName.'` can be used with this type of fixture'); 
  }
}