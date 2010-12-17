<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfPropelBaseTask.class.php');

/**
 * Generates code based on your schema.
 *
 * @package    sfPropelPlugin
 * @subpackage task
 * @author     Kris Wallsmith <kris.wallsmith@symfony-project.com>
 * @version    SVN: $Id: sfPropelBuildTask.class.php 23308 2009-10-24 14:43:25Z Kris.Wallsmith $
 */
class sfPropelBuildTask extends sfPropelBaseTask
{
  const
    BUILD_MODEL   = 1,
    BUILD_FORMS   = 2,
    BUILD_FILTERS = 4,
    BUILD_SQL     = 8,
    BUILD_DB      = 16,

    OPTION_MODEL       = 1,
    OPTION_FORMS       = 3, // model, forms
    OPTION_FILTERS     = 5, // model, filters
    OPTION_SQL         = 8,
    OPTION_DB          = 24, // sql, db
    OPTION_ALL_CLASSES = 7,  // model, forms, filters
    OPTION_ALL         = 31; // model, forms, filters, sql, db

  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'Whether to force dropping of the database'),
      new sfCommandOption('all', null, sfCommandOption::PARAMETER_NONE, 'Build everything and reset the database'),
      new sfCommandOption('all-classes', null, sfCommandOption::PARAMETER_NONE, 'Build all classes'),
      new sfCommandOption('model', null, sfCommandOption::PARAMETER_NONE, 'Build model classes'),
      new sfCommandOption('forms', null, sfCommandOption::PARAMETER_NONE, 'Build form classes'),
      new sfCommandOption('filters', null, sfCommandOption::PARAMETER_NONE, 'Build filter classes'),
      new sfCommandOption('sql', null, sfCommandOption::PARAMETER_NONE, 'Build SQL'),
      new sfCommandOption('db', null, sfCommandOption::PARAMETER_NONE, 'Drop, create, and insert SQL'),
      new sfCommandOption('and-load', null, sfCommandOption::PARAMETER_OPTIONAL | sfCommandOption::IS_ARRAY, 'Load fixture data'),
      new sfCommandOption('and-append', null, sfCommandOption::PARAMETER_OPTIONAL | sfCommandOption::IS_ARRAY, 'Append fixture data'),
    ));

    $this->namespace = 'propel';
    $this->name = 'build';

    $this->briefDescription = 'Generate code based on your schema';

    $this->detailedDescription = <<<EOF
The [propel:build|INFO] task generates code based on your schema:

  [./symfony propel:build|INFO]

You must specify what you would like built. For instance, if you want model
and form classes built use the [--model|COMMENT] and [--forms|COMMENT] options:

  [./symfony propel:build --model --forms|INFO]

You can use the [--all|COMMENT] shortcut option if you would like all classes and
SQL files generated and the database rebuilt:

  [./symfony propel:build --all|INFO]

This is equivalent to running the following tasks:

  [./symfony propel:build-model|INFO]
  [./symfony propel:build-forms|INFO]
  [./symfony propel:build-filters|INFO]
  [./symfony propel:build-sql|INFO]
  [./symfony propel:insert-sql|INFO]

You can also generate only class files by using the [--all-classes|COMMENT] shortcut
option. When this option is used alone, the database will not be modified.

  [./symfony propel:build --all-classes|INFO]

The [--and-load|COMMENT] option will load data from the project and plugin
[data/fixtures/|COMMENT] directories:

  [./symfony propel:build --db --and-load|INFO]

To specify what fixtures are loaded, add a parameter to the [--and-load|COMMENT] option:

  [./symfony propel:build --all --and-load="data/fixtures/dev/"|INFO]

To append fixture data without erasing any records from the database, include
the [--and-append|COMMENT] option:

  [./symfony propel:build --all --and-append|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (!$mode = $this->calculateMode($options))
    {
      throw new InvalidArgumentException(sprintf("You must include one or more of the following build options:\n--%s\n\nSee this task's help page for more information:\n\n  php symfony help propel:build", join(', --', array_keys($this->getBuildOptions()))));
    }

    if (self::BUILD_MODEL == (self::BUILD_MODEL & $mode))
    {
      $task = new sfPropelBuildModelTask($this->dispatcher, $this->formatter);
      $task->setCommandApplication($this->commandApplication);
      $task->setConfiguration($this->configuration);
      $ret = $task->run();

      if ($ret)
      {
        return $ret;
      }
    }

    if (self::BUILD_FORMS == (self::BUILD_FORMS & $mode))
    {
      $task = new sfPropelBuildFormsTask($this->dispatcher, $this->formatter);
      $task->setCommandApplication($this->commandApplication);
      $task->setConfiguration($this->configuration);
      $ret = $task->run();

      if ($ret)
      {
        return $ret;
      }
    }

    if (self::BUILD_FILTERS == (self::BUILD_FILTERS & $mode))
    {
      $task = new sfPropelBuildFiltersTask($this->dispatcher, $this->formatter);
      $task->setCommandApplication($this->commandApplication);
      $task->setConfiguration($this->configuration);
      $ret = $task->run();

      if ($ret)
      {
        return $ret;
      }
    }

    if (self::BUILD_SQL == (self::BUILD_SQL & $mode))
    {
      $task = new sfPropelBuildSqlTask($this->dispatcher, $this->formatter);
      $task->setCommandApplication($this->commandApplication);
      $task->setConfiguration($this->configuration);
      $ret = $task->run();

      if ($ret)
      {
        return $ret;
      }
    }

    if (self::BUILD_DB == (self::BUILD_DB & $mode))
    {
      $task = new sfPropelInsertSqlTask($this->dispatcher, $this->formatter);
      $task->setCommandApplication($this->commandApplication);
      $task->setConfiguration($this->configuration);
      $ret = $task->run(array(), array(
        'no-confirmation' => $options['no-confirmation'],
      ));

      if ($ret)
      {
        return $ret;
      }
    }

    if (count($options['and-load']) || count($options['and-append']))
    {
      $task = new sfPropelDataLoadTask($this->dispatcher, $this->formatter);
      $task->setCommandApplication($this->commandApplication);
      $task->setConfiguration($this->configuration);

      if (count($options['and-load']))
      {
        $ret = $task->run(array(
          'dir_or_file' => in_array(array(), $options['and-load'], true) ? null : $options['and-load'],
        ));

        if ($ret)
        {
          return $ret;
        }
      }

      if (count($options['and-append']))
      {
        $ret = $task->run(array(
          'dir_or_file' => in_array(array(), $options['and-append'], true) ? null : $options['and-append'],
        ), array(
          'append' => true,
        ));

        if ($ret)
        {
          return $ret;
        }
      }
    }
  }

  /**
   * Calculates a bit mode based on the supplied options.
   *
   * @param  array $options
   *
   * @return integer
   */
  protected function calculateMode($options = array())
  {
    $mode = 0;
    foreach ($this->getBuildOptions() as $name => $value)
    {
      if (isset($options[$name]) && true === $options[$name])
      {
        $mode = $mode | $value;
      }
    }

    return $mode;
  }

  /**
   * Returns an array of valid build options.
   *
   * @return array An array of option names and their mode
   */
  protected function getBuildOptions()
  {
    $options = array();
    foreach ($this->options as $option)
    {
      if (defined($constant = __CLASS__.'::OPTION_'.str_replace('-', '_', strtoupper($option->getName()))))
      {
        $options[$option->getName()] = constant($constant);
      }
    }

    return $options;
  }
}
