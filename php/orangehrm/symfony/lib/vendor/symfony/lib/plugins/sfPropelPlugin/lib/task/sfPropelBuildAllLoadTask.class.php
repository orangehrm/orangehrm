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
 * @version    SVN: $Id: sfPropelBuildAllLoadTask.class.php 23922 2009-11-14 14:58:38Z fabien $
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

    $buildAll = new sfPropelBuildAllTask($this->dispatcher, $this->formatter);
    $buildAll->setCommandApplication($this->commandApplication);
    $buildAll->setConfiguration($this->configuration);
    $ret = $buildAll->run(array(), array(
      'phing-arg'       => $options['phing-arg'],
      'skip-forms'      => $options['skip-forms'],
      'classes-only'    => $options['classes-only'],
      'no-confirmation' => $options['no-confirmation'],
    ));

    if (0 == $ret)
    {
      $loadData = new sfPropelDataLoadTask($this->dispatcher, $this->formatter);
      $loadData->setCommandApplication($this->commandApplication);
      $loadData->setConfiguration($this->configuration);
      $loadData->run($options['dir'], array(
        'append' => $options['append'],
      ));
    }

    $this->cleanup();

    return $ret;
  }
}
