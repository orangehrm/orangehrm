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
 * Loads data from fixtures directory.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelLoadDataTask.class.php 13140 2008-11-18 18:57:24Z Kris.Wallsmith $
 */
class sfPropelLoadDataTask extends sfPropelBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'cli'),
      new sfCommandOption('append', null, sfCommandOption::PARAMETER_NONE, 'Don\'t delete current data in the database'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('dir', null, sfCommandOption::PARAMETER_REQUIRED | sfCommandOption::IS_ARRAY, 'The directories to look for fixtures'),
    ));

    $this->aliases = array('propel-load-data');
    $this->namespace = 'propel';
    $this->name = 'data-load';
    $this->briefDescription = 'Loads data from fixtures directory';

    $this->detailedDescription = <<<EOF
The [propel:data-load|INFO] task loads data fixtures into the database:

  [./symfony propel:data-load|INFO]

The task loads data from all the files found in [data/fixtures/|COMMENT].

If you want to load data from other directories, you can use
the [--dir|COMMENT] option:

  [./symfony propel:data-load --dir="data/fixtures" --dir="data/data"|INFO]

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

    if (count($options['dir']))
    {
      $fixturesDirs = $options['dir'];
    }
    else
    {
      $fixturesDirs = sfFinder::type('dir')->name('fixtures')->in(array_merge($this->configuration->getPluginSubPaths('/data'), array(sfConfig::get('sf_data_dir'))));
    }

    $data = new sfPropelData();
    $data->setDeleteCurrentData(isset($options['append']) ? ($options['append'] ? false : true) : true);

    foreach ($fixturesDirs as $fixturesDir)
    {
      if (!is_readable($fixturesDir))
      {
        continue;
      }

      $this->logSection('propel', sprintf('load data from "%s"', $fixturesDir));
      $data->loadData($fixturesDir, $options['connection']);
    }
  }
}
