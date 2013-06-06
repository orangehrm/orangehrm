<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfPluginBaseTask.class.php');

/**
 * Lists installed plugins.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPluginListTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfPluginListTask extends sfPluginBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace = 'plugin';
    $this->name = 'list';

    $this->briefDescription = 'Lists installed plugins';

    $this->detailedDescription = <<<EOF
The [plugin:list|INFO] task lists all installed plugins:

  [./symfony plugin:list|INFO]

It also gives the channel and version for each plugin.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->log($this->formatter->format('Installed plugins:', 'COMMENT'));

    foreach ($this->getPluginManager()->getInstalledPlugins() as $package)
    {
      $alias = $this->getPluginManager()->getEnvironment()->getRegistry()->getChannel($package->getChannel())->getAlias();
      $this->log(sprintf(' %-40s %10s-%-6s %s', $this->formatter->format($package->getPackage(), 'INFO'), $package->getVersion(), $package->getState() ? $package->getState() : null, $this->formatter->format(sprintf('# %s (%s)', $package->getChannel(), $alias), 'COMMENT')));
    }
  }
}
