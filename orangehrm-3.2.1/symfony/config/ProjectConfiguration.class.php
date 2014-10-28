<?php

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__FILE__) . '/../../');
}
require_once dirname(__FILE__) . '/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
require_once dirname(__FILE__) . '/../lib/vendor/log4php/Logger.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    // for compatibility / remove and enable only the plugins you want
    $this->enableAllPluginsExcept(array());

    // Set up logging - use different config for test environment
    $logConfig = (sfConfig::get('sf_environment') == 'test') ? 'log4php_test.properties' : 'log4php.properties';

    Logger::configure(dirname(__FILE__) . '/' . $logConfig, 'OrangeHRMLogConfigurator');
    
    // set up resource dir
    $resourceIncFile = dirname(__FILE__) . '/../web/resource_dir_inc.php';
    
    if (file_exists($resourceIncFile)) {
        require_once $resourceIncFile;
    } else {
        sfConfig::set('ohrm_resource_dir', sfConfig::get('sf_web_dir'));
    }

    $this->dispatcher->connect('context.load_factories', array($this,
            'loadFactoriesListener'));
  }
  
  public function configureDoctrine(Doctrine_Manager $manager)
  {
    // Enable callbacks so that softDelete behavior can be used
    $manager->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, true);
  }
  
  /**
   * Listens for the context.load_factories event. By this time, all core
   * classes are loaded, and we can add any initialization which needs to
   * run after classes are loaded.
   * 
   * @param sfEvent $event
   */
  public function loadFactoriesListener(sfEvent $event) {

    // Create key cache for hs_hr_config values
    $ohrmConfigCache = new ohrmKeyValueCache('config', function() {
        $configService = new ConfigService();
        return $configService->getAllValues();  
    });

    sfContext::getInstance()->setOhrmConfigCache($ohrmConfigCache);

    // use csrf_secret from hs_hr_config (overrides value in settings.yml)
    $csrfSecret = $ohrmConfigCache->get('csrf_secret');

    if (!empty($csrfSecret)) {
        sfForm::enableCSRFProtection($csrfSecret);
    }    
  }
}
