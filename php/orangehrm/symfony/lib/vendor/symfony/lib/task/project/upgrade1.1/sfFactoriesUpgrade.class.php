<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrades factories.yml configuration file.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFactoriesUpgrade.class.php 7397 2008-02-08 06:48:35Z fabien $
 */
class sfFactoriesUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    $phpFinder = $this->getFinder('file')->name('factories.yml');
    foreach ($phpFinder->in($this->getProjectConfigDirectories()) as $file)
    {
      $content = file_get_contents($file);
      $content = str_replace('automaticCleaningFactor', 'automatic_cleaning_factor', $content, $count1);
      $content = str_replace('cacheDir', 'cache_dir', $content, $count2);

      if ($count1 || $count2)
      {
        $content = preg_replace('/^((.+)automatic_cleaning_factor:(\s+)(.+?))$/m', "$1\n$2prefix:$3                   %SF_APP_DIR%", $content);

        $this->logSection('factories.yml', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }
}
