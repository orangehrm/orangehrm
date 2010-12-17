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
 * sfDoctrine pager class.
 *
 * @package    sfDoctrinePlugin
 * @subpackage pager
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrinePager.class.php 28897 2010-03-30 20:30:24Z Jonathan.Wage $
 */
class sfDoctrinePager extends sfPager implements Serializable
{
  protected
    $query             = null,
    $tableMethodName   = null,
    $tableMethodCalled = false;

  /**
   * Get the name of the table method used to retrieve the query object for the pager
   *
   * @return string $tableMethodName
   */
  public function getTableMethod()
  {
    return $this->tableMethodName;
  }

  /**
   * Set the name of the table method used to retrieve the query object for the pager
   *
   * @param string $tableMethodName
   * @return void
   */
  public function setTableMethod($tableMethodName)
  {
    $this->tableMethodName = $tableMethodName;
  }

  /**
   * Serialize the pager object
   *
   * @return string $serialized
   */
  public function serialize()
  {
    $vars = get_object_vars($this);
    unset($vars['query']);
    return serialize($vars);
  }

  /**
   * Unserialize a pager object
   *
   * @param string $serialized
   */
  public function unserialize($serialized)
  {
    $array = unserialize($serialized);

    foreach ($array as $name => $values)
    {
      $this->$name = $values;
    }

    $this->tableMethodCalled = false; 
  }

  /**
   * Returns a query for counting the total results.
   *
   * @return Doctrine_Query
   */
  public function getCountQuery()
  {
    $query = clone $this->getQuery();
    $query
      ->offset(0)
      ->limit(0)
    ;

    return $query;
  }

  /**
   * @see sfPager
   */
  public function init()
  {
    $this->resetIterator();

    $countQuery = $this->getCountQuery();
    $count = $countQuery->count();

    $this->setNbResults($count);

    $query = $this->getQuery();
    $query
      ->offset(0)
      ->limit(0)
    ;

    if (0 == $this->getPage() || 0 == $this->getMaxPerPage() || 0 == $this->getNbResults())
    {
      $this->setLastPage(0);
    }
    else
    {
      $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

      $query
        ->offset($offset)
        ->limit($this->getMaxPerPage())
      ;
    }
  }

  /**
   * Get the query for the pager.
   *
   * @return Doctrine_Query
   */
  public function getQuery()
  {
    if (!$this->tableMethodCalled && $this->tableMethodName)
    {
      $method = $this->tableMethodName;
      $this->query = Doctrine_Core::getTable($this->getClass())->$method($this->query);
      $this->tableMethodCalled = true;
    }
    else if (!$this->query)
    {
      $this->query = Doctrine_Core::getTable($this->getClass())->createQuery();
    }

    return $this->query;
  }

  /**
   * Set query object for the pager
   *
   * @param Doctrine_Query $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }

  /**
   * Retrieve the object for a certain offset
   *
   * @param integer $offset
   *
   * @return Doctrine_Record
   */
  protected function retrieveObject($offset)
  {
    $queryForRetrieve = clone $this->getQuery();
    $queryForRetrieve
      ->offset($offset - 1)
      ->limit(1)
    ;

    $results = $queryForRetrieve->execute();

    return $results[0];
  }

  /**
   * Get all the results for the pager instance
   *
   * @param mixed $hydrationMode A hydration mode identifier
   *
   * @return Doctrine_Collection|array
   */
  public function getResults($hydrationMode = null)
  {
    return $this->getQuery()->execute(array(), $hydrationMode);
  }

  /**
   * @see sfPager
   */
  protected function initializeIterator()
  {
    parent::initializeIterator();

    if ($this->results instanceof Doctrine_Collection)
    {
      $this->results = $this->results->getData();
    }
  }
}
