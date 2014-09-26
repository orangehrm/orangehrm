<?php

class httpcacheActions extends sfActions
{
  public function executePage1(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

  public function executePage2(sfWebRequest $request)
  {
    $this->setTemplate('index');
  }

  public function executePage3(sfWebRequest $request)
  {
    $this->getResponse()->setHttpHeader('Last-Modified', sfWebResponse::getDate(time() - 86400));

    $this->setTemplate('index');
  }

  public function executePage4(sfWebRequest $request)
  {
    $this->getResponse()->setHttpHeader('Last-Modified', sfWebResponse::getDate(time() - 86400));

    $this->setTemplate('index');
  }
}
