<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrade a project to the 1.2 release (from 1.1).
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfUpgradeTo12Task.class.php 10628 2008-08-03 15:03:08Z fabien $
 */
class sfUpgradeTo12Task extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->namespace = 'project';
    $this->name = 'upgrade1.2';
    $this->briefDescription = 'Upgrade a symfony project to the 1.2 symfony release (from 1.1)';

    $this->detailedDescription = <<<EOF
The [project:upgrade1.2|INFO] task upgrades a symfony project
based on the 1.1 release to the 1.2 symfony release.

  [./symfony project:upgrade1.2|INFO]

Please read the UPGRADE_TO_1_2 file to have information on what does this task.
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
    $baseDir = dirname(__FILE__).'/upgrade1.2/';
    $classes = array();

    foreach (glob($baseDir.'*.class.php') as $file)
    {
      $class = str_replace(array($baseDir, '.class.php'), '', $file);

      if ('sfUpgrade' != $class)
      {
        $classes[] = $class;

        require_once $baseDir.$class.'.class.php';
      }
    }

    return $classes;
  }
}
