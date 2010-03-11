<?php

class sfPhingListener implements BuildListener
{
  static protected
    $exceptions = array(),
    $errors     = array();

  static public function hasErrors()
  {
    return count(self::$errors) || count(self::$exceptions);
  }

  static public function getErrors()
  {
    return self::$errors;
  }

  static public function getExceptions()
  {
    return self::$exceptions;
  }

  /**
   * Fired before any targets are started.
   *
   * @param BuildEvent The BuildEvent
   */
  public function buildStarted(BuildEvent $event)
  {
    self::$exceptions = array();
  }

  /**
   * Fired after the last target has finished.
   *
   * @param BuildEvent The BuildEvent
   * @see BuildEvent::getException()
   */
  public function buildFinished(BuildEvent $event)
  {
  }

  /**
   * Fired when a target is started.
   *
   * @param BuildEvent The BuildEvent
   * @see BuildEvent::getTarget()
   */
  public function targetStarted(BuildEvent $event)
  {
  }

  /**
   * Fired when a target has finished.
   *
   * @param BuildEvent The BuildEvent
   * @see BuildEvent#getException()
   */
  public function targetFinished(BuildEvent $event)
  {
    if (!is_null($event->getException()))
    {
      self::$exceptions[] = $event->getException();
    }
  }

  /**
   * Fired when a task is started.
   *
   * @param BuildEvent The BuildEvent
   * @see BuildEvent::getTask()
   */
  public function taskStarted(BuildEvent $event)
  {
  }

  /**
   *  Fired when a task has finished.
   *
   *  @param BuildEvent The BuildEvent
   *  @see BuildEvent::getException()
   */
  public function taskFinished(BuildEvent $event)
  {
  }

  /**
   *  Fired whenever a message is logged.
   *
   *  @param BuildEvent The BuildEvent
   *  @see BuildEvent::getMessage()
   */
  public function messageLogged(BuildEvent $event)
  {
    if ($event->getPriority() == Project::MSG_ERR)
    {
      if (preg_match('/XLST transformation/', $event->getMessage()))
      {
        // not really an error
        return;
      }

      $msg = '';
      if ($event->getTask() !== null)
      {
        $msg = sprintf('[%s] ', $event->getTask()->getTaskName());
      }

      $msg .= $event->getMessage();

      self::$errors[] = $msg;
    }
  }
}
