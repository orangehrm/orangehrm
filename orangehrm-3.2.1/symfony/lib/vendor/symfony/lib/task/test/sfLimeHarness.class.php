<?php

require_once dirname(__FILE__).'/../../vendor/lime/lime.php';

class sfLimeHarness extends lime_harness
{
  protected
    $plugins = array();

  public function addPlugins($plugins)
  {
    foreach ($plugins as $plugin)
    {
      $this->plugins[$plugin->getRootDir().DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR] = '['.preg_replace('/Plugin$/i', '', $plugin->getName()).'] ';
    }
  }

  protected function get_relative_file($file)
  {
    $file = strtr($file, $this->plugins);
    return str_replace(DIRECTORY_SEPARATOR, '/', str_replace(array(realpath($this->base_dir).DIRECTORY_SEPARATOR, $this->extension), '', $file));
  }
}
