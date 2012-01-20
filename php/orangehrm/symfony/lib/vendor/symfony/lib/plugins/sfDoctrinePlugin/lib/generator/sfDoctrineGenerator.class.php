<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Doctrine generator.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDoctrineGenerator.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfDoctrineGenerator extends sfModelGenerator
{
  protected
    $table = null;

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('sfDoctrineModule');
  }

  /** 
   * Configures this generator.
   */
  public function configure()
  {
    $this->table = Doctrine_Core::getTable($this->modelClass);

    // load all primary keys
    $this->loadPrimaryKeys();
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
    $relations = array();
    foreach ($this->table->getRelations() as $relation)
    {
      if ($relation->getType() === Doctrine_Relation::MANY && isset($relation['refTable']))
      {
        $relations[] = $relation;
      }
    }
    return $relations;
  }

  /**
   * Loads primary keys.
   *
   * @throws sfException
   */
  protected function loadPrimaryKeys()
  {
    $this->primaryKey = array();
    foreach ($this->getColumns() as $name => $column)
    {
      if ($column->isPrimaryKey())
      {
        $this->primaryKey[] = $name;
      }
    }

    if (!count($this->primaryKey))
    {
      throw new sfException(sprintf('Cannot generate a module for a model without a primary key (%s)', $this->modelClass));
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
    $getter = 'get'.sfInflector::camelize($column);
    if ($developed)
    {
      $getter = sprintf('$%s%s->%s()', $prefix, $this->getSingularName(), $getter);
    }

    return $getter;
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

    switch ($column->getDoctrineType())
    {
      case 'enum':
        return 'Enum';
      case 'boolean':
        return 'Boolean';
      case 'date':
      case 'timestamp':
        return 'Date';
      case 'time':
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
    foreach ($this->getColumns() as $name => $column)
    {
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
      $name = sfInflector::underscore($tables['alias']).'_list';
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
    foreach ($this->getColumns() as $name => $column)
    {
      $names[] = $name;
      $fields[$name] = isset($this->config[$context]['fields'][$name]) ? $this->config[$context]['fields'][$name] : array();
    }

    foreach ($this->getManyToManyTables() as $tables)
    {
      $name = sfInflector::underscore($tables['alias']).'_list';
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
    foreach ($this->getColumns() as $name => $column)
    {
      $names[] = $name;
    }

    if ($withM2M)
    {
      foreach ($this->getManyToManyTables() as $tables)
      {
        $names[] = sfInflector::underscore($tables['alias']).'_list';
      }
    }

    return $names;
  }

  /**
   * Get array of sfDoctrineAdminColumn objects
   *
   * @return array $columns
   */
  public function getColumns()
  {
    foreach (array_keys($this->table->getColumns()) as $name)
    {
      $name = $this->table->getFieldName($name);
      $columns[$name] = new sfDoctrineColumn($name, $this->table);
    }

    return $columns;
  }
}