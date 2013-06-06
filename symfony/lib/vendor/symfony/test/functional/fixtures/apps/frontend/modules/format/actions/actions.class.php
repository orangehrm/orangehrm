<?php

/**
 * format actions.
 *
 * @package    project
 * @subpackage format
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 11805 2008-09-26 18:08:49Z fabien $
 */
class formatActions extends sfActions
{
  public function executeIndex($request)
  {
    if ('xml' == $request->getRequestFormat())
    {
      $this->setLayout('layout');
    }
  }

  public function executeForTheIPhone($request)
  {
    $this->setTemplate('index');
  }

  public function executeJs($request)
  {
    $request->setRequestFormat('js');
  }

  public function executeJsWithAccept()
  {
    $this->setTemplate('index');
  }

  public function executeThrowsException()
  {
    throw new Exception('Descriptive message');
  }

  public function executeThrowsNonDebugException()
  {
    sfConfig::set('sf_debug', false);
    throw new Exception('Descriptive message');
  }
}
