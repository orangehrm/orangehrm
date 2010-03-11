<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
$fixtures = 'fixtures/fixtures.yml';
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestBrowser();

// file upload
$fileToUpload = dirname(__FILE__).'/fixtures/config/databases.yml';
$uploadedFile = sfConfig::get('sf_cache_dir').'/uploaded.yml';
$name = 'test';
$b->
  get('/attachment/index')->
  isRequestParameter('module', 'attachment')->
  isRequestParameter('action', 'index')->
  isStatusCode(200)->
  click('submit', array('attachment' => array('name' => $name, 'file' => $fileToUpload)))->
  isRedirected()->
  followRedirect()->
  responseContains('ok')
;

$b->test()->ok(file_exists($uploadedFile), 'file is uploaded');
$b->test()->is(file_get_contents($uploadedFile), file_get_contents($fileToUpload), 'file is correctly uploaded');

$c = new Criteria();
$c->add(AttachmentPeer::NAME, $name);
$attachments = AttachmentPeer::doSelect($c);

$b->test()->is(count($attachments), 1, 'the attachment has been saved in the database');
$b->test()->is($attachments[0]->getFile(), 'uploaded.yml', 'the attachment filename has been saved in the database');

@unlink($uploadedFile);
AttachmentPeer::doDeleteAll();
$b->test()->ok(!file_exists($uploadedFile), 'uploaded file is deleted');

// file upload in embedded form
$b->
  getAndCheck('attachment', 'embedded')->
  with('response')->begin()->
    checkElement('input[name="article[attachment][article_id]"]', false)->
    checkElement('input[type="file"][name="article[attachment][file]"]')->
  end()->

  setField('article[title]', 'Test Article')->
  setField('article[attachment][name]', $name)->
  setField('article[attachment][file]', $fileToUpload)->
  click('submit')->

  with('form')->hasErrors(false)->

  isRedirected()->
  followRedirect()->

  with('response')->contains('ok')
;

$b->test()->ok(file_exists($uploadedFile), 'file is uploaded');
$b->test()->is(file_get_contents($uploadedFile), file_get_contents($fileToUpload), 'file is correctly uploaded');

$c = new Criteria();
$c->add(AttachmentPeer::NAME, $name);
$attachments = AttachmentPeer::doSelect($c);

$b->test()->is(count($attachments), 1, 'the attachment has been saved in the database');
$b->test()->ok($attachments[0]->getArticleId(), 'the attachment is tied to an article');
$b->test()->is($attachments[0]->getFile(), 'uploaded.yml', 'the attachment filename has been saved in the database');

// sfValidatorPropelUnique

// create a category with a unique name
$b->
  get('/unique/category')->
  isRequestParameter('module', 'unique')->
  isRequestParameter('action', 'category')->
  isStatusCode(200)->
  click('submit', array('category' => array('name' => 'foo')))->
  isRedirected()->
  followRedirect()->
  responseContains('ok')
;

// create another category with the same name
// we must have an error
$b->
  get('/unique/category')->
  isRequestParameter('module', 'unique')->
  isRequestParameter('action', 'category')->
  isStatusCode(200)->
  click('submit', array('category' => array('name' => 'foo')))->
  with('form')->begin()->
    hasErrors(1)->
    hasGlobalError(false)->
    isError('name', 'invalid')->
  end()->
  checkResponseElement('td[colspan="2"] .error_list li', 0)->
  checkResponseElement('.error_list li', 'An object with the same "name" already exist.')->
  checkResponseElement('.error_list li', 1)
;

// same thing but with a global error
$b->
  get('/unique/category')->
  isRequestParameter('module', 'unique')->
  isRequestParameter('action', 'category')->
  isStatusCode(200)->
  click('submit', array('category' => array('name' => 'foo'), 'global' => 1))->
  with('form')->begin()->
    hasErrors(1)->
    hasGlobalError('invalid')->
    isError('name', false)->
  end()->
  checkResponseElement('td[colspan="2"] .error_list li', 'An object with the same "name" already exist.')->
  checkResponseElement('td[colspan="2"] .error_list li', 1)
;

// updating the same category again with the same name is allowed
$b->
  get('/unique/category?category[id]='.CategoryPeer::getByName('foo')->getId())->
  isRequestParameter('module', 'unique')->
  isRequestParameter('action', 'category')->
  isStatusCode(200)->
  click('submit')->
  isRedirected()->
  followRedirect()->
  responseContains('ok')
;

// create an article with a unique title-category_id
$b->
  get('/unique/article')->
  isRequestParameter('module', 'unique')->
  isRequestParameter('action', 'article')->
  isStatusCode(200)->
  click('submit', array('article' => array('title' => 'foo', 'category_id' => 1)))->
  isRedirected()->
  followRedirect()->
  responseContains('ok')
;

// create another article with the same title but a different category_id
$b->
  get('/unique/article')->
  isRequestParameter('module', 'unique')->
  isRequestParameter('action', 'article')->
  isStatusCode(200)->
  click('submit', array('article' => array('title' => 'foo', 'category_id' => 2)))->
  isRedirected()->
  followRedirect()->
  responseContains('ok')
;

// create another article with the same title and category_id as the first one
// we must have an error
$b->
  get('/unique/article')->
  isRequestParameter('module', 'unique')->
  isRequestParameter('action', 'article')->
  isStatusCode(200)->
  click('submit', array('article' => array('title' => 'foo', 'category_id' => 1)))->
  checkResponseElement('.error_list li', 'An object with the same "title, category_id" already exist.')
;

// update the category from the article form
$b->
  get('/unique/edit')->
  isRequestParameter('module', 'unique')->
  isRequestParameter('action', 'edit')->
  isStatusCode(200)->
  checkResponseElement('input[value="foo title"]')->
  checkResponseElement('#article_category_id option[selected="selected"]', 1)->
  checkResponseElement('input[value="Category 1"]')->
  click('submit', array('article' => array('title' => 'foo bar', 'category' => array('name' => 'Category foo'))))->
  isRedirected()->
  followRedirect()->
  checkResponseElement('input[value="foo bar"]')->
  checkResponseElement('#article_category_id option[selected="selected"]', 1)->
  checkResponseElement('input[value="Category foo"]')
;

// sfValidatorPropelChoice

// submit a form with an impossible choice validator
$b->
  get('/choice/article')->
  with('request')->begin()->
    isParameter('module', 'choice')->
    isParameter('action', 'article')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  click('submit', array('article' => array('title' => 'foobar', 'category_id' => 1, 'author_article_list' => array(1)), 'impossible_validator' => 1))->
  with('form')->begin()->
    hasErrors(1)->
    isError('category_id', 'invalid')->
  end()
;

// sfValidatorPropelChoiceMany

// submit a form with an impossible choice validator
$b->
  get('/choice/article')->
  with('request')->begin()->
    isParameter('module', 'choice')->
    isParameter('action', 'article')->
  end()->
  with('response')->begin()->
    isStatusCode(200)->
  end()->
  click('submit', array('article' => array('title' => 'foobar', 'category_id' => 1, 'author_article_list' => array(1)), 'impossible_validator_many' => 1))->
  with('form')->begin()->
    hasErrors(1)->
    isError('author_article_list', 'invalid')->
  end()
;
