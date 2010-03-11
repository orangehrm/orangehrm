<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfRequestRoute represents a route that is request aware.
 *
 * It implements the sf_method requirement.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfRequestRoute.class.php 11794 2008-09-26 09:05:48Z fabien $
 */
class sfRequestRoute extends sfRoute
{
  /**
   * Returns true if the URL matches this route, false otherwise.
   *
   * @param  string  $url     The URL
   * @param  array   $context The context
   *
   * @return array   An array of parameters
   */
  public function matchesUrl($url, $context = array())
  {
    if (false === $parameters = parent::matchesUrl($url, $context))
    {
      return false;
    }

    if (!isset($this->requirements['sf_method']))
    {
      $this->requirements['sf_method'] = array('get', 'head');
    }

    // enforce the sf_method requirement
    $methods = is_array($this->requirements['sf_method']) ? $this->requirements['sf_method'] : array($this->requirements['sf_method']);
    foreach ($methods as $method)
    {
      if (0 == strcasecmp($method, $context['method']))
      {
        return $parameters;
      }
    }

    return false;
  }

  /**
   * Returns true if the parameters matches this route, false otherwise.
   *
   * @param  mixed   $params The parameters
   * @param  array   $context The context
   *
   * @return Boolean         true if the parameters matches this route, false otherwise.
   */
  public function matchesParameters($params, $context = array())
  {
    if (isset($params['sf_method']))
    {
      if (!isset($this->requirements['sf_method']))
      {
        $this->requirements['sf_method'] = 'get';
      }

      // enforce the sf_method requirement
      if ($this->requirements['sf_method'] != $params['sf_method'])
      {
        return false;
      }

      unset($params['sf_method']);
    }

    return parent::matchesParameters($params, $context);
  }

  /**
   * Generates a URL from the given parameters.
   *
   * @param  mixed   $params    The parameter values
   * @param  array   $context   The context
   * @param  Boolean $absolute  Whether to generate an absolute URL
   *
   * @return string The generated URL
   */
  public function generate($params, $context = array(), $absolute = false)
  {
    unset($params['sf_method']);

    return parent::generate($params, $context, $absolute);
  }
}
