<?php

/**
 *  phpunit plugin's tests loader.
 *  
 *
 * @package    sfPhpunitPlugin
 * @subpackage loader
 * 
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitPluginTestLoader extends sfPhpunitBaseTestLoader
{
  protected $_pluginsTestPath = array();
  
  protected $_recursively = true;
  
  public function __construct(array $pluginsPath)
  {
    foreach ($pluginsPath as $pluginRootPath) {
      $path = $this->_preparePath("$pluginRootPath/test/phpunit");
      if (false !== $path) {
        $this->_pluginsTestPath[] = $path;
      }
    }
  }
  
  public function load()
  {
    foreach ($this->_pluginsTestPath as $pluginTestPath) {
      $pluginTestSuite = $this->_getTestSuiteForDir($pluginTestPath);
      $this->suite()->addTestSuite($pluginTestSuite);

      $this->_load($pluginTestPath, $pluginTestSuite);
    }
  }
  
  /**
   * @return string
   */
  protected function _getTestDir()
  {
    return sfConfig::get('sf_plugin_dir');
  }
  
  /**
   * 
   * @param string $path
   * 
   * @return sfPhpunitProjectTestLoader
   */
  public static function factory(array $pluginsPath)
  {
    $loader = new self($pluginsPath);
    $loader->load();
    
    return $loader;
  }
}