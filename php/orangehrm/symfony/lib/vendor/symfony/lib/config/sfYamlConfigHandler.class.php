<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfYamlConfigHandler is a base class for YAML (.yml) configuration handlers. This class
 * provides a central location for parsing YAML files.
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfYamlConfigHandler.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
abstract class sfYamlConfigHandler extends sfConfigHandler
{
  protected
    $yamlConfig = null;

  /**
   * Parses an array of YAMLs files and merges them in one configuration array.
   *
   * @param array $configFiles An array of configuration file paths
   *
   * @return array A merged configuration array
   */
  static public function parseYamls($configFiles)
  {
    $config = array();
    foreach ($configFiles as $configFile)
    {
      $config = sfToolkit::arrayDeepMerge($config, self::parseYaml($configFile));
    }

    return $config;
  }

  /**
   * Parses a YAML (.yml) configuration file.
   *
   * @param string $configFile An absolute filesystem path to a configuration file
   *
   * @return string A parsed .yml configuration
   *
   * @throws sfConfigurationException If a requested configuration file does not exist or is not readable
   * @throws sfParseException If a requested configuration file is improperly formatted
   */
  static public function parseYaml($configFile)
  {
    if (!is_readable($configFile))
    {
      // can't read the configuration
      throw new sfConfigurationException(sprintf('Configuration file "%s" does not exist or is not readable.', $configFile));
    }

    // parse our config
    $config = sfYaml::load($configFile);

    if ($config === false)
    {
      // configuration couldn't be parsed
      throw new sfParseException(sprintf('Configuration file "%s" could not be parsed', $configFile));
    }

    return is_null($config) ? array() : $config;
  }

  /**
   * Merges configuration values for a given key and category.
   *
   * @param string $keyName  The key name
   * @param string $category The category name
   *
   * @return string The value associated with this key name and category
   */
  protected function mergeConfigValue($keyName, $category)
  {
    $values = array();

    if (isset($this->yamlConfig['all'][$keyName]) && is_array($this->yamlConfig['all'][$keyName]))
    {
      $values = $this->yamlConfig['all'][$keyName];
    }

    if ($category && isset($this->yamlConfig[$category][$keyName]) && is_array($this->yamlConfig[$category][$keyName]))
    {
      $values = array_merge($values, $this->yamlConfig[$category][$keyName]);
    }

    return $values;
  }

  /**
   * Gets a configuration value for a given key and category.
   *
   * @param string $keyName      The key name
   * @param string $category     The category name
   * @param string $defaultValue The default value
   *
   * @return string The value associated with this key name and category
   */
  protected function getConfigValue($keyName, $category, $defaultValue = null)
  {
    if (isset($this->yamlConfig[$category][$keyName]))
    {
      return $this->yamlConfig[$category][$keyName];
    }
    else if (isset($this->yamlConfig['all'][$keyName]))
    {
      return $this->yamlConfig['all'][$keyName];
    }

    return $defaultValue;
  }

  static public function flattenConfiguration($config)
  {
    $config['all'] = sfToolkit::arrayDeepMerge(
      isset($config['default']) && is_array($config['default']) ? $config['default'] : array(),
      isset($config['all']) && is_array($config['all']) ? $config['all'] : array()
    );

    unset($config['default']);

    return $config;
  }

  /**
   * Merges default, all and current environment configurations.
   *
   * @param array $config The main configuratino array
   *
   * @return array The merged configuration
   */
  static public function flattenConfigurationWithEnvironment($config)
  {
    return sfToolkit::arrayDeepMerge(
      isset($config['default']) && is_array($config['default']) ? $config['default'] : array(),
      isset($config['all']) && is_array($config['all']) ? $config['all'] : array(),
      isset($config[sfConfig::get('sf_environment')]) && is_array($config[sfConfig::get('sf_environment')]) ? $config[sfConfig::get('sf_environment')] : array()
    );
  }
}
