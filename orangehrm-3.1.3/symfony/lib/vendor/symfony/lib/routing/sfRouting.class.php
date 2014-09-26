<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfRouting class controls the generation and parsing of URLs.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfRouting.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class sfRouting
{
  protected
    $dispatcher        = null,
    $cache             = null,
    $defaultParameters = array(),
    $options           = array();

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct(sfEventDispatcher $dispatcher, sfCache $cache = null, $options = array())
  {
    $this->initialize($dispatcher, $cache, $options);

    if (!isset($this->options['auto_shutdown']) || $this->options['auto_shutdown'])
    {
      register_shutdown_function(array($this, 'shutdown'));
    }
  }

  /**
   * Returns the routing cache object.
   *
   * @return sfCache A sfCache instance or null
   */
  public function getCache()
  {
    return $this->cache;
  }

  /**
   * Initializes this sfRouting instance.
   *
   * Available options:
   *
   *  * default_module: The default module name
   *  * default_action: The default action name
   *  * logging:        Whether to log or not (false by default)
   *  * debug:          Whether to cache or not (false by default)
   *  * context:        An array of context variables to help URL matching and generation
   *
   * @param sfEventDispatcher $dispatcher  An sfEventDispatcher instance
   * @param sfCache           $cache       An sfCache instance
   * @param array             $options     An associative array of initialization options.
   */
  public function initialize(sfEventDispatcher $dispatcher, sfCache $cache = null, $options = array())
  {
    $this->dispatcher = $dispatcher;

    $options['debug'] = isset($options['debug']) ? (boolean) $options['debug'] : false;

    // disable caching when in debug mode
    $this->cache = $options['debug'] ? null : $cache;

    $this->setDefaultParameter('module', isset($options['default_module']) ? $options['default_module'] : 'default');
    $this->setDefaultParameter('action', isset($options['default_action']) ? $options['default_action'] : 'index');

    if (!isset($options['logging']))
    {
      $options['logging'] = false;
    }

    if (!isset($options['context']))
    {
      $options['context'] = array();
    }

    $this->options = $options;

    $this->dispatcher->connect('user.change_culture', array($this, 'listenToChangeCultureEvent'));
    $this->dispatcher->connect('request.filter_parameters', array($this, 'filterParametersEvent'));

    $this->loadConfiguration();
  }

  /**
   * Returns the options.
   *
   * @return array An array of options
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Loads routing configuration.
   *
   * This methods notifies a routing.load_configuration event.
   */
  public function loadConfiguration()
  {
    $this->dispatcher->notify(new sfEvent($this, 'routing.load_configuration'));
  }

  /**
   * Gets the internal URI for the current request.
   *
   * @param  bool $with_route_name  Whether to give an internal URI with the route name (@route)
   *                                or with the module/action pair
   *
   * @return string The current internal URI
   */
  abstract public function getCurrentInternalUri($with_route_name = false);

  /**
   * Gets the current compiled route array.
   *
   * @return array The route array
   */
  abstract public function getRoutes();

  /**
   * Sets the compiled route array.
   *
   * @param  array $routes  The route array
   *
   * @return array The route array
   */
  abstract public function setRoutes($routes);

  /**
   * Returns true if this instance has some routes.
   *
   * @return bool
   */
  abstract public function hasRoutes();

  /**
   * Clears all current routes.
   */
  abstract public function clearRoutes();

 /**
  * Generates a valid URLs for parameters.
  *
  * @param  string  $name      The route name
  * @param  array   $params    The parameter values
  * @param  Boolean $absolute  Whether to generate an absolute URL
  *
  * @return string The generated URL
  */
  abstract public function generate($name, $params = array(), $absolute = false);

 /**
  * Parses a URL to find a matching route and sets internal state.
  *
  * Returns false if no route match the URL.
  *
  * @param  string $url  URL to be parsed
  *
  * @return array|false  An array of parameters or false if the route does not match
  */
  abstract public function parse($url);

  /**
   * Gets the default parameters for URL generation.
   *
   * @return array  An array of default parameters
   */
  public function getDefaultParameters()
  {
    return $this->defaultParameters;
  }

  /**
   * Gets a default parameter.
   *
   * @param  string $key    The key
   *
   * @return string The value
   */
  public function getDefaultParameter($key)
  {
    return isset($this->defaultParameters[$key]) ? $this->defaultParameters[$key] : null;
  }

  /**
   * Sets a default parameter.
   *
   * @param string $key    The key
   * @param string $value  The value
   */
  public function setDefaultParameter($key, $value)
  {
    $this->defaultParameters[$key] = $value;
  }

  /**
   * Sets the default parameters for URL generation.
   *
   * @param array $parameters  An array of default parameters
   */
  public function setDefaultParameters($parameters)
  {
    $this->defaultParameters = $parameters;
  }

  /**
   * Listens to the user.change_culture event.
   *
   * @param sfEvent $event An sfEvent instance
   *
   */
  public function listenToChangeCultureEvent(sfEvent $event)
  {
    // change the culture in the routing default parameters
    $this->setDefaultParameter('sf_culture', $event['culture']);
  }

  /**
   * Listens to the request.filter_parameters event.
   *
   * @param  sfEvent $event       An sfEvent instance
   *
   * @return array   $parameters  An array of parameters for the event
   */
  public function filterParametersEvent(sfEvent $event, $parameters)
  {
    $context = $event->getParameters();

    $this->options['context'] = $context;

    if (false === $params = $this->parse($event['path_info']))
    {
      return $parameters;
    }

    return array_merge($parameters, $params);
  }

  protected function fixGeneratedUrl($url, $absolute = false)
  {
    if (isset($this->options['context']['prefix']))
    {
      if (0 === strpos($url, 'http'))
      {
        $url = preg_replace('#https?\://[^/]+#', '$0'.$this->options['context']['prefix'], $url);
      }
      else
      {
        $url = $this->options['context']['prefix'].$url;
      }
    }

    if ($absolute && isset($this->options['context']['host']) && 0 !== strpos($url, 'http'))
    {
      $url = 'http'.(isset($this->options['context']['is_secure']) && $this->options['context']['is_secure'] ? 's' : '').'://'.$this->options['context']['host'].$url;
    }

    return $url;
  }

  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
  }
}
