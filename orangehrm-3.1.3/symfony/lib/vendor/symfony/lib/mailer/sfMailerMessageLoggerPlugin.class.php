<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfMailerMessageLoggerPlugin is a Swift plugin to log all sent messages.
 *
 * @package    symfony
 * @subpackage mailer
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfMailerMessageLoggerPlugin.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfMailerMessageLoggerPlugin implements Swift_Events_SendListener
{
  protected
    $messages   = array(),
    $dispatcher = null;

  /**
   * Constructor.
   *
   * @param sfEventDispatcher $dispatcher An event dispatcher instance
   */
  public function __construct(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  /**
   * Clears all the messages.
   */
  public function clear()
  {
    $this->messages = array();
  }

  /**
   * Gets all logged messages.
   *
   * @return array An array of message instances
   */
  public function getMessages()
  {
    return $this->messages;
  }

  /**
   * Returns the number of logged messages.
   *
   * @return int The number if logged messages
   */
  public function countMessages()
  {
    return count($this->messages);
  }

  /**
   * Invoked immediately before the Message is sent.
   * 
   * @param Swift_Events_SendEvent $evt
   */
  public function beforeSendPerformed(Swift_Events_SendEvent $evt)
  {
    $this->messages[] = $message = clone $evt->getMessage();

    $to = null === $message->getTo() ? '' : implode(', ', array_keys($message->getTo()));

    $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Sending email "%s" to "%s"', $message->getSubject(), $to))));
  }

  /**
   * Invoked immediately after the Message is sent.
   * 
   * @param Swift_Events_SendEvent $evt
   */
  public function sendPerformed(Swift_Events_SendEvent $evt)
  {
  }
}
