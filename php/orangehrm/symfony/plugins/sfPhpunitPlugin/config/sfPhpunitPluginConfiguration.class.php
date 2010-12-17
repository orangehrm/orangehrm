<?php

class sfPhpunitPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $configFiles = $this->configuration->getConfigPaths('config/phpunit.yml');
    $config = sfDefineEnvironmentConfigHandler::getConfiguration($configFiles);
    
    foreach ($config as $name => $value) {
      sfConfig::set("sf_phpunit_{$name}", $value);  
    }
     
    $this->_getProjectConfiguration()->getEventDispatcher()->connect(
      'plugin.post_install',
      array($this, 'postInstall'));
      
		self::initPhpunit();
  }
  
  /**
   * @return sfProjectConfiguration
   */
  protected function _getProjectConfiguration()
  {
    return $this->configuration;    
  }
  
  /**
   * Listen for event: command.post_command
   * 
   * @param sfEvent $event
   */
  public function postInstall(sfEvent $event) 
  {    
    $initTask = new sfPhpunitInitTask(
      $this->_getProjectConfiguration()->getEventDispatcher(), 
      new sfAnsiColorFormatter());
      
    $initTask->run();    
  }
  
  public static function initPhpunit(){
  	// but anyway I have to load this class to figure out phpunit version
  	require_once "PHPUnit/Runner/Version.php";
  	
	  if (version_compare(PHPUnit_Runner_Version::id(), '3.5.0RC1') < 0 ){
	  	// versions earlier 3.5.0RC1
			require_once 'PHPUnit/Framework.php';
			require_once 'PHPUnit/TextUI/TestRunner.php';
			require_once 'PHPUnit/TextUI/Command.php';
		}
		else{
			// 3.5.0RC1 and above
			require_once 'PHPUnit/Autoload.php';
		}
  }
  
  public static function initSeleniumExtension(){
  	if (version_compare(PHPUnit_Runner_Version::id(), '3.5.0RC1') < 0 ){
  		require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
  	}
  }
}