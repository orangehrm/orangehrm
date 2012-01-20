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
 * Provides support for session storage using a PostgreSQL brand database.
 *
 * <b>parameters:</b> see sfDatabaseSessionStorage
 *
 * @package    symfony
 * @subpackage storage
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfPostgreSQLSessionStorage.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfPostgreSQLSessionStorage extends sfDatabaseSessionStorage
{
  /**
   * Destroys a session.
   *
   * @param  string $id  A session ID
   *
   * @return bool true, if the session was destroyed, otherwise an exception is thrown
   *
   * @throws <b>sfDatabaseException</b> If the session cannot be destroyed
   */
  public function sessionDestroy($id)
  {
    // get table/column
    $db_table  = $this->options['db_table'];
    $db_id_col = $this->options['db_id_col'];

    // cleanup the session id, just in case
    $id = addslashes($id);

    // delete the record associated with this id
    $sql = 'DELETE FROM '.$db_table.' WHERE '.$db_id_col.' = \''.$id.'\'';

    if (@pg_query($this->db, $sql))
    {
      return true;
    }

    // failed to destroy session
    throw new sfDatabaseException(sprintf('sfPostgreSQLSessionStorage cannot destroy session id "%s".', $id));
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

    // delete the record associated with this id
    $sql = 'DELETE FROM '.$db_table.' WHERE '.$db_time_col.' < '.(time() - $lifetime);

    if (!@pg_query($this->db, $sql))
    {
      throw new sfDatabaseException('sfPostgreSQLSessionStorage cannot delete old sessions.');
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
    $id = addslashes($id);

    // delete the record associated with this id
    $sql = 'SELECT '.$db_data_col.' FROM '.$db_table.' WHERE '.$db_id_col.' = \''.$id.'\'';

    $result = @pg_query($this->db, $sql);

    if ($result != false && @pg_num_rows($result) == 1)
    {
      // found the session
      $data = pg_fetch_row($result);

      return $data[0];
    }
    else
    {
      // session does not exist, create it
      $sql = 'INSERT INTO '.$db_table.' ('.$db_id_col.', '.$db_data_col.', '.$db_time_col.') VALUES (\''.$id.'\', \'\', '.time().')';

      if (@pg_query($this->db, $sql))
      {
        return '';
      }

      // can't create record
      throw new sfDatabaseException(sprintf('sfPostgreSQLSessionStorage cannot create new record for id "%s".', $id));
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
    $id   = addslashes($id);
    $data = addslashes($data);

    // delete the record associated with this id
    $sql = 'UPDATE '.$db_table.' SET '.$db_data_col.' = \''.$data.'\', '.$db_time_col.' = '.time().' WHERE '.$db_id_col.' = \''.$id.'\'';

    if (@pg_query($this->db, $sql))
    {
      return true;
    }

    // failed to write session data
    throw new sfDatabaseException(sprintf('sfPostgreSQLSessionStorage cannot write session data for id "%s".', $id));
  }
}
