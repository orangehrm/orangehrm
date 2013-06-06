<?php

/*
 * This file is part of the symfony package.
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/sfDoctrineBaseTask.class.php');

/**
 * Create tables for specified list of models
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineCreateModelTablesTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfDoctrineCreateModelTables extends sfDoctrineBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('models', sfCommandArgument::IS_ARRAY, 'The list of models', array()),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'doctrine';
    $this->name = 'create-model-tables';
    $this->briefDescription = 'Drop and recreate tables for specified models.';

    $this->detailedDescription = <<<EOF
The [doctrine:create-model-tables|INFO] Drop and recreate tables for specified models:

  [./symfony doctrine:create-model-tables User|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    $buildModel = new sfDoctrineBuildModelTask($this->dispatcher, $this->formatter);
    $buildModel->setCommandApplication($this->commandApplication);
    $buildModel->setConfiguration($this->configuration);
    $ret = $buildModel->run();

    $connections = array();
    $models = $arguments['models'];
    foreach ($models as $key => $model)
    {
      $model = trim($model);
      $conn = Doctrine_Core::getTable($model)->getConnection();
      $connections[$conn->getName()][] = $model;
    }

    foreach ($connections as $name => $models)
    {
      $this->logSection('doctrine', 'dropping model tables for connection "'.$name.'"');

      $conn = Doctrine_Manager::getInstance()->getConnection($name);
      $models = $conn->unitOfWork->buildFlushTree($models);
      $models = array_reverse($models);

      foreach ($models as $model)
      {
        $tableName = Doctrine_Core::getTable($model)->getOption('tableName');

        $this->logSection('doctrine', 'dropping table "'.$tableName.'"');

        try {
          $conn->export->dropTable($tableName);
        }
        catch (Exception $e)
        {
          $this->logSection('doctrine', 'dropping table failed: '.$e->getMessage());
        }
      }

      $this->logSection('doctrine', 'recreating tables for models');

      Doctrine_Core::createTablesFromArray($models);
    }
  }
}