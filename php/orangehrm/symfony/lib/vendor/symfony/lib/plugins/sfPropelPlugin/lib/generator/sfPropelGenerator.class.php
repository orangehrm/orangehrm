<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Propel generator.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelGenerator.class.php 22943 2009-10-12 12:04:19Z Kris.Wallsmith $
 */
class sfPropelGenerator extends sfModelGenerator
{
  protected
    $tableMap = null,
    $dbMap    = null;

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('sfPropelModule');
  }

  /** 
   * Configures this generator.
   */
  public function configure()
  {
    // get some model metadata
    $this->loadMapBuilderClasses();

    // load all primary keys
    $this->loadPrimaryKeys();
  }

  /**
   * Gets the table map for the current model class.
   *
   * @return TableMap A TableMap instance
   */
  public function getTableMap()
  {
    return $this->tableMap;
  }

  /**
   * Returns an array of tables that represents a many to many relationship.
   *
   * A table is considered to be a m2m table if it has 2 foreign keys that are also primary keys.
   *
   * @return array An array of tables.
   */
  public function getManyToManyTables()
  {
    $tables = array();

    // go through all tables to find m2m relationships
    foreach ($this->dbMap->getTables() as $tableName => $table)
    {
      // load this table's relations and related tables
      $table->getRelations();

      foreach ($table->getColumns() as $column)
      {
        if ($column->isForeignKey() && $column->isPrimaryKey() && $this->getTableMap()->getClassname() == $this->dbMap->getTable($column->getRelatedTableName())->getClassname())
        {
          // we have a m2m relationship
          // find the other primary key
          foreach ($table->getColumns() as $relatedColumn)
          {
            if ($relatedColumn->isForeignKey() && $relatedColumn->isPrimaryKey() && $this->getTableMap()->getClassname() != $this->dbMap->getTable($relatedColumn->getRelatedTableName())->getClassname())
            {
              // we have the related table
              $tables[] = array(
                'middleTable'   => $table,
                'relatedTable'  => $this->dbMap->getTable($relatedColumn->getRelatedTableName()),
                'column'        => $column,
                'relatedColumn' => $relatedColumn,
              );

              break 2;
            }
          }
        }
      }
    }

    return $tables;
  }

  /**
   * Loads primary keys.
   *
   * @throws sfException
   */
  protected function loadPrimaryKeys()
  {
    $this->primaryKey = array();
    foreach ($this->tableMap->getColumns() as $column)
    {
      if ($column->isPrimaryKey())
      {
        $this->primaryKey[] = $column->getPhpName();
      }
    }

    if (!count($this->primaryKey))
    {
      throw new sfException(sprintf('Cannot generate a module for a model without a primary key (%s)', $this->modelClass));
    }
  }

  /**
   * Loads map builder classes.
   *
   * @throws sfException
   */
  protected function loadMapBuilderClasses()
  {
    $this->dbMap = Propel::getDatabaseMap();
    $this->tableMap = call_user_func(array($this->modelClass . 'Peer', 'getTableMap'));
    // load all related table maps, 
    // and all tables related to the related table maps (for m2m relations)
    foreach ($this->tableMap->getRelations() as $relation)
    {
      $relation->getForeignTable()->getRelations();
    }
  }

  /**
   * Returns the getter either non-developped: 'getFoo' or developped: '$class->getFoo()'.
   *
   * @param string  $column     The column name
   * @param boolean $developed  true if you want developped method names, false otherwise
   * @param string  $prefix     The prefix value
   *
   * @return string PHP code
   */
  public function getColumnGetter($column, $developed = false, $prefix = '')
  {
    try
    {
      $getter = 'get'.call_user_func(array(constant($this->getModelClass().'::PEER'), 'translateFieldName'), $column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME);
    }
    catch (PropelException $e)
    {
      // not a real column
      $getter = 'get'.sfInflector::camelize($column);
    }

    if (!$developed)
    {
      return $getter;
    }

    return sprintf('$%s%s->%s()', $prefix, $this->getSingularName(), $getter);
  }

  /**
   * Returns the type of a column.
   *
   * @param  object $column A column object
   *
   * @return string The column type
   */
  public function getType($column)
  {
    if ($column->isForeignKey())
    {
      return 'ForeignKey';
    }

    switch ($column->getType())
    {
      case PropelColumnTypes::BOOLEAN:
        return 'Boolean';
      case PropelColumnTypes::DATE:
      case PropelColumnTypes::TIMESTAMP:
        return 'Date';
      case PropelColumnTypes::TIME:
        return 'Time';
      default:
        return 'Text';
    }
  }

  /**
   * Returns the default configuration for fields.
   *
   * @return array An array of default configuration for all fields
   */
  public function getDefaultFieldsConfiguration()
  {
    $fields = array();

    $names = array();
    foreach ($this->getTableMap()->getColumns() as $column)
    {
      $name = $this->translateColumnName($column);
      $names[] = $name;
      $fields[$name] = array_merge(array(
        'is_link'      => (Boolean) $column->isPrimaryKey(),
        'is_real'      => true,
        'is_partial'   => false,
        'is_component' => false,
        'type'         => $this->getType($column),
      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
    }

    foreach ($this->getManyToManyTables() as $tables)
    {
      $name = sfInflector::underscore($tables['middleTable']->getClassname()).'_list';
      $names[] = $name;
      $fields[$name] = array_merge(array(
        'is_link'      => false,
        'is_real'      => false,
        'is_partial'   => false,
        'is_component' => false,
        'type'         => 'Text',
      ), isset($this->config['fields'][$name]) ? $this->config['fields'][$name] : array());
    }

    if (isset($this->config['fields']))
    {
      foreach ($this->config['fields'] as $name => $params)
      {
        if (in_array($name, $names))
        {
          continue;
        }

        $fields[$name] = array_merge(array(
          'is_link'      => false,
          'is_real'      => false,
          'is_partial'   => false,
          'is_component' => false,
          'type'         => 'Text',
        ), is_array($params) ? $params : array());
      }
    }

    unset($this->config['fields']);

    return $fields;
  }

  /**
   * Returns the configuration for fields in a given context.
   *
   * @param  string $context The Context
   *
   * @return array An array of configuration for all the fields in a given context 
   */
  public function getFieldsConfiguration($context)
  {
    $fields = array();

    $names = array();
    foreach ($this->getTableMap()->getColumns() as $column)
    {
      $name = $this->translateColumnName($column);
      $names[] = $name;
      $fields[$name] = isset($this->config[$context]['fields'][$name]) ? $this->config[$context]['fields'][$name] : array();
    }

    foreach ($this->getManyToManyTables() as $tables)
    {
      $name = sfInflector::underscore($tables['middleTable']->getClassname()).'_list';
      $names[] = $name;
      $fields[$name] = isset($this->config[$context]['fields'][$name]) ? $this->config[$context]['fields'][$name] : array();
    }

    if (isset($this->config[$context]['fields']))
    {
      foreach ($this->config[$context]['fields'] as $name => $params)
      {
        if (in_array($name, $names))
        {
          continue;
        }

        $fields[$name] = is_array($params) ? $params : array();
      }
    }

    unset($this->config[$context]['fields']);

    return $fields;
  }

  /**
   * Gets all the fields for the current model.
   *
   * @param  Boolean $withM2M Whether to include m2m fields or not
   *
   * @return array   An array of field names
   */
  public function getAllFieldNames($withM2M = true)
  {
    $names = array();
    foreach ($this->getTableMap()->getColumns() as $column)
    {
      $names[] = $this->translateColumnName($column);
    }

    if ($withM2M)
    {
      foreach ($this->getManyToManyTables() as $tables)
      {
        $names[] = sfInflector::underscore($tables['middleTable']->getClassname()).'_list';
      }
    }

    return $names;
  }

  public function translateColumnName($column, $related = false, $to = BasePeer::TYPE_FIELDNAME)
  {
    $peer = $related ? constant($column->getTable()->getDatabaseMap()->getTable($column->getRelatedTableName())->getPhpName().'::PEER') : constant($column->getTable()->getPhpName().'::PEER');
    $field = $related ? $column->getRelatedName() : $column->getFullyQualifiedName();

    return call_user_func(array($peer, 'translateFieldName'), $field, BasePeer::TYPE_COLNAME, $to);
  }
}
