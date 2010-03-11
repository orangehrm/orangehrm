<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebDebugLogger logs messages into the web debug toolbar.
 *
 * @package    symfony
 * @subpackage log
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugLogger.class.php 17749 2009-04-29 11:54:22Z fabien $
 */
class sfWebDebugLogger extends sfVarLogger
{
  protected
    $context       = null,
    $dispatcher    = null,
    $webDebugClass = null;

  /**
   * Initializes this logger.
   *
   * Available options:
   *
   * - web_debug_class: The web debug class (sfWebDebug by default).
   *
   * @param  sfEventDispatcher $dispatcher  A sfEventDispatcher instance
   * @param  array             $options     An array of options.
   *
   * @return Boolean           true, if initialization completes successfully, otherwise false.
   *
   * @see sfVarLogger
   */
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    $this->context    = sfContext::getInstance();
    $this->dispatcher = $dispatcher;

    $this->webDebugClass = isset($options['web_debug_class']) ? $options['web_debug_class'] : 'sfWebDebug';

    if (sfConfig::get('sf_web_debug'))
    {
      $dispatcher->connect('response.filter_content', array($this, 'filterResponseContent'));
    }

    return parent::initialize($dispatcher, $options);
  }

  /**
   * Listens to the response.filter_content event.
   *
   * @param  sfEvent $event   The sfEvent instance
   * @param  string  $content The response content
   *
   * @return string  The filtered response content
   */
  public function filterResponseContent(sfEvent $event, $content)
  {
    if (!sfConfig::get('sf_web_debug'))
    {
      return $content;
    }

    // log timers information
    $messages = array();
    foreach (sfTimerManager::getTimers() as $name => $timer)
    {
      $messages[] = sprintf('%s %.2f ms (%d)', $name, $timer->getElapsedTime() * 1000, $timer->getCalls());
    }
    $this->dispatcher->notify(new sfEvent($this, 'application.log', $messages));

    // don't add debug toolbar:
    // * for XHR requests
    // * if response status code is in the 3xx range
    // * if not rendering to the client
    // * if HTTP headers only
    $response = $event->getSubject();
    $request  = $this->context->getRequest();
    if (!$this->context->has('request') || !$this->context->has('response') || !$this->context->has('controller') ||
      $request->isXmlHttpRequest() ||
      strpos($response->getContentType(), 'html') === false ||
      '3' == substr($response->getStatusCode(), 0, 1) ||
      $this->context->getController()->getRenderMode() != sfView::RENDER_CLIENT ||
      $response->isHeaderOnly()
    )
    {
      return $content;
    }

    $webDebug = new $this->webDebugClass($this->dispatcher, $this, array(
      'image_root_path' => ($request->getRelativeUrlRoot() ? $request->getRelativeUrlRoot() : '').sfConfig::get('sf_web_debug_web_dir').'/images',
    ));

    return $webDebug->injectToolbar($content);
  }
}
