<?php

/**
 * extends @see sfPhpunitBaseLoader to allow loades custom test related with a project.
 *
 * @package    sfPhpunitPlugin
 * @subpackage loader
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitProjectTestLoader extends sfPhpunitBaseTestLoader
{
  /**
   * 
   * @var string
   */
  private $_path;
  
  /**
   * 
   * @param string $path
   * @return viod
   */
  public function __construct($path = null)
  {
  	$path = str_replace('\\', '/', $path);
  	
    if (empty($path)) {
      $path = $this->_getTestDir();
      $this->_recursively = true;
    } else if ($path[strlen($path) - 1] == '*') {
      $path = substr($path, 0, strlen($path) - 1);
      $this->_recursively = true;
    }
    
    $preparedPath = $this->_preparePath($path);
    
    if (false === $preparedPath) {
      throw new Exception('The path `'.$path.'` is invalid. Can be relative or absolute path that points to exist file or folder under sf_root_dir');
    }
    
    $this->_path = $preparedPath; 
  }
  
  /**
   * Load default suites and tests
   * 
   * @return viod
   */
  public function load()
  {
    $this->_load($this->_path, $this->_buildDefaultSuiteTree());
  }
  
  /**
   * @return string
   */
  protected function _getTestDir()
  {
    return str_replace('\\', '/', sfConfig::get('sf_phpunit_dir'));
  }
  
  protected function _initRootSuite()
  {
    $this->_suite = $this->_getTestSuiteForDir($this->_getTestDir());
  }
  
  /**
   * @return PHPUnit_Framework_TestSuite
   */
  protected function _buildDefaultSuiteTree()
  {
    $suite = $this->suite();
    
    $test_dir = $this->_getTestDir();
    $path = str_replace($test_dir, '', $this->_path);
    
    if (!empty($path)) {
      $dirs = array_filter(explode('/', $path));
      if (is_file($test_dir.'/'.$path)) {
        array_pop($dirs);
      }

      foreach ($dirs as $dir) {
        $test_dir .= '/'.$dir;
        
        $suite_next = $this->_getTestSuiteForDir($test_dir);
        $suite->addTestSuite($suite_next);
        $suite = $suite_next;
      }
    }
    
    return $suite;
  }
  
  /**
   * 
   * @param string $path
   * 
   * @return sfPhpunitProjectTestLoader
   */
  public static function factory($path = null)
  {
    $loader = new self($path);
    $loader->load();
    
    return $loader;
  }
}