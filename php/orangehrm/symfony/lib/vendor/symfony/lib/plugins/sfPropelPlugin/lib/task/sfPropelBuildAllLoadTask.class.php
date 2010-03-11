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
 * Generates Propel model, SQL, initializes database, and load data.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelBuildAllLoadTask.class.php 12537 2008-11-01 14:43:27Z fabien $
 */
class sfPropelBuildAllLoadTask extends sfPropelBaseTask
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
      new sfCommandOption('append', null, sfCommandOption::PARAMETER_NONE, 'Don\'t delete current data in the database'),
      new sfCommandOption('dir', null, sfCommandOption::PARAMETER_REQUIRED | sfCommandOption::IS_ARRAY, 'The directories to look for fixtures'),
    ));

    $this->aliases = array('propel-build-all-load');
    $this->namespace = 'propel';
    $this->name = 'build-all-load';
    $this->briefDescription = 'Generates Propel model and form classes, SQL, initializes the database, and loads data';

    $this->detailedDescription = <<<EOF
The [propel:build-all-load|INFO] task is a shortcut for two other tasks:

  [./symfony propel:build-all-load|INFO]

The task is equivalent to:

  [./symfony propel:build-all|INFO]
  [./symfony propel:data-load|INFO]

See those tasks' help pages for more information.

To bypass the confirmation, you can pass the [no-confirmation|COMMENT]
option:

  [./symfony propel:buil-all-load --no-confirmation|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // load Propel configuration before Phing
    $databaseManager = new sfDatabaseManager($this->configuration);

    require_once dirname(__FILE__) . '/../addon/sfPropelAutoload.php';

    $buildAll = new sfPropelBuildAllTask($this->dispatcher, $this->formatter);
    $buildAll->setCommandApplication($this->commandApplication);

    $buildAllOptions = array('--env='.$options['env'], '--connection='.$options['connection']);
    foreach ($options['phing-arg'] as $arg)
    {
      $buildAllOptions[] = '--phing-arg='.escapeshellarg($arg);
    }
    if ($options['application'])
    {
      $buildAllOptions[] = '--application='.$options['application'];
    }
    if ($options['skip-forms'])
    {
      $buildAllOptions[] = '--skip-forms';
    }
    if ($options['classes-only'])
    {
      $buildAllOptions[] = '--classes-only';
    }
    if ($options['no-confirmation'])
    {
      $buildAllOptions[] = '--no-confirmation';
    }
    $ret = $buildAll->run(array(), $buildAllOptions);

    if (0 == $ret)
    {
      $loadData = new sfPropelLoadDataTask($this->dispatcher, $this->formatter);
      $loadData->setCommandApplication($this->commandApplication);

      $dataLoadOptions = array('--env='.$options['env'], '--connection='.$options['connection']);
      if ($options['application'])
      {
        $dataLoadOptions[] = '--application='.$options['application'];
      }
      if ($options['dir'])
      {
        foreach ($options['dir'] as $dir)
        {
          $dataLoadOptions[] = '--dir='.$dir;
        }
      }
      if ($options['append'])
      {
        $dataLoadOptions[] = '--append';
      }

      $loadData->run(array(), $dataLoadOptions);
    }

    $this->cleanup();

    return $ret;
  }
}
