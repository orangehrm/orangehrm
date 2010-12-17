<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Swift_PropelSpool is a spool that uses Propel.
 *
 * Example schema:
 *
 *  mail_message:
 *   message:    { type: clob }
 *   created_at: ~
 *
 * @package    symfony
 * @subpackage mailer
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: Swift_PropelSpool.class.php 30529 2010-08-04 16:30:29Z fabien $
 */
class Swift_PropelSpool extends Swift_ConfigurableSpool
{
  protected
    $model = null,
    $column = null,
    $method = null;

  /**
   * Constructor.
   *
   * @param string The Propel model to use to store the messages (MailMessage by default)
   * @param string The column name to use for message storage (message by default)
   * @param string The method to call to retrieve the messages to send (optional)
   */
  public function __construct($model = 'MailMessage', $column = 'message', $method = 'doSelect')
  {
    $this->model = $model;
    $this->column = $column;
    $this->method = $method;
  }

  /**
   * Tests if this Transport mechanism has started.
   *
   * @return boolean
   */
  public function isStarted()
  {
    return true;
  }

  /**
   * Starts this Transport mechanism.
   */
  public function start()
  {
  }

  /**
   * Stops this Transport mechanism.
   */
  public function stop()
  {
  }

  /**
   * Stores a message in the queue.
   *
   * @param Swift_Mime_Message $message The message to store
   */
  public function queueMessage(Swift_Mime_Message $message)
  {
    $object = new $this->model;

    if (!$object instanceof BaseObject)
    {
      throw new InvalidArgumentException('The mailer message object must be a BaseObject object.');
    }

    $model = constant($this->model.'::PEER');
    $method = 'set'.call_user_func(array($model, 'translateFieldName'), $this->column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);

    $object->$method(serialize($message));
    $object->save();
  }

  /**
   * Sends messages using the given transport instance.
   *
   * @param Swift_Transport $transport         A transport instance
   * @param string[]        &$failedRecipients An array of failures by-reference
   *
   * @return int The number of sent emails
   */
  public function flushQueue(Swift_Transport $transport, &$failedRecipients = null)
  {
    $criteria = new Criteria();
    $criteria->setLimit($this->getMessageLimit());

    $model = constant($this->model.'::PEER');
    $objects = call_user_func(array($model, $this->method), $criteria);

    if (!$transport->isStarted())
    {
      $transport->start();
    }

    $method = 'get'.call_user_func(array($model, 'translateFieldName'), $this->column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
    $count = 0;
    $time = time();
    foreach ($objects as $object)
    {
      if (is_resource($object->getMessage()))
      {
        $message = unserialize(stream_get_contents($object->getMessage()));
      }
      else
      {
          $message = unserialize($object->getMessage());
      }

      $object->delete();

      try
      {
        $count += $transport->send($message, $failedRecipients);
      }
      catch (Exception $e)
      {
        // TODO: What to do with errors?
      }

      if ($this->getTimeLimit() && (time() - $time) >= $this->getTimeLimit())
      {
        break;
      }
    }

    return $count;
  }
}
