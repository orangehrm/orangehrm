<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrade a project to the 1.1 release.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfUpgradeTo11Task.class.php 10628 2008-08-03 15:03:08Z fabien $
 */
class sfUpgradeTo11Task extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace = 'project';
    $this->name = 'upgrade1.1';
    $this->briefDescription = 'Upgrade a symfony project to the 1.1 symfony release';

    $this->detailedDescription = <<<EOF
The [project:upgrade1.1|INFO] task upgrades a symfony project
based the 1.0 release to the 1.1 symfony release.

  [./symfony project:upgrade1.1|INFO]

Please read the UPGRADE_TO_1_1 file to have information on what does this task.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    foreach ($this->getUpgradeClasses() as $class)
    {
      $upgrader = new $class($this->dispatcher, $this->formatter);
      $upgrader->setCommandApplication($this->commandApplication);
      $upgrader->upgrade();
    }
  }

  protected function getUpgradeClasses()
  {
    $baseDir = dirname(__FILE__).'/upgrade1.1/';
    $classes = array();

    foreach (glob($baseDir.'*.class.php') as $file)
    {
      $class = str_replace(array($baseDir, '.class.php'), '', $file);

      if ('sfUpgrade' != $class)
      {
        $classes[] = $class;
      }
    }

    return $classes;
  }
}
