<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cache class to cache the HTML results for actions and templates.
 *
 * This class uses a sfCache instance implementation to store cache.
 *
 * To disable all caching, you can set the [sf_cache] constant to false.
 *
 * @package    symfony
 * @subpackage view
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfViewCacheManager.class.php 32699 2011-07-01 06:58:02Z fabien $
 */
class sfViewCacheManager
{
  protected
    $cache       = null,
    $cacheConfig = array(),
    $context     = null,
    $dispatcher  = null,
    $controller  = null,
    $routing     = null,
    $request     = null,
    $loaded      = array();

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct($context, sfCache $cache, $options = array())
  {
    $this->initialize($context, $cache, $options);
  }

  /**
   * Initializes the cache manager.
   *
   * @param sfContext $context  Current application context
   * @param sfCache   $cache    An sfCache instance
   */
  public function initialize($context, sfCache $cache, $options = array())
  {
    $this->context    = $context;
    $this->dispatcher = $context->getEventDispatcher();
    $this->controller = $context->getController();
    $this->request    = $context->getRequest();
    $this->options    = array_merge(array(
        'cache_key_use_vary_headers' => true,
        'cache_key_use_host_name'    => true,
      ), $options);

    if (sfConfig::get('sf_web_debug'))
    {
      $this->dispatcher->connect('view.cache.filter_content', array($this, 'decorateContentWithDebug'));
    }

    // empty configuration
    $this->cacheConfig = array();

    // cache instance
    $this->cache = $cache;

    // routing instance
    $this->routing = $context->getRouting();
  }

  /**
   * Retrieves the current cache context.
   *
   * @return sfContext The sfContext instance
   */
  public function getContext()
  {
    return $this->context;
  }

  /**
   * Retrieves the current cache object.
   *
   * @return sfCache The current cache object
   */
  public function getCache()
  {
    return $this->cache;
  }

  /**
   * Generates a unique cache key for an internal URI.
   * This cache key can be used by any of the cache engines as a unique identifier to a cached resource
   *
   * Basically, the cache key generated for the following internal URI:
   *   module/action?key1=value1&key2=value2
   * Looks like:
   *   /localhost/all/module/action/key1/value1/key2/value2
   *
   * @param  string $internalUri       The internal unified resource identifier
   *                                   Accepts rules formatted like 'module/action?key1=value1&key2=value2'
   *                                   Does not accept rules starting with a route name, except for '@sf_cache_partial'
   * @param  string $hostName          The host name
   *                                   Optional - defaults to the current host name bu default
   * @param  string $vary              The vary headers, separated by |, or "all" for all vary headers
   *                                   Defaults to 'all'
   * @param  string $contextualPrefix  The contextual prefix for contextual partials.
   *                                   Defaults to 'currentModule/currentAction/currentPAram1/currentvalue1'
   *                                   Used only by the sfViewCacheManager::remove() method
   *
   * @return string The cache key
   *                If some of the parameters contained wildcards (* or **), the generated key will also have wildcards
   */
  public function generateCacheKey($internalUri, $hostName = '', $vary = '', $contextualPrefix = '')
  {
    if ($callable = sfConfig::get('sf_cache_namespace_callable'))
    {
      if (!is_callable($callable))
      {
        throw new sfException(sprintf('"%s" cannot be called as a function.', var_export($callable, true)));
      }

      return call_user_func($callable, $internalUri, $hostName, $vary, $contextualPrefix, $this);
    }

    if (strpos($internalUri, '@') === 0 && strpos($internalUri, '@sf_cache_partial') === false)
    {
      throw new sfException('A cache key cannot be generated for an internal URI using the @rule syntax');
    }

    $cacheKey = '';

    if ($this->isContextual($internalUri))
    {
      // Contextual partial
      if (!$contextualPrefix)
      {
        list($route_name, $params) = $this->controller->convertUrlStringToParameters($this->routing->getCurrentInternalUri());

        // if there is no module/action, it means that we have a 404 and the user is trying to cache it
        if (!isset($params['module']) || !isset($params['action']))
        {
          $params['module'] = sfConfig::get('sf_error_404_module');
          $params['action'] = sfConfig::get('sf_error_404_action');
        }
        $cacheKey = $this->convertParametersToKey($params);
      }
      else
      {
        $cacheKey = $contextualPrefix;
      }
      list($route_name, $params) = $this->controller->convertUrlStringToParameters($internalUri);
      $cacheKey .= sprintf('/%s/%s/%s', $params['module'], $params['action'], isset($params['sf_cache_key']) ? $params['sf_cache_key'] : '');
    }
    else
    {
      // Regular action or non-contextual partial
      list($route_name, $params) = $this->controller->convertUrlStringToParameters($internalUri);
      if ($route_name == 'sf_cache_partial')
      {
        $cacheKey = 'sf_cache_partial/';
      }

      $cacheKey .= $this->convertParametersToKey($params);
    }

    // add vary headers
    if ($varyPart = $this->getCacheKeyVaryHeaderPart($internalUri, $vary))
    {
      $cacheKey = '/'.$varyPart.'/'.ltrim($cacheKey, '/');
    }

    // add hostname
    if ($hostNamePart = $this->getCacheKeyHostNamePart($hostName))
    {
      $cacheKey = '/'.$hostNamePart.'/'.ltrim($cacheKey, '/');
    }

    // normalize to a leading slash
    if (0 !== strpos($cacheKey, '/'))
    {
      $cacheKey = '/'.$cacheKey;
    }

    // distinguish multiple slashes
    while (false !== strpos($cacheKey, '//'))
    {
      $cacheKey = str_replace('//', '/'.substr(sha1($cacheKey), 0, 7).'/', $cacheKey);
    }

    // prevent directory traversal
    $cacheKey = strtr($cacheKey, array(
      '/.'  => '/_.',
      '/_'  => '/__',
      '\\.' => '\\_.',
      '\\_' => '\\__',
    ));

    return $cacheKey;
  }

