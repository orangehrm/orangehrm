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
 * Drops database for current model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineDropDbTask.class.php 14213 2008-12-19 21:03:13Z Jonathan.Wage $
 */
class sfDoctrineDropDbTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('no-confirmation', null, sfCommandOption::PARAMETER_NONE, 'Whether to force dropping of the database')
    ));

    $this->aliases = array('doctrine-drop-db');
    $this->namespace = 'doctrine';
    $this->name = 'drop-db';
    $this->briefDescription = 'Drops database for current model';

    $this->detailedDescription = <<<EOF
The [doctrine:drop-db|INFO] task drops the database:

  [./symfony doctrine:drop-db|INFO]

The task read connection information in [config/doctrine/databases.yml|COMMENT]:
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    if (
      !$options['no-confirmation']
      &&
      !$this->askConfirmation(array('This command will remove all data in your database.', 'Are you sure you want to proceed? (y/N)'), null, false)
    )
    {
      $this->logSection('doctrine', 'task aborted');

      return 1;
    }

    $this->logSection('doctrine', 'dropping databases');

    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->callDoctrineCli('drop-db', array('force' => true));
  }
}