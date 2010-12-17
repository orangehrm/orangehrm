<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfPatternRouting class controls the generation and parsing of URLs.
 *
 * It parses and generates URLs by delegating the work to an array of sfRoute objects.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPatternRouting.class.php 29418 2010-05-12 07:36:59Z fabien $
 */
class sfPatternRouting extends sfRouting
{
  protected
    $currentRouteName   = null,
    $currentInternalUri = array(),
    $routes             = array(),
    $defaultParamsDirty = false,
    $cacheData          = array(),
    $cacheChanged       = false;

  /**
   * Initializes this Routing.
   *
   * Available options:
   *
   *  * suffix:                           The default suffix
   *  * variable_prefixes:                An array of characters that starts a variable name (: by default)
   *  * segment_separators:               An array of allowed characters for segment separators (/ and . by default)
   *  * variable_regex:                   A regex that match a valid variable name ([\w\d_]+ by default)
   *  * generate_shortest_url:            Whether to generate the shortest URL possible (true by default)
   *  * extra_parameters_as_query_string: Whether to generate extra parameters as a query string
   *  * lookup_cache_dedicated_keys:      Whether to use dedicated keys for parse/generate cache (false by default)
   *                                      WARNING: When this option is activated, do not use sfFileCache; use a fast access
   *                                      cache backend (like sfAPCCache).
   *
   * @see sfRouting
   */
  public function initialize(sfEventDispatcher $dispatcher, sfCache $cache = null, $options = array())
  {
    $options = array_merge(array(
      'variable_prefixes'                => array(':'),
      'segment_separators'               => array('/', '.'),
      'variable_regex'                   => '[\w\d_]+',
      'load_configuration'               => false,
      'suffix'                           => '',
      'generate_shortest_url'            => true,
      'extra_parameters_as_query_string' => true,
      'lookup_cache_dedicated_keys'      => false,
    ), $options);

    // for BC
    if ('.' == $options['suffix'])
    {
      $options['suffix'] = '';
    }

    parent::initialize($dispatcher, $cache, $options);

    if (null !== $this->cache && !$options['lookup_cache_dedicated_keys'] && $cacheData = $this->cache->get('symfony.routing.data'))
    {
      $this->cacheData = unserialize($cacheData);
    }
  }

  /**
   * @see sfRouting
   */
  public function loadConfiguration()
  {
    if ($this->options['load_configuration'] && $config = $this->getConfigFilename())
    {
      include($config);
    }

    parent::loadConfiguration();
  }

  /**
   * Added for better performance. We need to ensure that changed default parameters
   * are set, but resetting them everytime wastes many cpu cycles
   */
  protected function ensureDefaultParametersAreSet()
  {
    if ($this->defaultParamsDirty)
    {
      foreach ($this->routes as $route)
      {
        $route->setDefaultParameters($this->defaultParameters);
      }
      $this->defaultParamsDirty = false;
    }
  }

  /**
   * @see sfRouting
   */
  public function setDefaultParameter($key, $value)
  {
    parent::setDefaultParameter($key, $value);
    $this->defaultParamsDirty = true;
  }

  /**
   * @see sfRouting
   */
  public function setDefaultParameters($parameters)
  {
    parent::setDefaultParameters($parameters);
    $this->defaultParamsDirty = true;
  }

  protected function getConfigFileName()
  {
    return sfContext::getInstance()->getConfigCache()->checkConfig('config/routing.yml', true);
  }

  /**
   * @see sfRouting
   */
  public function getCurrentInternalUri($withRouteName = false)
  {
    return null === $this->currentRouteName ? null : $this->currentInternalUri[$withRouteName ? 0 : 1];
  }

  /**
   * Gets the current route name.
   *
   * @return string The route name
   */
  public function getCurrentRouteName()
  {
    return $this->currentRouteName;
  }

  /**
   * @see sfRouting
   */
  public function getRoutes()
  {
    return $this->routes;
  }

  /**
   * @see sfRouting
   */
  public function setRoutes($routes)
  {
    foreach ($routes as $name => $route)
    {
      $this->connect($name, $route);
    }
  }

  /**
   * @see sfRouting
   */
  public function hasRoutes()
  {
    return count($this->routes) ? true : false;
  }