  /**
   * Gets the vary header part of view cache key.
   *
   * @param  string $vary
   * @return string
   */
  protected function getCacheKeyVaryHeaderPart($internalUri, $vary = '')
  {
    if (!$this->options['cache_key_use_vary_headers'])
    {
      return '';
    }

    // prefix with vary headers
    if (!$vary)
    {
      $varyHeaders = $this->getVary($internalUri);

      if (!$varyHeaders)
      {
        return 'all';
      }

      sort($varyHeaders);
      $request = $this->context->getRequest();
      $varys = array();

      foreach ($varyHeaders as $header)
      {
        $varys[] = $header . '-' . preg_replace('/\W+/', '_', $request->getHttpHeader($header));
      }
      $vary = implode($varys, '-');
    }

    return $vary;
  }

  /**
   * Gets the hostname part of view cache key.
   *
   * @param string $hostName
   * @return void
   */
  protected function getCacheKeyHostNamePart($hostName = '')
  {
    if (!$this->options['cache_key_use_host_name'])
    {
      return '';
    }

    if (!$hostName)
    {
      $hostName = $this->context->getRequest()->getHost();
    }

    $hostName = preg_replace('/[^a-z0-9\*]/i', '_', $hostName);
    $hostName = preg_replace('/_+/', '_', $hostName);

    return strtolower($hostName);
  }

  /**
   * Transforms an associative array of parameters from an URI into a unique key
   *
   * @param  array $params  Associative array of parameters from the URI (including, at least, module and action)
   *
   * @return string Unique key
   */
  protected function convertParametersToKey($params)
  {
    if(!isset($params['module']) || !isset($params['action']))
    {
      throw new sfException('A cache key must contain both a module and an action parameter');
    }
    $module = $params['module'];
    unset($params['module']);
    $action = $params['action'];
    unset($params['action']);
    ksort($params);
    $cacheKey = sprintf('%s/%s', $module, $action);
    foreach ($params as $key => $value)
    {
      $cacheKey .= sprintf('/%s/%s', $key, $value);
    }

    return $cacheKey;
  }

