<?php

class i18nConfiguration extends sfApplicationConfiguration
{
  public function setup()
  {
    parent::setup();
    $this->enablePlugins('sfI18NPlugin');
  }
}
