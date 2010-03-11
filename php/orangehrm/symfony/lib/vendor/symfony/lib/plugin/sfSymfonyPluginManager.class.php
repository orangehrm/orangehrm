<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfSymfonyPluginManager allows you to manage symfony plugins installation and uninstallation.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfSymfonyPluginManager.class.php 12847 2008-11-09 19:01:49Z FabianLange $
 */
class sfSymfonyPluginManager extends sfPluginManager
{
  /**
   * Initializes this sfPluginManager instance.
   *
   * Available options:
   *
   * * web_dir: The directory where to plugins assets (images, stylesheets, javascripts, ...)
   *
   * See sfPluginManager for other options.
   *
   * @param sfEventDispatcher $dispatcher   An event dispatcher instance
   * @param sfPearEnvironment $environment  A sfPearEnvironment instance
   */
  public function initialize(sfEventDispatcher $dispatcher, sfPearEnvironment $environment)
  {
    parent::initialize($dispatcher, $environment);

    if (!$environment->getOption('web_dir'))
    {
      throw new sfPluginException('You must provide a "web_dir" option.');
    }
  }

  /**
   * Configures this plugin manager.
   */
  public function configure()
  {
    // register symfony channel
    $this->environment->registerChannel('pear.symfony-project.com', true);

    // register symfony plugins channel
    $this->environment->registerChannel('plugins.symfony-project.org', true);

    // register symfony for dependencies
    $this->registerSymfonyPackage();

    // register callbacks to manage web content
    $this->dispatcher->connect('plugin.post_install',  array($this, 'ListenToPluginPostInstall'));
    $this->dispatcher->connect('plugin.pre_uninstall', array($this, 'ListenToPluginPostUninstall'));
  }

  /**
   * Installs web content for a plugin.
   *
   * @param string $plugin The plugin name
   */
  public function installWebContent($plugin, $sourceDirectory)
  {
    $webDir = $sourceDirectory.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR.'web';
    if (is_dir($webDir))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array('Installing web data for plugin')));

      $filesystem = new sfFilesystem();
      $filesystem->relativeSymlink($webDir, $this->environment->getOption('web_dir').DIRECTORY_SEPARATOR.$plugin, true);
    }
  }

  /**
   * Unnstalls web content for a plugin.
   *
   * @param string $plugin The plugin name
   */
  public function uninstallWebContent($plugin)
  {
    $targetDir = $this->environment->getOption('web_dir').DIRECTORY_SEPARATOR.$plugin;
    if (is_dir($targetDir))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array('Uninstalling web data for plugin')));

      $filesystem = new sfFilesystem();

      if (is_link($targetDir))
      {
        $filesystem->remove($targetDir);
      }
      else
      {
        $filesystem->remove(sfFinder::type('any')->in($targetDir));
        $filesystem->remove($targetDir);
      }
    }
  }

  /**
   * Listens to the plugin.post_install event.
   *
   * @param sfEvent $event An sfEvent instance
   */
  public function ListenToPluginPostInstall($event)
  {
    $this->installWebContent($event['plugin'], 
           isset($event['plugin_dir']) ? $event['plugin_dir'] : $this->environment->getOption('plugin_dir'));
  }

  /**
   * Listens to the plugin.post_uninstall event.
   *
   * @param sfEvent $event An sfEvent instance
   */
  public function ListenToPluginPostUninstall($event)
  {
    $this->uninstallWebContent($event['plugin']);
  }

  /**
   * Registers the symfony package for the current version.
   */
  protected function registerSymfonyPackage()
  {
    $symfony = new PEAR_PackageFile_v2_rw();
    $symfony->setPackage('symfony');
    $symfony->setChannel('pear.symfony-project.com');
    $symfony->setConfig($this->environment->getConfig());
    $symfony->setPackageType('php');
    $symfony->setAPIVersion('1.1.0');
    $symfony->setAPIStability('stable');
    $symfony->setReleaseVersion(preg_replace('/\-\w+$/', '', SYMFONY_VERSION));
    $symfony->setReleaseStability(false === strpos(SYMFONY_VERSION, 'DEV') ? 'stable' : 'beta');
    $symfony->setDate(date('Y-m-d'));
    $symfony->setDescription('symfony');
    $symfony->setSummary('symfony');
    $symfony->setLicense('MIT License');
    $symfony->clearContents();
    $symfony->resetFilelist();
    $symfony->addMaintainer('lead', 'fabpot', 'Fabien Potencier', 'fabien.potencier@symfony-project.com');
    $symfony->setNotes('-');
    $symfony->setPearinstallerDep('1.4.3');
    $symfony->setPhpDep('5.1.0');

    $this->environment->getRegistry()->deletePackage('symfony', 'pear.symfony-project.com');
    if (!$this->environment->getRegistry()->addPackage2($symfony))
    {
      throw new sfPluginException('Unable to register the symfony package');
    }
  }

  /**
   * Returns true if the plugin is comptatible with the dependency.
   *
   * @param  array   $dependency A dependency array
   *
   * @return Boolean true if the plugin is compatible, false otherwise
   */
  protected function isPluginCompatibleWithDependency($dependency)
  {
    if (isset($dependency['channel']) && 'symfony' == $dependency['name'] && 'pear.symfony-project.com' == $dependency['channel'])
    {
      return $this->checkDependency($dependency);
    }

    return true;
  }
}
