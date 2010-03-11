<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A View to render partials.
 *
 * @package    symfony
 * @subpackage view
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPartialView.class.php 17749 2009-04-29 11:54:22Z fabien $
 */
class sfPartialView extends sfPHPView
{
  protected
    $viewCache   = null,
    $checkCache  = false,
    $cacheKey    = null,
    $partialVars = array();

  /**
   * Constructor.
   * 
   * @see sfView
   */
  public function initialize($context, $moduleName, $actionName, $viewName)
  {
    $ret = parent::initialize($context, $moduleName, $actionName, $viewName);

    $this->viewCache = $this->context->getViewCacheManager();

    if (sfConfig::get('sf_cache'))
    {
      $this->checkCache = sfConfig::get('sf_lazy_cache_key') ? $this->viewCache->isActionCacheable($moduleName, $actionName) : true;
    }

    return $ret;
  }

  /**
   * Executes any presentation logic for this view.
   */
  public function execute()
  {
  }

  /**
   * @param array $partialVars
   */
  public function setPartialVars(array $partialVars)
  {
    $this->partialVars = $partialVars;
    $this->getAttributeHolder()->add($partialVars);
  }

  /**
   * Configures template for this view.
   */
  public function configure()
  {
    $this->setDecorator(false);
    $this->setTemplate($this->actionName.$this->getExtension());
    if ('global' == $this->moduleName)
    {
      $this->setDirectory($this->context->getConfiguration()->getDecoratorDir($this->getTemplate()));
    }
    else
    {
      $this->setDirectory($this->context->getConfiguration()->getTemplateDir($this->moduleName, $this->getTemplate()));
    }
  }

  /**
   * Renders the presentation.
   *
   * @return string Current template content
   */
  public function render()
  {
    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $timer = sfTimerManager::getTimer(sprintf('Partial "%s/%s"', $this->moduleName, $this->actionName));
    }

    if ($retval = $this->getCache())
    {
      return $retval;
    }
    else if ($this->checkCache)
    {
      $mainResponse = $this->context->getResponse();
      $responseClass = get_class($mainResponse);
      $this->context->setResponse($response = new $responseClass($this->context->getEventDispatcher(), array_merge($mainResponse->getOptions(), array('content_type' => $mainResponse->getContentType()))));
    }

    try
    {
      // execute pre-render check
      $this->preRenderCheck();

      $this->getAttributeHolder()->set('sf_type', 'partial');

      // render template
      $retval = $this->renderFile($this->getDirectory().'/'.$this->getTemplate());
    }
    catch (Exception $e)
    {
      if ($this->checkCache)
      {
        $this->context->setResponse($mainResponse);
        $mainResponse->merge($response);
      }

      throw $e;
    }

    if ($this->checkCache)
    {
      $retval = $this->viewCache->setPartialCache($this->moduleName, $this->actionName, $this->cacheKey, $retval);
      $this->context->setResponse($mainResponse);
      $mainResponse->merge($response);
    }

    if (sfConfig::get('sf_debug') && sfConfig::get('sf_logging_enabled'))
    {
      $timer->addTime();
    }

    return $retval;
  }

  public function getCache()
  {
    if (!$this->checkCache)
    {
      return null;
    }

    $this->cacheKey = $this->viewCache->checkCacheKey($this->partialVars);
    if ($retval = $this->viewCache->getPartialCache($this->moduleName, $this->actionName, $this->cacheKey))
    {
      return $retval;
    }
  }
}
