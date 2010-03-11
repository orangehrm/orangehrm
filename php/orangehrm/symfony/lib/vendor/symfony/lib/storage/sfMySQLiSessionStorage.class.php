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
 * Provides support for session storage using a MySQL brand database 
 * using the MySQL improved API.
 *
 * <b>parameters:</b> see sfDatabaseSessionStorage
 *
 * @package    symfony
 * @subpackage storage
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @author     Julien Garand <julien.garand@gmail.com>
 * @version    SVN: $Id: sfMySQLiSessionStorage.class.php 15545 2009-02-16 21:47:19Z dwhittle $
 */
class sfMySQLiSessionStorage extends sfMySQLSessionStorage
{
  /**
   * Execute an SQL Query
   *
   * @param  string $query  The query to execute
   * @return mixed The result of the query
   */
  protected function db_query($query)
  {
    return mysqli_query($this->db, $query);
  }

  /**
   * Escape a string before using it in a query statement
   *
   * @param  string $string  The string to escape
   * @return string The escaped string
   */
  protected function db_escape($string)
  {
    return mysqli_real_escape_string($this->db, $string);
  }

  /**
   * Count the rows in a query result
   *
   * @param  resource $result  Result of a query
   * @return int Number of rows
   */
  protected function db_num_rows($result)
  {
    return $result->num_rows;
  }

  /**
   * Extract a row from a query result set
   *
   * @param  resource $result  Result of a query
   * @return array Extracted row as an indexed array
   */
  protected function db_fetch_row($result)
  {
    return $result->fetch_row();
  }
}
