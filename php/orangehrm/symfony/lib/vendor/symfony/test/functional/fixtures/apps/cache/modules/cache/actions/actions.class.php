<?php

/**
 * cache actions.
 *
 * @package    project
 * @subpackage cache
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 28625 2010-03-19 19:00:53Z Kris.Wallsmith $
 */
class cacheActions extends sfActions
{
  public function executeIndex()
  {
  }

  public function executePage()
  {
  }

  public function executeList($request)
  {
    $this->page = $request->getParameter('page', 1);
  }

  public function executeForward()
  {
    $this->forward('cache', 'page');
  }

  public function executeMulti()
  {
  }

  public function executeMultiBis()
  {
  }

  public function executePartial()
  {
  }

  public function executeAnotherPartial()
  {
  }

  public function executeComponent()
  {
  }

  public function executeSpecificCacheKey()
  {
  }

  public function executeAction()
  {
    $response = $this->getResponse();
    $response->setHttpHeader('symfony', 'foo');
    $response->setContentType('text/plain');
    $response->setTitle('My title');
    $response->addMeta('meta1', 'bar');
    $response->addHttpMeta('httpmeta1', 'foobar');

    sfConfig::set('ACTION_EXECUTED', true);
  }

  public function executeImageWithLayoutCacheWithLayout()
  {
    $this->prepareImage();
    $this->setLayout('image');
  }

  public function executeImageWithLayoutCacheNoLayout()
  {
    $this->prepareImage();
    $this->setLayout('image');
  }

  public function executeImageNoLayoutCacheWithLayout()
  {
    $this->prepareImage();
    $this->setLayout(false);
  }

  public function executeImageNoLayoutCacheNoLayout()
  {
    $this->prepareImage();
    $this->setLayout(false);
  }

  protected function prepareImage()
  {
    $this->getResponse()->setContentType('image/png');
    $this->image = file_get_contents(dirname(__FILE__).'/../data/ok48.png');
    $this->setTemplate('image');
  }

  public function executeLastModifiedResponse()
  {
    $this->getResponse()->setHttpHeader('Last-Modified', $this->getResponse()->getDate(sfConfig::get('LAST_MODIFIED')));
    $this->setTemplate('action');
  }
}
