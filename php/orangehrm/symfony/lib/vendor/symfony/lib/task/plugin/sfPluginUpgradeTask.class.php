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
 * Upgrades a plugin.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPluginUpgradeTask.class.php 8474 2008-04-15 22:47:27Z fabien $
 */
class sfPluginUpgradeTask extends sfPluginBaseTask
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
      new sfCommandOption('stability', 's', sfCommandOption::PARAMETER_REQUIRED, 'The preferred stability (stable, beta, alpha)', null),
      new sfCommandOption('release', 'r', sfCommandOption::PARAMETER_REQUIRED, 'The preferred version', null),
      new sfCommandOption('channel', 'c', sfCommandOption::PARAMETER_REQUIRED, 'The PEAR channel name', null),
    ));

    $this->aliases = array('plugin-upgrade');
    $this->namespace = 'plugin';
    $this->name = 'upgrade';

    $this->briefDescription = 'Upgrades a plugin';

    $this->detailedDescription = <<<EOF
The [plugin:upgrade|INFO] task tries to upgrade a plugin:

  [./symfony plugin:upgrade sfGuardPlugin|INFO]

The default channel is [symfony|INFO].

If the plugin contains some web content (images, stylesheets or javascripts),
the task also updates the [web/%name%|COMMENT] directory content on Windows.

See [plugin:install|INFO] for more information about the format of the plugin name and options.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('plugin', sprintf('upgrading plugin "%s"', $arguments['name']));

    $this->getPluginManager()->installPlugin($arguments['name'], $options);
  }
}
