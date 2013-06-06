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
 * Creates database for current model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineBuildDbTask.class.php 24341 2009-11-24 15:01:58Z Kris.Wallsmith $
 */
class sfDoctrineBuildDbTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('database', sfCommandArgument::OPTIONAL | sfCommandArgument::IS_ARRAY, 'A specific database'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->aliases = array('doctrine:create-db');
    $this->namespace = 'doctrine';
    $this->name = 'build-db';
    $this->briefDescription = 'Creates database for current model';

    $this->detailedDescription = <<<EOF
The [doctrine:build-db|INFO] task creates one or more databases based on
configuration in [config/databases.yml|COMMENT]:

  [./symfony doctrine:build-db|INFO]

You can specify what databases to create by providing their names:

  [./symfony doctrine:build-db slave1 slave2|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $databases = $this->getDoctrineDatabases($databaseManager, count($arguments['database']) ? $arguments['database'] : null);

    $environment = $this->configuration instanceof sfApplicationConfiguration ? $this->configuration->getEnvironment() : 'all';

    foreach ($databases as $name => $database)
    {
      $this->logSection('doctrine', sprintf('Creating "%s" environment "%s" database', $environment, $name));
      try
      {
        $database->getDoctrineConnection()->createDatabase();
      }
      catch (Exception $e)
      {
        $this->logSection('doctrine', $e->getMessage(), null, 'ERROR');
      }
    }
  }
}
