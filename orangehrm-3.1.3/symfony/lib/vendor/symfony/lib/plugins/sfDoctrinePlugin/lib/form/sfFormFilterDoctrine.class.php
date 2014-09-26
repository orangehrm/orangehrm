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
 * Available options:
 *
 *  * query:        The query object to use
 *  * table_method: A method on the table class that will either filter the passed query object or create a new one
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfFormFilterDoctrine.class.php 33150 2011-10-24 07:57:16Z fabien $
 */
abstract class sfFormFilterDoctrine extends sfFormFilter
{
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
   * @return string
   */
  public function getTableMethod()
  {
    return $this->getOption('table_method');
  }

  /**
   * Set the name of the table method used to retrieve the query object for the filter
   *
   * The specified method will be passed the query object before any changes
   * are made based on incoming parameters.
   *
   * @param string $tableMethod
   */
  public function setTableMethod($tableMethod)
  {
    $this->setOption('table_method', $tableMethod);
  }

  /**
   * Sets the query object to use.
   * 
   * @param Doctrine_Query $query
   */
  public function setQuery($query)
  {
    $this->setOption('query', $query);
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
      if (method_exists($this, $method = sprintf('convert%sValue', self::camelize($field))))
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
    return $this->doBuildQuery($this->processValues($values));
  }

  /**
   * Builds a Doctrine query with processed values.
   *
   * Overload this method instead of {@link buildQuery()} to avoid running
   * {@link processValues()} multiple times.
   *
   * @param  array $values
   *
   * @return Doctrine_Query
   */
  protected function doBuildQuery(array $values)
  {
    $query = isset($this->options['query']) ? clone $this->options['query'] : $this->getTable()->createQuery('r');

    if ($method = $this->getTableMethod())
    {
      $tmp = $this->getTable()->$method($query);

      // for backward compatibility
      if ($tmp instanceof Doctrine_Query)
      {
        $query = $tmp;
      }
    }

    $fields = $this->getFields();

    // add those fields that are not represented in getFields() with a null type
    $names = array_merge($fields, array_diff(array_keys($this->validatorSchema->getFields()), array_keys($fields)));
    $fields = array_merge($fields, array_combine($names, array_fill(0, count($names), null)));

    foreach ($fields as $field => $type)
    {
      if (!isset($values[$field]) || null === $values[$field] || '' === $values[$field])
      {
        continue;
      }

      if ($this->getTable()->hasField($field))
      {
        $method = sprintf('add%sColumnQuery', self::camelize($this->getFieldName($field)));
      }
      else if (!method_exists($this, $method = sprintf('add%sColumnQuery', self::camelize($field))) && null !== $type)
      {
        throw new LogicException(sprintf('You must define a "%s" method to be able to filter with the "%s" field.', $method, $field));
      }

      if (method_exists($this, $method))
      {
        $this->$method($query, $field, $values[$field]);
      }
      else if (null !== $type)
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
      $query->andWhereIn(sprintf('%s.%s', $query->getRootAlias(), $fieldName), $value);
    }
    else
    {
      $query->addWhere(sprintf('%s.%s = ?', $query->getRootAlias(), $fieldName), $value);
    }
  }

  protected function addEnumQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);

    $query->addWhere(sprintf('%s.%s = ?', $query->getRootAlias(), $fieldName), $value);
  }

  protected function addTextQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);

    if (is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $query->addWhere(sprintf('(%s.%s IS NULL OR %1$s.%2$s = ?)', $query->getRootAlias(), $fieldName), array(''));
    }
    else if (is_array($values) && isset($values['text']) && '' != $values['text'])
    {
      $query->addWhere(sprintf('%s.%s LIKE ?', $query->getRootAlias(), $fieldName), '%'.$values['text'].'%');
    }
  }

  protected function addNumberQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);

    if (is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $query->addWhere(sprintf('(%s.%s IS NULL OR %1$s.%2$s = ?)', $query->getRootAlias(), $fieldName), array(''));
    }
    else if (is_array($values) && isset($values['text']) && '' !== $values['text'])
    {
      $query->addWhere(sprintf('%s.%s = ?', $query->getRootAlias(), $fieldName), $values['text']);
    }
  }

  protected function addBooleanQuery(Doctrine_Query $query, $field, $value)
  {
    $fieldName = $this->getFieldName($field);
    $query->addWhere(sprintf('%s.%s = ?', $query->getRootAlias(), $fieldName), $value);
  }

  protected function addDateQuery(Doctrine_Query $query, $field, $values)
  {
    $fieldName = $this->getFieldName($field);

    if (isset($values['is_empty']) && $values['is_empty'])
    {
      $query->addWhere(sprintf('%s.%s IS NULL', $query->getRootAlias(), $fieldName));
    }
    else
    {
      if (null !== $values['from'] && null !== $values['to'])
      {
        $query->andWhere(sprintf('%s.%s >= ?', $query->getRootAlias(), $fieldName), $values['from']);
        $query->andWhere(sprintf('%s.%s <= ?', $query->getRootAlias(), $fieldName), $values['to']);
      }
      else if (null !== $values['from'])
      {
        $query->andWhere(sprintf('%s.%s >= ?', $query->getRootAlias(), $fieldName), $values['from']);
      }
      else if (null !== $values['to'])
      {
        $query->andWhere(sprintf('%s.%s <= ?', $query->getRootAlias(), $fieldName), $values['to']);
      }
    }
  }

  /**
   * Used in generated forms when models use inheritance.
   */
  protected function setupInheritance()
  {
  }

  /**
   * Returns the name of the related model.
   * 
   * @param string $alias A relation alias
   * 
   * @return string
   * 
   * @throws InvalidArgumentException If no relation with the supplied alias exists on the current model
   */
  protected function getRelatedModelName($alias)
  {
    $table = Doctrine_Core::getTable($this->getModelName());

    if (!$table->hasRelation($alias))
    {
      throw new InvalidArgumentException(sprintf('The "%s" model has no "%s" relation.', $this->getModelName(), $alias));
    }

    $relation = $table->getRelation($alias);

    return $relation['class'];
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
    return Doctrine_Core::getTable($this->getModelName());
  }
}
