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
 * sfPDODatabase provides connectivity for the PDO database abstraction layer.
 *
 * @package    symfony
 * @subpackage database
 * @author     Daniel Swarbrick (daniel@pressure.net.nz)
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @author     Dustin Whittle <dustin.whittle@symfony-project.com>
 * @version    SVN: $Id: sfPDODatabase.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfPDODatabase extends sfDatabase
{
  /**
   * Connects to the database.
   *
   * @throws <b>sfDatabaseException</b> If a connection could not be created
   */
  public function connect()
  {
    if (!$dsn = $this->getParameter('dsn'))
    {
      // missing required dsn parameter
      throw new sfDatabaseException('Database configuration is missing the "dsn" parameter.');
    }

    try
    {
      $pdo_class  = $this->getParameter('class', 'PDO');
      $username   = $this->getParameter('username');
      $password   = $this->getParameter('password');
      $persistent = $this->getParameter('persistent');

      $options = ($persistent) ? array(PDO::ATTR_PERSISTENT => true) : array();

      $this->connection = new $pdo_class($dsn, $username, $password, $options);

    }
    catch (PDOException $e)
    {
      throw new sfDatabaseException($e->getMessage());
    }

    // lets generate exceptions instead of silent failures
    if (sfConfig::get('sf_debug'))
    {
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    else
    {
      $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    // compatability
    $compatability = $this->getParameter('compat');
    if ($compatability)
    {
      $this->connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);
    }

    // nulls
    $nulls = $this->getParameter('nulls');
    if ($nulls)
    {
      $this->connection->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_EMPTY_STRING);
    }

    // auto commit
    $autocommit = $this->getParameter('autocommit');
    if ($autocommit)
    {
      $this->connection->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }

    $this->resource = $this->connection;

  }

  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
    if ($this->connection !== null)
    {
      @$this->connection = null;
    }
  }

  /**
   * Magic method for calling PDO directly via sfPDODatabase
   *
   * @param string $method
   * @param array $arguments
   * @return mixed
   */
  public function __call($method, $arguments)
  {
    return $this->getConnection()->$method($arguments);
  }
}
