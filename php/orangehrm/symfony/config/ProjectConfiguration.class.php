<?php

require_once dirname(__FILE__) . '/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    // for compatibility / remove and enable only the plugins you want 
    $this->enablePlugins(array('sfDoctrinePlugin'));
    $this->enablePlugins(array('sfPhpunitPlugin'));
	$this->enablePlugins(array('sfProtoculousPlugin'));
	$this->enablePlugins(array('sfJqueryPlugin'));
    $this->disablePlugins(array('sfPropelPlugin'));
  }
}
