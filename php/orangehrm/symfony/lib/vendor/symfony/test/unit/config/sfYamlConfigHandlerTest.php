<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(7, new lime_output_color());

class myConfigHandler extends sfYamlConfigHandler
{
  public $yamlConfig = null;

  public function execute($configFiles) {}

  static public function parseYamls($configFiles)
  {
    return parent::parseYamls($configFiles);
  }

  static public function parseYaml($configFile)
  {
    return parent::parseYaml($configFile);
  }

  public function mergeConfigValue($keyName, $category)
  {
    return parent::mergeConfigValue($keyName, $category);
  }

  public function getConfigValue($keyName, $category, $defaultValue = null)
  {
    return parent::getConfigValue($keyName, $category, $defaultValue);
  }
}

$config = new myConfigHandler();
$config->initialize();

// ->parseYamls()
$t->diag('->parseYamls()');

// ->parseYaml()
$t->diag('->parseYaml()');

// ->mergeConfigValue()
$t->diag('->mergeConfigValue()');
$config->yamlConfig = array(
  'bar' => array(
    'foo' => array(
      'foo' => 'foobar',
      'bar' => 'bar',
    ),
  ),
  'all' => array(
    'foo' => array(
      'foo' => 'fooall',
      'barall' => 'barall',
    ),
  ),
);
$values = $config->mergeConfigValue('foo', 'bar');
$t->is($values['foo'], 'foobar', '->mergeConfigValue() merges values for a given key under a given category');
$t->is($values['bar'], 'bar', '->mergeConfigValue() merges values for a given key under a given category');
$t->is($values['barall'], 'barall', '->mergeConfigValue() merges values for a given key under a given category');

// ->getConfigValue()
$t->diag('->getConfigValue()');
$config->yamlConfig = array(
  'bar' => array(
    'foo' => 'foobar'
  ),
  'all' => array(
    'foo' => 'fooall'
  ),
);
$t->is($config->getConfigValue('foo', 'bar'), 'foobar', '->getConfigValue() returns the value for the key in the given category');
$t->is($config->getConfigValue('foo', 'all'), 'fooall', '->getConfigValue() returns the value for the key in the given category');
$t->is($config->getConfigValue('foo', 'foofoo'), 'fooall', '->getConfigValue() returns the value for the key in the "all" category if the key does not exist in the given category');
$t->is($config->getConfigValue('foofoo', 'foofoo', 'default'), 'default', '->getConfigValue() returns the default value if key is not found in the category and in the "all" category');
