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
 * sfRequest provides methods for manipulating client request information such
 * as attributes, and parameters. It is also possible to manipulate the
 * request method originally sent by the user.
 *
 * @package    symfony
 * @subpackage request
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfRequest.class.php 12660 2008-11-05 11:32:23Z fabien $
 */
abstract class sfRequest
{
  const GET    = 'GET';
  const POST   = 'POST';
  const PUT    = 'PUT';
  const DELETE = 'DELETE';
  const HEAD   = 'HEAD';

  protected
    $dispatcher      = null,
    $method          = null,
    $options         = array(),
    $parameterHolder = null,
    $attributeHolder = null;

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct(sfEventDispatcher $dispatcher, $parameters = array(), $attributes = array(), $options = array())
  {
    $this->initialize($dispatcher, $parameters, $attributes, $options);
  }

  /**
   * Initializes this sfRequest.
   *
   * Available options:
   *
   *  * logging: Whether to enable logging or not (false by default)
   *
   * @param  sfEventDispatcher $dispatcher  An sfEventDispatcher instance
   * @param  array             $parameters  An associative array of initialization parameters
   * @param  array             $attributes  An associative array of initialization attributes
   * @param  array             $options     An associative array of options
   *
   * @return bool true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this sfRequest
   */
  public function initialize(sfEventDispatcher $dispatcher, $parameters = array(), $attributes = array(), $options = array())
  {
    $this->dispatcher = $dispatcher;

    $this->options = $options;

    if (!isset($this->options['logging']))
    {
      $this->options['logging'] = false;
    }

    // initialize parameter and attribute holders
    $this->parameterHolder = new sfParameterHolder();
    $this->attributeHolder = new sfParameterHolder();

    $this->parameterHolder->add($parameters);
    $this->attributeHolder->add($attributes);
  }

  /**
   * Extracts parameter values from the request.
   *
   * @param  array $names  An indexed array of parameter names to extract
   *
   * @return array An associative array of parameters and their values. If
   *               a specified parameter doesn't exist an empty string will
   *               be returned for its value
   */
  public function extractParameters($names)
  {
    $array = array();

    $parameters = $this->parameterHolder->getAll();
    foreach ($parameters as $key => $value)
    {
      if (in_array($key, $names))
      {
        $array[$key] = $value;
      }
    }

    return $array;
  }

  /**
   * Gets the request method.
   *
   * @return string The request method
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * Sets the request method.
   *
   * @param string $method  The request method
   *
   * @throws <b>sfException</b> - If the specified request method is invalid
   */
  public function setMethod($method)
  {
    if (!in_array(strtoupper($method), array(self::GET, self::POST, self::PUT, self::DELETE, self::HEAD)))
    {
      throw new sfException(sprintf('Invalid request method: %s.', $method));
    }

    $this->method = strtoupper($method);
  }

  /**
   * Retrieves the parameters for the current request.
   *
   * @return sfParameterHolder The parameter holder
   */
  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  /**
   * Retrieves the attributes holder.
   *
   * @return sfParameterHolder The attribute holder
   */
  public function getAttributeHolder()
  {
    return $this->attributeHolder;
  }

  /**
   * Retrieves an attribute from the current request.
   *
   * @param  string $name     Attribute name
   * @param  string $default  Default attribute value
   *
   * @return mixed An attribute value
   */
  public function getAttribute($name, $default = null)
  {
    return $this->attributeHolder->get($name, $default);
  }

  /**
   * Indicates whether or not an attribute exist for the current request.
   *
   * @param  string $name  Attribute name
   *
   * @return bool true, if the attribute exists otherwise false
   */
  public function hasAttribute($name)
  {
    return $this->attributeHolder->has($name);
  }

  /**
   * Sets an attribute for the request.
   *
   * @param string $name   Attribute name
   * @param string $value  Value for the attribute
   *
   */
  public function setAttribute($name, $value)
  {
    $this->attributeHolder->set($name, $value);
  }

  /**
   * Retrieves a paramater for the current request.
   *
   * @param string $name     Parameter name
   * @param string $default  Parameter default value
   *
   */
  public function getParameter($name, $default = null)
  {
    return $this->parameterHolder->get($name, $default);
  }

  /**
   * Indicates whether or not a parameter exist for the current request.
   *
   * @param  string $name  Parameter name
   *
   * @return bool true, if the paramater exists otherwise false
   */
  public function hasParameter($name)
  {
    return $this->parameterHolder->has($name);
  }

  /**
   * Sets a parameter for the current request.
   *
   * @param string $name   Parameter name
   * @param string $value  Parameter value
   *
   */
  public function setParameter($name, $value)
  {
    $this->parameterHolder->set($name, $value);
  }

  /**
   * Calls methods defined via sfEventDispatcher.
   *
   * @param  string $method     The method name
   * @param  array  $arguments  The method arguments
   *
   * @return mixed The returned value of the called method
   *
   * @throws <b>sfException</b> if call fails
   */
  public function __call($method, $arguments)
  {
    $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'request.method_not_found', array('method' => $method, 'arguments' => $arguments)));
    if (!$event->isProcessed())
    {
      throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
    }

    return $event->getReturnValue();
  }

  public function __clone()
  {
    $this->parameterHolder = clone $this->parameterHolder;
    $this->attributeHolder = clone $this->attributeHolder;
  }
}
