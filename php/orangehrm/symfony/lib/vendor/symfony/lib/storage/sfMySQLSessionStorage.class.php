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
 * Provides support for session storage using a MySQL brand database.
 *
 * <b>parameters:</b> see sfDatabaseSessionStorage
 *
 * @package    symfony
 * @subpackage storage
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @author     Julien Garand <julien.garand@gmail.com>
 * @version    SVN: $Id: sfMySQLSessionStorage.class.php 12061 2008-10-07 20:18:39Z fabien $
 */
class sfMySQLSessionStorage extends sfDatabaseSessionStorage
{
  /**
   * Destroys a session.
   *
   * @param  string $id  A session ID
   *
   * @return bool true, if the session was destroyed, otherwise an exception is thrown
   *
   * @throws <b>sfDatabaseException</b> If the session cannot be destroyed.
   */
  public function sessionDestroy($id)
  {
    // get table/column
    $db_table  = $this->options['db_table'];
    $db_id_col = $this->options['db_id_col'];

    // cleanup the session id, just in case
    $id = $this->db_escape($id);

    // delete the record associated with this id
    $sql = "DELETE FROM $db_table WHERE $db_id_col = '$id'";

    if ($this->db_query($sql))
    {
      return true;
    }

    // failed to destroy session
    throw new sfDatabaseException(sprintf('%s cannot destroy session id "%s" (%s).', get_class($this), $id, mysql_error()));
  }

  /**
   * Cleans up old sessions.
   *
   * @param  int $lifetime  The lifetime of a session
   *
   * @return bool true, if old sessions have been cleaned, otherwise an exception is thrown
   *
   * @throws <b>sfDatabaseException</b> If any old sessions cannot be cleaned
   */
  public function sessionGC($lifetime)
  {
    // get table/column
    $db_table    = $this->options['db_table'];
    $db_time_col = $this->options['db_time_col'];

    // delete the record older than the authorised session life time 
    $lifetime = $this->db_escape($lifetime); // We never know...
    $sql = "DELETE FROM $db_table WHERE $db_time_col + $lifetime < UNIX_TIMESTAMP()";

    if (!$this->db_query($sql))
    {
      throw new sfDatabaseException(sprintf('%s cannot delete old sessions (%s).', get_class($this), mysql_error()));
    }

    return true;
  }

  /**
   * Reads a session.
   *
   * @param  string $id  A session ID
   *
   * @return string      The session data if the session was read or created, otherwise an exception is thrown
   *
   * @throws <b>sfDatabaseException</b> If the session cannot be read
   */
  public function sessionRead($id)
  {
    // get table/column
    $db_table    = $this->options['db_table'];
    $db_data_col = $this->options['db_data_col'];
    $db_id_col   = $this->options['db_id_col'];
    $db_time_col = $this->options['db_time_col'];

    // cleanup the session id, just in case
    $id = $this->db_escape($id);

    // get the record associated with this id
    $sql = "SELECT $db_data_col FROM $db_table WHERE $db_id_col = '$id'";

    $result = $this->db_query($sql);

    if ($result != false && $this->db_num_rows($result) == 1)
    {
      // found the session
      $data = $this->db_fetch_row($result);

      return $data[0];
    }
    else
    {
      // session does not exist, create it
      $sql = "INSERT INTO $db_table ($db_id_col, $db_data_col, $db_time_col) VALUES ('$id', '', UNIX_TIMESTAMP())";
      if ($this->db_query($sql))
      {
        return '';
      }

      // can't create record
      throw new sfDatabaseException(sprintf('%s cannot create new record for id "%s" (%s).', get_class($this), $id, mysql_error()));
    }
  }

  /**
   * Writes session data.
   *
   * @param  string $id    A session ID
   * @param  string $data  A serialized chunk of session data
   *
   * @return bool true, if the session was written, otherwise an exception is thrown
   *
   * @throws <b>sfDatabaseException</b> If the session data cannot be written
   */
  public function sessionWrite($id, $data)
  {
    // get table/column
    $db_table    = $this->options['db_table'];
    $db_data_col = $this->options['db_data_col'];
    $db_id_col   = $this->options['db_id_col'];
    $db_time_col = $this->options['db_time_col'];

    // cleanup the session id and data, just in case
    $id   = $this->db_escape($id);
    $data = $this->db_escape($data);

    // update the record associated with this id
    $sql = "UPDATE $db_table SET $db_data_col='$data', $db_time_col=UNIX_TIMESTAMP() WHERE $db_id_col='$id'";

    if ($this->db_query($sql))
    {
      return true;
    }

    // failed to write session data
    throw new sfDatabaseException(sprintf('%s cannot write session data for id "%s" (%s).', get_class($this), $id, mysql_error()));
  }

  /**
   * Executes an SQL Query
   *
   * @param  string $query  The query to execute
   * @return mixed The result of the query
   */
  protected function db_query($query)
  {
    return @mysql_query($query, $this->db);
  }

  /**
   * Escapes a string before using it in a query statement
   *
   * @param  string $string  The string to escape
   * @return string The escaped string
   */
  protected function db_escape($string)
  {
    return mysql_real_escape_string($string, $this->db);
  }

  /**
   * Counts the rows in a query result
   *
   * @param  resource $result  Result of a query
   * @return int Number of rows
   */
  protected function db_num_rows($result)
  {
    return mysql_num_rows($result);
  }

  /**
   * Extracts a row from a query result set
   *
   * @param  resource $result  Result of a query
   * @return array Extracted row as an indexed array
   */
  protected function db_fetch_row($result)
  {
    return mysql_fetch_row($result);
  }
}
