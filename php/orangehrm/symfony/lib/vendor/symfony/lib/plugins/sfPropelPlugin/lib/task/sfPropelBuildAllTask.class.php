<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfPropelBaseTask.class.php');

/**
 * Generates Propel model, SQL and initializes the database.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelBuildAllTask.class.php 13645 2008-12-02 23:08:25Z Kris.Wallsmith $
 */
class sfPropelBuildAllTask extends sfPropelBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'Do not ask for confirmation'),
      new sfCommandOption('skip-forms', 'F', sfCommandOption::PARAMETER_NONE, 'Skip generating forms'),
      new sfCommandOption('classes-only', 'C', sfCommandOption::PARAMETER_NONE, 'Do not initialize the database'),
      new sfCommandOption('phing-arg', null, sfCommandOption::PARAMETER_REQUIRED | sfCommandOption::IS_ARRAY, 'Arbitrary phing argument'),
    ));

    $this->aliases = array('propel-build-all');
    $this->namespace = 'propel';
    $this->name = 'build-all';
    $this->briefDescription = 'Generates Propel model and form classes, SQL and initializes the database';

    $this->detailedDescription = <<<EOF
The [propel:build-all|INFO] task is a shortcut for five other tasks:

  [./symfony propel:build-all|INFO]

The task is equivalent to:

  [./symfony propel:build-model|INFO]
  [./symfony propel:build-forms|INFO]
  [./symfony propel:build-filters|INFO]
  [./symfony propel:build-sql|INFO]
  [./symfony propel:insert-sql|INFO]

See those tasks' help pages for more information.

To bypass confirmation prompts, you can pass the [no-confirmation|COMMENT] option:

  [./symfony propel:buil-all --no-confirmation|INFO]

To build all classes but skip initializing the database, use the [classes-only|COMMENT]
option:

  [./symfony propel:build-all --classes-only|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $basePhingOptions = array();
    foreach ($options['phing-arg'] as $arg)
    {
      $basePhingOptions[] = '--phing-arg='.escapeshellarg($arg);
    }

    $buildModel = new sfPropelBuildModelTask($this->dispatcher, $this->formatter);
    $buildModel->setCommandApplication($this->commandApplication);
    $ret = $buildModel->run(array(), $basePhingOptions);

    if ($ret)
    {
      return $ret;
    }

    if (!$options['skip-forms'])
    {
      $this->logBlock(array(
        'Phing was run before and used many custom classes that might conflict with',
        'your model classes. In case of errors try running "propel:build-forms" and',
        '"propel:build-filters" alone. This is due to a PHP limitation that cannot be',
        'fixed in symfony.',
      ), 'INFO');

      $buildForms = new sfPropelBuildFormsTask($this->dispatcher, $this->formatter);
      $buildForms->setCommandApplication($this->commandApplication);
      $ret = $buildForms->run();

      if ($ret)
      {
        return $ret;
      }

      $buildFilters = new sfPropelBuildFiltersTask($this->dispatcher, $this->formatter);
      $buildFilters->setCommandApplication($this->commandApplication);
      $ret = $buildFilters->run();

      if ($ret)
      {
        return $ret;
      }
    }

    if (!$options['classes-only'])
    {
      $buildSql = new sfPropelBuildSqlTask($this->dispatcher, $this->formatter);
      $buildSql->setCommandApplication($this->commandApplication);
      $ret = $buildSql->run(array(), $basePhingOptions);

      if ($ret)
      {
        return $ret;
      }

      $insertSql = new sfPropelInsertSqlTask($this->dispatcher, $this->formatter);
      $insertSql->setCommandApplication($this->commandApplication);

      $insertSqlOptions = array_merge($basePhingOptions, array('--env='.$options['env'], '--connection='.$options['connection']));
      if ($options['application'])
      {
        $insertSqlOptions[] = '--application='.$options['application'];
      }
      if ($options['no-confirmation'])
      {
        $insertSqlOptions[] = '--no-confirmation';
      }

      $ret = $insertSql->run(array(), $insertSqlOptions);

      if ($ret)
      {
        return $ret;
      }
    }
  }
}
