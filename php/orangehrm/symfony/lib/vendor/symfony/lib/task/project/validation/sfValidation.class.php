<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract class for validation classes.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidation.class.php 24610 2009-11-30 22:07:34Z FabianLange $
 */
abstract class sfValidation extends sfBaseTask
{
  protected
    $task = null;

  /**
   * Validates the current project.
   */
  abstract public function validate();

  abstract public function getHeader();

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
   * 
   * @param string $subdirectory A subdirectory within lib (i.e. "/form")
   */
  protected function getProjectLibDirectories($subdirectory = null)
  {
    return array_merge(
      glob(sfConfig::get('sf_apps_dir').'/*/modules/*/lib'.$subdirectory),
      glob(sfConfig::get('sf_apps_dir').'/*/lib'.$subdirectory),
      array(
        sfConfig::get('sf_apps_dir').'/lib'.$subdirectory,
        sfConfig::get('sf_lib_dir').$subdirectory,
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
