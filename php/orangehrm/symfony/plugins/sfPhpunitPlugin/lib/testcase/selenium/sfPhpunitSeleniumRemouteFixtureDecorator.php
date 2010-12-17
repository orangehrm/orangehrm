<?php
/**
 * You can used it in case of the test application and selenium server are run on different computers.
 * but you have to upload the file stored in fixtures through web page.
 * You need to mount prj dir to selenium computer and define this option.
 * From the test case use for example method fixture()->getDirOwnAsRemote()
 *
 * @package    sfPhpunitPlugin
 * @subpackage fixture
 * @author     Maksim Kotlyar <mkotlar@ukr.net>
 */
class sfPhpunitSeleniumRemouteFixtureDecorator 
{
  /**
   * @var sfPhpunitFixture
   */
  protected $_fixture;
  
  protected $_decoratedMethods = array('getDir', 'getFile');
  
  public function __construct(sfPhpunitFixture $fixture)
  {
    $this->_fixture = $fixture;
  }
  
  public function __call($name, $args)
  {      
    if (strpos($name, 'AsRemote')) {
      $name = str_replace('AsRemote', '', $name);
      $name_allowed = str_replace($this->_decoratedMethods, '', $name);
      if ($name === $name_allowed) {
        throw new Exception('You try to call method `'.$name.'` as remote but It is not have any sense. You can only decorate next methods `'.implode('`, `', $this->_decoratedMethods).'`and their magic equivalents');
      }
      
      $local_path = call_user_func_array(array($this->_fixture, $name), $args);
      
      return $this->_doRemotePath($local_path);
    }
    
    return call_user_func_array(array($this->_fixture, $name), $args);
  }
  
  protected function _doRemotePath($local_path)
  {
    $options = sfConfig::get('sf_phpunit_selenium');
    if (empty($options['remote_project_dir'])) {
      return $local_path;
    }
    $remote_path = $options['remote_project_dir'];
    
    if (is_string($local_path) || is_array($local_path)) {
      return str_replace(sfConfig::get('sf_root_dir'), $remote_path, $local_path); 
    }
    
    throw new Exception('Invalid input param must be string or array of string. Given `'.gettype($local_path).'`');  
  }
}
