<?php

/**
 * browser actions.
 *
 * @package    project
 * @subpackage browser
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 12215 2008-10-16 12:11:16Z fabien $
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
}
