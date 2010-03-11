<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfDoctrineBaseTask.class.php');

/**
 * Drops Databases, Creates Databases, Generates Doctrine model, SQL, initializes database, load data and run 
 * all test suites
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineBuildAllReloadTask.class.php 8743 2008-05-03 05:02:39Z Jonathan.Wage $
 */
class sfDoctrineBuildAllReloadTestAllTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('append', null, sfCommandOption::PARAMETER_NONE, 'Don\'t delete current data in the database'),
      new sfCommandOption('dir', null, sfCommandOption::PARAMETER_REQUIRED | sfCommandOption::IS_ARRAY, 'The directories to look for fixtures'),
      new sfCommandOption('force', null, sfCommandOption::PARAMETER_NONE, 'Whether to force dropping of the database'),
    ));

    $this->aliases = array('doctrine-build-all-reload-test-all');
    $this->namespace = 'doctrine';
    $this->name = 'build-all-reload-test-all';
    $this->briefDescription = 'Generates Doctrine model, SQL, initializes database, load data and run all tests';

    $this->detailedDescription = <<<EOF
The [doctrine:build-all-reload|INFO] task is a shortcut for four other tasks:

  [./symfony doctrine:build-all-reload-test-all frontend|INFO]

The task is equivalent to:
  
  [./symfony doctrine:drop-db|INFO]
  [./symfony doctrine:build-db|INFO]
  [./symfony doctrine:build-model|INFO]
  [./symfony doctrine:insert-sql|INFO]
  [./symfony doctrine:data-load|INFO]
  [./symfony test-all|INFO]

The task takes an application argument because of the [doctrine:data-load|COMMENT]
task. See [doctrine:data-load|COMMENT] help page for more information.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $buildAllReload = new sfDoctrineBuildAllReloadTask($this->dispatcher, $this->formatter);
    $buildAllReload->setCommandApplication($this->commandApplication);

    $buildAllReloadOptions = array();
    if (!empty($options['application']))
    {
      $buildAllReloadOptions[] = '--application=' . $options['application'];
    }
    $buildAllReloadOptions[] = '--env='.$options['env'];
    if (!empty($options['dir']))
    {
      $buildAllReloadOptions[] = '--dir=' . implode(' --dir=', $options['dir']);
    }
    if (isset($options['append']) && $options['append'])
    {
      $buildAllReloadOptions[] = '--append';
    }
    if (isset($options['no-confirmation']) && $options['no-confirmation'])
    {
      $buildAllReloadOptions[] = '--no-confirmation';
    }
    $ret = $buildAllReload->run(array(), $buildAllReloadOptions);

    if ($ret)
    {
      return $ret;
    }

    $this->logSection('doctrine', 'running test suite');
    
    $testAll = new sfTestAllTask($this->dispatcher, $this->formatter);
    $testAll->setCommandApplication($this->commandApplication);
    $testAll->run();
  }
}