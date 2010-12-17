<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class is the Propel implementation of sfPager.  It interacts with the propel record set and
 * manages criteria.
 *
 * @package    sfPropelPlugin
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfPropelPager.class.php 27747 2010-02-08 18:02:19Z Kris.Wallsmith $
 */
class sfPropelPager extends sfPager
{
  protected
    $criteria               = null,
    $peer_method_name       = 'doSelect',
    $peer_count_method_name = 'doCount';

  /**
   * Constructor.
   *
   * @see sfPager
   */
  public function __construct($class, $maxPerPage = 10)
  {
    parent::__construct($class, $maxPerPage);

    $this->setCriteria(new Criteria());
    $this->tableName = constant($this->getClassPeer().'::TABLE_NAME');
  }

  /**
   * @see sfPager
   */
  public function init()
  {
    $this->resetIterator();

    $hasMaxRecordLimit = ($this->getMaxRecordLimit() !== false);
    $maxRecordLimit = $this->getMaxRecordLimit();

    $criteriaForCount = clone $this->getCriteria();
    $criteriaForCount
      ->setOffset(0)
      ->setLimit(0)
      ->clearGroupByColumns()
    ;

    $count = call_user_func(array($this->getClassPeer(), $this->getPeerCountMethod()), $criteriaForCount);

    $this->setNbResults($hasMaxRecordLimit ? min($count, $maxRecordLimit) : $count);

    $criteria = $this->getCriteria()
      ->setOffset(0)
      ->setLimit(0)
    ;

    if (0 == $this->getPage() || 0 == $this->getMaxPerPage())
    {
      $this->setLastPage(0);
    }
    else
    {
      $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

      $offset = ($this->getPage() - 1) * $this->getMaxPerPage();
      $criteria->setOffset($offset);

      if ($hasMaxRecordLimit)
      {
        $maxRecordLimit = $maxRecordLimit - $offset;
        if ($maxRecordLimit > $this->getMaxPerPage())
        {
          $criteria->setLimit($this->getMaxPerPage());
        }
        else
        {
          $criteria->setLimit($maxRecordLimit);
        }
      }
      else
      {
        $criteria->setLimit($this->getMaxPerPage());
      }
    }
  }

  /**
   * @see sfPager
   */
  protected function retrieveObject($offset)
  {
    $criteriaForRetrieve = clone $this->getCriteria();
    $criteriaForRetrieve
      ->setOffset($offset - 1)
      ->setLimit(1)
    ;

    $results = call_user_func(array($this->getClassPeer(), $this->getPeerMethod()), $criteriaForRetrieve);

    return is_array($results) && isset($results[0]) ? $results[0] : null;
  }

  /**
   * @see sfPager
   */
  public function getResults()
  {
    return call_user_func(array($this->getClassPeer(), $this->getPeerMethod()), $this->getCriteria());
  }

  /**
   * Returns the peer method name.
   *
   * @return string
   */
  public function getPeerMethod()
  {
    return $this->peer_method_name;
  }

  /**
   * Sets the peer method name.
   *
   * @param string $method A method on the current peer class
   */
  public function setPeerMethod($peer_method_name)
  {
    $this->peer_method_name = $peer_method_name;
  }

  /**
   * Returns the peer count method name.
   *
   * @return string
   */
  public function getPeerCountMethod()
  {
    return $this->peer_count_method_name;
  }

  /**
   * Sets the peer count method name.
   *
   * @param string $peer_count_method_name
   */
  public function setPeerCountMethod($peer_count_method_name)
  {
    $this->peer_count_method_name = $peer_count_method_name;
  }

  /**
   * Returns the name of the current model class' peer class.
   *
   * @return string
   */
  public function getClassPeer()
  {
    return constant($this->class.'::PEER');
  }

  /**
   * Returns the current Criteria.
   *
   * @return Criteria
   */
  public function getCriteria()
  {
    return $this->criteria;
  }

  /**
   * Sets the Criteria for the current pager.
   *
   * @param Criteria $criteria
   */
  public function setCriteria($criteria)
  {
    $this->criteria = $criteria;
  }
}
