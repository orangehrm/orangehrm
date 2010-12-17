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
 * @version    SVN: $Id: sfPluginPublishAssetsTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfPluginPublishAssetsTask extends sfPluginBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('plugins', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'Publish this plugin\'s assets'),
    ));

    $this->addOptions(array(
      new sfCommandOption('core-only', '', sfCommandOption::PARAMETER_NONE, 'If set only core plugins will publish their assets'),
    ));

    $this->namespace = 'plugin';
    $this->name = 'publish-assets';

    $this->briefDescription = 'Publishes web assets for all plugins';

    $this->detailedDescription = <<<EOF
The [plugin:publish-assets|INFO] task will publish web assets from all plugins.

  [./symfony plugin:publish-assets|INFO]

In fact this will send the [plugin.post_install|INFO] event to each plugin.

You can specify which plugin or plugins should install their assets by passing
those plugins' names as arguments:

  [./symfony plugin:publish-assets sfDoctrinePlugin|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $enabledPlugins = $this->configuration->getPlugins();

    if ($diff = array_diff($arguments['plugins'], $enabledPlugins))
    {
      throw new InvalidArgumentException('Plugin(s) not found: '.join(', ', $diff));
    }

    if ($options['core-only'])
    {
      $corePlugins = sfFinder::type('dir')->relative()->maxdepth(0)->in($this->configuration->getSymfonyLibDir().'/plugins');
      $arguments['plugins'] = array_unique(array_merge($arguments['plugins'], array_intersect($enabledPlugins, $corePlugins)));
    }
    else if (!count($arguments['plugins']))
    {
      $arguments['plugins'] = $enabledPlugins;
    }

    foreach ($arguments['plugins'] as $plugin)
    {
      $pluginConfiguration = $this->configuration->getPluginConfiguration($plugin);

      $this->logSection('plugin', 'Configuring plugin - '.$plugin);
      $this->installPluginAssets($plugin, $pluginConfiguration->getRootDir());
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
      $this->getFilesystem()->relativeSymlink($webDir, sfConfig::get('sf_web_dir').DIRECTORY_SEPARATOR.$plugin, true);
    }
  }
}