  /**
   * Adds a cache to the manager.
   *
   * @param string $moduleName  Module name
   * @param string $actionName  Action name
   * @param array  $options     Options for the cache
   */
  public function addCache($moduleName, $actionName, $options = array())
  {
    // normalize vary headers
    if (isset($options['vary']))
    {
      foreach ($options['vary'] as $key => $name)
      {
        $options['vary'][$key] = strtr(strtolower($name), '_', '-');
      }
    }

    $options['lifeTime'] = isset($options['lifeTime']) ? $options['lifeTime'] : 0;
    if (!isset($this->cacheConfig[$moduleName]))
    {
      $this->cacheConfig[$moduleName] = array();
    }
    $this->cacheConfig[$moduleName][$actionName] = array(
      'withLayout'     => isset($options['withLayout']) ? $options['withLayout'] : false,
      'lifeTime'       => $options['lifeTime'],
      'clientLifeTime' => isset($options['clientLifeTime']) ? $options['clientLifeTime'] : $options['lifeTime'],
      'contextual'     => isset($options['contextual']) ? $options['contextual'] : false,
      'vary'           => isset($options['vary']) ? $options['vary'] : array(),
    );
  }

  /**
   * Registers configuration options for the cache.
   *
   * @param string $moduleName  Module name
   */
  public function registerConfiguration($moduleName)
  {
    if (!isset($this->loaded[$moduleName]))
    {
      require($this->context->getConfigCache()->checkConfig('modules/'.$moduleName.'/config/cache.yml'));
      $this->loaded[$moduleName] = true;
    }
  }

