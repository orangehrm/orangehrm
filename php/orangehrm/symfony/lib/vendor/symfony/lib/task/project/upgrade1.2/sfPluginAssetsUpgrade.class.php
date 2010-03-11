<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Fixes plugin assets symlink for core plugins.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPluginAssetsUpgrade.class.php 12542 2008-11-01 15:38:31Z fabien $
 */
class sfPluginAssetsUpgrade extends sfUpgrade
{
  public function upgrade()
  {
    $publishAssets = new sfPluginPublishAssetsTask($this->dispatcher, $this->formatter);
    $publishAssets->setCommandApplication($this->commandApplication);
    $publishAssets->run(array(), array('--core-only'));
  }
}
