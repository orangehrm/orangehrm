<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract class for upgrade classes.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfUpgrade.class.php 17749 2009-04-29 11:54:22Z fabien $
 */
abstract class sfUpgrade extends sfBaseTask
{
  protected
    $task = null;

  /**
   * Upgrades the current project from 1.0 to 1.1.
   */
  abstract public function upgrade();

  public function execute($arguments = array(), $options = array())
  {
    throw new sfException('You can\'t execute this task.');
  }

  /**
   * Returns a finder that exclude upgrade scripts from being upgraded!
   *
   * @param  string $type String directory or file or any (for both file and directory)
   *
   * @return sfFinder A sfFinder instance
   */
  protected function getFinder($type)
  {
    return sfFinder::type($type)->prune('symfony')->discard('symfony');
  }

  /**
   * Returns all project directories where you can put PHP classes.
   */
  protected function getProjectClassDirectories()
  {
    return array_merge(
      $this->getProjectLibDirectories(),
      $this->getProjectActionDirectories()
    );
  }

  /**
   * Returns all project directories where you can put templates.
   */
  protected function getProjectTemplateDirectories()
  {
    return array_merge(
      glob(sfConfig::get('sf_apps_dir').'/*/modules/*/templates'),
      glob(sfConfig::get('sf_apps_dir').'/*/templates')
    );
  }

  /**
   * Returns all project directories where you can put actions and components.
   */
  protected function getProjectActionDirectories()
  {
    return glob(sfConfig::get('sf_apps_dir').'/*/modules/*/actions');
  }

  /**
   * Returns all project lib directories.
   */
  protected function getProjectLibDirectories()
  {
    return array_merge(
      glob(sfConfig::get('sf_apps_dir').'/*/modules/*/lib'),
      glob(sfConfig::get('sf_apps_dir').'/*/lib'),
      array(
        sfConfig::get('sf_apps_dir').'/lib',
        sfConfig::get('sf_lib_dir'),
      )
    );
  }

  /**
   * Returns all project config directories.
   */
  protected function getProjectConfigDirectories()
  {
    return array_merge(
      glob(sfConfig::get('sf_apps_dir').'/*/modules/*/config'),
      glob(sfConfig::get('sf_apps_dir').'/*/config'),
      glob(sfConfig::get('sf_config_dir'))
    );
  }

  /**
   * Returns all application names.
   *
   * @return array An array of application names
   */
  protected function getApplications()
  {
    return sfFinder::type('dir')->maxdepth(0)->relative()->in(sfConfig::get('sf_apps_dir'));
  }
}
