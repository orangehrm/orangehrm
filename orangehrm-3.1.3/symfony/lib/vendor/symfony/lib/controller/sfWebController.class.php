<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWebController provides web specific methods to sfController such as, url redirection.
 *
 * @package    symfony
 * @subpackage controller
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfWebController.class.php 30563 2010-08-06 11:22:44Z fabien $
 */
abstract class sfWebController extends sfController
{
  /**
   * Generates an URL from an array of parameters.
   *
   * @param mixed   $parameters An associative array of URL parameters or an internal URI as a string.
   * @param boolean $absolute   Whether to generate an absolute URL
   *
   * @return string A URL to a symfony resource
   */
  public function genUrl($parameters = array(), $absolute = false)
  {
    $route = '';
    $fragment = '';

    if (is_string($parameters))
    {
      // absolute URL or symfony URL?
      if (preg_match('#^[a-z][a-z0-9\+.\-]*\://#i', $parameters))
      {
        return $parameters;
      }

      // relative URL?
      if (0 === strpos($parameters, '/'))
      {
        return $parameters;
      }

      if ($parameters == '#')
      {
        return $parameters;
      }
  
      // strip fragment
      if (false !== ($pos = strpos($parameters, '#')))
      {
        $fragment = substr($parameters, $pos + 1);
        $parameters = substr($parameters, 0, $pos);
      }

      list($route, $parameters) = $this->convertUrlStringToParameters($parameters);
    }
    else if (is_array($parameters))
    {
      if (isset($parameters['sf_route']))
      {
        $route = $parameters['sf_route'];
        unset($parameters['sf_route']);
      }
    }

    // routing to generate path
    $url = $this->context->getRouting()->generate($route, $parameters, $absolute);

    if ($fragment)
    {
      $url .= '#'.$fragment;
    }

    return $url;
  }

  /**
   * Converts an internal URI string to an array of parameters.
   *
   * @param string $url An internal URI
   *
   * @return array An array of parameters
   */
  public function convertUrlStringToParameters($url)
  {
    $givenUrl = $url;

    $params = array();
    $queryString = '';
    $route = '';

    // empty url?
    if (!$url)
    {
      $url = '/';
    }

    // we get the query string out of the url
    if ($pos = strpos($url, '?'))
    {
      $queryString = substr($url, $pos + 1);
      $url = substr($url, 0, $pos);
    }

    // 2 url forms
    // @routeName?key1=value1&key2=value2...
    // module/action?key1=value1&key2=value2...

    // first slash optional
    if ($url[0] == '/')
    {
      $url = substr($url, 1);
    }

    // routeName?
    if ($url[0] == '@')
    {
      $route = substr($url, 1);
    }
    else if (false !== strpos($url, '/'))
    {
      list($params['module'], $params['action']) = explode('/', $url);
    }
    else if (!$queryString)
    {
      $route = $givenUrl;
    }
    else
    {
      throw new InvalidArgumentException(sprintf('An internal URI must contain a module and an action (module/action) ("%s" given).', $givenUrl));
    }

    // split the query string
    if ($queryString)
    {
      $matched = preg_match_all('/
        ([^&=]+)            # key
        =                   # =
        (.*?)               # value
        (?:
          (?=&[^&=]+=) | $  # followed by another key= or the end of the string
        )
      /x', $queryString, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      foreach ($matches as $match)
      {
        $params[urldecode($match[1][0])] = urldecode($match[2][0]);
      }

      // check that all string is matched
      if (!$matched)
      {
        throw new sfParseException(sprintf('Unable to parse query string "%s".', $queryString));
      }
    }

    return array($route, $params);
  }

  /**
   * Redirects the request to another URL.
   *
   * @param string $url        An associative array of URL parameters or an internal URI as a string
   * @param int    $delay      A delay in seconds before redirecting. This is only needed on
   *                           browsers that do not support HTTP headers
   * @param int    $statusCode The status code
   *
   * @throws InvalidArgumentException If the url argument is null or an empty string
   */
  public function redirect($url, $delay = 0, $statusCode = 302)
  {
    if (empty($url))
    {
      throw new InvalidArgumentException('Cannot redirect to an empty URL.'); 
    }

    $url = $this->genUrl($url, true);
    // see #8083
    $url = str_replace('&amp;', '&', $url);

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Redirect to "%s"', $url))));
    }

    // redirect
    $response = $this->context->getResponse();
    $response->clearHttpHeaders();
    $response->setStatusCode($statusCode);

    // The Location header should only be used for status codes 201 and 3..
    // For other code, only the refresh meta tag is used
    if ($statusCode == 201 || ($statusCode >= 300 && $statusCode < 400))
    {
      $response->setHttpHeader('Location', $url);
    }

    $response->setContent(sprintf('<html><head><meta http-equiv="refresh" content="%d;url=%s"/></head></html>', $delay, htmlspecialchars($url, ENT_QUOTES, sfConfig::get('sf_charset'))));
    $response->send();
  }
}
