<?php

/**
 * attachment actions.
 *
 * @package    test
 * @subpackage attachment
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 14010 2008-12-13 14:27:57Z Kris.Wallsmith $
 */
class attachmentActions extends sfActions
{
  public function executeIndex($request)
  {
    $this->form = new AttachmentForm();
    unset($this->form['article_id']);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('attachment'), $request->getFiles('attachment'));

      if ($this->form->isValid())
      {
        $this->form->save();

        $this->redirect('attachment/ok');
      }
    }
  }

  public function executeEmbedded($request)
  {
    $this->form = new ArticleForm(null, array('with_attachment' => true));

    if (
      $request->isMethod('post')
      &&
      $this->form->bindAndSave($request->getParameter('article'), $request->getFiles('article'))
    )
    {
      $this->redirect('attachment/ok');
    }

    $this->setTemplate('index');
  }

  public function executeOk()
  {
    return $this->renderText('ok');
  }
}
