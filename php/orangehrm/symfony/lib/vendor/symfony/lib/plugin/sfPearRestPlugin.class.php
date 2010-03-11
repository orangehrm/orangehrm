<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfPearRestPlugin interacts with a symfony plugin channel.
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPearRestPlugin.class.php 14830 2009-01-17 09:31:34Z dwhittle $
 */
class sfPearRestPlugin extends sfPearRest11
{
  protected
    $config,
    $rest10,
    $restBase,
    $channel;

  /**
   * Constructs a new sfRestPlugin instance.
   *
   * @param PEAR_Config $config   The PEAR Config object
   * @param array       $options  An array of options
   */
  public function __construct(PEAR_Config $config, $options = array())
  {
    parent::__construct($config, $options);

    $this->config = $config;
    $this->rest10 = new sfPearRest10($config, $options);
  }

  /**
   * Sets the channel for the REST object.
   *
   * @param string $channel The channel name
   */
  public function setChannel($channel)
  {
    $this->channel = $channel;
    $this->restBase = $this->getRESTBase($channel);
  }

  /**
   * Gets the REST base path.
   *
   * @param string $channelName The channel name
   */
  protected function getRESTBase($channelName)
  {
    $channel = $this->config->getRegistry()->getChannel($channelName);
    if (PEAR::isError($channel))
    {
      throw new sfPluginException(sprintf('Unable to initialize channel "%s"', $channel->getMessage()));
    }

    $mirror = $this->config->get('preferred_mirror', null, $channelName);
    if (!$channel->supportsREST($mirror))
    {
      throw new sfPluginRestException(sprintf('The channel "%s" does not support the REST protocol', $channelName));
    }

    return $channel->getBaseURL('REST1.1', $mirror);
  }

  /**
   * Returns the license for a given plugin and version.
   *
   * @param string $plugin  The plugin name
   * @param string $version The version
   *
   * @return string The license
   */
  public function getPluginLicense($plugin, $version)
  {
    $info = $this->packageInfo($this->restBase, $plugin);

    if (PEAR::isError($info))
    {
      throw new sfPluginRestException(sprintf('Unable to get plugin licence information for plugin "%s": %s', $plugin, $info->getMessage())); 
    }

    if (is_null($info))
    {
      // plugin does not exist
      return null;
    }

    if (!isset($info['license']) || is_null($info['license']))
    {
      throw new Exception('No license found for this plugin!');
    }

    return $info['releases'][$version]['license'];
  }

  /**
   * Gets the all available versions for a given plugin.
   *
   * @param  string $plugin     The plugin name
   * @param  string $stability  The stability name
   *
   * @return array  An array of versions
   */
  public function getPluginVersions($plugin, $stability = null)
  {
    $allreleases = $this->_rest->retrieveData($this->restBase.'r/'.strtolower($plugin).'/allreleases.xml');
    if (PEAR::isError($allreleases))
    {
      throw new sfPluginRestException(sprintf('Unable to get information for plugin "%s": %s', $plugin, $allreleases->getMessage()));
    }

    if (!isset($allreleases['r']) || (isset($allreleases['r']) && !is_array($allreleases['r']) || !count($allreleases['r'])))
    {
      throw new sfPluginRestException(sprintf('No release available for plugin "%s"', $plugin));
    }

    if (!isset($allreleases['r'][0]))
    {
      $allreleases['r'] = array($allreleases['r']);
    }

    $versions = array();
    $allowedStates = $this->getAllowedStates($stability);
    foreach ($allreleases['r'] as $release)
    {
      if (!isset($allowedStates[$release['s']]))
      {
        continue;
      }

      $versions[] = $release['v'];
    }

    if (!count($versions))
    {
      throw new sfPluginException(sprintf('No release available for plugin "%s" in state "%s"', $plugin, $stability));
    }

    return $versions;
  }

  /**
   * Returns plugin dependencies.
   *
   * @param string $plugin  The plugin name
   * @param string $version The plugin version
   *
   * @return array  An array of depedencies
   */
  public function getPluginDependencies($plugin, $version)
  {
    $dependencies = $this->_rest->retrieveData($this->restBase.'r/'.strtolower($plugin).'/deps.'.$version.'.txt');
    if (PEAR::isError($dependencies))
    {
      throw new sfPluginRestException(sprintf('Unable to get dependencies information for plugin "%s": %s', $plugin, $dependencies->getMessage()));
    }

    return unserialize($dependencies);
  }

  /**
   * Gets the plugin download URL.
   *
   * @param  string $plugin     The plugin name
   * @param  string $version    The plugin version
   * @param  string $stability  The stability
   *
   * @return string The URL for the plugin
   */
  public function getPluginDownloadURL($plugin, $version, $stability)
  {
    $installed = $this->config->getRegistry()->packageInfo($plugin, 'version', $this->channel);
    if ($installed >= $version)
    {
      throw new sfPluginException(sprintf('Plugin "%s" version "%s" is already installed (you tried to install version "%s")', $plugin, $installed, $version));
    }

    $info = $this->getDownloadURL($this->restBase, array('channel' => $this->channel, 'package' => $plugin, 'version' => $version), $stability, $installed);
    if (PEAR::isError($info))
    {
      throw new sfPluginRestException(sprintf('Unable to get download information for plugin "%s | %s | %s": %s', $plugin, $version, $stability, $info->getMessage()));
    }

    if (!isset($info['url']))
    {
      throw new sfPluginRestException(sprintf('Plugin "%s" cannot be installed (No URL found)', $plugin));
    }

    return $info['url'].(extension_loaded('zlib') ? '.tgz' : '.tar');
  }

  /**
   * Returns an array of set of possible states sorted from most to least stable.
   *
   * @param  string $stability Stability name
   *
   * @return array  An array of stability names
   */
  protected function getAllowedStates($stability = null)
  {
    $stability = is_null($stability) ? $this->config->get('preferred_state', null, $this->channel) : $stability;

    return array_flip($this->betterStates($stability, true));
  }

  /**
   * Proxies method to the PEAR REST10 object.
   *
   * @param string $method    The method name
   * @param array  $arguments An array of arguments
   */
  public function __call($method, $arguments)
  {
    if (method_exists($this->rest10, $method))
    {
      return call_user_func_array(array($this->rest10, $method), $arguments);
    }
  }
}
