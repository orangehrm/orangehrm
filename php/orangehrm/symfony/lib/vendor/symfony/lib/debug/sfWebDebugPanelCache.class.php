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
 * @version    SVN: $Id: sfWebDebugPanelCache.class.php 22932 2009-10-11 22:40:20Z Kris.Wallsmith $
 */
class sfWebDebugPanelCache extends sfWebDebugPanel
{
  public function getTitle()
  {
    return '<img src="'.$this->webDebug->getOption('image_root_path').'/reload.png" alt="Reload" />';
  }

  public function getTitleUrl()
  {
    $queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

    if (false === strpos($queryString, '_sf_ignore_cache'))
    {
      return sprintf('?%s_sf_ignore_cache=1', $queryString ? $queryString.'&' : '');
    }
    else
    {
      return '?'.$queryString;
    }
  }

  public function getPanelTitle()
  {
    return 'reload and ignore cache';
  }

  public function getPanelContent()
  {
  }
}
