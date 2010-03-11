<?php

sfConfig::set('sf_orm', 'propel');
if (!sfConfig::get('sf_admin_module_web_dir'))
{
  sfConfig::set('sf_admin_module_web_dir', '/sfPropelPlugin');
}

sfToolkit::addIncludePath(array(
  sfConfig::get('sf_root_dir'),
  sfConfig::get('sf_symfony_lib_dir'),
  realpath(dirname(__FILE__).'/../lib/vendor'),
));

if (sfConfig::get('sf_web_debug'))
{
  require_once dirname(__FILE__).'/../lib/debug/sfWebDebugPanelPropel.class.php';

  $this->dispatcher->connect('debug.web.load_panels', array('sfWebDebugPanelPropel', 'listenToAddPanelEvent'));
}

if (sfConfig::get('sf_test'))
{
  $this->dispatcher->connect('context.load_factories', array('sfPropel', 'clearAllInstancePools'));
}
