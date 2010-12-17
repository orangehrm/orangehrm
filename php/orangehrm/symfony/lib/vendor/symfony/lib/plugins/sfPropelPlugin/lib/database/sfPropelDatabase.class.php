<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A symfony database driver for Propel.
 *
 * @package    sfPropelPlugin
 * @subpackage database
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelDatabase.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfPropelDatabase extends sfPDODatabase
{
  /**
   * Returns the current propel configuration.
   *
   * @return array
   *
   * @deprecated Use Propel::getConfiguration() instead
   */
  static public function getConfiguration()
  {
    return array('propel' => Propel::getConfiguration(PropelConfiguration::TYPE_ARRAY));
  }

  /**
   * Configures a Propel datasource.
   *
   * @param array  $parameters The datasource parameters
   * @param string $name       The datasource name
   */
  public function initialize($parameters = null, $name = 'propel')
  {
    parent::initialize($parameters);

    if (!$this->hasParameter('datasource') && $this->hasParameter('name'))
    {
      $this->setParameter('datasource', $this->getParameter('name'));
    }
    elseif (!$this->hasParameter('datasource') && !empty($name))
    {
      $this->setParameter('datasource', $name);
    }

    $this->addConfig();

    // mark the first connection as the default
    if (!Propel::getConfiguration(PropelConfiguration::TYPE_OBJECT)->getParameter('datasources.default'))
    {
      $this->setDefaultConfig();
    }

    // for BC
    if ($this->getParameter('pooling', false))
    {
      Propel::enableInstancePooling();
    }
    else
    {
      Propel::disableInstancePooling();
    }
  }

  /**
   * Connect to the database.
   *
   * Stores the PDO connection in $connection.
   *
   * @return void
   */
  public function connect()
  {
    $this->connection = Propel::getConnection($this->getParameter('datasource'));
  }

  /**
   * Marks the current database as the default.
   */
  public function setDefaultConfig()
  {
    Propel::getConfiguration(PropelConfiguration::TYPE_OBJECT)->setParameter('datasources.default', $this->getParameter('datasource'));
  }

  /**
   * Adds configuration for current datasource.
   */
  public function addConfig()
  {
    if ($dsn = $this->getParameter('dsn'))
    {
      $params = $this->parseDsn($dsn);

      $options = array('dsn', 'phptype', 'hostspec', 'database', 'username', 'password', 'port', 'protocol', 'encoding', 'persistent', 'socket', 'compat_assoc_lower', 'compat_rtrim_string');
      foreach ($options as $option)
      {
        if (!$this->getParameter($option) && isset($params[$option]))
        {
          $this->setParameter($option, $params[$option]);
        }
      }
    }

    if ($this->hasParameter('persistent'))
    {
      // for BC
      $this->setParameter('options', array_merge(
        $this->getParameter('options', array()),
        array('ATTR_PERSISTENT' => $this->getParameter('persistent'))
      ));
    }

    $propelConfiguration = Propel::getConfiguration(PropelConfiguration::TYPE_OBJECT);

    if ($this->hasParameter('debug'))
    {
      $propelConfiguration->setParameter('debugpdo.logging', sfToolkit::arrayDeepMerge(
        $propelConfiguration->getParameter('debugpdo.logging', array()),
        $this->getParameter('debug')
      ));
    }

    $event = new sfEvent($propelConfiguration, 'propel.filter_connection_config', array('name' => $this->getParameter('datasource'), 'database' => $this));
    $event = sfProjectConfiguration::getActive()->getEventDispatcher()->filter($event, array(
      'adapter'    => $this->getParameter('phptype'),
      'connection' => array(
        'dsn'       => $this->getParameter('dsn'),
        'user'      => $this->getParameter('username'),
        'password'  => $this->getParameter('password'),
        'classname' => $this->getParameter('classname', 'PropelPDO'),
        'options'   => $this->getParameter('options', array()),
        'settings'  => array(
          'charset' => array('value' => $this->getParameter('encoding', sfConfig::get('sf_charset'))),
          'queries' => $this->getParameter('queries', array()),
        ),
      ),
    ));

    $propelConfiguration->setParameter('datasources.'.$this->getParameter('datasource'), $event->getReturnValue());
  }

  /**
   * Sets database configuration parameter
   *
   * @param string $key
   * @param mixed  $value
   */
  public function setConnectionParameter($key, $value)
  {
    if ('host' == $key)
    {
      $key = 'hostspec';
    }

    Propel::getConfiguration(PropelConfiguration::TYPE_OBJECT)->setParameter('datasources.'.$this->getParameter('datasource').'.connection.'.$key, $value);
    $this->setParameter($key, $value);
  }

  /**
   * Execute the shutdown procedure.
   *
   * @return void
   */
  public function shutdown()
  {
    if (null !== $this->connection)
    {
      @$this->connection = null;
    }
  }

  /**
   * Parses PDO style DSN.
   *
   * @param  string $dsn
   *
   * @return array the parsed dsn
   */
  protected function parseDsn($dsn)
  {
    return array('phptype' => substr($dsn, 0, strpos($dsn, ':')));
  }
}
