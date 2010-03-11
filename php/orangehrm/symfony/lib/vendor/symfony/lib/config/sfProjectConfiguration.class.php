<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfProjectConfiguration represents a configuration for a symfony project.
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfProjectConfiguration.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfProjectConfiguration
{
  protected
    $rootDir               = null,
    $symfonyLibDir         = null,
    $plugins               = array('sfPropelPlugin'),
    $pluginPaths           = array(),
    $overriddenPluginPaths = array(),
    $pluginConfigurations  = array(),
    $pluginsLoaded         = false;

  static protected
    $active = null;

  /**
   * Constructor.
   *
   * @param string            $rootDir    The project root directory
   * @param sfEventDispatcher $dispatcher The event dispatcher
   */
  public function __construct($rootDir = null, sfEventDispatcher $dispatcher = null)
  {
    if (is_null(sfProjectConfiguration::$active) || $this instanceof sfApplicationConfiguration)
    {
      sfProjectConfiguration::$active = $this;
    }

    $this->rootDir = is_null($rootDir) ? self::guessRootDir() : realpath($rootDir);
    $this->symfonyLibDir = realpath(dirname(__FILE__).'/..');
    $this->dispatcher = is_null($dispatcher) ? new sfEventDispatcher() : $dispatcher;

    ini_set('magic_quotes_runtime', 'off');

    sfConfig::set('sf_symfony_lib_dir', $this->symfonyLibDir);

    $this->setRootDir($this->rootDir);

    $this->setup();

    $this->loadPlugins();
  }

  /**
   * Setups the current configuration.
   *
   * Override this method if you want to customize your project configuration.
   */
  public function setup()
  {
  }

  /**
   * Loads the project's plugin configurations.
   */
  public function loadPlugins()
  {
    foreach ($this->getPluginPaths() as $path)
    {
      if (false === $plugin = array_search($path, $this->overriddenPluginPaths))
      {
        $plugin = basename($path);
      }
      $class = $plugin.'Configuration';

      if (is_readable($file = sprintf('%s/config/%s.class.php', $path, $class)))
      {
        require_once $file;
        $configuration = new $class($this, $path, $plugin);
      }
      else
      {
        $configuration = new sfPluginConfigurationGeneric($this, $path, $plugin);
      }

      $this->pluginConfigurations[$plugin] = $configuration;
    }

    $this->pluginsLoaded = true;
  }

  /**
   * Sets the project root directory.
   *
   * @param string $rootDir The project root directory
   */
  public function setRootDir($rootDir)
  {
    $this->rootDir = $rootDir;

    sfConfig::add(array(
      'sf_root_dir' => $rootDir,

      // global directory structure
      'sf_apps_dir'    => $rootDir.DIRECTORY_SEPARATOR.'apps',
      'sf_lib_dir'     => $rootDir.DIRECTORY_SEPARATOR.'lib',
      'sf_log_dir'     => $rootDir.DIRECTORY_SEPARATOR.'log',
      'sf_data_dir'    => $rootDir.DIRECTORY_SEPARATOR.'data',
      'sf_config_dir'  => $rootDir.DIRECTORY_SEPARATOR.'config',
      'sf_test_dir'    => $rootDir.DIRECTORY_SEPARATOR.'test',
      'sf_doc_dir'     => $rootDir.DIRECTORY_SEPARATOR.'doc',
      'sf_plugins_dir' => $rootDir.DIRECTORY_SEPARATOR.'plugins',
    ));

    $this->setWebDir($rootDir.DIRECTORY_SEPARATOR.'web');
    $this->setCacheDir($rootDir.DIRECTORY_SEPARATOR.'cache');
  }

  /**
   * Returns the project root directory.
   *
   * @return string The project root directory
   */
  public function getRootDir()
  {
    return $this->rootDir;
  }

  /**
   * Sets the cache root directory.
   *
   * @param string $cacheDir The absolute path to the cache dir.
   */
  public function setCacheDir($cacheDir)
  {
    sfConfig::set('sf_cache_dir', $cacheDir);
  }

  /**
   * Sets the log directory.
   *
   * @param string $logDir The absolute path to the log dir.
   */
  public function setLogDir($logDir)
  {
    sfConfig::set('sf_log_dir', $logDir);
  }

  /**
   * Sets the web root directory.
   *
   * @param string $webDir The absolute path to the web dir.
   */
  public function setWebDir($webDir)
  {
    sfConfig::add(array(
      'sf_web_dir'    => $webDir,
      'sf_upload_dir' => $webDir.DIRECTORY_SEPARATOR.'uploads',
    ));
  }

  /**
   * Gets directories where model classes are stored. The order of returned paths is lowest precedence
   * to highest precedence.
   *
   * @return array An array of directories
   */
  public function getModelDirs()
  {
    return array_merge(
      $this->getPluginSubPaths('/lib/model'),     // plugins
      array(sfConfig::get('sf_lib_dir').'/model') // project
    );
  }

  /**
   * Gets directories where template files are stored for a generator class and a specific theme.
   *
   * @param string $class The generator class name
   * @param string $theme The theme name
   *
   * @return array An array of directories
   */
  public function getGeneratorTemplateDirs($class, $theme)
  {
    return array_merge(
      array(sfConfig::get('sf_data_dir').'/generator/'.$class.'/'.$theme.'/template'), // project
      $this->getPluginSubPaths('/data/generator/'.$class.'/'.$theme.'/template'),      // plugins
      array(sfConfig::get('sf_data_dir').'/generator/'.$class.'/default/template'),    // project (default theme)
      $this->getPluginSubPaths('/data/generator/'.$class.'/default/template')          // plugins (default theme)
    );
  }

  /**
   * Gets directories where the skeleton is stored for a generator class and a specific theme.
   *
   * @param string $class The generator class name
   * @param string $theme The theme name
   *
   * @return array An array of directories
   */
  public function getGeneratorSkeletonDirs($class, $theme)
  {
    return array_merge(
      array(sfConfig::get('sf_data_dir').'/generator/'.$class.'/'.$theme.'/skeleton'), // project
      $this->getPluginSubPaths('/data/generator/'.$class.'/'.$theme.'/skeleton'),      // plugins
      array(sfConfig::get('sf_data_dir').'/generator/'.$class.'/default/skeleton'),    // project (default theme)
      $this->getPluginSubPaths('/data/generator/'.$class.'/default/skeleton')          // plugins (default theme)
    );
  }

  /**
   * Gets the template to use for a generator class.
   *
   * @param string $class The generator class name
   * @param string $theme The theme name
   * @param string $path  The template path
   *
   * @return string A template path
   *
   * @throws sfException
   */
  public function getGeneratorTemplate($class, $theme, $path)
  {
    $dirs = $this->getGeneratorTemplateDirs($class, $theme);
    foreach ($dirs as $dir)
    {
      if (is_readable($dir.'/'.$path))
      {
        return $dir.'/'.$path;
      }
    }

    throw new sfException(sprintf('Unable to load "%s" generator template in: %s.', $path, implode(', ', $dirs)));
  }

  /**
   * Gets the configuration file paths for a given relative configuration path.
   *
   * @param string $configPath The configuration path
   *
   * @return array An array of paths
   */
  public function getConfigPaths($configPath)
  {
    $globalConfigPath = basename(dirname($configPath)).'/'.basename($configPath);

    $files = array(
      sfConfig::get('sf_symfony_lib_dir').'/config/'.$globalConfigPath,              // symfony
    );

    foreach ($this->getPluginPaths() as $path)
    {
      if (is_file($file = $path.'/'.$globalConfigPath))
      {
        $files[] = $file;                                                            // plugins
      }
    }

    $files = array_merge($files, array(
      sfConfig::get('sf_root_dir').'/'.$globalConfigPath,                            // project
      sfConfig::get('sf_root_dir').'/'.$configPath,                                  // project
    ));

    foreach ($this->getPluginPaths() as $path)
    {
      if (is_file($file = $path.'/'.$configPath))
      {
        $files[] = $file;                                                            // plugins
      }
    }

    $configs = array();
    foreach (array_unique($files) as $file)
    {
      if (is_readable($file))
      {
        $configs[] = $file;
      }
    }

    return $configs;
  }

  /**
   * Sets the enabled plugins.
   *
   * @param array $plugins An array of plugin names
   * 
   * @throws LogicException If plugins have already been loaded
   */
  public function setPlugins(array $plugins)
  {
    if ($this->pluginsLoaded)
    {
      throw new LogicException('Plugins have already been loaded.');
    }

    $this->plugins = $plugins;

    $this->pluginPaths = array();
  }

  /**
   * Enables a plugin or a list of plugins.
   *
   * @param array|string $plugins A plugin name or a plugin list
   */
  public function enablePlugins($plugins)
  {
    $this->setPlugins(array_merge($this->plugins, is_array($plugins) ? $plugins : array($plugins)));
  }

  /**
   * Disables a plugin.
   *
   * @param array|string $plugins A plugin name or a plugin list
   * 
   * @throws LogicException If plugins have already been loaded
   */
  public function disablePlugins($plugins)
  {
    if ($this->pluginsLoaded)
    {
      throw new LogicException('Plugins have already been loaded.');
    }

    if (!is_array($plugins))
    {
      $plugins = array($plugins);
    }

    foreach ($plugins as $plugin)
    {
      if (false !== $pos = array_search($plugin, $this->plugins))
      {
        unset($this->plugins[$pos]);
      }
      else
      {
        throw new InvalidArgumentException(sprintf('The plugin "%s" does not exist.', $plugin));
      }
    }

    $this->pluginPaths = array();
  }

  /**
   * Enabled all installed plugins except the one given as argument.
   *
   * @param array|string $plugins A plugin name or a plugin list
   * 
   * @throws LogicException If plugins have already been loaded
   */
  public function enableAllPluginsExcept($plugins = array())
  {
    if ($this->pluginsLoaded)
    {
      throw new LogicException('Plugins have already been loaded.');
    }

    $this->plugins = array();
    foreach ($this->getAllPluginPaths() as $plugin => $path)
    {
      $this->plugins[] = $plugin;
    }

    $this->disablePlugins($plugins);
  }

  /**
   * Gets the list of enabled plugins.
   *
   * @return array An array of enabled plugins
   */
  public function getPlugins()
  {
    return $this->plugins;
  }

  /**
   * Gets the paths plugin sub-directories, minding overloaded plugins.
   *
   * @param string $subPath The subdirectory to look for
   *
   * @return array The plugin paths.
   */
  public function getPluginSubPaths($subPath = '')
  {
    if (array_key_exists($subPath, $this->pluginPaths))
    {
      return $this->pluginPaths[$subPath];
    }

    $this->pluginPaths[$subPath] = array();
    $pluginPaths = $this->getPluginPaths();
    foreach ($pluginPaths as $pluginPath)
    {
      if (is_dir($pluginPath.$subPath))
      {
        $this->pluginPaths[$subPath][] = $pluginPath.$subPath;
      }
    }

    return $this->pluginPaths[$subPath];
  }

  /**
   * Gets the paths to plugins root directories, minding overloaded plugins.
   *
   * @return array The plugin root paths.
   */
  public function getPluginPaths()
  {
    if (array_key_exists('', $this->pluginPaths))
    {
      return $this->pluginPaths[''];
    }

    $pluginPaths = $this->getAllPluginPaths();

    $this->pluginPaths[''] = array();
    foreach ($this->getPlugins() as $plugin)
    {
      if (isset($pluginPaths[$plugin]))
      {
        $this->pluginPaths[''][] = $pluginPaths[$plugin];
      }
      else
      {
        throw new InvalidArgumentException(sprintf('The plugin "%s" does not exist.', $plugin));
      }
    }

    return $this->pluginPaths[''];
  }

  /**
   * Returns an array of paths for all available plugins.
   * 
   * @return array
   */
  public function getAllPluginPaths()
  {
    $pluginPaths = array();

    $finder = sfFinder::type('dir')->maxdepth(0)->follow_link()->name('*Plugin');
    $dirs = array(
      sfConfig::get('sf_symfony_lib_dir').'/plugins',
      sfConfig::get('sf_plugins_dir'),
    );

    foreach ($finder->in($dirs) as $path)
    {
      $pluginPaths[basename($path)] = $path;
    }

    foreach ($this->overriddenPluginPaths as $plugin => $path)
    {
      $pluginPaths[$plugin] = $path;
    }

    return $pluginPaths;
  }

  /**
   * Manually sets the location of a particular plugin.
   * 
   * This method can be used to ease functional testing of plugins. It is not
   * intended to support sharing plugins between projects, as many plugins
   * save project specific code (to /lib/form/base, for example).
   * 
   * @param string $plugin
   * @param string $path
   */
  public function setPluginPath($plugin, $path)
  {
    $this->overriddenPluginPaths[$plugin] = realpath($path);
  }

  /**
   * Returns the configuration for the requested plugin.
   * 
   * @param string $name
   * 
   * @return sfPluginConfiguration
   */
  public function getPluginConfiguration($name)
  {
    if (!isset($this->pluginConfigurations[$name]))
    {
      throw new InvalidArgumentException(sprintf('There is no configuration object for the "%s" object.', $name));
    }

    return $this->pluginConfigurations[$name];
  }

  /**
   * Returns the event dispatcher.
   *
   * @return sfEventDispatcher A sfEventDispatcher instance
   */
  public function getEventDispatcher()
  {
    return $this->dispatcher;
  }

  /**
   * Returns the symfony lib directory.
   *
   * @return string The symfony lib directory
   */
  public function getSymfonyLibDir()
  {
    return $this->symfonyLibDir;
  }

  /**
   * Returns the active configuration.
   *
   * @return sfProjectConfiguration The current sfProjectConfiguration instance
   */
  static public function getActive()
  {
    if (is_null(sfProjectConfiguration::$active))
    {
      throw new RuntimeException('There is no active configuration.');
    }

    return sfProjectConfiguration::$active;
  }

  static public function guessRootDir()
  {
    $r = new ReflectionClass('ProjectConfiguration');

    return realpath(dirname($r->getFileName()).'/..');
  }

  /**
   * Returns a sfApplicationConfiguration configuration for a given application.
   *
   * @param string            $application An application name
   * @param string            $environment The environment name
   * @param Boolean           $debug       true to enable debug mode
   * @param string            $rootDir     The project root directory
   * @param sfEventDispatcher $dispatcher  An event dispatcher
   *
   * @return sfApplicationConfiguration A sfApplicationConfiguration instance
   */
  static public function getApplicationConfiguration($application, $environment, $debug, $rootDir = null, sfEventDispatcher $dispatcher = null)
  {
    $class = $application.'Configuration';

    if (is_null($rootDir))
    {
      $rootDir = self::guessRootDir();
    }

    if (!file_exists($file = $rootDir.'/apps/'.$application.'/config/'.$class.'.class.php'))
    {
      throw new InvalidArgumentException(sprintf('The application "%s" does not exist.', $application));
    }

    require_once $file;

    return new $class($environment, $debug, $rootDir, $dispatcher);
  }

  /**
   * Calls methods defined via sfEventDispatcher.
   *
   * @param string $method The method name
   * @param array  $arguments The method arguments
   *
   * @return mixed The returned value of the called method
   */
  public function __call($method, $arguments)
  {
    $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'configuration.method_not_found', array('method' => $method, 'arguments' => $arguments)));
    if (!$event->isProcessed())
    {
      throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
    }

    return $event->getReturnValue();
  }
}
