<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTesterDoctrine implements tests for Doctrine classes.
 *
 * @package    symfony
 * @subpackage test
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTesterDoctrine.class.php 12237 2008-10-17 22:25:25Z Kris.Wallsmith $
 */
class sfTesterDoctrine extends sfTester
{
  /**
   * Prepares the tester.
   */
  public function prepare()
  {
  }

  /**
   * Initializes the tester.
   */
  public function initialize()
  {
  }

  /**
   * Tests a model.
   *
   * @param string         $model    The model class name
   * @param array|Doctrine_Query $query A Doctrine_Query object or an array of conditions
   * @param string         $value    The value to test
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function check($model, $query, $value = true)
  {
    if (is_null($query))
    {
      $query = Doctrine::getTable($model)
        ->createQuery('a');
    }

    if (is_array($query))
    {
      $conditions = $query;
      $query = $query = Doctrine::getTable($model)
        ->createQuery('a');
      foreach ($conditions as $column => $condition)
      {
        $column = Doctrine::getTable($model)->getFieldName($column);

        if (is_null($condition))
        {
          $query->andWhere('a.'.$column.' IS NULL');
          continue;
        }

        $operator = '=';
        if ('!' == $condition[0])
        {
          $operator = false !== strpos($condition, '%') ? 'NOT LIKE' : '!=';
          $condition = substr($condition, 1);
        }
        else if (false !== strpos($condition, '%'))
        {
          $operator = 'LIKE';
        }

        $query->andWhere('a.' . $column . ' ' . $operator . ' ?', $condition);
      }
    }

    $objects = $query->execute();

    if (false === $value)
    {
      $this->tester->is(count($objects), 0, sprintf('no %s object that matches the criteria has been found', $model));
    }
    else if (true === $value)
    {
      $this->tester->cmp_ok(count($objects), '>', 0, sprintf('%s objects that matches the criteria have been found', $model));
    }
    else if (is_int($value))
    {
      $this->tester->is(count($objects), $value, sprintf('"%s" %s objects have been found', $value, $model));
    }
    else
    {
      throw new InvalidArgumentException('The "check()" method does not takes this kind of argument.');
    }

    return $this->getObjectToReturn();
  }
}