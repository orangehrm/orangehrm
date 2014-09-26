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
 * sfDoctrineRoute represents a route that is bound to a Doctrine class.
 *
 * A Doctrine route can represent a single Doctrine object or a list of objects.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineRoute.class.php 28633 2010-03-20 14:35:57Z Kris.Wallsmith $
 */
class sfDoctrineRoute extends sfObjectRoute
{
  protected
    $query = null;

  public function setListQuery(Doctrine_Query $query)
  {
    if (!$this->isBound())
    {
      throw new LogicException('The route is not bound.');
    }

    $this->query = $query;
  }

  protected function getObjectForParameters($parameters)
  {
    $results = $this->getObjectsForParameters($parameters);

    // If query returned Doctrine_Collection with results inside then we
    // need to return the first Doctrine_Record
    if ($results instanceof Doctrine_Collection)
    {
      if (count($results))
      {
        $results = $results->getFirst();
      } else {
        $results = null;
      }
    }
    // If an object is returned then lets return it otherwise return null
    else if(!is_object($results))
    {
      $results = null;
    }

    return $results;
  }

  protected function getObjectsForParameters($parameters)
  {
    $tableModel = Doctrine_Core::getTable($this->options['model']);

    $variables = array();
    $values = array();
    foreach($this->getRealVariables() as $variable)
    {
      if($tableModel->hasColumn($tableModel->getColumnName($variable)))
      {
        $variables[] = $variable;
        $values[$variable] = $parameters[$variable];
      }
    }

    if (!isset($this->options['method']))
    {
      if (null === $this->query)
      {
        $q = $tableModel->createQuery('a');
        foreach ($values as $variable => $value)
        {
          $fieldName = $tableModel->getFieldName($variable);
          $q->andWhere('a.'. $fieldName . ' = ?', $parameters[$variable]);
        }
      }
      else
      {
        $q = $this->query;
      }
      if (isset($this->options['method_for_query']))
      {
        $method = $this->options['method_for_query'];
        $results = $tableModel->$method($q);
      }
      else
      {
        $results = $q->execute();
      }
    }
    else
    {
      $method = $this->options['method'];
      $results = $tableModel->$method($this->filterParameters($parameters));
    }

    // If query returned a Doctrine_Record instance instead of a 
    // Doctrine_Collection then we need to create a new Doctrine_Collection with
    // one element inside and return that
    if ($results instanceof Doctrine_Record)
    {
      $obj = $results;
      $results = new Doctrine_Collection($obj->getTable());
      $results[] = $obj;
    }

    return $results;
  }

  protected function doConvertObjectToArray($object)
  {
    if (isset($this->options['convert']) || method_exists($object, 'toParams'))
    {
      return parent::doConvertObjectToArray($object);
    }

    $parameters = array();
    foreach ($this->getRealVariables() as $variable)
    {
      try {
        $parameters[$variable] = $object->$variable;
      } catch (Exception $e) {
        try {
          $method = 'get'.sfInflector::camelize($variable);
          $parameters[$variable] = $object->$method();
        } catch (Exception $e) {}
      }
    }

    return $parameters;
  }
}