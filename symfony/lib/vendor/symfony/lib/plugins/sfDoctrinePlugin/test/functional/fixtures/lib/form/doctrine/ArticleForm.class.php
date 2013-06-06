<?php

/**
 * Article form.
 *
 * @package    form
 * @subpackage Article
 * @version    SVN: $Id: ArticleForm.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ArticleForm extends BaseArticleForm
{
  public function configure()
  {
    $this->embedI18n(array('en', 'fr'));
  }
}