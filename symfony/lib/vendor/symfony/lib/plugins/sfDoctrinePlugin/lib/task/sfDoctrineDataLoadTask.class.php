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
 * Loads YAML fixture data.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineDataLoadTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfDoctrineDataLoadTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('dir_or_file', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'Directory or file to load'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('append', null, sfCommandOption::PARAMETER_NONE, 'Don\'t delete current data in the database'),
    ));

    $this->namespace = 'doctrine';
    $this->name = 'data-load';
    $this->briefDescription = 'Loads YAML fixture data';

    $this->detailedDescription = <<<EOF
The [doctrine:data-load|INFO] task loads data fixtures into the database:

  [./symfony doctrine:data-load|INFO]

The task loads data from all the files found in [data/fixtures/|COMMENT].

If you want to load data from specific files or directories, you can append
them as arguments:

  [./symfony doctrine:data-load data/fixtures/dev data/fixtures/users.yml|INFO]

If you don't want the task to remove existing data in the database,
use the [--append|COMMENT] option:

  [./symfony doctrine:data-load --append|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    if (!count($arguments['dir_or_file']))
    {
      // pull default from CLI config array
      $config = $this->getCliConfig();
      $arguments['dir_or_file'] = $config['data_fixtures_path'];
    }

    $doctrineArguments = array(
      'data_fixtures_path' => $arguments['dir_or_file'],
      'append'             => $options['append'],
    );

    foreach ($arguments['dir_or_file'] as $target)
    {
      $this->logSection('doctrine', sprintf('Loading data fixtures from "%s"', $target));
    }

    $this->callDoctrineCli('load-data', $doctrineArguments);
  }
}
