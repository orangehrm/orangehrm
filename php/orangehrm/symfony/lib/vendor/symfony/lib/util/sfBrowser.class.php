<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfBrowser simulates a browser which can surf a symfony application.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfBrowser.class.php 15726 2009-02-23 15:56:12Z fabien $
 */
class sfBrowser extends sfBrowserBase
{
  protected
    $listeners        = array(),
    $context          = null,
    $currentException = null;

  /**
   * Calls a request to a uri.
   */
  protected function doCall()
  {
    // recycle our context object
    $this->context = $this->getContext(true);

    sfConfig::set('sf_test', true);

    // we register a fake rendering filter
    sfConfig::set('sf_rendering_filter', array('sfFakeRenderingFilter', null));

    $this->resetCurrentException();

    // dispatch our request
    ob_start();
    $this->context->getController()->dispatch();
    $retval = ob_get_clean();

    // append retval to the response content
    $this->context->getResponse()->setContent($retval);

    // manually shutdown user to save current session data
    if ($this->context->getUser())
    {
      $this->context->getUser()->shutdown();
      $this->context->getStorage()->shutdown();
    }
  }

  /**
   * Returns the current application context.
   *
   * @param  bool $forceReload  true to force context reload, false otherwise
   *
   * @return sfContext
   */
  public function getContext($forceReload = false)
  {
    if (is_null($this->context) || $forceReload)
    {
      $isContextEmpty = is_null($this->context);
      $context = $isContextEmpty ? sfContext::getInstance() : $this->context;

      $currentConfiguration = $context->getConfiguration();
      $configuration = ProjectConfiguration::getApplicationConfiguration($currentConfiguration->getApplication(), $currentConfiguration->getEnvironment(), $currentConfiguration->isDebug());
      $this->context = sfContext::createInstance($configuration);
      unset($currentConfiguration);

      if (!$isContextEmpty)
      {
        sfConfig::clear();
        sfConfig::add($this->rawConfiguration);
      }
      else
      {
        $this->rawConfiguration = sfConfig::getAll();
      }

      $this->context->getEventDispatcher()->connect('application.throw_exception', array($this, 'ListenToException'));
      foreach ($this->listeners as $name => $listener)
      {
        $this->context->getEventDispatcher()->connect($name, $listener);
      }
    }

    return $this->context;
  }

  public function addListener($name, $listener)
  {
    $this->listeners[$name] = $listener;
  }

  /**
   * Gets response.
   *
   * @return sfWebResponse
   */
  public function getResponse()
  {
    return $this->context->getResponse();
  }

  /**
   * Gets request.
   *
   * @return sfWebRequest
   */
  public function getRequest()
  {
    return $this->context->getRequest();
  }

  /**
   * Gets user.
   *
   * @return sfUser
   */
  public function getUser()
  {
    return $this->context->getUser();
  }

  /**
   * Shutdown function to clean up and remove sessions
   *
   * @return void
   */
  public function shutdown()
  {
    parent::shutdown();

    // we remove all session data
    sfToolkit::clearDirectory(sfConfig::get('sf_test_cache_dir').'/sessions');
  }

  /**
   * Listener for exceptions
   *
   * @param  sfEvent $event  The event to handle
   *
   * @return void
   */
  public function listenToException(sfEvent $event)
  {
    $this->setCurrentException($event->getSubject());
  }
}

class sfFakeRenderingFilter extends sfFilter
{
  public function execute($filterChain)
  {
    $filterChain->execute();

    $this->context->getResponse()->sendContent();
  }
}