  /**
   * @see sfRouting
   */
  public function clearRoutes()
  {
    if ($this->options['logging'])
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array('Clear all current routes')));
    }

    $this->routes = array();
  }

  /**
   * Returns true if the route name given is defined.
   *
   * @param  string $name  The route name
   *
   * @return boolean
   */
  public function hasRouteName($name)
  {
    return isset($this->routes[$name]) ? true : false;
  }

  /**
   * Adds a new route at the beginning of the current list of routes.
   *
   * @see connect
   */
  public function prependRoute($name, $route)
  {
    $routes = $this->routes;
    $this->routes = array();
    $this->connect($name, $route);
    $this->routes = array_merge($this->routes, $routes);
  }

  /**
   * Adds a new route.
   *
   * Alias for the connect method.
   *
   * @see connect
   */
  public function appendRoute($name, $route)
  {
    return $this->connect($name, $route);
  }

  /**
   * Adds a new route before a given one in the current list of routes.
   *
   * @see connect
   */
  public function insertRouteBefore($pivot, $name, $route)
  {
    if (!isset($this->routes[$pivot]))
    {
      throw new sfConfigurationException(sprintf('Unable to insert route "%s" before inexistent route "%s".', $name, $pivot));
    }

    $routes = $this->routes;
    $this->routes = array();
    $newroutes = array();
    foreach ($routes as $key => $value)
    {
      if ($key == $pivot)
      {
        $this->connect($name, $route);
        $newroutes = array_merge($newroutes, $this->routes);
      }
      $newroutes[$key] = $value;
    }

    $this->routes = $newroutes;
  }

  /**
   * Adds a new route at the end of the current list of routes.
   *
   * A route string is a string with 2 special constructions:
   * - :string: :string denotes a named parameter (available later as $request->getParameter('string'))
   * - *: * match an indefinite number of parameters in a route
   *
   * Here is a very common rule in a symfony project:
   *
   * <code>
   * $r->connect('default', new sfRoute('/:module/:action/*'));
   * </code>
   *
   * @param  string  $name  The route name
   * @param  sfRoute $route A sfRoute instance
   *
   * @return array  current routes
   */
  public function connect($name, $route)
  {
    $routes = $route instanceof sfRouteCollection ? $route : array($name => $route);
    foreach (self::flattenRoutes($routes) as $name => $route)
    {
      $this->routes[$name] = $route;
      $this->configureRoute($route);

      if ($this->options['logging'])
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Connect %s "%s" (%s)', get_class($route), $name, $route->getPattern()))));
      }
    }
  }

  public function configureRoute(sfRoute $route)
  {
    $route->setDefaultParameters($this->defaultParameters);
    $route->setDefaultOptions($this->options);
  }

  /**
   * @see sfRouting
   */
  public function generate($name, $params = array(), $absolute = false)
  {
    // fetch from cache
    if (null !== $this->cache)
    {
      $cacheKey = 'generate_'.$name.'_'.md5(serialize(array_merge($this->defaultParameters, $params))).'_'.md5(serialize($this->options['context']));
      if ($this->options['lookup_cache_dedicated_keys'] && $url = $this->cache->get('symfony.routing.data.'.$cacheKey))
      {
        return $this->fixGeneratedUrl($url, $absolute);
      }
      elseif (isset($this->cacheData[$cacheKey]))
      {
        return $this->fixGeneratedUrl($this->cacheData[$cacheKey], $absolute);
      }
    }

    if ($name)
    {
      // named route
      if (!isset($this->routes[$name]))
      {
        throw new sfConfigurationException(sprintf('The route "%s" does not exist.', $name));
      }
      $route = $this->routes[$name];
      $this->ensureDefaultParametersAreSet();
    }
    else
    {
      // find a matching route
      if (false === $route = $this->getRouteThatMatchesParameters($params, $this->options['context']))
      {
        throw new sfConfigurationException(sprintf('Unable to find a matching route to generate url for params "%s".', is_object($params) ? 'Object('.get_class($params).')' : str_replace("\n", '', var_export($params, true))));
      }
    }

    $url = $route->generate($params, $this->options['context'], $absolute);

    // store in cache
    if (null !== $this->cache)
    {
      if ($this->options['lookup_cache_dedicated_keys'])
      {
        $this->cache->set('symfony.routing.data.'.$cacheKey, $url);
      }
      else
      {
        $this->cacheChanged = true;
        $this->cacheData[$cacheKey] = $url;
      }
    }

    return $this->fixGeneratedUrl($url, $absolute);
  }

  /**
   * @see sfRouting
   */
  public function parse($url)
  {
    if (false === $info = $this->findRoute($url))
    {
      $this->currentRouteName = null;
      $this->currentInternalUri = array();

      return false;
    }

    if ($this->options['logging'])
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Match route "%s" (%s) for %s with parameters %s', $info['name'], $info['pattern'], $url, str_replace("\n", '', var_export($info['parameters'], true))))));
    }

    // store the current internal URI
    $this->updateCurrentInternalUri($info['name'], $info['parameters']);

    $route = $this->routes[$info['name']];

    $this->ensureDefaultParametersAreSet();

    $route->bind($this->options['context'], $info['parameters']);
    $info['parameters']['_sf_route'] = $route;

    return $info['parameters'];
  }

  protected function updateCurrentInternalUri($name, array $parameters)
  {
    // store the route name
    $this->currentRouteName = $name;

    $internalUri = array('@'.$this->currentRouteName, $parameters['module'].'/'.$parameters['action']);
    unset($parameters['module'], $parameters['action']);

    $params = array();
    foreach ($parameters as $key => $value)
    {
      $params[] = $key.'='.$value;
    }

    // sort to guaranty unicity
    sort($params);

    $params = $params ? '?'.implode('&', $params) : '';

    $this->currentInternalUri = array($internalUri[0].$params, $internalUri[1].$params);
  }

  /**
   * Finds a matching route for given URL.
   *
   * Returns false if no route matches.
   *
   * Returned array contains:
   *
   *  - name:       name or alias of the route that matched
   *  - pattern:    the compiled pattern of the route that matched
   *  - parameters: array containing key value pairs of the request parameters including defaults
   *
   * @param  string $url     URL to be parsed
   *
   * @return array|false  An array with routing information or false if no route matched
   */
  public function findRoute($url)
  {
    $url = $this->normalizeUrl($url);

    // fetch from cache
    if (null !== $this->cache)
    {
      $cacheKey = 'parse_'.$url.'_'.md5(serialize($this->options['context']));
      if ($this->options['lookup_cache_dedicated_keys'] && $info = $this->cache->get('symfony.routing.data.'.$cacheKey))
      {
        return unserialize($info);
      }
      elseif (isset($this->cacheData[$cacheKey]))
      {
        return $this->cacheData[$cacheKey];
      }
    }

    $info = $this->getRouteThatMatchesUrl($url);

    // store in cache
    if (null !== $this->cache)
    {
      if ($this->options['lookup_cache_dedicated_keys'])
      {
        $this->cache->set('symfony.routing.data.'.$cacheKey, serialize($info));
      }
      else
      {
        $this->cacheChanged = true;
        $this->cacheData[$cacheKey] = $info;
      }
    }

    return $info;
  }

  static public function flattenRoutes($routes)
  {
    $flattenRoutes = array();
    foreach ($routes as $name => $route)
    {
      if ($route instanceof sfRouteCollection)
      {
        $flattenRoutes = array_merge($flattenRoutes, self::flattenRoutes($route));
      }
      else
      {
        $flattenRoutes[$name] = $route;
      }
    }

    return $flattenRoutes;
  }

  protected function getRouteThatMatchesUrl($url)
  {
    $this->ensureDefaultParametersAreSet();
    foreach ($this->routes as $name => $route)
    {
      if (false === $parameters = $route->matchesUrl($url, $this->options['context']))
      {
        continue;
      }

      return array('name' => $name, 'pattern' => $route->getPattern(), 'parameters' => $parameters);
    }

    return false;
  }

  protected function getRouteThatMatchesParameters($parameters)
  {
    $this->ensureDefaultParametersAreSet();
    foreach ($this->routes as $route)
    {
      if ($route->matchesParameters($parameters, $this->options['context']))
      {
        return $route;
      }
    }

    return false;
  }

  protected function normalizeUrl($url)
  {
    // an URL should start with a '/', mod_rewrite doesn't respect that, but no-mod_rewrite version does.
    if ('/' != substr($url, 0, 1))
    {
      $url = '/'.$url;
    }

    // we remove the query string
    if (false !== $pos = strpos($url, '?'))
    {
      $url = substr($url, 0, $pos);
    }

    // remove multiple /
    $url = preg_replace('#/+#', '/', $url);

    return $url;
  }

  /**
   * @see sfRouting
   */
  public function shutdown()
  {
    if (null !== $this->cache && $this->cacheChanged)
    {
      $this->cacheChanged = false;
      $this->cache->set('symfony.routing.data', serialize($this->cacheData));
    }
  }
}
