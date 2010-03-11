<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTesterPropel implements tests for Propel classes.
 *
 * @package    symfony
 * @subpackage test
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTesterPropel.class.php 12237 2008-10-17 22:25:25Z Kris.Wallsmith $
 */
class sfTesterPropel extends sfTester
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
   * @param array|Criteria $criteria A Criteria object or an array of conditions
   * @param string         $value    The value to test
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function check($model, $criteria, $value = true)
  {
    if (is_null($criteria))
    {
      $criteria = new Criteria();
    }

    if (is_array($criteria))
    {
      $conditions = $criteria;
      $criteria = new Criteria();
      foreach ($conditions as $column => $condition)
      {
        $column = call_user_func(array(constant($model.'::PEER'), 'translateFieldName'), $column, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_COLNAME);
        $operator = Criteria::EQUAL;
        if ('!' == $condition[0])
        {
          $operator = false !== strpos($condition, '%') ? Criteria::NOT_LIKE : Criteria::NOT_EQUAL;
          $condition = substr($condition, 1);
        }
        else if (false !== strpos($condition, '%'))
        {
          $operator = Criteria::LIKE;
        }

        $criteria->add($column, $condition, $operator);
      }
    }

    $objects = call_user_func(array(constant($model.'::PEER'), 'doSelect'), $criteria);

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
