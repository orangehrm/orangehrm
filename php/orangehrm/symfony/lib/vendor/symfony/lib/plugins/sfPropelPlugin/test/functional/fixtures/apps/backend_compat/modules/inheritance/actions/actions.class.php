<?php

/**
 * inheritance actions.
 *
 * @package    project
 * @subpackage inheritance
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 5125 2007-09-16 00:53:55Z dwhittle $
 */
class inheritanceActions extends autoinheritanceActions
{
  protected function addFiltersCriteria($c)
  {
    if ($this->getRequestParameter('filter'))
    {
      $c->add(ArticlePeer::ONLINE, true);
    }
  }

  protected function addSortCriteria($c)
  {
    if ($this->getRequestParameter('sort'))
    {
      $c->addAscendingOrderByColumn(ArticlePeer::TITLE);
    }
  }
}
