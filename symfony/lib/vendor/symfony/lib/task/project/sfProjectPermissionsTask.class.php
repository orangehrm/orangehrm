<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
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
 * @version    SVN: $Id: sfProjectPermissionsTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfProjectPermissionsTask extends sfBaseTask
{
  protected
    $current = null,
    $failed  = array();

  /**
   * @see sfTask
   */
  protected function configure()
  {
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
      $this->chmod(sfConfig::get('sf_upload_dir'), 0777);
    }

    $this->chmod(sfConfig::get('sf_cache_dir'), 0777);
    $this->chmod(sfConfig::get('sf_log_dir'), 0777);
    $this->chmod(sfConfig::get('sf_root_dir').'/symfony', 0777);

    $dirs = array(
      sfConfig::get('sf_cache_dir'),
      sfConfig::get('sf_log_dir'),
      sfConfig::get('sf_upload_dir'),
    );

    $dirFinder = sfFinder::type('dir');
    $fileFinder = sfFinder::type('file');

    foreach ($dirs as $dir)
    {
      $this->chmod($dirFinder->in($dir), 0777);
      $this->chmod($fileFinder->in($dir), 0666);
    }

    // note those files that failed
    if (count($this->failed))
    {
      $this->logBlock(array_merge(
        array('Permissions on the following file(s) could not be fixed:', ''),
        array_map(create_function('$f', 'return \' - \'.sfDebug::shortenFilePath($f);'), $this->failed)
      ), 'ERROR_LARGE');
    }
  }

  /**
   * Chmod and capture any failures.
   * 
   * @param string  $file
   * @param integer $mode
   * @param integer $umask
   * 
   * @see sfFilesystem
   */
  protected function chmod($file, $mode, $umask = 0000)
  {
    if (is_array($file))
    {
      foreach ($file as $f)
      {
        $this->chmod($f, $mode, $umask);
      }
    }
    else
    {
      set_error_handler(array($this, 'handleError'));

      $this->current = $file;
      @$this->getFilesystem()->chmod($file, $mode, $umask);
      $this->current = null;

      restore_error_handler();
    }
  }

  /**
   * Captures those chmod commands that fail.
   * 
   * @see http://www.php.net/set_error_handler
   */
  public function handleError($no, $string, $file, $line, $context)
  {
    $this->failed[] = $this->current;
  }
}
