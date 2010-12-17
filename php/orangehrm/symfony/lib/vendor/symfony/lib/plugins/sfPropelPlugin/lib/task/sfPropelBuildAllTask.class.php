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
 * @version    SVN: $Id: sfPropelBuildAllTask.class.php 23922 2009-11-14 14:58:38Z fabien $
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
    $buildModel->setConfiguration($this->configuration);
    $ret = $buildModel->run(array(), array(
      'phing-arg' => $options['phing-arg'],
    ));

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
      $buildForms->setConfiguration($this->configuration);
      $ret = $buildForms->run();

      if ($ret)
      {
        return $ret;
      }

      $buildFilters = new sfPropelBuildFiltersTask($this->dispatcher, $this->formatter);
      $buildFilters->setCommandApplication($this->commandApplication);
      $buildFilters->setConfiguration($this->configuration);
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
      $buildSql->setConfiguration($this->configuration);
      $ret = $buildSql->run(array(), array(
        'phing-arg' => $options['phing-arg'],
      ));

      if ($ret)
      {
        return $ret;
      }

      $insertSql = new sfPropelInsertSqlTask($this->dispatcher, $this->formatter);
      $insertSql->setCommandApplication($this->commandApplication);
      $insertSql->setConfiguration($this->configuration);
      $ret = $insertSql->run(array(), array(
        'phing-arg'       => $options['phing-arg'],
        'connection'      => $options['connection'],
        'no-confirmation' => $options['no-confirmation'],
      ));

      if ($ret)
      {
        return $ret;
      }
    }

    $this->reloadAutoload();
  }
}
