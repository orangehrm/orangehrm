<?php

/**
 * i18n actions.
 *
 * @package    project
 * @subpackage i18n
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 8645 2008-04-27 15:37:17Z fabien $
 */
class i18nActions extends sfActions
{
  public function executeIndex()
  {
    $i18n = $this->getContext()->getI18N();

    $this->test = $i18n->__('an english sentence');
    $this->localTest = $i18n->__('a local english sentence');
    $this->otherTest = $i18n->__('an english sentence', array(), 'other');
    $this->otherLocalTest = $i18n->__('a local english sentence', array(), 'other');
  }

  public function executeIndexForFr()
  {
    // change user culture
    $this->getUser()->setCulture('fr');
    $this->getUser()->setCulture('en');
    $this->getUser()->setCulture('fr');

    $this->forward('i18n', 'index');
  }
  
  public function executeI18nForm(sfWebRequest $request)
  {
    $this->form = new I18nForm();
    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('i18n'));
    }
  }
  
  public function executeI18nCustomCatalogueForm(sfWebRequest $request)
  {
    $this->form = new I18nCustomCatalogueForm();
    $this->setTemplate('i18nForm');
  }
}
