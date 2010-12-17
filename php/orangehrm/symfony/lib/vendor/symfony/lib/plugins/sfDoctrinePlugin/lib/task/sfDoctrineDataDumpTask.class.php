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
 * Dumps data to the fixtures directory.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineDataDumpTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfDoctrineDataDumpTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('target', sfCommandArgument::OPTIONAL, 'The target filename'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', true),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'doctrine';
    $this->name = 'data-dump';
    $this->briefDescription = 'Dumps data to the fixtures directory';

    $this->detailedDescription = <<<EOF
The [doctrine:data-dump|INFO] task dumps database data:

  [./symfony doctrine:data-dump|INFO]

The task dumps the database data in [data/fixtures/%target%|COMMENT].

The dump file is in the YML format and can be reimported by using
the [doctrine:data-load|INFO] task.

  [./symfony doctrine:data-load|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $config = $this->getCliConfig();

    $args = array(
      'data_fixtures_path' => $config['data_fixtures_path'][0],
    );

    if (!is_dir($args['data_fixtures_path']))
    {
      $this->getFilesystem()->mkdirs($args['data_fixtures_path']);
    }

    if ($arguments['target'])
    {
      $filename = $arguments['target'];

      if (!sfToolkit::isPathAbsolute($filename))
      {
        $filename = $args['data_fixtures_path'].'/'.$filename;
      }

      $this->getFilesystem()->mkdirs(dirname($filename));

      $args['data_fixtures_path'] = $filename;
    }

    $this->logSection('doctrine', sprintf('dumping data to fixtures to "%s"', $args['data_fixtures_path']));
    $this->callDoctrineCli('dump-data', $args);
  }
}