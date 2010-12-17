<?php

/**
 * pooling actions.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage pooling
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 14025 2008-12-14 15:41:43Z Kris.Wallsmith $
 */
class poolingActions extends sfActions
{
  public function executeAddArticleButDontSave(sfWebRequest $request)
  {
    $article = new Article();
    $article->setTitle(__METHOD__.'()');

    $category = CategoryPeer::retrieveByPK($request->getParameter('category_id'));
    $category->addArticle($article);

    return sfView::NONE;
  }

  public function executeAddArticleAndSave(sfWebRequest $request)
  {
    $article = new Article();
    $article->setTitle(__METHOD__.'()');

    $category = CategoryPeer::retrieveByPK($request->getParameter('category_id'));
    $category->addArticle($article);
    $category->save();

    return sfView::NONE;
  }
}
