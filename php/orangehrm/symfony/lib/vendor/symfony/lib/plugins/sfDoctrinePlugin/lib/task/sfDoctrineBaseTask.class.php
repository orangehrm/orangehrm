<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for all symfony Doctrine tasks.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineBaseTask.class.php 15865 2009-02-28 03:34:26Z Jonathan.Wage $
 */
abstract class sfDoctrineBaseTask extends sfBaseTask
{
  static protected $done = false;

  public function initialize(sfEventDispatcher $dispatcher, sfFormatter $formatter)
  {
    parent::initialize($dispatcher, $formatter);
    self::$done = true;
  }

  protected function createConfiguration($application, $env)
  {
    $configuration = parent::createConfiguration($application, $env);

    $autoloader = sfSimpleAutoload::getInstance();
    $config = new sfAutoloadConfigHandler();
    $mapping = $config->evaluate($configuration->getConfigPaths('config/autoload.yml'));
    foreach ($mapping as $class => $file)
    {
      $autoloader->setClassPath($class, $file);
    }
    $autoloader->register();

    return $configuration;
  }
  /**
   * Get array of configuration variables for the Doctrine cli
   *
   * @return array $config
   */
  public function getCliConfig()
  {
    $fixtures = array();
    $fixtures[] = sfConfig::get('sf_root_dir').'/data/fixtures';
    $pluginPaths = $this->configuration->getPluginPaths();
    foreach ($pluginPaths as $pluginPath)
    {
      if (is_dir($dir = $pluginPath.'/data/fixtures'))
      {
        $fixtures[] = $dir;
      }
    }
    $models = sfConfig::get('sf_lib_dir') . DIRECTORY_SEPARATOR . 'model' . DIRECTORY_SEPARATOR . 'doctrine';
    $migrations = sfConfig::get('sf_lib_dir') . DIRECTORY_SEPARATOR . 'migration' . DIRECTORY_SEPARATOR . 'doctrine';
    $sql = sfConfig::get('sf_data_dir') . DIRECTORY_SEPARATOR . 'sql';
    $yaml = sfConfig::get('sf_config_dir') . DIRECTORY_SEPARATOR . 'doctrine';

    $config = array('data_fixtures_path'  =>  $fixtures,
                    'models_path'         =>  $models,
                    'migrations_path'     =>  $migrations,
                    'sql_path'            =>  $sql,
                    'yaml_schema_path'    =>  $yaml);

    foreach ($config as $dir)
    {
      $dirs = (array) $dir;
      foreach ($dirs as $dir)
      {
        Doctrine_Lib::makeDirectories($dir);
      }
    }

    return $config;
  }

  /**
   * Call a command from the Doctrine CLI
   *
   * @param string $task Name of the Doctrine task to call
   * @param string $args Arguments for the task
   * @return void
   */
  public function callDoctrineCli($task, $args = array())
  {
    $config = $this->getCliConfig();

    $arguments = array('./symfony', $task);

    foreach ($args as $key => $arg)
    {
      if (isset($config[$key]))
      {
        $config[$key] = $arg;
      } else {
        $arguments[] = $arg;
      }
    }

    $cli = new sfDoctrineCli($config);
    $cli->setDispatcher($this->dispatcher);
    $cli->setFormatter($this->formatter);
    $cli->run($arguments);
  }
}