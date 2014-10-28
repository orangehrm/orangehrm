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
 * @version    SVN: $Id: sfDoctrineBaseTask.class.php 28976 2010-04-05 00:27:39Z Kris.Wallsmith $
 */
abstract class sfDoctrineBaseTask extends sfBaseTask
{
  /**
   * Returns an array of configuration variables for the Doctrine CLI.
   *
   * @return array $config
   *
   * @see sfDoctrinePluginConfiguration::getCliConfig()
   */
  public function getCliConfig()
  {
    return $this->configuration->getPluginConfiguration('sfDoctrinePlugin')->getCliConfig();
  }

  /**
   * Calls a Doctrine CLI command.
   *
   * @param string $task Name of the Doctrine task to call
   * @param array  $args Arguments for the task
   *
   * @see sfDoctrineCli
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
      }
      else
      {
        $arguments[] = $arg;
      }
    }

    $cli = new sfDoctrineCli($config);
    $cli->setSymfonyDispatcher($this->dispatcher);
    $cli->setSymfonyFormatter($this->formatter);
    $cli->run($arguments);
  }

  /**
   * Returns Doctrine databases from the supplied database manager.
   *
   * @param sfDatabaseManager $databaseManager
   * @param array|null        $names An array of names or NULL for all databases
   *
   * @return array An associative array of {@link sfDoctrineDatabase} objects and their names
   * 
   * @throws InvalidArgumentException If a requested database is not a Doctrine database
   */
  protected function getDoctrineDatabases(sfDatabaseManager $databaseManager, array $names = null)
  {
    $databases = array();

    if (null === $names)
    {
      foreach ($databaseManager->getNames() as $name)
      {
        $database = $databaseManager->getDatabase($name);

        if ($database instanceof sfDoctrineDatabase)
        {
          $databases[$name] = $database;
        }
      }
    }
    else
    {
      foreach ($names as $name)
      {
        $database = $databaseManager->getDatabase($name);

        if (!$database instanceof sfDoctrineDatabase)
        {
          throw new InvalidArgumentException(sprintf('The database "%s" is not a Doctrine database.', $name));
        }

        $databases[$name] = $database;
      }
    }

    return $databases;
  }

  /**
   * Merges all project and plugin schema files into one.
   *
   * Schema files are merged similar to how other configuration files are in
   * symfony, utilizing a configuration cascade. Files later in the cascade
   * can change values from earlier in the cascade.
   *
   * The order in which schema files are processed is like so:
   *
   *  1. Plugin schema files
   *    * Plugins are processed in the order which they were enabled in ProjectConfiguration
   *    * Each plugin's schema files are processed in alphabetical order
   *  2. Project schema files
   *    * Project schema files are processed in alphabetical order
   *
   * A schema file is any file saved in a plugin or project's config/doctrine/
   * directory that matches the "*.yml" glob.
   *
   * @return string Absolute path to the consolidated schema file
   */
  protected function prepareSchemaFile($yamlSchemaPath)
  {
    $models = array();
    $finder = sfFinder::type('file')->name('*.yml')->sort_by_name()->follow_link();

    // plugin models
    foreach ($this->configuration->getPlugins() as $name)
    {
      $plugin = $this->configuration->getPluginConfiguration($name);
      foreach ($finder->in($plugin->getRootDir().'/config/doctrine') as $schema)
      {
        $pluginModels = (array) sfYaml::load($schema);
        $globals = $this->filterSchemaGlobals($pluginModels);

        foreach ($pluginModels as $model => $definition)
        {
          // canonicalize this definition
          $definition = $this->canonicalizeModelDefinition($model, $definition);

          // merge in the globals
          $definition = array_merge($globals, $definition);

          // merge this model into the schema
          $models[$model] = isset($models[$model]) ? sfToolkit::arrayDeepMerge($models[$model], $definition) : $definition;

          // the first plugin to define this model gets the package
          if (!isset($models[$model]['package']))
          {
            $models[$model]['package'] = $plugin->getName().'.lib.model.doctrine';
          }

          if (!isset($models[$model]['package_custom_path']) && 0 === strpos($models[$model]['package'], $plugin->getName()))
          {
            $models[$model]['package_custom_path'] = $plugin->getRootDir().'/lib/model/doctrine';
          }
        }
      }
    }

    // project models
    foreach ($finder->in($yamlSchemaPath) as $schema)
    {
      $projectModels = (array) sfYaml::load($schema);
      $globals = $this->filterSchemaGlobals($projectModels);

      foreach ($projectModels as $model => $definition)
      {
        // canonicalize this definition
        $definition = $this->canonicalizeModelDefinition($model, $definition);

        // merge in the globals
        $definition = array_merge($globals, $definition);

        // merge this model into the schema
        $models[$model] = isset($models[$model]) ? sfToolkit::arrayDeepMerge($models[$model], $definition) : $definition;
      }
    }

    // create one consolidated schema file
    $file = realpath(sys_get_temp_dir()).'/doctrine_schema_'.rand(11111, 99999).'.yml';
    $this->logSection('file+', $file);
    file_put_contents($file, sfYaml::dump($models, 4));

    return $file;
  }

  /**
   * Removes and returns globals from the supplied array of models.
   *
   * @param array $models An array of model definitions
   *
   * @return array An array of globals
   * 
   * @see Doctrine_Import_Schema::getGlobalDefinitionKeys()
   */
  protected function filterSchemaGlobals(& $models)
  {
    $globals = array();
    $globalKeys = Doctrine_Import_Schema::getGlobalDefinitionKeys();

    foreach ($models as $key => $value)
    {
      if (in_array($key, $globalKeys))
      {
        $globals[$key] = $value;
        unset($models[$key]);
      }
    }

    return $globals;
  }

  /**
   * Canonicalizes a model definition in preparation for merging.
   * 
   * @param string $model      The model name
   * @param array  $definition The model definition
   * 
   * @return array The canonicalized model definition
   */
  protected function canonicalizeModelDefinition($model, $definition)
  {
    // expand short "type" syntax
    if (isset($definition['columns']))
    {
      foreach ($definition['columns'] as $key => $value)
      {
        if (!is_array($value))
        {
          $definition['columns'][$key] = array('type' => $value);
          $value = $definition['columns'][$key];
        }

        // expand short type(length, scale) syntax
        if (isset($value['type']) && preg_match('/ *(\w+) *\( *(\d+)(?: *, *(\d+))? *\)/', $value['type'], $match))
        {
          $definition['columns'][$key]['type'] = $match[1];
          $definition['columns'][$key]['length'] = $match[2];

          if (isset($match[3]))
          {
            $definition['columns'][$key]['scale'] = $match[3];
          }
        }
      }
    }

    // expand short "actAs" syntax
    if (isset($definition['actAs']))
    {
      foreach ($definition['actAs'] as $key => $value)
      {
        if (is_numeric($key))
        {
          $definition['actAs'][$value] = array();
          unset($definition['actAs'][$key]);
        }
      }
    }

    // expand short "listeners" syntax
    if (isset($definition['listeners']))
    {
      foreach ($definition['listeners'] as $key => $value)
      {
        if (is_numeric($key))
        {
          $definition['listeners'][$value] = array();
          unset($definition['listeners'][$key]);
        }
      }
    }

    return $definition;
  }
}
