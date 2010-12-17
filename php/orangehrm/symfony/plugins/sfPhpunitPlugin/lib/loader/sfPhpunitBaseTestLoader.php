<?php

/**
 * Base phpunit's tests loader
 * Provide base functionality for all kind of loaders. 
 *
 * @package    sfPhpunitPlugin
 * @subpackage loader
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
abstract class sfPhpunitBaseTestLoader
{
  protected $_excludeDirs = array('.svn', 'fixtures');
  
  /**
   * root suite
   * 
   * @var sfBasePhpunitTestSuite
   */
  protected $_suite;
  
  protected $_recursively = false;

  /**
   * @deprecated
   * 
   * @return PHPUnit_Framework_TestSuite
   */
  public function getSuite()
  {
    return $this->suite();
  }
  
  /**
   * @return PHPUnit_Framework_TestSuite
   */
  public function suite()
  {
    if (!$this->_suite) {
      $this->_initRootSuite();
    }
    
    return $this->_suite;
  }
  
  abstract public function load();
  
  /**
   * 
   * @return PHPUnit_Framework_TestResult
   */
  public function run($result = null)
  {
    return $this->suite()->run($result);
  }

  /**
   * Load unit tests
   * 
   * @param string $path
   * @param sfBasePhpunitTestSuite $suite
   * @param bool $recursively
   * 
   * @return void
   */
  protected function _load($path, sfBasePhpunitTestSuite $suite)
  {
    if (is_file($path)) {
      $this->_loadFile($path, $suite);  
    } else {
      $this->_loadDir($path, $suite); 
    }
  }

  /**
   * 
   * @param string $path
   * @param sfBasePhpunitTestSuite $suite
   * @param bool $recursively
   * 
   * @return void
   */
  protected function _loadDir($path, sfBasePhpunitTestSuite $suite)
  {
    foreach (new DirectoryIterator($path) as $item) {
      //exclude system\metadirs;
      if (in_array($item->getFilename(),$this->_excludeDirs)) {
        continue;
      }
      
      if ($item->isFile()) {
        $this->_loadFile($item->getPathname(), $suite);
      } else if (!$item->isDot() && $item->isDir() && $this->_recursively) {
        $suite_next = $this->_getTestSuiteForDir($item->getPathname());
        $suite->addTestSuite($suite_next);
        $this->_loadDir($item->getPathname(), $suite_next);
      }
    }
  }

  /**
   * 
   * @param string $path
   * @param sfBasePhpunitTestSuite $suite
   * 
   * @return void
   */
  protected function _loadFile($path, sfBasePhpunitTestSuite $suite)
  {
    if(preg_match('/\.php$/', $path)) {
      $suite->addTestFile($path);
    }
  }

  /**
   * 
   * @param string $path
   * @throws Exception if file ending on TestSuite.class.php exists but does not contain TestSuite class.
   * 
   * @return sfBasePhpunitTestSuite
   */
  protected function _getTestSuiteForDir($path)
  {
    if (is_file($path)) $path = dirname($path);
    
    $test_suite_files = sfFinder::type('file')->name('*TestSuite.php')->maxdepth(0)->in($path);
    if (!$path = array_shift($test_suite_files)) {
      return new sfBasePhpunitTestSuite();
    }
    
    require_once $path;
    foreach ($this->_getClassessInFile($path) as $class) {
      $reflection = new ReflectionClass($class);
      if ($reflection->isSubclassOf('sfBasePhpunitTestSuite')) {
        return new $class();
      }
    }
        
    throw new Exception('The file `'.$path.'` does not contain a suite class');
  }
  
  protected function _getClassessInFile($path)
  {
    if (version_compare(PHPUnit_Runner_Version::id(), '3.4', '<')) {
      $classes = array();
      foreach (PHPUnit_Util_Class::getClassesInFile($path) as $reflection) {
        $classes[] = $reflection->getName(); 
      }
      
      return $classes;
    } else {
      return array_keys(PHPUnit_Util_File::getClassesInFile($path));
    }
  }
  
  /**
   * 
   * @param string $path
   * 
   * @return string|false absolute path or false if invalid
   */
  protected function _preparePath($path)
  {
    $test_dir = $this->_getTestDir();
    $root_dir = $this->_getRootDir();
    
    //absolute
    if (false !== strpos($path, $test_dir) && file_exists($path)) {      
      return $path;
    }
    
    //relative from the root dir
    $relativeFromRoot = realpath($root_dir.'/'.$path);
    if (file_exists($relativeFromRoot)) {
      return $relativeFromRoot;
    }
    
    //relative from the test dir 
    $relativeFromTest = realpath($test_dir.'/'.$path);
    if (file_exists($relativeFromTest)) {
      return $relativeFromTest;
    }
    
    return false;
  }
  
  /**
   * @return string
   */
  protected function _getTestDir()
  {
    return $this->_getRootDir();
  }
  
  /**
   * @return string
   */
  protected function _getRootDir()
  {
    return str_replace('\\', '/', sfConfig::get('sf_root_dir'));
  }
  
  protected function _initRootSuite()
  {
    $this->_suite = $this->_getTestSuiteForDir($this->_getTestDir());
  }
} 