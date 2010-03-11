<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfPathInfoRouting class is a very simple routing class that uses PATH_INFO.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPathInfoRouting.class.php 11313 2008-09-03 17:28:33Z fabien $
 */
class sfPathInfoRouting extends sfRouting
{
  protected
    $currentRouteParameters = array();

  /**
   * @see sfRouting
   */
  public function getCurrentInternalUri($with_route_name = false)
  {
    $parameters = $this->currentRouteParameters;

    // other parameters
    unset($parameters['module'], $parameters['action']);
    ksort($parameters);
    $parameters = count($parameters) ? '?'.http_build_query($parameters, null, '&') : '';

    return sprintf('%s/%s%s', $this->currentRouteParameters['module'], $this->currentRouteParameters['action'], $parameters);
  }

 /**
  * @see sfRouting
  */
  public function generate($name, $params = array(), $absolute = false)
  {
    $parameters = $this->mergeArrays($this->defaultParameters, $params);
    if ($this->getDefaultParameter('module') == $parameters['module'])
    {
      unset($parameters['module']);
    }
    if ($this->getDefaultParameter('action') == $parameters['action'])
    {
      unset($parameters['action']);
    }

    $url = '';
    foreach ($parameters as $key => $value)
    {
      $url .= '/'.$key.'/'.$value;
    }

    return $this->fixGeneratedUrl($url ? $url : '/', $absolute);
  }

 /**
  * @see sfRouting
  */
  public function parse($url)
  {
    $this->currentRouteParameters = $this->defaultParameters;
    $array = explode('/', trim($url, '/'));
    $count = count($array);

    for ($i = 0; $i < $count; $i++)
    {
      // see if there's a value associated with this parameter, if not we're done with path data
      if ($count > ($i + 1))
      {
        $this->currentRouteParameters[$array[$i]] = $array[++$i];
      }
    }

    return $this->currentRouteParameters;
  }

  /**
   * @see sfRouting
   */
  public function getRoutes()
  {
    return array();
  }

  /**
   * @see sfRouting
   */
  public function setRoutes($routes)
  {
    return array();
  }

  /**
   * @see sfRouting
   */
  public function hasRoutes()
  {
    return false;
  }

  /**
   * @see sfRouting
   */
  public function clearRoutes()
  {
  }
}
