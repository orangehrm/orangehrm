<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for tasks that depends on a sfCommandApplication object.
 *
 * @package    symfony
 * @subpackage task
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCommandApplicationTask.class.php 23651 2009-11-06 08:08:54Z fabien $
 */
abstract class sfCommandApplicationTask extends sfTask
{
  protected
    $mailer = null,
    $routing = null,
    $commandApplication = null;

  /**
   * Sets the command application instance for this task.
   *
   * @param sfCommandApplication $commandApplication A sfCommandApplication instance
   */
  public function setCommandApplication(sfCommandApplication $commandApplication = null)
  {
    $this->commandApplication = $commandApplication;
  }

  /**
   * @see sfTask
   */
  public function log($messages)
  {
    if (null === $this->commandApplication || $this->commandApplication->isVerbose())
    {
      parent::log($messages);
    }
  }

  /**
   * @see sfTask
   */
  public function logSection($section, $message, $size = null, $style = 'INFO')
  {
    if (null === $this->commandApplication || $this->commandApplication->isVerbose())
    {
      parent::logSection($section, $message, $size, $style);
    }
  }

  /**
   * Creates a new task object.
   *
   * @param  string $name The name of the task
   *
   * @return sfTask
   *
   * @throws LogicException If the current task has no command application
   */
  protected function createTask($name)
  {
    if (null === $this->commandApplication)
    {
      throw new LogicException('Unable to create a task as no command application is associated with this task yet.');
    }

    $task = $this->commandApplication->getTaskToExecute($name);

    if ($task instanceof sfCommandApplicationTask)
    {
      $task->setCommandApplication($this->commandApplication);
    }

    return $task;
  }

  /**
   * Executes another task in the context of the current one.
   *
   * @param  string  $name      The name of the task to execute
   * @param  array   $arguments An array of arguments to pass to the task
   * @param  array   $options   An array of options to pass to the task
   *
   * @return Boolean The returned value of the task run() method
   *
   * @see createTask()
   */
  protected function runTask($name, $arguments = array(), $options = array())
  {
    return $this->createTask($name)->run($arguments, $options);
  }

  /**
   * Returns a mailer instance.
   *
   * Notice that your task should accept an application option.
   * The mailer configuration is read from the current configuration
   * instance, which is automatically created according to the current
   * --application option.
   *
   * @return sfMailer A sfMailer instance
   */
  protected function getMailer()
  {
    if (!$this->mailer)
    {
      $this->mailer = $this->initializeMailer();
    }

    return $this->mailer;
  }

  protected function initializeMailer()
  {
    require_once sfConfig::get('sf_symfony_lib_dir').'/vendor/swiftmailer/classes/Swift.php';
    Swift::registerAutoload();
    sfMailer::initialize();

    $config = sfFactoryConfigHandler::getConfiguration($this->configuration->getConfigPaths('config/factories.yml'));

    return new $config['mailer']['class']($this->dispatcher, $config['mailer']['param']);
  }

  /**
   * Returns a routing instance.
   *
   * Notice that your task should accept an application option.
   * The routing configuration is read from the current configuration
   * instance, which is automatically created according to the current
   * --application option.
   *
   * @return sfRouting A sfRouting instance
   */
  protected function getRouting()
  {
    if (!$this->routing)
    {
      $this->routing = $this->initializeRouting();
    }

    return $this->routing;
  }

  protected function initializeRouting()
  {
    $config = sfFactoryConfigHandler::getConfiguration($this->configuration->getConfigPaths('config/factories.yml'));
    $params = array_merge($config['routing']['param'], array('load_configuration' => false, 'logging' => false));

    $handler = new sfRoutingConfigHandler();
    $routes = $handler->evaluate($this->configuration->getConfigPaths('config/routing.yml'));

    $routing = new $config['routing']['class']($this->dispatcher, null, $params);
    $routing->setRoutes($routes);

    $this->dispatcher->notify(new sfEvent($routing, 'routing.load_configuration'));

    return $routing;
  }
}
