<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(3, new lime_output_color());

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins(array('sfAutoloadPlugin', 'sfConfigPlugin'));
    $this->setPluginPath('sfConfigPlugin', $this->rootDir.'/lib/plugins/sfConfigPlugin');
  }
}

$configuration = new ProjectConfiguration(dirname(__FILE__).'/../../functional/fixtures/project');

$t->diag('->setPlugins(), ->disablePlugins(), ->enableAllPluginsExcept()');
foreach (array('setPlugins', 'disablePlugins', 'enableAllPluginsExcept') as $method)
{
  try
  {
    $configuration->$method(array());
    $t->fail('->'.$method.'() throws an exception if called too late');
  }
  catch (Exception $e)
  {
    $t->pass('->'.$method.'() throws an exception if called too late');
  }
}
