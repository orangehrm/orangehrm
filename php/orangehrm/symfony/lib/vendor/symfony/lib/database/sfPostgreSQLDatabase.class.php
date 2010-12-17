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
 * # <b>username</b>   - [none]      - The database username.
 * # <b>password</b>   - [none]      - The database password.
 * # <b>persistent</b> - [No]        - Indicates that the connection should be persistent.
 * # <b>port</b>       - [none]      - TCP/IP port on which PostgreSQL is listening.
 *
 * @package    symfony
 * @subpackage database
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfPostgreSQLDatabase.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
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
