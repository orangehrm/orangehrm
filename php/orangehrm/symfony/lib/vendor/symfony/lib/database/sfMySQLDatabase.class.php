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
 * sfMySQLDatabase provides connectivity for the MySQL brand database.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>database</b>   - [none]      - The database name.
 * # <b>host</b>       - [localhost] - The database host.
 * # <b>method</b>     - [normal]    - How to read connection parameters.
 *                                     Possible values are normal, server, and
 *                                     env. The normal method reads them from
 *                                     the specified values. server reads them
 *                                     from $_SERVER where the keys to retrieve
 *                                     the values are what you specify the value
 *                                     as in the settings. env reads them from
 *                                     $_ENV and works like $_SERVER.
 * # <b>password</b>   - [none]      - The database password.
 * # <b>persistent</b> - [No]        - Indicates that the connection should be
 *                                     persistent.
 * # <b>username</b>       - [none]  - The database username.
 *
 * @package    symfony
 * @subpackage database
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfMySQLDatabase.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfMySQLDatabase extends sfDatabase
{
  /**
   * Connects to the database.
   *
   * @throws <b>sfDatabaseException</b> If a connection could not be created
   */
  public function connect()
  {
    // determine how to get our
    $method = $this->getParameter('method', 'normal');

    switch ($method)
    {
      case 'normal':
        // get parameters normally
        $database = $this->getParameter('database');
        $host     = $this->getParameter('host', 'localhost');
        $password = $this->getParameter('password');
        $username = $this->getParameter('username');
        $encoding = $this->getParameter('encoding');

        break;

      case 'server':
        // construct a connection string from existing $_SERVER values
        // and extract them to local scope
        $parameters =& $this->loadParameters($_SERVER);
        extract($parameters);

        break;

      case 'env':
        // construct a connection string from existing $_ENV values
        // and extract them to local scope
        $string =& $this->loadParameters($_ENV);
        extract($parameters);

        break;

      default:
        // who knows what the user wants...
        throw new sfDatabaseException(sprintf('Invalid MySQLDatabase parameter retrieval method "%s".', $method));
    }
    
    // let's see if we need a persistent connection
    $connect = $this->getConnectMethod($this->getParameter('persistent', false));
    if ($password == null)
    {
      if ($username == null)
      {
        $this->connection = @$connect($host);
      }
      else
      {
        $this->connection = @$connect($host, $username);
      }
    }
    else
    {
      $this->connection = @$connect($host, $username, $password);
    }

    // make sure the connection went through
    if ($this->connection === false)
    {
      // the connection's foobar'd
      throw new sfDatabaseException('Failed to create a MySQLDatabase connection.');
    }

    // select our database
    if ($this->selectDatabase($database))
    {
      // can't select the database
      throw new sfDatabaseException(sprintf('Failed to select MySQLDatabase "%s".', $database));
    }

    // set encoding if specified
    if ($encoding)
    {
      @mysql_query("SET NAMES '".$encoding."'", $this->connection);
    }

    // since we're not an abstraction layer, we copy the connection
    // to the resource
    $this->resource = $this->connection;
  }

  /**
   * Returns the appropriate connect method.
   * 
   * @param bool $persistent wether persistent connections are use or not
   * @return string name of connect method.
   */
  protected function getConnectMethod($persistent)
  {
    return $persistent ? 'mysql_pconnect' : 'mysql_connect';
  }
  
  /**
   * Selects the database to be used in this connection
   * 
   * @param string $database Name of database to be connected
   *
   * @return bool true if this was successful
   */
  protected function selectDatabase($database)
  {
   return ($database != null && !@mysql_select_db($database, $this->connection));
  }
  
  /**
   * Loads connection parameters from an existing array.
   *
   * @return array An associative array of connection parameters
   */
  protected function & loadParameters(&$array)
  {
    // list of available parameters
    $available = array('database', 'host', 'password', 'user');

    $parameters = array();

    foreach ($available as $parameter)
    {
      $$parameter = $this->getParameter($parameter);

      $parameters[$parameter] = ($$parameter != null) ? $array[$$parameter] : null;
    }

    return $parameters;
  }

  /**
   * Execute the shutdown procedure
   *
   * @return void
   *
   * @throws <b>sfDatabaseException</b> If an error occurs while shutting down this database
   */
  public function shutdown()
  {
    if ($this->connection != null)
    {
      @mysql_close($this->connection);
    }
  }
}
