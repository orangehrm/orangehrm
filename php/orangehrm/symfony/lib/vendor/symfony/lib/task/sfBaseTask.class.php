<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for all symfony tasks.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfBaseTask.class.php 16171 2009-03-11 08:04:47Z fabien $
 */
abstract class sfBaseTask extends sfCommandApplicationTask
{
  protected
    $configuration = null;

  /**
   * @see sfTask
   */
  protected function doRun(sfCommandManager $commandManager, $options)
  {
    $event = $this->dispatcher->filter(new sfEvent($this, 'command.filter_options', array('command_manager' => $commandManager)), $options);
    $options = $event->getReturnValue();

    $this->process($commandManager, $options);

    $event = new sfEvent($this, 'command.pre_command', array('arguments' => $commandManager->getArgumentValues(), 'options' => $commandManager->getOptionValues()));
    $this->dispatcher->notifyUntil($event);
    if ($event->isProcessed())
    {
      return $event->getReturnValue();
    }

    $this->checkProjectExists();

    $application = $commandManager->getArgumentSet()->hasArgument('application') ? $commandManager->getArgumentValue('application') : ($commandManager->getOptionSet()->hasOption('application') ? $commandManager->getOptionValue('application') : null);
    $env = $commandManager->getOptionSet()->hasOption('env') ? $commandManager->getOptionValue('env') : 'test';

    if (true === $application)
    {
      $application = $this->getFirstApplication();

      if ($commandManager->getOptionSet()->hasOption('application'))
      {
        $commandManager->setOption($commandManager->getOptionSet()->getOption('application'), $application);
      }
    }

    $this->configuration = $this->createConfiguration($application, $env);

    if (!is_null($this->commandApplication) && !$this->commandApplication->withTrace())
    {
      sfConfig::set('sf_logging_enabled', false);
    }

    $ret = $this->execute($commandManager->getArgumentValues(), $commandManager->getOptionValues());

    $this->dispatcher->notify(new sfEvent($this, 'command.post_command'));

    return $ret;
  }

  /**
   * Returns the filesystem instance.
   *
   * @return sfFilesystem A sfFilesystem instance
   */
  public function getFilesystem()
  {
    if (!isset($this->filesystem))
    {
      if (is_null($this->commandApplication) || $this->commandApplication->isVerbose())
      {
        $this->filesystem = new sfFilesystem($this->dispatcher, $this->formatter);
      }
      else
      {
        $this->filesystem = new sfFilesystem();
      }
    }

    return $this->filesystem;
  }

  /**
   * Checks if the current directory is a symfony project directory.
   *
   * @return true if the current directory is a symfony project directory, false otherwise
   */
  public function checkProjectExists()
  {
    if (!file_exists('symfony'))
    {
      throw new sfException('You must be in a symfony project directory.');
    }
  }

  /**
   * Checks if an application exists.
   *
   * @param  string $app  The application name
   *
   * @return bool true if the application exists, false otherwise
   */
  public function checkAppExists($app)
  {
    if (!is_dir(sfConfig::get('sf_apps_dir').'/'.$app))
    {
      throw new sfException(sprintf('Application "%s" does not exist', $app));
    }
  }

  /**
   * Checks if a module exists.
   *
   * @param  string $app     The application name
   * @param  string $module  The module name
   *
   * @return bool true if the module exists, false otherwise
   */
  public function checkModuleExists($app, $module)
  {
    if (!is_dir(sfConfig::get('sf_apps_dir').'/'.$app.'/modules/'.$module))
    {
      throw new sfException(sprintf('Module "%s/%s" does not exist.', $app, $module));
    }
  }

  /**
   * Creates a configuration object.
   *
   * @param string  $application The application name
   * @param string  $env         The environment name
   *
   * @return sfProjectConfiguration A sfProjectConfiguration instance
   */
  protected function createConfiguration($application, $env)
  {
    if (!is_null($application))
    {
      $this->checkAppExists($application);

      require_once sfConfig::get('sf_config_dir').'/ProjectConfiguration.class.php';

      $configuration = ProjectConfiguration::getApplicationConfiguration($application, $env, true, null, $this->dispatcher);
    }
    else
    {
      if (file_exists(sfConfig::get('sf_config_dir').'/ProjectConfiguration.class.php'))
      {
        require_once sfConfig::get('sf_config_dir').'/ProjectConfiguration.class.php';
        $configuration = new ProjectConfiguration(null, $this->dispatcher);
      }
      else
      {
        $configuration = new sfProjectConfiguration(getcwd(), $this->dispatcher);
      }

      if (!is_null($env))
      {
        sfConfig::set('sf_environment', $env);
      }

      $autoloader = sfSimpleAutoload::getInstance(sfConfig::get('sf_cache_dir').'/project_autoload.cache');
      $autoloader->addFiles(sfFinder::type('file')->prune('symfony')->follow_link()->name('*.php')->in(sfConfig::get('sf_lib_dir')));
      $autoloader->register();
    }

    return $configuration;
  }

  /**
   * Returns the first application in apps.
   *
   * @return string The Application name
   */
  protected function getFirstApplication()
  {
    if (count($dirs = sfFinder::type('dir')->ignore_version_control()->maxdepth(0)->follow_link()->relative()->in(sfConfig::get('sf_apps_dir'))))
    {
      return $dirs[0];
    }

    return null;
  }
}
