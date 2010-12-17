<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfMailer is the main entry point for the mailer system.
 *
 * This class is instanciated by sfContext on demand.
 *
 * @package    symfony
 * @subpackage mailer
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfMailer.class.php 28841 2010-03-29 08:13:57Z fabien $
 */
class sfMailer extends Swift_Mailer
{
  const
    REALTIME       = 'realtime',
    SPOOL          = 'spool',
    SINGLE_ADDRESS = 'single_address',
    NONE           = 'none';

  protected
    $spool             = null,
    $logger            = null,
    $strategy          = 'realtime',
    $address           = '',
    $realtimeTransport = null,
    $force             = false,
    $redirectingPlugin = null;

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * charset: The default charset to use for messages
   *  * logging: Whether to enable logging or not
   *  * delivery_strategy: The delivery strategy to use
   *  * spool_class: The spool class (for the spool strategy)
   *  * spool_arguments: The arguments to pass to the spool constructor
   *  * delivery_address: The email address to use for the single_address strategy
   *  * transport: The main transport configuration
   *  *   * class: The main transport class
   *  *   * param: The main transport parameters
   *
   * @param sfEventDispatcher $dispatcher An event dispatcher instance
   * @param array             $options    An array of options
   */
  public function __construct(sfEventDispatcher $dispatcher, $options)
  {
    // options
    $options = array_merge(array(
      'charset' => 'UTF-8',
      'logging' => false,
      'delivery_strategy' => 'realtime',
      'transport' => array(
        'class' => 'Swift_MailTransport',
        'param' => array(),
       ),
    ), $options);

    $constantName = 'sfMailer::'.strtoupper($options['delivery_strategy']);
    $this->strategy = defined($constantName) ? constant($constantName) : false;
    if (!$this->strategy)
    {
      throw new InvalidArgumentException(sprintf('Unknown mail delivery strategy "%s" (should be one of realtime, spool, single_address, or none)', $options['delivery_strategy']));
    }

    // transport
    $class = $options['transport']['class'];
    $transport = new $class();
    if (isset($options['transport']['param']))
    {
      foreach ($options['transport']['param'] as $key => $value)
      {
        $method = 'set'.ucfirst($key);
        if (method_exists($transport, $method))
        {
          $transport->$method($value);
        }
        elseif (method_exists($transport, 'getExtensionHandlers'))
        {
          foreach ($transport->getExtensionHandlers() as $handler)
          {
            if (in_array(strtolower($method), array_map('strtolower', (array) $handler->exposeMixinMethods())))
            {
              $transport->$method($value);
            }
          }
        }
      }
    }
    $this->realtimeTransport = $transport;

    if (sfMailer::SPOOL == $this->strategy)
    {
      if (!isset($options['spool_class']))
      {
        throw new InvalidArgumentException('For the spool mail delivery strategy, you must also define a spool_class option');
      }
      $arguments = isset($options['spool_arguments']) ? $options['spool_arguments'] : array();

      if ($arguments)
      {
        $r = new ReflectionClass($options['spool_class']);
        $this->spool = $r->newInstanceArgs($arguments);
      }
      else
      {
        $this->spool = new $options['spool_class'];
      }

      $transport = new Swift_SpoolTransport($this->spool);
    }
    elseif (sfMailer::SINGLE_ADDRESS == $this->strategy)
    {
      if (!isset($options['delivery_address']))
      {
        throw new InvalidArgumentException('For the single_address mail delivery strategy, you must also define a delivery_address option');
      }

      $this->address = $options['delivery_address'];

      $transport->registerPlugin($this->redirectingPlugin = new Swift_Plugins_RedirectingPlugin($this->address));
    }

    parent::__construct($transport);

    // logger
    if ($options['logging'])
    {
      $this->logger = new sfMailerMessageLoggerPlugin($dispatcher);

      $transport->registerPlugin($this->logger);
    }

    if (sfMailer::NONE == $this->strategy)
    {
      // must be registered after logging
      $transport->registerPlugin(new Swift_Plugins_BlackholePlugin());
    }

    // preferences
    Swift_Preferences::getInstance()->setCharset($options['charset']);

    $dispatcher->notify(new sfEvent($this, 'mailer.configure'));
  }

