<?php

/**
 * exception actions.
 *
 * @package    project
 * @subpackage exception
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 2980 2006-12-08 20:02:11Z fabien $
 */
class exceptionActions extends sfActions
{
  public function executeNoException()
  {
    return $this->renderText('foo');
  }

  public function executeThrowsException()
  {
    throw new Exception('Exception message');
  }

  public function executeThrowsSfException()
  {
    throw new sfException('sfException message');
  }
}
