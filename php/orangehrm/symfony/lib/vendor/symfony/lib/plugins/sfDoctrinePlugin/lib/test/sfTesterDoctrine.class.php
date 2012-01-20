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
 * @version    SVN: $Id: sfTesterDoctrine.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
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
   * @param string               $model The model class name
   * @param array|Doctrine_Query $query A Doctrine_Query object or an array of conditions
   * @param string               $value The value to test
   *
   * @return sfTestFunctionalBase|sfTester
   */
  public function check($model, $query, $value = true)
  {
    if (null === $query)
    {
      $query = Doctrine_Core::getTable($model)
        ->createQuery('a');
    }

    if (is_array($query))
    {
      $conditions = $query;
      $query = $query = Doctrine_Core::getTable($model)
        ->createQuery('a');
      foreach ($conditions as $column => $condition)
      {
        $column = Doctrine_Core::getTable($model)->getFieldName($column);

        if (null === $condition)
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

  /**
   * Outputs some debug information about queries run during the current request.
   * 
   * @param integer|string $limit Either an integer to return the last many queries, a regular expression or a substring to search for
   */
  public function debug($limit = null)
  {
    if (!$databaseManager = $this->browser->getContext()->getDatabaseManager())
    {
      throw new LogicConnection('The current context does not include a database manager.');
    }

    $events = array();
    foreach ($databaseManager->getNames() as $name)
    {
      $database = $databaseManager->getDatabase($name);
      if ($database instanceof sfDoctrineDatabase && $profiler = $database->getProfiler())
      {
        foreach ($profiler->getQueryExecutionEvents() as $event)
        {
          $events[$event->getSequence()] = $event;
        }
      }
    }

    // sequence events
    ksort($events);

    if (is_integer($limit))
    {
      $events = array_slice($events, $limit * -1);
    }
    else if (preg_match('/^(!)?([^a-zA-Z0-9\\\\]).+?\\2[ims]?$/', $limit, $match))
    {
      if ($match[1] == '!')
      {
        $pattern = substr($limit, 1);
        $match = false;
      }
      else
      {
        $pattern = $limit;
        $match = true;
      }
    }
    else if ($limit)
    {
      $substring = $limit;
    }

    echo "\nDumping SQL executed in the current context:\n\n";

    foreach ($events as $event)
    {
      if (
        (!isset($pattern) && !isset($substring))
        ||
        (isset($pattern) && $match == preg_match($pattern, $event->getQuery()))
        ||
        (isset($substring) && false !== stripos($event->getQuery(), $substring))
      )
      {
        $conn = $event->getInvoker() instanceof Doctrine_Connection ? $event->getInvoker() : $event->getInvoker()->getConnection();

        echo $event->getQuery()."\n";
        echo '  Parameters: '.sfYaml::dump(sfDoctrineConnectionProfiler::fixParams($event->getParams()), 0)."\n";
        echo '  Connection: '.$conn->getName()."\n";
        echo '  Time:       '.number_format($event->getElapsedSecs(), 2)."s\n\n";
      }
    }

    exit(1);
  }
}
