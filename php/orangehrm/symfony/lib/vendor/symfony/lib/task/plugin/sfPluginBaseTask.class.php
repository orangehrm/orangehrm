<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for all symfony plugin tasks.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPluginBaseTask.class.php 6931 2008-01-04 06:31:12Z fabien $
 */
abstract class sfPluginBaseTask extends sfBaseTask
{
  private
   $pluginManager = null;

  public function getPluginManager()
  {
    if (is_null($this->pluginManager))
    {
      $environment = new sfPearEnvironment($this->dispatcher, array(
        'plugin_dir' => sfConfig::get('sf_plugins_dir'),
        'cache_dir'  => sfConfig::get('sf_cache_dir').'/.pear',
        'web_dir'    => sfConfig::get('sf_web_dir'),
      ));

      $this->pluginManager = new sfSymfonyPluginManager($this->dispatcher, $environment);
    }

    return $this->pluginManager;
  }
}
