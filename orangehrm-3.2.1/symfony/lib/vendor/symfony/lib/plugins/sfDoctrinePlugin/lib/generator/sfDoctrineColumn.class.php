<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a Doctrine column
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineColumn.class.php 24604 2009-11-30 21:00:46Z Jonathan.Wage $
 */
class sfDoctrineColumn implements ArrayAccess
{
  /**
   * Array mapping Doctrine column types to the native symfony type
   */
  static $doctrineToSymfony = array(
    'boolean'   => 'BOOLEAN',
    'string'    => 'LONGVARCHAR',
    'integer'   => 'INTEGER',
    'date'      => 'DATE',
    'timestamp' => 'TIMESTAMP',
    'time'      => 'TIME',
    'enum'      => 'LONGVARCHAR',
    'float'     => 'FLOAT',
    'double'    => 'DOUBLE',
    'clob'      => 'CLOB',
    'blob'      => 'BLOB',
    'object'    => 'LONGVARCHAR',
    'array'     => 'LONGVARCHAR',
    'decimal'   => 'DECIMAL',
  );

  /**
   * Store the name of the related class for this column if it is
   * a foreign key
   *
   * @var string
   */
  protected $foreignClassName = null;

  /**
   * Doctrine_Table instance this column belongs to
   *
   * @var Doctrine_Table $table
   */
  protected $table = null;

  /**
   * Field name of the column
   *
   * @var string
   */
  protected $name = null;

  /**
   * Definition of the column
   *
   * @var array $definition
   */
  protected $definition = array();

  public function __construct($name, Doctrine_Table $table)
  {
    $this->name = $name;
    $this->table = $table;
    $this->definition = $table->getDefinitionOf($name);
  }

  /**
   * Get the name of the column
   *
   * @return string $name
   */
  public function getName()
  {
    return $this->table->getColumnName($this->name);
  }

  /**
   * Get the alias/field name
   *
   * @return string $fieldName
   */
  public function getFieldName()
  {
    return $this->table->getFieldName($this->getName());
  }

  /**
   * Get php name. Exists for backwards compatibility with propel orm
   *
   * @return string $fieldName
   */
  public function getPhpName()
  {
    return $this->getFieldName();
  }

  /**
   * Get the Doctrine type of the column
   *
   * @return void
   */
  public function getDoctrineType()
  {
    return isset($this->definition['type']) ? $this->definition['type']:null;
  }

  /**
   * Get symfony type of the column
   *
   * @return void
   */
  public function getType()
  {
    $doctrineType = $this->getDoctrineType();

    // we simulate the CHAR/VARCHAR types to generate input_tags
    if ('string' == $doctrineType && null !== $this->getSize() && $this->getSize() <= 255)
    {
      return 'VARCHAR';
    }

    return $doctrineType ? self::$doctrineToSymfony[$doctrineType] : 'VARCHAR';
  }

  /**
   * Get size/length of the column
   *
   * @return void
   */
  public function getSize()
  {
    return $this->definition['length'];
  }

  public function getLength()
  {
    return $this->getSize();
  }

  /**
   * Check if the column definition has a certain key
   *
   * @param string $key
   * @return bool
   */
  public function hasDefinitionKey($key)
  {
    return isset($this->definition[$key]) ? true:false;
  }

  /**
   * Get the value of a column definition key
   *
   * @param string $key
   * @return array $definition
   */
  public function getDefinitionKey($key)
  {
    if ($this->hasDefinitionKey($key))
    {
      return $this->definition[$key];
    } else {
      return false;
    }
  }

  /**
   * Returns a value from the current column's relation.
   * 
   * @param string $key
   * 
   * @return mixed|null
   */
  public function getRelationKey($key)
  {
    foreach ($this->table->getRelations() as $relation)
    {
      $local = (array) $relation['local'];
      $local = array_map('strtolower', $local);
      if (in_array(strtolower($this->name), $local))
      {
        return $relation[$key];
      }
    }
  }

  /**
   * Returns true of the column is not null and false if it is null
   *
   * @return boolean
   */
  public function isNotNull()
  {
    if (isset($this->definition['notnull']))
    {
      return $this->definition['notnull'];
    }
    if (isset($this->definition['notblank']))
    {
      return $this->definition['notblank'];
    }
    return false;
  }

  /**
   * Returns true if the column is a primary key and false if it is not
   *
   * @return void
   */
  public function isPrimaryKey()
  {
    if (isset($this->definition['primary']))
    {
      return $this->definition['primary'];
    }
    return false;
  }

  /**
   * Returns true if this column is a foreign key and false if it is not
   *
   * @return boolean $isForeignKey
   */
  public function isForeignKey()
  {
    if (isset($this->foreignClassName))
    {
      return true;
    }

    if ($this->isPrimaryKey())
    {
      return false;
    }

    foreach ($this->table->getRelations() as $relation)
    {
      $local = (array) $relation['local'];
      $local = array_map('strtolower', $local);
      if (in_array(strtolower($this->name), $local))
      {
        $this->foreignClassName = $relation['class'];
        return true;
      }
    }
    return false;
  }

  /**
   * Get the name of the related class for this column foreign key.
   *
   * @return string $foreignClassName
   */
  public function getForeignClassName()
  {
    if ($this->isForeignKey())
    {
      return $this->foreignClassName;
    } else {
      return false;
    }
  }

  /**
   * If foreign key get the related Doctrine_Table object
   *
   * @return Doctrine_Table $table
   */
  public function getForeignTable()
  {
    if ($this->isForeignKey())
    {
      return Doctrine_Core::getTable($this->foreignClassName);
    } else {
      return false;
    }
  }

  /**
   * Set the Doctrine_Table object this column belongs to
   *
   * @param Doctrine_Table $table
   * @return void
   */
  public function setTable(Doctrine_Table $table)
  {
    $this->table = $table;
  }

  /**
   * Get the Doctrine_Table object this column belongs to
   *
   * @return Doctrine_Table $table
   */
  public function getTable()
  {
    return $this->table;
  }

  public function offsetExists($offset)
  {
    return isset($this->definition[$offset]);
  }

  public function offsetSet($offset, $value)
  {
    $this->definition[$offset] = $value;
  }

  public function offsetGet($offset)
  {
    return $this->definition[$offset];
  }

  public function offsetUnset($offset)
  {
    unset($this->definition[$offset]);
  }
}