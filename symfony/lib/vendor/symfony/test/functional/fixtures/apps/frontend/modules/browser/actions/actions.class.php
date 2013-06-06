<?php

/**
 * browser actions.
 *
 * @package    project
 * @subpackage browser
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 24992 2009-12-06 20:48:52Z Kris.Wallsmith $
 */
class browserActions extends sfActions
{
  public function executeIndex()
  {
    return $this->renderText('<html><body><h1>html</h1></body></html>');
  }

  public function executeText()
  {
    $this->getResponse()->setContentType('text/plain');

    return $this->renderText('text');
  }

  public function executeResponseHeader()
  {
    $response = $this->getResponse();

    $response->setContentType('text/plain');
    $response->setHttpHeader('foo', 'bar', true);
    $response->setHttpHeader('foo', 'foobar', false);

    return $this->renderText('ok');
  }

  public function executeTemplateCustom($request)
  {
    if ($request->getParameter('custom'))
    {
      $this->setTemplate('templateCustomCustom');
    }
  }

  public function executeRedirect1()
  {
    $this->redirect('browser/redirectTarget1');
  }

  public function executeRedirectTarget1()
  {
  }

  public function executeRedirect2()
  {
    $this->redirect('browser/redirectTarget2');
  }

  public function executeRedirectTarget2()
  {
    return $this->renderText('ok');
  }
}
