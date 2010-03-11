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
 * sfFormFilterDoctrine is the base class for filter forms based on Doctrine objects.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfFormFilterDoctrine.class.php 11690 2008-09-20 19:50:03Z fabien $
 */
abstract class sfFormFilterDoctrine extends sfFormFilter
{
  protected
    $tableMethodName       = null;

  /**
   * Returns the current model name.
   *
   * @return string The model class name
   */
  abstract public function getModelName();

  /**
   * Returns the fields and their filter type.
   *
   * @return array An array of fields with their filter type
   */
  abstract public function getFields();

  /**
   * Get the name of the table method used to retrieve the query object for the filter
   *
   * @return string $tableMethodName
   */
  public function getTableMethod()
  {
    return $this->tableMethodName;
  }

  /**
   * Set the name of the table method used to retrieve the query object for the filter
   *
   * @param string $tableMethodName 
   * @return void
   */
  public function setTableMethod($tableMethodName)
  {
    $this->tableMethodName = $tableMethodName;
  }

  /**
   * Returns a Doctrine Query based on the current values form the form.
   *
   * @return Query A Doctrine Query object
   */
  public function getQuery()
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    return $this->buildQuery($this->getValues());
  }

  /**
   * Processes cleaned up values with user defined methods.
   *
   * To process a value before it is used by the buildQuery() method,
   * you need to define an convertXXXValue() method where XXX is the PHP name
   * of the column.
   *
   * The method must return the processed value or false to remove the value
   * from the array of cleaned up values.
   *
   * @param  array An array of cleaned up values to process
   *
   * @return array An array of cleaned up values processed by the user defined methods
   */
  public function processValues($values)
  {
    // see if the user has overridden some column setter
    $originalValues = $values;
    foreach ($originalValues as $field => $value)
    {
      try
      {
        $method = sprintf('convert%sValue', self::camelize($field));
      }
      catch (Exception $e)
      {
        // no a "real" column of this object
        continue;
      }

      if (method_exists($this, $method))
      {
        if (false === $ret = $this->$method($value))
        {
          unset($values[$field]);
        }
        else
        {
          $values[$field] = $ret;
        }
      }
    }

    return $values;
  }

  /**
   * Builds a Doctrine Query based on the passed values.
   *
   * @param  array    An array of parameters to build the Query object
   *
   * @return Query A Doctrine Query object
   */
  public function buildQuery(array $values)
  {
    $values = $this->processValues($values);

    $query = Doctrine::getTable($this->getModelName())->createQuery('r');

    if ($this->tableMethodName)
    {
      $method = $this->tableMethodName;
      $query = Doctrine::getTable($this->getModelName())->$method($query);
    }

    foreach ($this->getFields() as $field => $type)
    {
      if (!isset($values[$field]) || is_null($values[$field]) || '' === $values[$field])
      {
        continue;
      }

      if ($this->getTable()->hasField($field))
      {
        $method = sprintf('add%sColumnQuery', self::camelize($this->getFieldName($field)));
      } else {
        // not a "real" column
        if (!method_exists($this, $method = sprintf('add%sColumnQuery', self::camelize($field))))
        {
          throw new LogicException(sprintf('You must define a "%s" method to be able to filter with the "%s" field.', $method, $field));
        }  
      }

      if (method_exists($this, $method))
      {
        $this->$method($query, $field, $values[$field]);
      }
      else
      {
        if (!method_exists($this, $method = sprintf('add%sQuery', $type)))
        {
          throw new LogicException(sprintf('Unable to filter for the "%s" type.', $type));
        }

        $this->$method($query, $field, $values[$field]);
      }
    }

    return $query;
  }

  protected function addForeignKeyQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);

    if (is_array($value))
    {
      $query->orWhereIn('r.' . $fieldName, $value);
    }
    else
    {
      $query->addWhere('r.' . $fieldName . ' = ?', $value);
    }
  }

  protected function addEnumQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);

    $query->addWhere('r.' . $fieldName . ' = ?', $value);
  }

  protected function addTextQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);

    if (is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $query->addWhere('r.' . $fieldName . ' IS NULL');
    }
    else if (is_array($values) && isset($values['text']) && '' != $values['text'])
    {
      $query->addWhere('r.' . $fieldName . ' LIKE ?', '%' . $values['text'] . '%');
    }
  }

  protected function addNumberQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);

    if (is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $query->addWhere('r.' . $fieldName . ' IS NULL');
    }
    else if (is_array($values) && isset($values['text']) && '' != $values['text'])
    {
      $query->addWhere('r.' . $fieldName . ' = ?', $values['text']);
    }
  }

  protected function addBooleanQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);
    $query->addWhere('r.' . $fieldName . ' = ?', $value);
  }

  protected function addDateQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);

    if (isset($values['is_empty']) && $values['is_empty'])
    {
      $query->addWhere('r.' . $fieldName . ' IS NULL');
    }
    else
    {
      $criterion = null;
      if (!is_null($values['from']) && !is_null($values['to']))
      {
        $query->andWhere('r.' . $fieldName . ' >= ?', $values['from']);
        $query->andWhere('r.' . $fieldName . ' <= ?', $values['to']);
      }
      else if (!is_null($values['from']))
      {
        $query->andWhere('r.' . $fieldName . ' >= ?', $values['from']);
      }
      else if (!is_null($values['to']))
      {
        $query->andWhere('r.' . $fieldName . ' <= ?', $values['to']);
      }
    }
  }

  protected function getColName($field)
  {
    return $this->getTable()->getColumnName($field);
  }

  protected function getFieldName($colName)
  {
    return $this->getTable()->getFieldName($colName);
  }

  protected function camelize($text)
  {
    return sfToolkit::pregtr($text, array('#/(.?)#e' => "'::'.strtoupper('\\1')", '/(^|_|-)+(.)/e' => "strtoupper('\\2')"));
  }

  protected function getTable()
  {
    return Doctrine::getTable($this->getModelName());
  }
}