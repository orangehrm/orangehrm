<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrade sfComponent.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfComponentUpgrade.class.php 7397 2008-02-08 06:48:35Z fabien $
 */
class sfComponentUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    $phpFinder = $this->getFinder('file')->name('*.php');
    $dirs = glob(sfConfig::get('sf_apps_dir').'/*/modules/*/actions');
    foreach ($phpFinder->in($dirs) as $file)
    {
      $content = file_get_contents($file);
      $content = str_replace(
        array('$this->sendEmail', '$this->getPresentationFor'),
        array('$this->getController()->sendEmail', '$this->getController()->getPresentationFor'),
        $content, $count
      );
      if ($count)
      {
        $this->logSection('component', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }
}
