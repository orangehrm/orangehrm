<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for all symfony Propel tasks.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelBaseTask.class.php 29001 2010-04-06 18:02:32Z Kris.Wallsmith $
 */
abstract class sfPropelBaseTask extends sfBaseTask
{
  const CHECK_SCHEMA = true;
  const DO_NOT_CHECK_SCHEMA = false;

  static protected $done = false;

  protected $additionalPhingArgs = array();

  public function initialize(sfEventDispatcher $dispatcher, sfFormatter $formatter)
  {
    parent::initialize($dispatcher, $formatter);

    if (!self::$done)
    {
      sfToolkit::addIncludePath(array(
        sfConfig::get('sf_propel_runtime_path', realpath(dirname(__FILE__).'/../lib/vendor')),
        dirname(__FILE__),
      ));

      self::$done = true;
    }
  }

  protected function process(sfCommandManager $commandManager, $options)
  {
    parent::process($commandManager, $options);

    // capture phing-arg options
    if ($commandManager->getOptionSet()->hasOption('phing-arg'))
    {
      $this->additionalPhingArgs = $commandManager->getOptionValue('phing-arg');
    }
  }

  protected function schemaToYML($checkSchema = self::CHECK_SCHEMA, $prefix = '')
  {
    $finder = sfFinder::type('file')->name('*schema.xml')->prune('doctrine');

    $schemas = array_unique(array_merge($finder->in(sfConfig::get('sf_config_dir')), $finder->in($this->configuration->getPluginSubPaths('/config'))));
    if (self::CHECK_SCHEMA === $checkSchema && !count($schemas))
    {
      throw new sfCommandException('You must create a schema.xml file.');
    }

    $dbSchema = new sfPropelDatabaseSchema();
    foreach ($schemas as $schema)
    {
      $dbSchema->loadXML($schema);

      $this->logSection('schema', sprintf('converting "%s" to YML', $schema));

      $localprefix = $prefix;

      // change prefix for plugins
      if (preg_match('#plugins[/\\\\]([^/\\\\]+)[/\\\\]#', $schema, $match))
      {
        $localprefix = $prefix.$match[1].'-';
      }

      // save converted xml files in original directories
      $yml_file_name = str_replace('.xml', '.yml', basename($schema));

      $file = str_replace(basename($schema), $prefix.$yml_file_name,  $schema);
      $this->logSection('schema', sprintf('putting %s', $file));
      file_put_contents($file, $dbSchema->asYAML());
    }
  }

  protected function schemaToXML($checkSchema = self::CHECK_SCHEMA, $prefix = '')
  {
    $finder = sfFinder::type('file')->name('*schema.yml')->prune('doctrine');
    $dirs = array_merge(array(sfConfig::get('sf_config_dir')), $this->configuration->getPluginSubPaths('/config'));
    $schemas = $finder->in($dirs);
    if (self::CHECK_SCHEMA === $checkSchema && !count($schemas))
    {
      throw new sfCommandException('You must create a schema.yml file.');
    }

    $dbSchema = new sfPropelDatabaseSchema();

    foreach ($schemas as $schema)
    {
      $schemaArray = sfYaml::load($schema);

      if (!is_array($schemaArray))
      {
        continue; // No defined schema here, skipping
      }

      if (!isset($schemaArray['classes']))
      {
        // Old schema syntax: we convert it
        $schemaArray = $dbSchema->convertOldToNewYaml($schemaArray);
      }

      $customSchemaFilename = str_replace(array(
        str_replace(DIRECTORY_SEPARATOR, '/', sfConfig::get('sf_root_dir')).'/',
        'plugins/',
        'config/',
        '/',
        'schema.yml'
      ), array('', '', '', '_', 'schema.custom.yml'), $schema);
      $customSchemas = sfFinder::type('file')->name($customSchemaFilename)->in($dirs);

      foreach ($customSchemas as $customSchema)
      {
        $this->logSection('schema', sprintf('found custom schema %s', $customSchema));

        $customSchemaArray = sfYaml::load($customSchema);
        if (!isset($customSchemaArray['classes']))
        {
          // Old schema syntax: we convert it
          $customSchemaArray = $dbSchema->convertOldToNewYaml($customSchemaArray);
        }
        $schemaArray = sfToolkit::arrayDeepMerge($schemaArray, $customSchemaArray);
      }

      $dbSchema->loadArray($schemaArray);

      $this->logSection('schema', sprintf('converting "%s" to XML', $schema));

      $localprefix = $prefix;

      // change prefix for plugins
      if (preg_match('#plugins[/\\\\]([^/\\\\]+)[/\\\\]#', $schema, $match))
      {
        $localprefix = $prefix.$match[1].'-';
      }

      // save converted xml files in original directories
      $xml_file_name = str_replace('.yml', '.xml', basename($schema));

      $file = str_replace(basename($schema), $localprefix.$xml_file_name,  $schema);
      $this->logSection('schema', sprintf('putting %s', $file));
      file_put_contents($file, $dbSchema->asXML());
    }
  }

