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
 * Inserts SQL for current model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineInsertSqlTask.class.php 27942 2010-02-12 14:05:53Z Kris.Wallsmith $
 */
class sfDoctrineInsertSqlTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'doctrine';
    $this->name = 'insert-sql';
    $this->briefDescription = 'Inserts SQL for current model';

    $this->detailedDescription = <<<EOF
The [doctrine:insert-sql|INFO] task creates database tables:

  [./symfony doctrine:insert-sql|INFO]

The task connects to the database and creates tables for all the
[lib/model/doctrine/*.class.php|COMMENT] files.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->logSection('doctrine', 'creating tables');

    $databaseManager = new sfDatabaseManager($this->configuration);
    $config = $this->getCliConfig();

    Doctrine_Core::loadModels($config['models_path'], Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
    Doctrine_Core::createTablesFromArray(Doctrine_Core::getLoadedModels());

    $this->logSection('doctrine', 'created tables successfully');
  }
}
