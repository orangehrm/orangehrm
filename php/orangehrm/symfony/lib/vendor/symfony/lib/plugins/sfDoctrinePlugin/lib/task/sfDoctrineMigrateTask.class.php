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
 * @version    SVN: $Id: sfDoctrineMigrateTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfDoctrineMigrateTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('version', sfCommandArgument::OPTIONAL, 'The version to migrate to'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('up', null, sfCommandOption::PARAMETER_NONE, 'Migrate up one version'),
      new sfCommandOption('down', null, sfCommandOption::PARAMETER_NONE, 'Migrate down one version'),
      new sfCommandOption('dry-run', null, sfCommandOption::PARAMETER_NONE, 'Do not persist migrations'),
    ));

    $this->namespace = 'doctrine';
    $this->name = 'migrate';
    $this->briefDescription = 'Migrates database to current/specified version';

    $this->detailedDescription = <<<EOF
The [doctrine:migrate|INFO] task migrates the database:

  [./symfony doctrine:migrate|INFO]

Provide a version argument to migrate to a specific version:

  [./symfony doctrine:migrate 10|INFO]

To migration up or down one migration, use the [--up|COMMENT] or [--down|COMMENT] options:

  [./symfony doctrine:migrate --down|INFO]

If your database supports rolling back DDL statements, you can run migrations
in dry-run mode using the [--dry-run|COMMENT] option:

  [./symfony doctrine:migrate --dry-run|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    $config = $this->getCliConfig();
    $migration = new Doctrine_Migration($config['migrations_path']);
    $from = $migration->getCurrentVersion();

    if (is_numeric($arguments['version']))
    {
      $version = $arguments['version'];
    }
    else if ($options['up'])
    {
      $version = $from + 1;
    }
    else if ($options['down'])
    {
      $version = $from - 1;
    }
    else
    {
      $version = $migration->getLatestVersion();
    }

    if ($from == $version)
    {
      $this->logSection('doctrine', sprintf('Already at migration version %s', $version));
      return;
    }

    $this->logSection('doctrine', sprintf('Migrating from version %s to %s%s', $from, $version, $options['dry-run'] ? ' (dry run)' : ''));
    try
    {
      $migration->migrate($version, $options['dry-run']);
    }
    catch (Exception $e)
    {
    }

    // render errors
    if ($migration->hasErrors())
    {
      if ($this->commandApplication && $this->commandApplication->withTrace())
      {
        $this->logSection('doctrine', 'The following errors occurred:');
        foreach ($migration->getErrors() as $error)
        {
          $this->commandApplication->renderException($error);
        }
      }
      else
      {
        $this->logBlock(array_merge(
          array('The following errors occurred:', ''),
          array_map(create_function('$e', 'return \' - \'.$e->getMessage();'), $migration->getErrors())
        ), 'ERROR_LARGE');
      }

      return 1;
    }

    $this->logSection('doctrine', 'Migration complete');
  }
}
