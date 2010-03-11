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
 * Uninstall a plugin.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPluginUninstallTask.class.php 8474 2008-04-15 22:47:27Z fabien $
 */
class sfPluginUninstallTask extends sfPluginBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::REQUIRED, 'The plugin name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('channel', 'c', sfCommandOption::PARAMETER_REQUIRED, 'The PEAR channel name', null),
      new sfCommandOption('install_deps', 'd', sfCommandOption::PARAMETER_NONE, 'Whether to force installation of dependencies', null),
    ));

    $this->aliases = array('plugin-uninstall');
    $this->namespace = 'plugin';
    $this->name = 'uninstall';

    $this->briefDescription = 'Uninstalls a plugin';

    $this->detailedDescription = <<<EOF
The [plugin:uninstall|INFO] task uninstalls a plugin:

  [./symfony plugin:uninstall sfGuardPlugin|INFO]

The default channel is [symfony|INFO].

You can also uninstall a plugin which has a different channel:

  [./symfony plugin:uninstall --channel=mypearchannel sfGuardPlugin|INFO]

  [./symfony plugin:uninstall -c mypearchannel sfGuardPlugin|INFO]

Or you can use the [channel/package|INFO] notation:

  [./symfony plugin:uninstall mypearchannel/sfGuardPlugin|INFO]

You can get the PEAR channel name of a plugin by launching the
[plugin:list] task.

If the plugin contains some web content (images, stylesheets or javascripts),
the task also removes the [web/%name%|COMMENT] symbolic link (on *nix)
or directory (on Windows).
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('plugin', sprintf('uninstalling plugin "%s"', $arguments['name']));

    $this->getPluginManager()->uninstallPlugin($arguments['name'], $options['channel']);
  }
}
