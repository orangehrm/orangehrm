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
 * sfDatabase is a base abstraction class that allows you to setup any type of
 * database connection via a configuration file.
 *
 * @package    symfony
 * @subpackage database
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfDatabase.class.php 9086 2008-05-20 01:56:29Z Carl.Vondrick $
 */
abstract class sfDatabase
{
  protected
    $parameterHolder = null,
    $connection      = null,
    $resource        = null;

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct($parameters = array())
  {
    $this->initialize($parameters);
  }

  /**
   * Initializes this sfDatabase object.
   *
   * @param array $parameters An associative array of initialization parameters
   *
   * @return bool true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this sfDatabase object
   */
  public function initialize($parameters = array())
  {
    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);
  }

  /**
   * Connects to the database.
   *
   * @throws <b>sfDatabaseException</b> If a connection could not be created
   */
  abstract function connect();

  /**
   * Retrieves the database connection associated with this sfDatabase implementation.
   *
   * When this is executed on a Database implementation that isn't an
   * abstraction layer, a copy of the resource will be returned.
   *
   * @return mixed A database connection
   *
   * @throws <b>sfDatabaseException</b> If a connection could not be retrieved
   */
  public function getConnection()
  {
    if (is_null($this->connection))
    {
      $this->connect();
    }

    return $this->connection;
  }

  /**
   * Retrieves a raw database resource associated with this sfDatabase implementation.
   *
   * @return mixed A database resource
   *
   * @throws <b>sfDatabaseException</b> If a resource could not be retrieved
   */
  public function getResource()
  {
    if (is_null($this->resource))
    {
      $this->connect();
    }

    return $this->resource;
  }

  /**
   * Gets the parameter holder for this object.
   *
   * @return sfParameterHolder A sfParameterHolder instance
   */
  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  /**
   * Gets the parameter associated with the given key.
   *
   * This is a shortcut for:
   *
   * <code>$this->getParameterHolder()->get()</code>
   *
   * @param string $name    The key name
   * @param string $default The default value
   *
   * @return string The value associated with the key
   *
   * @see sfParameterHolder
   */
  public function getParameter($name, $default = null)
  {
    return $this->parameterHolder->get($name, $default);
  }

  /**
   * Returns true if the given key exists in the parameter holder.
   *
   * This is a shortcut for:
   *
   * <code>$this->getParameterHolder()->has()</code>
   *
   * @param string $name The key name
   *
   * @return boolean true if the given key exists, false otherwise
   *
   * @see sfParameterHolder
   */
  public function hasParameter($name)
  {
    return $this->parameterHolder->has($name);
  }

  /**
   * Sets the value for the given key.
   *
   * This is a shortcut for:
   *
   * <code>$this->getParameterHolder()->set()</code>
   *
   * @param string $name  The key name
   * @param string $value The value
   *
   * @see sfParameterHolder
   */
  public function setParameter($name, $value)
  {
    $this->parameterHolder->set($name, $value);
  }

  /**
   * Executes the shutdown procedure.
   *
   * @return void
   *
   * @throws <b>sfDatabaseException</b> If an error occurs while shutting down this database
   */
  abstract function shutdown();
}
