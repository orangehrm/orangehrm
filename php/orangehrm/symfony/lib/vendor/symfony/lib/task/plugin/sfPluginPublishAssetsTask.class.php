<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfPluginBaseTask.class.php');

/**
 * Publishes Web Assets for Core and third party plugins
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabian Lange <fabian.lange@symfony-project.com>
 * @version    SVN: $Id: sfPluginPublishAssetsTask.class.php 7655 2008-02-28 09:52:40Z fabien $
 */
class sfPluginPublishAssetsTask extends sfPluginBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('core-only', '', sfCommandOption::PARAMETER_NONE, 'If set only core plugins will publish their assets'),
      new sfCommandOption('symfony-lib-dir', '', sfCommandOption::PARAMETER_REQUIRED, 'The symfony lib dir'),
    ));

    $this->aliases = array();
    $this->namespace = 'plugin';
    $this->name = 'publish-assets';

    $this->briefDescription = 'Publishes web assets for all plugins';

    $this->detailedDescription = <<<EOF
The [plugin:publish-assets|INFO] task will publish web assets from all plugins.

  [./symfony plugin:publish-assets|INFO]

In fact this will send the [plugin.post_install|INFO] event to each plugin.

EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $plugins = $this->configuration->getPlugins();

    foreach ($this->configuration->getAllPluginPaths() as $pluginName => $pluginPath)
    {
      if (!in_array($pluginName, $plugins) || ($options['core-only'] && dirname($pluginPath) != $this->configuration->getSymfonyLibDir().'/plugins'))
      {
        continue;
      }

      $this->logSection('plugin', 'Configuring plugin - '.$pluginName);
      $this->installPluginAssets($pluginName, $pluginPath);
    }
  }

  /**
   * Installs web content for a plugin.
   *
   * @param string $plugin The plugin name
   * @param string $dir    The plugin directory
   */
  protected function installPluginAssets($plugin, $dir)
  {
    $webDir = $dir.DIRECTORY_SEPARATOR.'web';

    if (is_dir($webDir))
    {
      $filesystem = new sfFilesystem();
      $filesystem->relativeSymlink($webDir, sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$plugin, true);
    }
  }
}
