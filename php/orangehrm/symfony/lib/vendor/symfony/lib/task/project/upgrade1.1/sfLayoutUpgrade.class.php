<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrades layouts.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfLayoutUpgrade.class.php 8201 2008-04-02 08:19:06Z fabien $
 */
class sfLayoutUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    $finder = $this->getFinder('file')->name('*.php');
    foreach ($finder->in(glob(sfConfig::get('sf_apps_dir').'/*/templates')) as $file)
    {
      $content = file_get_contents($file);
      $content = preg_replace('#\$sf_data\->getRaw\(\'sf_content\'\)#s', '$sf_content', $content, -1, $count);
      if ($count)
      {
        $this->logSection('layout', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }
}
