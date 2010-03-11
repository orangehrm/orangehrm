<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfResponse provides methods for manipulating client response information such
 * as headers, cookies and content.
 *
 * @package    symfony
 * @subpackage response
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfResponse.class.php 9091 2008-05-20 07:19:07Z FabianLange $
 */
abstract class sfResponse implements Serializable
{
  protected
    $options    = array(),
    $dispatcher = null,
    $content    = '';

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct(sfEventDispatcher $dispatcher, $options = array())
  {
    $this->initialize($dispatcher, $options);
  }

  /**
   * Initializes this sfResponse.
   *
   * Available options:
   *
   *  * logging: Whether to enable logging or not (false by default)
   *
   * @param  sfEventDispatcher  $dispatcher  An sfEventDispatcher instance
   * @param  array              $options     An array of options
   *
   * @return bool true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this sfResponse
   */
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    $this->dispatcher = $dispatcher;
    $this->options = $options;

    if (!isset($this->options['logging']))
    {
      $this->options['logging'] = false;
    }
  }

  /**
   * Sets the event dispatcher.
   *
   * @param sfEventDispatcher $dispatcher  An sfEventDispatcher instance
   */
  public function setEventDispatcher(sfEventDispatcher $dispatcher)
  {
    $this->dispatcher = $dispatcher;
  }

  /**
   * Sets the response content
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }

  /**
   * Gets the current response content
   *
   * @return string Content
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * Outputs the response content
   */
  public function sendContent()
  {
    $event = $this->dispatcher->filter(new sfEvent($this, 'response.filter_content'), $this->getContent());
    $content = $event->getReturnValue();

    if ($this->options['logging'])
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Send content (%s o)', strlen($content)))));
    }

    echo $content;
  }

  /**
   * Sends the content.
   */
  public function send()
  {
    $this->sendContent();
  }

  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Calls methods defined via sfEventDispatcher.
   *
   * @param string $method     The method name
   * @param array  $arguments  The method arguments
   *
   * @return mixed The returned value of the called method
   *
   * @throws <b>sfException</b> If the calls fails
   */
  public function __call($method, $arguments)
  {
    $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'response.method_not_found', array('method' => $method, 'arguments' => $arguments)));
    if (!$event->isProcessed())
    {
      throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
    }

    return $event->getReturnValue();
  }

  /**
   * Serializes the current instance.
   *
   * @return array Objects instance
   */
  public function serialize()
  {
    return serialize($this->content);
  }

  /**
   * Unserializes a sfResponse instance.
   *
   * You need to inject a dispatcher after unserializing a sfResponse instance.
   *
   * @param string $serialized  A serialized sfResponse instance
   *
   */
  public function unserialize($serialized)
  {
    $this->content = unserialize($serialized);
  }
}
