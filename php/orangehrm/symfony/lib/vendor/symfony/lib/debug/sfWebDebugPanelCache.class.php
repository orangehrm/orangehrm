<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugPanelCache adds a panel to the web debug toolbar with a link to ignore the cache
 * on the next request.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugPanelCache.class.php 12982 2008-11-13 17:25:10Z hartym $
 */
class sfWebDebugPanelCache extends sfWebDebugPanel
{
  public function getTitle()
  {
    return '<img src="'.$this->webDebug->getOption('image_root_path').'/reload.png" alt="Reload" />';
  }

  public function getTitleUrl()
  {
    return $_SERVER['PHP_SELF'].((strpos($_SERVER['PHP_SELF'], '_sf_ignore_cache') === false) ? '?_sf_ignore_cache=1' : '');
  }

  public function getPanelTitle()
  {
    return 'reload and ignore cache';
  }

  public function getPanelContent()
  {
  }
}
