<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfRouteCollection represents a collection of routes.
 *
 * @package    symfony
 * @subpackage routing
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfRouteCollection.class.php 29491 2010-05-17 13:10:55Z fabien $
 */
class sfRouteCollection implements Iterator
{
  protected
    $count   = 0,
    $options = array(),
    $routes  = array();

  /**
   * Constructor.
   *
   * @param array $options An array of options
   */
  public function __construct(array $options)
  {
    if (!isset($options['name']))
    {
      throw new InvalidArgumentException('You must pass a "name" option to sfRouteCollection');
    }

    $this->options = $options;
  }

  /**
   * Returns the routes.
   *
   * @return array The routes
   */
  public function getRoutes()
  {
    return $this->routes;
  }

  /**
   * Returns the options.
   *
   * @return array The options
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Reset the error array to the beginning (implements the Iterator interface).
   */
  public function rewind()
  {
    reset($this->routes);

    $this->count = count($this->routes);
  }

  /**
   * Get the name of the current route (implements the Iterator interface).
   *
   * @return string The key
   */
  public function key()
  {
    return key($this->routes);
  }

  /**
   * Returns the current route (implements the Iterator interface).
   *
   * @return mixed The escaped value
   */
  public function current()
  {
    return current($this->routes);
  }

  /**
   * Moves to the next route (implements the Iterator interface).
   */
  public function next()
  {
    next($this->routes);

    --$this->count;
  }

  /**
   * Returns true if the current route is valid (implements the Iterator interface).
   *
   * @return boolean The validity of the current route; true if it is valid
   */
  public function valid()
  {
    return $this->count > 0;
  }
}
