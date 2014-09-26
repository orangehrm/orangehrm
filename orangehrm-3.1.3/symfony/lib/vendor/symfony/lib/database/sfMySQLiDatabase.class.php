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
 * sfMySQLiDatabase provides connectivity for the MySQL brand database.
 * @see sfMySQLDatabase
 */
class sfMySQLiDatabase extends sfMySQLDatabase
{

  /**
   * Returns the appropriate connect method.
   *
   * @param bool $persistent Whether persistent connections are use or not
   *                         The MySQLi driver does not support persistent
   *                         connections so this argument is ignored.
   *
   * @return string name of connect method
   */
  protected function getConnectMethod($persistent)
  {
    return 'mysqli_connect';
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
   return ($database != null && !@mysqli_select_db($this->connection, $database));
  }

  /**
   * Execute the shutdown procedure
   *
   * @throws <b>sfDatabaseException</b> If an error occurs while shutting down this database
   */
  public function shutdown()
  {
    if ($this->connection != null)
    {
      @mysqli_close($this->connection);
    }
  }
}
