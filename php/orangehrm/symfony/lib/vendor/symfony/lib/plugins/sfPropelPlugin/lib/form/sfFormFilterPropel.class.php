<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfFormFilterPropel is the base class for filter forms based on Propel objects.
 *
 * @package    symfony
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfFormFilterPropel.class.php 14499 2009-01-06 18:15:39Z Jonathan.Wage $
 */
abstract class sfFormFilterPropel extends sfFormFilter
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
   * Returns a Propel Criteria based on the current values form the form.
   *
   * @return Criteria A Propel Criteria object
   */
  public function getCriteria()
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    return $this->buildCriteria($this->getValues());
  }

  /**
   * Processes cleaned up values with user defined methods.
   *
   * To process a value before it is used by the buildCriteria() method,
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
        $method = sprintf('convert%sValue', call_user_func(array(constant($this->getModelName().'::PEER'), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME));
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
   * Builds a Propel Criteria based on the passed values.
   *
   * @param  array    An array of parameters to build the Criteria object
   *
   * @return Criteria A Propel Criteria object
   */
  public function buildCriteria(array $values)
  {
    $values = $this->processValues($values);

    $criteria = new Criteria();

    $peer = constant($this->getModelName().'::PEER');
    foreach ($this->getFields() as $field => $type)
    {
      if (!isset($values[$field]) || is_null($values[$field]) || '' === $values[$field])
      {
        continue;
      }

      try
      {
        $method = sprintf('add%sColumnCriteria', call_user_func(array($peer, 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_PHPNAME));
      }
      catch (Exception $e)
      {
        // not a "real" column
        if (!method_exists($this, $method = sprintf('add%sColumnCriteria', self::camelize($field))))
        {
          throw new LogicException(sprintf('You must define a "%s" method to be able to filter with the "%s" field.', $method, $field));
        }
      }

      if (method_exists($this, $method))
      {
        $this->$method($criteria, $field, $values[$field]);
      }
      else
      {
        if (!method_exists($this, $method = sprintf('add%sCriteria', $type)))
        {
          throw new LogicException(sprintf('Unable to filter for the "%s" type.', $type));
        }

        $this->$method($criteria, $field, $values[$field]);
      }
    }

    return $criteria;
  }

  protected function addForeignKeyCriteria(Criteria $criteria, $field, $value)
  {
    $colname = $this->getColname($field);

    if (is_array($value))
    {
      $values = $value;
      $value = array_pop($values);
      $criterion = $criteria->getNewCriterion($colname, $value);

      foreach ($values as $value)
      {
        $criterion->addOr($criteria->getNewCriterion($colname, $value));
      }

      $criteria->add($criterion);
    }
    else
    {
      $criteria->add($colname, $value);
    }
  }

  protected function addTextCriteria(Criteria $criteria, $field, $values)
  {
    $colname = $this->getColname($field);

    if (is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $criterion = $criteria->getNewCriterion($colname, '');
      $criterion->addOr($criteria->getNewCriterion($colname, null, Criteria::ISNULL));
      $criteria->add($criterion);
    }
    else if (is_array($values) && isset($values['text']) && '' != $values['text'])
    {
      $criteria->add($colname, '%'.$values['text'].'%', Criteria::LIKE);
    }
  }

  protected function addNumberCriteria(Criteria $criteria, $field, $values)
  {
    $colname = $this->getColname($field);

    if (is_array($values) && isset($values['is_empty']) && $values['is_empty'])
    {
      $criterion = $criteria->getNewCriterion($colname, '');
      $criterion->addOr($criteria->getNewCriterion($colname, null, Criteria::ISNULL));
      $criteria->add($criterion);
    }
    else if (is_array($values) && isset($values['text']) && '' != $values['text'])
    {
      $criteria->add($colname, $values['text']);
    }
  }

  protected function addBooleanCriteria(Criteria $criteria, $field, $value)
  {
    $criteria->add($this->getColname($field), $value);
  }

  protected function addDateCriteria(Criteria $criteria, $field, $values)
  {
    $colname = $this->getColname($field);

    if (isset($values['is_empty']) && $values['is_empty'])
    {
      $criteria->add($colname, null, Criteria::ISNULL);
    }
    else
    {
      $criterion = null;
      if (!is_null($values['from']) && !is_null($values['to']))
      {
        $criterion = $criteria->getNewCriterion($colname, $values['from'], Criteria::GREATER_EQUAL);
        $criterion->addAnd($criteria->getNewCriterion($colname, $values['to'], Criteria::LESS_EQUAL));
      }
      else if (!is_null($values['from']))
      {
        $criterion = $criteria->getNewCriterion($colname, $values['from'], Criteria::GREATER_EQUAL);
      }
      else if (!is_null($values['to']))
      {
        $criterion = $criteria->getNewCriterion($colname, $values['to'], Criteria::LESS_EQUAL);
      }

      if (!is_null($criterion))
      {
        $criteria->add($criterion);
      }
    }
  }

  protected function getColName($field)
  {
    return call_user_func(array(constant($this->getModelName().'::PEER'), 'translateFieldName'), $field, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);
  }

  protected function camelize($text)
  {
    return sfToolkit::pregtr($text, array('#/(.?)#e' => "'::'.strtoupper('\\1')", '/(^|_|-)+(.)/e' => "strtoupper('\\2')"));
  }
}
