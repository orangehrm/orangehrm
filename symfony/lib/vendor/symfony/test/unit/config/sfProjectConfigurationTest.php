<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(6);

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins(array('sfAutoloadPlugin', 'sfConfigPlugin'));
    $this->setPluginPath('sfConfigPlugin', $this->rootDir.'/lib/plugins/sfConfigPlugin');
  }
}

$configuration = new ProjectConfiguration(dirname(__FILE__).'/../../functional/fixtures');

// ->setPlugins() ->disablePlugins() ->enablePlugins() ->enableAllPluginsExcept()
$t->diag('->setPlugins() ->disablePlugins() ->enablePlugins() ->enableAllPluginsExcept()');

foreach (array('setPlugins', 'disablePlugins', 'enablePlugins', 'enableAllPluginsExcept') as $method)
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

class ProjectConfiguration2 extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins('sfAutoloadPlugin', 'sfConfigPlugin');
  }
}

$configuration = new ProjectConfiguration2(dirname(__FILE__).'/../../functional/fixtures');
$t->is_deeply($configuration->getPlugins(), array('sfAutoloadPlugin', 'sfConfigPlugin'), '->enablePlugins() can enable plugins passed as arguments instead of array');

// ->__construct()
$t->diag('->__construct()');

class ProjectConfiguration3 extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins('NonExistantPlugin');
  }
}

try
{
  $configuration = new ProjectConfiguration3(dirname(__FILE__).'/../../functional/fixtures');
  $t->fail('->__construct() throws an exception if a non-existant plugin is enabled');
}
catch (Exception $e)
{
  $t->pass('->__construct() throws an exception if a non-existant plugin is enabled');
}
