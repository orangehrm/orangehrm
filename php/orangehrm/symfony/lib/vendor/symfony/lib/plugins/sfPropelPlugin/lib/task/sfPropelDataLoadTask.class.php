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
 * Loads YAML fixture data.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelDataLoadTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfPropelDataLoadTask extends sfPropelBaseTask
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
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'cli'),
      new sfCommandOption('append', null, sfCommandOption::PARAMETER_NONE, 'Don\'t delete current data in the database'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
    ));

    $this->namespace = 'propel';
    $this->name = 'data-load';
    $this->briefDescription = 'Loads YAML fixture data';

    $this->detailedDescription = <<<EOF
The [propel:data-load|INFO] task loads data fixtures into the database:

  [./symfony propel:data-load|INFO]

The task loads data from all the files found in [data/fixtures/|COMMENT].

If you want to load data from specific files or directories, you can append
them as arguments:

  [./symfony propel:data-load data/fixtures/dev data/fixtures/users.yml|INFO]

The task use the [propel|COMMENT] connection as defined in [config/databases.yml|COMMENT].
You can use another connection by using the [--connection|COMMENT] option:

  [./symfony propel:data-load --connection="name"|INFO]

If you don't want the task to remove existing data in the database,
use the [--append|COMMENT] option:

  [./symfony propel:data-load --append|INFO]

If you want to use a specific database configuration from an application, you can use
the [application|COMMENT] option:

  [./symfony propel:data-load --application=frontend|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    if (count($arguments['dir_or_file']))
    {
      $fixturesDirs = $arguments['dir_or_file'];
    }
    else
    {
      $fixturesDirs = array_merge(array(sfConfig::get('sf_data_dir').'/fixtures'), $this->configuration->getPluginSubPaths('/data/fixtures'));
    }

    $data = new sfPropelData();
    $data->setDeleteCurrentData(!$options['append']);

    $dirs = array();
    foreach ($fixturesDirs as $fixturesDir)
    {
      if (!is_readable($fixturesDir))
      {
        continue;
      }

      $this->logSection('propel', sprintf('load data from "%s"', $fixturesDir));
      $dirs[] = $fixturesDir;
    }

    $data->loadData($dirs, $options['connection']);
  }
}
