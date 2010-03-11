<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrades symfony environement.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfEnvironmentUpgrade.class.php 7397 2008-02-08 06:48:35Z fabien $
 */
class sfEnvironmentUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    // we can only upgrade once
    if (file_exists(sfConfig::get('sf_data_dir').'/environment.migrated'))
    {
      return;
    }

    $phpFinder = $this->getFinder('file')->name('*.php');
    foreach ($phpFinder->in(array_merge($this->getProjectClassDirectories(), $this->getProjectConfigDirectories())) as $file)
    {
      $content = file_get_contents($file);
      $content = str_replace(
        array('sf_cache_dir', 'sf_root_cache_dir', 'sf_base_cache_dir'),
        array('sf_app_cache_dir', 'sf_cache_dir', 'sf_app_base_cache_dir'),
        $content, $count
      );

      if ($count)
      {
        $this->logSection('environment', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }

    touch(sfConfig::get('sf_data_dir').'/environment.migrated');
  }
}
