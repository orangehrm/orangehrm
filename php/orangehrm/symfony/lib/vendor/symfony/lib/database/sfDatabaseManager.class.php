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
 * sfDatabaseManager allows you to setup your database connectivity before the
 * request is handled. This eliminates the need for a filter to manage database
 * connections.
 *
 * @package    symfony
 * @subpackage database
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfDatabaseManager.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfDatabaseManager
{
  protected
    $configuration = null,
    $databases     = array();

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct(sfProjectConfiguration $configuration, $options = array())
  {
    $this->initialize($configuration);

    if (!isset($options['auto_shutdown']) || $options['auto_shutdown'])
    {
      register_shutdown_function(array($this, 'shutdown'));
    }
  }

  /**
   * Initializes this sfDatabaseManager object
   *
   * @param sfProjectConfiguration $configuration A sfProjectConfiguration instance
   *
   * @return bool true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this sfDatabaseManager object
   */
  public function initialize(sfProjectConfiguration $configuration)
  {
    $this->configuration = $configuration;

    $this->loadConfiguration();
  }

  /**
   * Loads database configuration.
   */
  public function loadConfiguration()
  {
    if ($this->configuration instanceof sfApplicationConfiguration)
    {
      $databases = include($this->configuration->getConfigCache()->checkConfig('config/databases.yml'));
    }
    else
    {
      $configHandler = new sfDatabaseConfigHandler();
      $databases = $configHandler->evaluate(array($this->configuration->getRootDir().'/config/databases.yml'));
    }

    foreach ($databases as $name => $database)
    {
      $this->setDatabase($name, $database);
    }
  }

  /**
   * Sets a database connection.
   *
   * @param string     $name     The database name
   * @param sfDatabase $database A sfDatabase instance
   */
  public function setDatabase($name, sfDatabase $database)
  {
    $this->databases[$name] = $database;
  }

  /**
   * Retrieves the database connection associated with this sfDatabase implementation.
   *
   * @param string $name A database name
   *
   * @return mixed A Database instance
   *
   * @throws <b>sfDatabaseException</b> If the requested database name does not exist
   */
  public function getDatabase($name = 'default')
  {
    if (isset($this->databases[$name]))
    {
      return $this->databases[$name];
    }

    // nonexistent database name
    throw new sfDatabaseException(sprintf('Database "%s" does not exist.', $name));
  }

  /**
   * Returns the names of all database connections.
   *
   * @return array An array containing all database connection names
   */
  public function getNames()
  {
    return array_keys($this->databases);
  }

  /**
   * Executes the shutdown procedure
   *
   * @return void
   *
   * @throws <b>sfDatabaseException</b> If an error occurs while shutting down this DatabaseManager
   */
  public function shutdown()
  {
    // loop through databases and shutdown connections
    foreach ($this->databases as $database)
    {
      $database->shutdown();
    }
  }
}
