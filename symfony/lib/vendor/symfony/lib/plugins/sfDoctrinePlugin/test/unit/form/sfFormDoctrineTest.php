<?php

$app = 'frontend';
include dirname(__FILE__).'/../../bootstrap/functional.php';

$t = new lime_test(13);

// ->__construct()
$t->diag('->__construct()');

class NumericFieldForm extends ArticleForm
{
  public function configure()
  {
    $this->widgetSchema[1] = new sfWidgetFormInputText();
    $this->validatorSchema[1] = new sfValidatorPass();
    $this->setDefault(1, '==DEFAULT_VALUE==');
  }
}

$form = new NumericFieldForm();
$defaults = $form->getDefaults();
$t->is($defaults[1], '==DEFAULT_VALUE==', '->__construct() allows ->configure() to set defaults on numeric fields');

class DefaultValuesForm extends AuthorForm
{
  public function configure()
  {
    $this->setDefault('name', 'John Doe');
  }
}

$author = new Author();
$form = new DefaultValuesForm($author);
$t->is($form->getDefault('name'), 'John Doe', '->__construct() uses form defaults for new objects');

$author = new Author();
$author->name = 'Jacques Doe';
$author->save();
$form = new DefaultValuesForm($author);
$t->is($form->getDefault('name'), 'Jacques Doe', '->__construct() uses object value as a default for existing objects');
$author->delete();

// ->embedRelation()
$t->diag('->embedRelation()');

class myArticleForm extends ArticleForm
{
}

$table = Doctrine_Core::getTable('Author');
$form = new AuthorForm($table->create(array(
  'Articles' => array(
    array('title' => 'Article 1'),
    array('title' => 'Article 2'),
    array('title' => 'Article 3'),
  ),
)));

$form->embedRelation('Articles');
$embeddedForms = $form->getEmbeddedForms();

$t->ok(isset($form['Articles']), '->embedRelation() embeds forms');
$t->is(count($embeddedForms['Articles']), 3, '->embedRelation() embeds one form for each related object');

$form->embedRelation('Articles', 'myArticleForm', array(array('test' => true)));
$embeddedForms = $form->getEmbeddedForms();
$moreEmbeddedForms = $embeddedForms['Articles']->getEmbeddedForms();
$t->isa_ok($moreEmbeddedForms[0], 'myArticleForm', '->embedRelation() accepts a form class argument');
$t->ok($moreEmbeddedForms[0]->getOption('test'), '->embedRelation() accepts a form arguments argument');

$form = new AuthorForm($table->create(array(
  'Articles' => array(
    array('title' => 'Article 1'),
    array('title' => 'Article 2'),
  ),
)));
$form->embedRelation('Articles as author_articles');
$t->is(isset($form['author_articles']), true, '->embedRelation() embeds using an alias');
$t->is(count($form['author_articles']), 2, '->embedRelation() embeds one form for each related object using an alias');

$form = new AuthorForm($table->create(array(
  'Articles' => array(
    array('title' => 'Article 1'),
    array('title' => 'Article 2'),
  ),
)));
$form->embedRelation('Articles AS author_articles');
$t->is(isset($form['author_articles']), true, '->embedRelation() embeds using an alias with a case insensitive separator');

$form = new ArticleForm(Doctrine_Core::getTable('Article')->create(array(
  'Author' => array('name' => 'John Doe'),
)));
$form->embedRelation('Author');
$t->is(isset($form['Author']), true, '->embedRelation() embeds a ONE type relation');
$t->is(isset($form['Author']['name']), true, '->embedRelation() embeds a ONE type relation');
$t->is($form['Author']['name']->getValue(), 'John Doe', '->embedRelation() uses values from the related object');
