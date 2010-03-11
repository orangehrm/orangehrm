<?php

/**
 * fillInFilter actions.
 *
 * @package    project
 * @subpackage fillInFilter
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 3050 2006-12-16 15:11:35Z fabien $
 */
class fillInFilterActions extends sfActions
{
  public function executeForward()
  {
    if ($this->getRequest()->getMethod() === sfRequest::POST)
    {
      $this->forward('fillInFilter', 'done');
    }
  }

  public function executeDone()
  {
  }

  public function handleErrorForward()
  {
    return sfView::SUCCESS;
  }

  public function executeIndex()
  {
  }

  public function executeUpdate()
  {
    $this->forward('fillInFilter', 'index');
  }

  public function handleErrorUpdate()
  {
    $this->forward('fillInFilter', 'index');
  }
}
