<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Remove E_STRICT from error_reporting
error_reporting(error_reporting() & ~E_STRICT);
date_default_timezone_set('UTC');

require_once 'PEAR.php';
require_once 'PEAR/Config.php';
require_once 'PEAR/Registry.php';
require_once 'PEAR/Command.php';
require_once 'PEAR/PackageFile/v2/rw.php';
require_once 'PEAR/Dependency2.php';
require_once 'PEAR/Installer.php';

/**
 * sfPearEnvironment represents a PEAR environment.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPearEnvironment.class.php 17450 2009-04-20 17:37:49Z fabien $
 */
class sfPearEnvironment
{
  protected
    $dispatcher = null,
    $config     = null,
    $registry   = null,
    $rest       = null,
    $frontend   = null,
    $options    = array();

  /**
   * Constructs a new sfPluginManager.
   *
   * @param sfEventDispatcher $dispatcher   An event dispatcher instance
   * @param array             $options      An array of options
   */
  public function __construct(sfEventDispatcher $dispatcher, $options)
  {
    $this->initialize($dispatcher, $options);
  }

  /**
   * Initializes this sfPluginManager instance.
   *
   * Available options:
   *
   * * plugin_dir:            The directory where to put plugins
   * * cache_dir:             The local PEAR cache directory
   * * rest_base_class:       The base class for REST calls (default to sfPearRest)
   *                          (mainly used for testing)
   * * downloader_base_class: The base class for downloads (default to sfPearDownloader)
   *                          (mainly used for testing)
   *
   * @param sfEventDispatcher $dispatcher   An event dispatcher instance
   * @param array             $options      An array of options
   */
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    $this->dispatcher = $dispatcher;

    // initialize options
    if (!isset($options['plugin_dir']))
    {
      throw new sfConfigurationException('You must provide a "plugin_dir" option.');
    }

    if (!isset($options['cache_dir']))
    {
      throw new sfConfigurationException('You must provide a "cache_dir" option.');
    }

    if (!is_dir($options['cache_dir']))
    {
      mkdir($options['cache_dir'], 0777, true);
    }

    if (!isset($options['rest_base_class']))
    {
      $options['rest_base_class'] = 'sfPearRest';
    }

    if (!isset($options['downloader_base_class']))
    {
      $options['downloader_base_class'] = 'sfPearDownloader';
    }

    $this->options = $options;

    // initialize some PEAR objects
    $this->initializeConfiguration($options['plugin_dir'], $options['cache_dir']);
    $this->initializeRegistry();
    $this->initializeFrontend();

    // initializes the REST object
    $this->rest = new sfPearRestPlugin($this->config, array('base_class' => $options['rest_base_class']));
    $this->rest->setChannel($this->config->get('default_channel'));
  }

  /**
   * Returns a configuration value.
   *
   * @param  string $name The configuration name
   *
   * @return mixed  The configuration value
   */
  public function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
  }

  /**
   * Returns whether configuration name exists.
   *
   * @param  string $name The configuration name
   *
   * @return boolean True if configuration name exists
   */
  public function hasOption($name)
  {
    return isset($this->options[$name]);
  }

  /**
   * Sets a configuration value.
   *
   * @param string $name  The configuration name
   * @param mixed  $value The configuration value
   */
  public function setOption($name, $value)
  {
    $this->options[$name] = $value;
  }

  /**
   * Returns the PEAR Rest instance.
   *
   * @return object The PEAR Rest instance
   */
  public function getRest()
  {
    return $this->rest;
  }

  /**
   * Returns the PEAR Config instance.
   *
   * @return object The PEAR Config instance
   */
  public function getConfig()
  {
    return $this->config;
  }

  /**
   * Returns the PEAR Frontend instance.
   *
   * @return object The PEAR Frontend instance
   */
  public function getFrontend()
  {
    return $this->frontend;
  }

  /**
   * Returns the PEAR Registry instance.
   *
   * @return object The PEAR Registry instance
   */
  public function getRegistry()
  {
    return $this->registry;
  }

  /**
   * Registers a PEAR channel.
   *
   * @param string  $channel    The channel name
   * @param Boolean $isDefault  true if this is the default PEAR channel, false otherwise
   */
  public function registerChannel($channel, $isDefault = false)
  {
    $this->config->set('auto_discover', true);

    if (!$this->registry->channelExists($channel, true))
    {
      $class = $this->options['downloader_base_class'];
      $downloader = new $class($this->frontend, array(), $this->config);
      if (!$downloader->discover($channel))
      {
        throw new sfPluginException(sprintf('Unable to register channel "%s"', $channel));
      }
    }

    if ($isDefault)
    {
      $this->config->set('default_channel', $channel);
      $this->rest->setChannel($channel);
    }
  }

  /**
   * Initializes the PEAR Frontend instance.
   */
  protected function initializeFrontend()
  {
    $this->frontend = PEAR_Frontend::singleton('sfPearFrontendPlugin');
    if (PEAR::isError($this->frontend))
    {
      throw new sfPluginException(sprintf('Unable to initialize PEAR Frontend object: %s', $this->frontend->getMessage()));
    }

    $this->frontend->setEventDispatcher($this->dispatcher);
  }

  /**
   * Initializes the PEAR Registry instance.
   */
  protected function initializeRegistry()
  {
    $this->registry = $this->config->getRegistry();
    if (PEAR::isError($this->registry))
    {
      throw new sfPluginException(sprintf('Unable to initialize PEAR registry: %s', $this->registry->getMessage()));
    }
  }

  /**
   * Registers the PEAR Configuration instance.
   *
   * @param string $pluginDir   The plugin path
   * @param string $cacheDir    The cache path
   */
  public function initializeConfiguration($pluginDir, $cacheDir)
  {
    $this->config = $GLOBALS['_PEAR_Config_instance'] = new sfPearConfig();

    // change the configuration for use
    $this->config->set('php_dir',  $pluginDir);
    $this->config->set('data_dir', $pluginDir);
    $this->config->set('test_dir', $pluginDir);
    $this->config->set('doc_dir',  $pluginDir);
    $this->config->set('bin_dir',  $pluginDir);

    if($this->hasOption('preferred_state'))
    {
      $this->config->set('preferred_state', $this->getOption('preferred_state'));
    }

    // change the PEAR temp dirs
    $this->config->set('cache_dir',    $cacheDir);
    $this->config->set('download_dir', $cacheDir);
    $this->config->set('temp_dir',     $cacheDir);

    $this->config->set('verbose', 1);
  }
}