  /**
   * Retrieves the layout from the cache option list.
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return bool true, if have layout otherwise false
   */
  public function withLayout($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'withLayout', false);
  }

  /**
   * Retrieves lifetime from the cache option list.
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return int LifeTime
   */
  public function getLifeTime($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'lifeTime', 0);
  }

  /**
   * Retrieves client lifetime from the cache option list
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return int Client lifetime
   */
  public function getClientLifeTime($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'clientLifeTime', 0);
  }

  /**
   * Retrieves contextual option from the cache option list.
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return boolean true, if is contextual otherwise false
   */
  public function isContextual($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'contextual', false);
  }

  /**
   * Retrieves vary option from the cache option list.
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return array Vary options for the cache
   */
  public function getVary($internalUri)
  {
    return $this->getCacheConfig($internalUri, 'vary', array());
  }

  /**
   * Gets a config option from the cache.
   *
   * @param string $internalUri   Internal uniform resource identifier
   * @param string $key           Option name
   * @param string $defaultValue  Default value of the option
   *
   * @return mixed Value of the option
   */
  protected function getCacheConfig($internalUri, $key, $defaultValue = null)
  {
    list($route_name, $params) = $this->controller->convertUrlStringToParameters($internalUri);

    if (!isset($params['module']))
    {
        return $defaultValue;
    }

    $this->registerConfiguration($params['module']);

    $value = $defaultValue;
    if (isset($this->cacheConfig[$params['module']][$params['action']][$key]))
    {
      $value = $this->cacheConfig[$params['module']][$params['action']][$key];
    }
    else if (isset($this->cacheConfig[$params['module']]['DEFAULT'][$key]))
    {
      $value = $this->cacheConfig[$params['module']]['DEFAULT'][$key];
    }

    return $value;
  }

  /**
   * Returns true if the current content is cacheable.
   *
   * Possible break in backward compatibility: If the sf_lazy_cache_key
   * setting is turned on in settings.yml, this method is not used when
   * initially checking a partial's cacheability.
   *
   * @see sfPartialView, isActionCacheable()
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return bool true, if the content is cacheable otherwise false
   */
  public function isCacheable($internalUri)
  {
    if ($this->request instanceof sfWebRequest && !$this->request->isMethod(sfRequest::GET))
    {
      return false;
    }

    list($route_name, $params) = $this->controller->convertUrlStringToParameters($internalUri);

    if (!isset($params['module']))
    {
        return false;
    }

    $this->registerConfiguration($params['module']);

    if (isset($this->cacheConfig[$params['module']][$params['action']]))
    {
      return ($this->cacheConfig[$params['module']][$params['action']]['lifeTime'] > 0);
    }
    else if (isset($this->cacheConfig[$params['module']]['DEFAULT']))
    {
      return ($this->cacheConfig[$params['module']]['DEFAULT']['lifeTime'] > 0);
    }

    return false;
  }

  /**
   * Returns true if the action is cacheable.
   *
   * @param  string $moduleName A module name
   * @param  string $actionName An action or partial template name
   *
   * @return boolean True if the action is cacheable
   *
   * @see isCacheable()
   */
  public function isActionCacheable($moduleName, $actionName)
  {
    if ($this->request instanceof sfWebRequest && !$this->request->isMethod(sfRequest::GET))
    {
      return false;
    }

    $this->registerConfiguration($moduleName);

    if (isset($this->cacheConfig[$moduleName][$actionName]))
    {
      return $this->cacheConfig[$moduleName][$actionName]['lifeTime'] > 0;
    }
    else if (isset($this->cacheConfig[$moduleName]['DEFAULT']))
    {
      return $this->cacheConfig[$moduleName]['DEFAULT']['lifeTime'] > 0;
    }

    return false;
  }

  /**
   * Retrieves content in the cache.
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return string The content in the cache
   */
  public function get($internalUri)
  {
    // no cache or no cache set for this action
    if (!$this->isCacheable($internalUri) || $this->ignore())
    {
      return null;
    }

    $retval = $this->cache->get($this->generateCacheKey($internalUri));

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Cache for "%s" %s', $internalUri, $retval !== null ? 'exists' : 'does not exist'))));
    }

    return $retval;
  }

  /**
   * Returns true if there is a cache.
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return bool true, if there is a cache otherwise false
   */
  public function has($internalUri)
  {
    if (!$this->isCacheable($internalUri) || $this->ignore())
    {
      return null;
    }

    return $this->cache->has($this->generateCacheKey($internalUri));
  }

  /**
   * Ignores the cache functionality.
   *
   * @return bool true, if the cache is ignore otherwise false
   */
  protected function ignore()
  {
    // ignore cache parameter? (only available in debug mode)
    if (sfConfig::get('sf_debug') && $this->context->getRequest()->getAttribute('sf_ignore_cache'))
    {
      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array('Discard cache')));
      }

      return true;
    }

    return false;
  }

  /**
   * Sets the cache content.
   *
   * @param  string $data         Data to put in the cache
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return boolean true, if the data get set successfully otherwise false
   */
  public function set($data, $internalUri)
  {
    if (!$this->isCacheable($internalUri))
    {
      return false;
    }

    try
    {
      $ret = $this->cache->set($this->generateCacheKey($internalUri), $data, $this->getLifeTime($internalUri));
    }
    catch (Exception $e)
    {
      return false;
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Save cache for "%s"', $internalUri))));
    }

    return true;
  }

  /**
   * Removes the content in the cache.
   *
   * @param  string $internalUri       Internal uniform resource identifier
   * @param  string $hostName          The host name
   * @param  string $vary              The vary headers, separated by |, or "all" for all vary headers
   * @param  string $contextualPrefix  The removal prefix for contextual partials. Defaults to '**' (all actions, all params)
   *
   * @return bool true, if the remove happened, false otherwise
   */
  public function remove($internalUri, $hostName = '', $vary = '', $contextualPrefix = '**')
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Remove cache for "%s"', $internalUri))));
    }

    $cacheKey = $this->generateCacheKey($internalUri, $hostName, $vary, $contextualPrefix);

    if(strpos($cacheKey, '*'))
    {
      return $this->cache->removePattern($cacheKey);
    }
    elseif ($this->cache->has($cacheKey))
    {
      return $this->cache->remove($cacheKey);
    }
  }

  /**
   * Retrieves the last modified time.
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return int    The last modified datetime
   */
  public function getLastModified($internalUri)
  {
    if (!$this->isCacheable($internalUri))
    {
      return 0;
    }

    return $this->cache->getLastModified($this->generateCacheKey($internalUri));
  }

  /**
   * Retrieves the timeout.
   *
   * @param  string $internalUri  Internal uniform resource identifier
   *
   * @return int    The timeout datetime
   */
  public function getTimeout($internalUri)
  {
    if (!$this->isCacheable($internalUri))
    {
      return 0;
    }

    return $this->cache->getTimeout($this->generateCacheKey($internalUri));
  }

  /**
   * Starts the fragment cache.
   *
   * @param  string $name            Unique fragment name
   * @param  string $lifeTime        Life time for the cache
   * @param  string $clientLifeTime  Client life time for the cache
   * @param  array  $vary            Vary options for the cache
   *
   * @return bool true, if success otherwise false
   */
  public function start($name, $lifeTime, $clientLifeTime = null, $vary = array())
  {
    $internalUri = $this->routing->getCurrentInternalUri();

    if (!$clientLifeTime)
    {
      $clientLifeTime = $lifeTime;
    }

    // add cache config to cache manager
    list($route_name, $params) = $this->controller->convertUrlStringToParameters($internalUri);
    $this->addCache($params['module'], $params['action'], array('withLayout' => false, 'lifeTime' => $lifeTime, 'clientLifeTime' => $clientLifeTime, 'vary' => $vary));

    // get data from cache if available
    $data = $this->get($internalUri.(strpos($internalUri, '?') ? '&' : '?').'_sf_cache_key='.$name);
    if ($data !== null)
    {
      return $data;
    }
    else
    {
      ob_start();
      ob_implicit_flush(0);

      return null;
    }
  }

  /**
   * Stops the fragment cache.
   *
   * @param  string $name Unique fragment name
   *
   * @return bool true, if success otherwise false
   */
  public function stop($name)
  {
    $data = ob_get_clean();

    // save content to cache
    $internalUri = $this->routing->getCurrentInternalUri();
    try
    {
      $this->set($data, $internalUri.(strpos($internalUri, '?') ? '&' : '?').'_sf_cache_key='.$name);
    }
    catch (Exception $e)
    {
    }

    return $data;
  }

  /**
   * Computes the cache key based on the passed parameters.
   *
   * @param array $parameters  An array of parameters
   */
  public function computeCacheKey(array $parameters)
  {
    if (isset($parameters['sf_cache_key']))
    {
      return $parameters['sf_cache_key'];
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array('Generate cache key')));
    }
    ksort($parameters);

    return md5(serialize($parameters));
  }

  /**
   * Checks that the supplied parameters include a cache key.
   *
   * If no 'sf_cache_key' parameter is present one is added to the array as
   * it is passed by reference.
   *
   * @param  array  $parameters An array of parameters
   *
   * @return string The cache key
   */
  public function checkCacheKey(array & $parameters)
  {
    $parameters['sf_cache_key'] = $this->computeCacheKey($parameters);

    return $parameters['sf_cache_key'];
  }

  /**
   * Computes a partial internal URI.
   *
   * @param  string $module    The module name
   * @param  string $action    The action name
   * @param  string $cacheKey  The cache key
   *
   * @return string The internal URI
   */
  public function getPartialUri($module, $action, $cacheKey)
  {
    return sprintf('@sf_cache_partial?module=%s&action=%s&sf_cache_key=%s', $module, $action, $cacheKey);
  }

  /**
   * Returns whether a partial template is in the cache.
   *
   * @param  string $module    The module name
   * @param  string $action    The action name
   * @param  string $cacheKey  The cache key
   *
   * @return bool true if a partial is in the cache, false otherwise
   */
  public function hasPartialCache($module, $action, $cacheKey)
  {
    return $this->has($this->getPartialUri($module, $action, $cacheKey));
  }

  /**
   * Gets a partial template from the cache.
   *
   * @param  string $module    The module name
   * @param  string $action    The action name
   * @param  string $cacheKey  The cache key
   *
   * @return string The cache content
   */
  public function getPartialCache($module, $action, $cacheKey)
  {
    $uri = $this->getPartialUri($module, $action, $cacheKey);

    if (!$this->isCacheable($uri))
    {
      return null;
    }

    // retrieve content from cache
    $cache = $this->get($uri);

    if (null === $cache)
    {
      return null;
    }

    $cache = unserialize($cache);
    $content = $cache['content'];
    $this->context->getResponse()->merge($cache['response']);

    if (sfConfig::get('sf_web_debug'))
    {
      $content = $this->dispatcher->filter(new sfEvent($this, 'view.cache.filter_content', array('response' => $this->context->getResponse(), 'uri' => $uri, 'new' => false)), $content)->getReturnValue();
    }

    return $content;
  }

  /**
   * Sets an action template in the cache.
   *
   * @param  string $module    The module name
   * @param  string $action    The action name
   * @param  string $cacheKey  The cache key
   * @param  string $content   The content to cache
   *
   * @return string The cached content
   */
  public function setPartialCache($module, $action, $cacheKey, $content)
  {
    $uri = $this->getPartialUri($module, $action, $cacheKey);
    if (!$this->isCacheable($uri))
    {
      return $content;
    }

    $saved = $this->set(serialize(array('content' => $content, 'response' => $this->context->getResponse())), $uri);

    if ($saved && sfConfig::get('sf_web_debug'))
    {
      $content = $this->dispatcher->filter(new sfEvent($this, 'view.cache.filter_content', array('response' => $this->context->getResponse(), 'uri' => $uri, 'new' => true)), $content)->getReturnValue();
    }

    return $content;
  }

  /**
   * Returns whether an action template is in the cache.
   *
   * @param  string  $uri  The internal URI
   *
   * @return bool true if an action is in the cache, false otherwise
   */
  public function hasActionCache($uri)
  {
    return $this->has($uri) && !$this->withLayout($uri);
  }

  /**
   * Gets an action template from the cache.
   *
   * @param  string $uri  The internal URI
   *
   * @return array  An array composed of the cached content and the view attribute holder
   */
  public function getActionCache($uri)
  {
    if (!$this->isCacheable($uri) || $this->withLayout($uri))
    {
      return null;
    }

    // retrieve content from cache
    $cache = $this->get($uri);

    if (null === $cache)
    {
      return null;
    }

    $cache = unserialize($cache);
    $content = $cache['content'];
    $cache['response']->setEventDispatcher($this->dispatcher);
    $this->context->getResponse()->copyProperties($cache['response']);

    if (sfConfig::get('sf_web_debug'))
    {
      $content = $this->dispatcher->filter(new sfEvent($this, 'view.cache.filter_content', array('response' => $this->context->getResponse(), 'uri' => $uri, 'new' => false)), $content)->getReturnValue();
    }

    return array($content, $cache['decoratorTemplate']);
  }

  /**
   * Sets an action template in the cache.
   *
   * @param  string $uri                The internal URI
   * @param  string $content            The content to cache
   * @param  string $decoratorTemplate  The view attribute holder to cache
   *
   * @return string The cached content
   */
  public function setActionCache($uri, $content, $decoratorTemplate)
  {
    if (!$this->isCacheable($uri) || $this->withLayout($uri))
    {
      return $content;
    }

    $saved = $this->set(serialize(array('content' => $content, 'decoratorTemplate' => $decoratorTemplate, 'response' => $this->context->getResponse())), $uri);

    if ($saved && sfConfig::get('sf_web_debug'))
    {
      $content = $this->dispatcher->filter(new sfEvent($this, 'view.cache.filter_content', array('response' => $this->context->getResponse(), 'uri' => $uri, 'new' => true)), $content)->getReturnValue();
    }

    return $content;
  }

  /**
   * Sets a page in the cache.
   *
   * @param string $uri  The internal URI
   */
  public function setPageCache($uri)
  {
    if (sfView::RENDER_CLIENT != $this->controller->getRenderMode())
    {
      return;
    }

    // save content in cache
    $saved = $this->set(serialize($this->context->getResponse()), $uri);

    if ($saved && sfConfig::get('sf_web_debug'))
    {
      $content = $this->dispatcher->filter(new sfEvent($this, 'view.cache.filter_content', array('response' => $this->context->getResponse(), 'uri' => $uri, 'new' => true)), $this->context->getResponse()->getContent())->getReturnValue();

      $this->context->getResponse()->setContent($content);
    }
  }

  /**
   * Gets a page from the cache.
   *
   * @param  string $uri  The internal URI
   *
   * @return string The cached page
   */
  public function getPageCache($uri)
  {
    $retval = $this->get($uri);

    if (null === $retval)
    {
      return false;
    }

    $cachedResponse = unserialize($retval);
    $cachedResponse->setEventDispatcher($this->dispatcher);

    if (sfView::RENDER_VAR == $this->controller->getRenderMode())
    {
      $this->controller->getActionStack()->getLastEntry()->setPresentation($cachedResponse->getContent());
      $this->context->getResponse()->setContent('');
    }
    else
    {
      $this->context->setResponse($cachedResponse);

      if (sfConfig::get('sf_web_debug'))
      {
        $content = $this->dispatcher->filter(new sfEvent($this, 'view.cache.filter_content', array('response' => $this->context->getResponse(), 'uri' => $uri, 'new' => false)), $this->context->getResponse()->getContent())->getReturnValue();

        $this->context->getResponse()->setContent($content);
      }
    }

    return true;
  }

  /**
   * Returns the current request's cache key.
   *
   * This cache key is calculated based on the routing factory's current URI
   * and any GET parameters from the current request factory.
   *
   * @return string The cache key for the current request
   */
  public function getCurrentCacheKey()
  {
    $cacheKey = $this->routing->getCurrentInternalUri();

    if ($getParameters = $this->request->getGetParameters())
    {
      $cacheKey .= false === strpos($cacheKey, '?') ? '?' : '&';
      $cacheKey .= http_build_query($getParameters, null, '&');
    }

    return $cacheKey;
  }

  /**
   * Listens to the 'view.cache.filter_content' event to decorate a chunk of HTML with cache information.
   *
   * @param sfEvent $event   A sfEvent instance
   * @param string  $content The HTML content
   *
   * @return string The decorated HTML string
   */
  public function decorateContentWithDebug(sfEvent $event, $content)
  {
    // don't decorate if not html or if content is null
    if (!$content || false === strpos($event['response']->getContentType(), 'html'))
    {
      return $content;
    }

    $this->context->getConfiguration()->loadHelpers(array('Helper', 'Url', 'Asset', 'Tag'));

    $sf_cache_key = $this->generateCacheKey($event['uri']);
    $bgColor      = $event['new'] ? '#9ff' : '#ff9';
    $lastModified = $this->cache->getLastModified($sf_cache_key);
    $cacheKey     = $this->cache->getOption('prefix').$sf_cache_key;
    $id           = md5($event['uri']);

    return '
      <div id="main_'.$id.'" class="sfWebDebugActionCache" style="border: 1px solid #f00">
      <div id="sub_main_'.$id.'" class="sfWebDebugCache" style="background-color: '.$bgColor.'; border-right: 1px solid #f00; border-bottom: 1px solid #f00;">
      <div style="height: 16px; padding: 2px"><a href="#" onclick="sfWebDebugToggle(\'sub_main_info_'.$id.'\'); return false;"><strong>cache information</strong></a>&nbsp;<a href="#" onclick="sfWebDebugToggle(\'sub_main_'.$id.'\'); document.getElementById(\'main_'.$id.'\').style.border = \'none\'; return false;">'.image_tag(sfConfig::get('sf_web_debug_web_dir').'/images/close.png', array('alt' => 'close')).'</a>&nbsp;</div>
        <div style="padding: 2px; display: none" id="sub_main_info_'.$id.'">
        [uri]&nbsp;'.htmlspecialchars($event['uri'], ENT_QUOTES, sfConfig::get('sf_charset')).'<br />
        [key&nbsp;for&nbsp;cache]&nbsp;'.htmlspecialchars($cacheKey, ENT_QUOTES, sfConfig::get('sf_charset')).'<br />
        [life&nbsp;time]&nbsp;'.$this->getLifeTime($event['uri']).'&nbsp;seconds<br />
        [last&nbsp;modified]&nbsp;'.(time() - $lastModified).'&nbsp;seconds<br />
        &nbsp;<br />&nbsp;
        </div>
      </div><div>
      '.$content.'
      </div></div>
    ';
  }
}