  /**
   * Gets the realtime transport instance.
   *
   * @return Swift_Transport The realtime transport instance.
   */
  public function getRealtimeTransport()
  {
    return $this->realtimeTransport;
  }

  /**
   * Sets the realtime transport instance.
   *
   * @param Swift_Transport $transport The realtime transport instance.
   */
  public function setRealtimeTransport(Swift_Transport $transport)
  {
    $this->realtimeTransport = $transport;
  }

  /**
   * Gets the logger instance.
   *
   * @return sfMailerMessageLoggerPlugin The logger instance.
   */
  public function getLogger()
  {
    return $this->logger;
  }

  /**
   * Sets the logger instance.
   *
   * @param sfMailerMessageLoggerPlugin $logger The logger instance.
   */
  public function setLogger($logger)
  {
    $this->logger = $logger;
  }

  /**
   * Gets the delivery strategy.
   *
   * @return string The delivery strategy
   */
  public function getDeliveryStrategy()
  {
    return $this->strategy;
  }

  /**
   * Gets the delivery address.
   *
   * @return string The delivery address
   */
  public function getDeliveryAddress()
  {
    return $this->address;
  }

  /**
   * Sets the delivery address.
   *
   * @param string $address The delivery address
   */
  public function setDeliveryAddress($address)
  {
    $this->address = $address;

    if (sfMailer::SINGLE_ADDRESS == $this->strategy)
    {
      $this->redirectingPlugin->setRecipient($address);
    }
  }

  /**
   * Creates a new message.
   *
   * @param string|array $from    The from address
   * @param string|array $to      The recipient(s)
   * @param string       $subject The subject
   * @param string       $body    The body
   *
   * @return Swift_Message A Swift_Message instance
   */
  public function compose($from = null, $to = null, $subject = null, $body = null)
  {
    return Swift_Message::newInstance()
      ->setFrom($from)
      ->setTo($to)
      ->setSubject($subject)
      ->setBody($body)
    ;
  }

  /**
   * Sends a message.
   *
   * @param string|array $from    The from address
   * @param string|array $to      The recipient(s)
   * @param string       $subject The subject
   * @param string       $body    The body
   *
   * @return int The number of sent emails
   */
  public function composeAndSend($from, $to, $subject, $body)
  {
    return $this->send($this->compose($from, $to, $subject, $body));
  }

  /**
   * Forces the next call to send() to use the realtime strategy.
   *
   * @return sfMailer The current sfMailer instance
   */
  public function sendNextImmediately()
  {
    $this->force = true;

    return $this;
  }

  /**
   * Sends the given message.
   *
   * @param Swift_Transport $transport         A transport instance
   * @param string[]        &$failedRecipients An array of failures by-reference
   *
   * @return int|false The number of sent emails
   */
  public function send(Swift_Mime_Message $message, &$failedRecipients = null)
  {
    if ($this->force)
    {
      $this->force = false;

      if (!$this->realtimeTransport->isStarted())
      {
        $this->realtimeTransport->start();
      }

      return $this->realtimeTransport->send($message, $failedRecipients);
    }

    return parent::send($message, $failedRecipients);
  }

  /**
   * Sends the current messages in the spool.
   *
   * The return value is the number of recipients who were accepted for delivery.
   *
   * @param string[] &$failedRecipients An array of failures by-reference
   *
   * @return int The number of sent emails
   */
  public function flushQueue(&$failedRecipients = null)
  {
    return $this->getSpool()->flushQueue($this->realtimeTransport, $failedRecipients);
  }

  public function getSpool()
  {
    if (self::SPOOL != $this->strategy)
    {
      throw new LogicException(sprintf('You can only send messages in the spool if the delivery strategy is "spool" (%s is the current strategy).', $this->strategy));
    }

    return $this->spool;
  }

  static public function initialize()
  {
    require_once sfConfig::get('sf_symfony_lib_dir').'/vendor/swiftmailer/swift_init.php';
  }
}
