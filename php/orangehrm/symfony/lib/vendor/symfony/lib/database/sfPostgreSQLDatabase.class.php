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
 * sfPostgreSQLDatabase provides connectivity for the PostgreSQL brand database.
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
 * # <b>port</b>       - [none]      - TCP/IP port on which PostgreSQL is
 *                                     listening.
 * # <b>username</b>       - [none]  - The database username.
 *
 * @package    symfony
 * @subpackage database
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfPostgreSQLDatabase.class.php 7792 2008-03-09 22:06:59Z fabien $
 */
class sfPostgreSQLDatabase extends sfDatabase
{
  /**
   * Connects to the database.
   *
   * @throws <b>sfDatabaseException</b> If a connection could not be created
   */
  public function connect()
  {
    // determine how to get our parameters
    $method = $this->getParameter('method', 'normal');

    // get parameters
    switch ($method)
    {
      case 'normal':
        // get parameters normally
        $database = $this->getParameter('database');
        $host     = $this->getParameter('host');
        $password = $this->getParameter('password');
        $port     = $this->getParameter('port');
        $username = $this->getParameter('username');

        // construct connection string
        $string = ($database != null ? (' dbname='   .$database) : '').
                  ($host != null     ? (' host='     .$host)     : '').
                  ($password != null ? (' password=' .$password) : '').
                  ($port != null     ? (' port='     .$port)     : '').
                  ($username != null ? (' user='     .$username) : '');

        break;

      case 'server':
        // construct a connection string from existing $_SERVER values
        $string = $this->loadParameters($_SERVER);

        break;

      case 'env':
        // construct a connection string from existing $_ENV values
        $string = $this->loadParameters($_ENV);

        break;

      default:
        // who knows what the user wants...
        throw new sfDatabaseException(sprintf('Invalid PostgreSQLDatabase parameter retrieval method "%s".', $method));
    }

    // let's see if we need a persistent connection
    $persistent = $this->getParameter('persistent', false);
    $connect    = $persistent ? 'pg_pconnect' : 'pg_connect';

    $this->connection = @$connect($string);

    // make sure the connection went through
    if ($this->connection === false)
    {
      // the connection's foobar'd
      throw new sfDatabaseException('Failed to create a PostgreSQLDatabase connection.');
    }

    // since we're not an abstraction layer, we copy the connection
    // to the resource
    $this->resource = $this->connection;
  }

  /**
   * Loads connection parameters from an existing array.
   *
   * @return string A connection string
   */
  protected function loadParameters(&$array)
  {
    $database = $this->getParameter('database');
    $host     = $this->getParameter('host');
    $password = $this->getParameter('password');
    $port     = $this->getParameter('port');
    $username = $this->getParameter('username');

    // construct connection string
    $string = ($database != null ? (' dbname='  .$array[$database]) : '').
              ($host != null     ? (' host='    .$array[$host])     : '').
              ($password != null ? (' password='.$array[$password]) : '').
              ($port != null     ? (' port='    .$array[$port])     : '').
              ($username != null ? (' user='    .$array[$username]) : '');

    return $string;
  }

  /**
   * Executes the shutdown procedure.
   *
   * @throws <b>sfDatabaseException</b> If an error occurs while shutting down this database
   */
  public function shutdown()
  {
    if ($this->connection != null)
    {
      @pg_close($this->connection);
    }
  }
}
