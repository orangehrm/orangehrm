<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Upgrade flash.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFlashUpgrade.class.php 7397 2008-02-08 06:48:35Z fabien $
 */
class sfFlashUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    $this->upgradeActions();
    $this->upgradeTemplates();
    $this->upgradeFilters();
  }

  protected function upgradeActions()
  {
    $phpFinder = $this->getFinder('file')->prune('model')->name('*.php');
    foreach ($phpFinder->in($dirs = $this->getProjectClassDirectories()) as $file)
    {
      $content = file_get_contents($file);
      $content = str_replace(
        array('$this->setFlash', '$this->getFlash', '$this->hasFlash'),
        array('$this->getUser()->setFlash', '$this->getUser()->getFlash', '$this->getUser()->hasFlash'),
        $content, $count
      );
      if ($count)
      {
        $this->logSection('flash', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }

  protected function upgradeTemplates()
  {
    $phpFinder = $this->getFinder('file')->name('*.php');
    foreach ($phpFinder->in($this->getProjectTemplateDirectories()) as $file)
    {
      $content = file_get_contents($file);
      $content = str_replace(
        array('$sf_flash->set', '$sf_flash->get', '$sf_flash->has'),
        array('$sf_user->setFlash', '$sf_user->getFlash', '$sf_user->hasFlash'),
        $content, $count
      );
      if ($count)
      {
        $this->logSection('flash', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }

  protected function upgradeFilters()
  {
    $filtersFinder = $this->getFinder('file')->name('filters.yml');
    foreach ($filtersFinder->in($this->getProjectConfigDirectories()) as $file)
    {
      $content = file_get_contents($file);
      $content = preg_replace("#flash\:\s+~\s*\n#s", '', $content, -1, $count);
      if ($count)
      {
        $this->logSection('flash', sprintf('Migrating %s', $file));
        file_put_contents($file, $content);
      }
    }
  }
}
