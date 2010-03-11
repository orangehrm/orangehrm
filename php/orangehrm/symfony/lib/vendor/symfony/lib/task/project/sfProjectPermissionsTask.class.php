<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Fixes symfony directory permissions.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfProjectPermissionsTask.class.php 13423 2008-11-27 13:30:37Z pookey $
 */
class sfProjectPermissionsTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->aliases = array('permissions', 'fix-perms');
    $this->namespace = 'project';
    $this->name = 'permissions';
    $this->briefDescription = 'Fixes symfony directory permissions';

    $this->detailedDescription = <<<EOF
The [project:permissions|INFO] task fixes directory permissions:

  [./symfony project:permissions|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (file_exists(sfConfig::get('sf_upload_dir')))
    {
      $this->getFilesystem()->chmod(sfConfig::get('sf_upload_dir'), 0777);
    }
    $this->getFilesystem()->chmod(sfConfig::get('sf_cache_dir'), 0777);
    $this->getFilesystem()->chmod(sfConfig::get('sf_log_dir'), 0777);
    $this->getFilesystem()->chmod(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR.'symfony', 0777);

    $dirs = array(sfConfig::get('sf_cache_dir'), sfConfig::get('sf_upload_dir'), sfConfig::get('sf_log_dir'));
    $dirFinder = sfFinder::type('dir');
    $fileFinder = sfFinder::type('file');
    foreach ($dirs as $dir)
    {
      $this->getFilesystem()->chmod($dirFinder->in($dir), 0777);
      $this->getFilesystem()->chmod($fileFinder->in($dir), 0666);
    }
  }
}
