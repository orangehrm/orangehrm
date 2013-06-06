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
 * Generate migration classes by producing a diff between your old and new schema.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineGenerateMigrationsDiffTask.class.php 28871 2010-03-29 17:28:03Z Jonathan.Wage $
 */
class sfDoctrineGenerateMigrationsDiffTask extends sfDoctrineBaseTask
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
    $this->name = 'generate-migrations-diff';
    $this->briefDescription = 'Generate migration classes by producing a diff between your old and new schema.';

    $this->detailedDescription = <<<EOF
The [doctrine:generate-migrations-diff|INFO] task generates migration classes by
producing a diff between your old and new schema.

  [./symfony doctrine:generate-migrations-diff|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $config = $this->getCliConfig();

    $this->logSection('doctrine', 'generating migration diff');

    if (!is_dir($config['migrations_path']))
    {
      $this->getFilesystem()->mkdirs($config['migrations_path']);
    }

    spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));

    $this->callDoctrineCli('generate-migrations-diff', array(
      'yaml_schema_path' => $this->prepareSchemaFile($config['yaml_schema_path']),
    ));
  }
}
