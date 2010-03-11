<?php

sfConfig::set('sf_orm', 'doctrine');
if (!sfConfig::get('sf_admin_module_web_dir'))
{
  sfConfig::set('sf_admin_module_web_dir', '/sfDoctrinePlugin');
}

if (sfConfig::get('sf_web_debug'))
{
  require_once dirname(__FILE__).'/../lib/debug/sfWebDebugPanelDoctrine.class.php';

  $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelDoctrine', 'listenToAddPanelEvent'));
}

require_once sfConfig::get('sfDoctrinePlugin_doctrine_lib_path', dirname(__FILE__).'/../lib/vendor/doctrine/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));

$manager = Doctrine_Manager::getInstance();
$manager->setAttribute('export', 'all');
$manager->setAttribute('validate', 'all');
$manager->setAttribute('recursive_merge_fixtures', true);
$manager->setAttribute('auto_accessor_override', true);
$manager->setAttribute('autoload_table_classes', true);

$configuration = sfProjectConfiguration::getActive();

if (method_exists($configuration, 'configureDoctrine'))
{
  $configuration->configureDoctrine($manager);
}