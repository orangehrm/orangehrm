<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrades logger constants.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfLoggerUpgrade.class.php 7397 2008-02-08 06:48:35Z fabien $
 */
class sfLoggerUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    $phpFinder = $this->getFinder('file')->name('*.php');
    foreach ($phpFinder->in($this->getProjectClassDirectories()) as $file)
    {
      $content = file_get_contents($file);
      $content = preg_replace('/SF_LOG_([A-Z]+)/', 'sfLogger::$1', $content, -1, $count);

      if ($count)
      {
        $this->logSection('logger', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }
}
