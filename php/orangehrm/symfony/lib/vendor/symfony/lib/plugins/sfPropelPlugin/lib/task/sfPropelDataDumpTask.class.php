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
 * Dumps data to the fixtures directory.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelDataDumpTask.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
class sfPropelDataDumpTask extends sfPropelBaseTask
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
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environement', 'cli'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'propel'),
      new sfCommandOption('classes', null, sfCommandOption::PARAMETER_REQUIRED, 'The class names to dump (separated by a colon)', null),
    ));

    $this->namespace = 'propel';
    $this->name = 'data-dump';
    $this->briefDescription = 'Dumps data to the fixtures directory';

    $this->detailedDescription = <<<EOF
The [propel:data-dump|INFO] task dumps database data:

  [./symfony propel:data-dump > data/fixtures/dump.yml|INFO]

By default, the task outputs the data to the standard output,
but you can also pass a filename as a second argument:

  [./symfony propel:data-dump dump.yml|INFO]

The task will dump data in [data/fixtures/%target%|COMMENT]
(data/fixtures/dump.yml in the example).

The dump file is in the YML format and can be re-imported by using
the [propel:data-load|INFO] task.

By default, the task use the [propel|COMMENT] connection as defined in [config/databases.yml|COMMENT].
You can use another connection by using the [connection|COMMENT] option:

  [./symfony propel:data-dump --connection="name"|INFO]

If you only want to dump some classes, use the [classes|COMMENT] option:

  [./symfony propel:data-dump --classes="Article,Category"|INFO]

If you want to use a specific database configuration from an application, you can use
the [application|COMMENT] option:

  [./symfony propel:data-dump --application=frontend|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    $filename = $arguments['target'];
    if (null !== $filename && !sfToolkit::isPathAbsolute($filename))
    {
      $dir = sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'fixtures';
      $this->getFilesystem()->mkdirs($dir);
      $filename = $dir.DIRECTORY_SEPARATOR.$filename;

      $this->logSection('propel', sprintf('dumping data to "%s"', $filename));
    }

    $data = new sfPropelData();

    $classes = null === $options['classes'] ? 'all' : explode(',', $options['classes']);

    if (null !== $filename)
    {
      $data->dumpData($filename, $classes, $options['connection']);
    }
    else
    {
      fwrite(STDOUT, sfYaml::dump($data->getData($classes, $options['connection']), 3));
    }
  }
}
