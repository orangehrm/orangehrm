<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../../bootstrap/unit.php';

$rootDir    = realpath(dirname(__FILE__).'/../../functional/fixtures/project');
$pluginRoot = $rootDir.'/plugins/sfAutoloadPlugin';

require_once $pluginRoot.'/config/sfAutoloadPluginConfiguration.class.php';

$t = new lime_test(2, new lime_output_color());

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins('sfAutoloadPlugin');
  }
}

$configuration = new sfProjectConfiguration($rootDir);
$pluginConfig = new sfAutoloadPluginConfiguration($configuration);

$t->is($pluginConfig->getRootDir(), $pluginRoot, '->guessRootDir() guesses plugin root directory');
$t->is($pluginConfig->getName(), 'sfAutoloadPlugin', '->guessName() guesses plugin name');
