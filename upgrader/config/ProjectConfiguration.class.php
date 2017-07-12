<?php

require_once dirname(__FILE__) . '/../../symfony/lib/vendor/autoload.php';

sfCoreAutoload::register();

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins('sfDoctrinePlugin');
  }
}
