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
    $this->enablePlugins(array('sfDoctrinePlugin',
                               'sfPhpunitPlugin',
                               'orangehrmCorePlugin',
                               'orangehrmPimPlugin',
                               'orangehrmCoreLeavePlugin'));

    // Set up logging - use different config for test environment
    $logConfig = (sfConfig::get('sf_environment') == 'test') ? 'log4php_test.properties' : 'log4php.properties';

    Logger::configure(dirname(__FILE__) . '/' . $logConfig, 'OrangeHRMLogConfigurator');
  }
}