  protected function copyXmlSchemaFromPlugins($prefix = '')
  {
    if (!$dirs = $this->configuration->getPluginSubPaths('/config'))
    {
      return;
    }

    $schemas = sfFinder::type('file')->name('*schema.xml')->prune('doctrine')->in($dirs);
    foreach ($schemas as $schema)
    {
      // reset local prefix
      $localprefix = '';

      // change prefix for plugins
      if (preg_match('#plugins[/\\\\]([^/\\\\]+)[/\\\\]#', $schema, $match))
      {
        // if the plugin name is not in the schema filename, add it
        if (!strstr(basename($schema), $match[1]))
        {
          $localprefix = $match[1].'-';
        }
      }

      // if the prefix is not in the schema filename, add it
      if (!strstr(basename($schema), $prefix))
      {
        $localprefix = $prefix.$localprefix;
      }

      $this->getFilesystem()->copy($schema, 'config'.DIRECTORY_SEPARATOR.$localprefix.basename($schema));
      if ('' === $localprefix)
      {
        $this->getFilesystem()->remove($schema);
      }
    }
  }

  protected function cleanup()
  {
    if (null === $this->commandApplication || !$this->commandApplication->withTrace())
    {
      $finder = sfFinder::type('file')->name('generated-*schema.xml')->name('*schema-transformed.xml');
      $this->getFilesystem()->remove($finder->in(array('config', 'plugins')));
    }
  }

  protected function callPhing($taskName, $checkSchema, $properties = array())
  {
    $schemas = sfFinder::type('file')->name('*schema.xml')->relative()->follow_link()->in(sfConfig::get('sf_config_dir'));
    if (self::CHECK_SCHEMA === $checkSchema && !$schemas)
    {
      throw new sfCommandException('You must create a schema.yml or schema.xml file.');
    }

    // Call phing targets
    sfToolkit::addIncludePath(array(
      sfConfig::get('sf_symfony_lib_dir'),
      sfConfig::get('sf_propel_generator_path', realpath(dirname(__FILE__).'/../vendor/propel-generator/classes')),
    ));

    $args = array();
    $bufferPhingOutput = null === $this->commandApplication || !$this->commandApplication->withTrace();

    $properties = array_merge(array(
      'build.properties'  => 'propel.ini',
      'project.dir'       => sfConfig::get('sf_config_dir'),
      'propel.output.dir' => sfConfig::get('sf_root_dir'),
    ), $properties);
    foreach ($properties as $key => $value)
    {
      $args[] = "-D$key=$value";
    }

    // Build file
    $args[] = '-f';
    $args[] = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'propel-generator'.DIRECTORY_SEPARATOR.'build.xml');

    // Logger
    if (DIRECTORY_SEPARATOR != '\\' && (function_exists('posix_isatty') && @posix_isatty(STDOUT)))
    {
      $args[] = '-logger';
      $args[] = 'phing.listener.AnsiColorLogger';
    }

    // Add our listener to detect errors
    $args[] = '-listener';
    $args[] = 'sfPhingListener';

    // Add any arbitrary arguments last
    foreach ($this->additionalPhingArgs as $arg)
    {
      if (in_array($arg, array('verbose', 'debug')))
      {
        $bufferPhingOutput = false;
      }

      $args[] = '-'.$arg;
    }

    $args[] = $taskName;

    // filter arguments through the event dispatcher
    $args = $this->dispatcher->filter(new sfEvent($this, 'propel.filter_phing_args'), $args)->getReturnValue();

    require_once dirname(__FILE__).'/sfPhing.class.php';

    // enable output buffering
    Phing::setOutputStream(new OutputStream(fopen('php://output', 'w')));
    Phing::startup();
    Phing::setProperty('phing.home', getenv('PHING_HOME'));

    $this->logSection('propel', 'Running "'.$taskName.'" phing task');

    if ($bufferPhingOutput)
    {
      ob_start();
    }

    $m = new sfPhing();
    $m->execute($args);
    $m->runBuild();

    if ($bufferPhingOutput)
    {
      ob_end_clean();
    }

    chdir(sfConfig::get('sf_root_dir'));

    // any errors?
    $ret = true;
    if (sfPhingListener::hasErrors())
    {
      $messages = array('Some problems occurred when executing the task:');

      foreach (sfPhingListener::getExceptions() as $exception)
      {
        $messages[] = '';
        $messages[] = preg_replace('/^.*build\-propel\.xml/', 'build-propel.xml', $exception->getMessage());
        $messages[] = '';
      }

      if (count(sfPhingListener::getErrors()))
      {
        $messages[] = 'If the exception message is not clear enough, read the output of the task for';
        $messages[] = 'more information';
      }

      $this->logBlock($messages, 'ERROR_LARGE');

      $ret = false;
    }

    return $ret;
  }

  protected function getPhingPropertiesForConnection($databaseManager, $connection)
  {
    $database = $databaseManager->getDatabase($connection);

    return array(
      'propel.database'          => $database->getParameter('phptype'),
      'propel.database.driver'   => $database->getParameter('phptype'),
      'propel.database.url'      => $database->getParameter('dsn'),
      'propel.database.user'     => $database->getParameter('username'),
      'propel.database.password' => $database->getParameter('password'),
      'propel.database.encoding' => $database->getParameter('encoding'),
    );
  }

  protected function getProperties($file)
  {
    $properties = array();

    if (false === $lines = @file($file))
    {
      throw new sfCommandException('Unable to parse contents of the "sqldb.map" file.');
    }

    foreach ($lines as $line)
    {
      $line = trim($line);

      if ('' == $line)
      {
        continue;
      }

      if (in_array($line[0], array('#', ';')))
      {
        continue;
      }

      $pos = strpos($line, '=');
      $properties[trim(substr($line, 0, $pos))] = trim(substr($line, $pos + 1));
    }

    return $properties;
  }
}
