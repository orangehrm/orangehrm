<?php

/**
 * i18n actions.
 *
 * @package    test
 * @subpackage i18n
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12854 2008-11-09 20:08:32Z fabien $
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

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('movie'));

      if ($this->form->isValid())
      {
        $movie = $this->form->save();

        $this->redirect('i18n/movie?id='.$movie->getId());
      }
    }
  }
}
