<?php

/**
 * i18n actions.
 *
 * @package    test
 * @subpackage i18n
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 24597 2009-11-30 19:53:50Z Kris.Wallsmith $
 */
class i18nActions extends sfActions
{
  public function executeIndex()
  {
    $this->getUser()->setCulture('fr');

    $this->movies = MoviePeer::doSelect(new Criteria());
  }

  public function executeDefault()
  {
    $this->movies = MoviePeer::doSelect(new Criteria());

    $this->setTemplate('index');
  }

  public function executeMovie($request)
  {
    $this->form = new MovieForm(MoviePeer::retrieveByPk($request->getParameter('id')));

    if ($request->isMethod(sfRequest::POST))
    {
      $this->form->bind($request->getParameter('movie'));

      if ($this->form->isValid())
      {
        $movie = $this->form->save();

        $this->redirect('i18n/movie?id='.$movie->getId());
      }
    }
  }

  public function executeProducts()
  {
    $this->products = ProductPeer::doSelect(new Criteria());
  }
}
