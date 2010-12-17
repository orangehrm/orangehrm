<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Utilities for managing many to many relationships in propel.
 *
 * @package    symfony
 * @subpackage propel
 * @author     Nick Lane <nick.lane@internode.on.net>
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelManyToMany.class.php 22881 2009-10-08 16:50:37Z Kris.Wallsmith $
 */
class sfPropelManyToMany
{
  public static function getColumn($class, $middleClass, $relatedColumn = '')
  {
    // find the related class
    $tableMap = call_user_func(array(constant($middleClass.'::PEER'), 'getTableMap'));
    $object_table_name = constant(constant($class.'::PEER').'::TABLE_NAME');

    if (!empty($relatedColumn))
    {
      $relatedColumnName = $tableMap->getColumn($relatedColumn)->getPhpName();
    }

    foreach ($tableMap->getColumns() as $column)
    {
      if ($column->isForeignKey() && $object_table_name == $column->getRelatedTableName())
      {
        if (!empty($relatedColumn))
        {
          if ($column->getPhpName() != $relatedColumnName)
          {
            return $column;
          }
        }
        else
        {
          return $column;
        }
      }
    }
  }

  public static function getRelatedColumn($class, $middleClass, $relatedColumn = '')
  {
    // find the related class
    $tableMap = call_user_func(array(constant($middleClass.'::PEER'), 'getTableMap'));
    $object_table_name = constant(constant($class.'::PEER').'::TABLE_NAME');

    if (!empty($relatedColumn))
    {
      return $tableMap->getColumn($relatedColumn);
    }

    foreach ($tableMap->getColumns() as $column)
    {
      if ($column->isForeignKey() && $object_table_name != $column->getRelatedTableName())
      {
        return $column;
      }
    }
  }

  public static function getRelatedClass($class, $middleClass, $relatedColumn = '')
  {
    $column = self::getRelatedColumn($class, $middleClass, $relatedColumn);

    $tableMap = call_user_func(array(constant($middleClass.'::PEER'), 'getTableMap'));
    $tableMap->getRelations();

    return $tableMap->getDatabaseMap()->getTable($column->getRelatedTableName())->getPhpName();
  }

  public static function getAllObjects($object, $middleClass, $relatedColumn = '', $criteria = null)
  {
    if (null === $criteria)
    {
      $criteria = new Criteria();
    }

    $relatedClass = self::getRelatedClass(get_class($object), $middleClass, $relatedColumn);

    // don't show $this object for self-referential relation
    // make sure to use all primary keys
    if (!empty($relatedColumn))
    {
      $tempCriteria = $object->buildPkeyCriteria();
      foreach ($tempCriteria->getIterator() as $criterion)
      {
        $criteria->add($criterion->getTable().'.'.$criterion->getColumn(), $criterion->getValue(), Criteria::NOT_EQUAL);
      }
    }

    return call_user_func(array(constant($relatedClass.'::PEER'), 'doSelect'), $criteria);
  }

  /**
   * Gets objects related by a many-to-many relationship, with a middle table.
   *
   * @param  $object        The object to get related objects for.
   * @param  $middleClass   The middle class used for the many-to-many relationship.
   * @param  $criteria      Criteria to apply to the selection.
   */
  public static function getRelatedObjects($object, $middleClass, $relatedColumn = '', $criteria = null)
  {
    if (null === $criteria)
    {
      $criteria = new Criteria();
    }

    $relatedClass = self::getRelatedClass(get_class($object), $middleClass, $relatedColumn);

    $relatedObjects = array();
    if (empty($relatedColumn))
    {
      $objectMethod = 'get'.$middleClass.'sJoin'.$relatedClass;
      $relatedMethod = 'get'.$relatedClass;
      $rels = $object->$objectMethod($criteria);
    }
    else
    {
      // as there is no way to join the related objects starting from this object we'll use the through class peer instead
      $localColumn = self::getColumn(get_class($object), $middleClass, $relatedColumn);
      $remoteColumn = self::getRelatedColumn(get_class($object), $middleClass, $relatedColumn);
      $c = new Criteria();
      $c->add(constant(constant($middleClass.'::PEER').'::'.$localColumn->getName()), $object->getId());
      $relatedMethod = 'get'.$relatedClass.'RelatedBy'.$remoteColumn->getPhpName();
      $rels = call_user_func(array(constant($middleClass.'::PEER'), 'doSelectJoin'.$relatedClass.'RelatedBy'.$remoteColumn->getPhpName()), $c);
    }
    foreach ($rels as $rel)
    {
      $relatedObjects[] = $rel->$relatedMethod();
    }

    return $relatedObjects;
  }
}
