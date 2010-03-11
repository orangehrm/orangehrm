<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'backend_compat';
$fixtures = 'fixtures/fixtures.yml';
if (!include(dirname(__FILE__).'/../../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestBrowser();

// edit page
$b->
  get('/validation/edit/id/1')->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'edit')->

  // parameters
  isRequestParameter('id', 1)->

  // save
  click('save', array('article' => array('title' => '', 'body' => '')))->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'save')->

  // check error messages
  isRedirected(false)->

  checkResponseElement('.form-errors dt', 'Title:')->
  checkResponseElement('.form-errors dt', 'Body:', array('position' => 1))->
  checkResponseElement('#error_for_article_title', true)->
  checkResponseElement('#error_for_article_body', true)->

  // check form repopulation
  checkResponseElement('body form#sf_admin_edit_form input[name="article[title]"][id="article_title"][value=""]')->
  checkResponseElement('body form#sf_admin_edit_form textarea[name="article[body]"][id="article_body"]', '')->
  checkResponseElement('body form#sf_admin_edit_form input[name="article[online]"][id="article_online"][type="checkbox"][checked="checked"]', true)->
  checkResponseElement('body form#sf_admin_edit_form select[name="article[category_id]"][id="article_category_id"] option[value="1"][selected="selected"]')
;

$b->
  get('/validation/edit/id/2')->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'edit')->

  // parameters
  isRequestParameter('id', 2)->

  // save
  click('save', array('article' => array('title' => '', 'body' => '', 'online' => false)))->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'save')->

  // check form repopulation
  checkResponseElement('body form#sf_admin_edit_form input[name="article[title]"][id="article_title"][value=""]')->
  checkResponseElement('body form#sf_admin_edit_form textarea[name="article[body]"][id="article_body"]', '')->
  checkResponseElement('body form#sf_admin_edit_form input[name="article[online]"][id="article_online"][type="checkbox"][checked="checked"]', false)->
  checkResponseElement('body form#sf_admin_edit_form select[name="article[category_id]"][id="article_category_id"] option[value="2"][selected="selected"]')
;

$b->
  get('/validation/edit/id/2')->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'edit')->

  // parameters
  isRequestParameter('id', 2)->

  // save
  click('save', array('article' => array('title' => '', 'body' => '', 'online' => '1')))->
  isStatusCode(200)->
  isRequestParameter('module', 'validation')->
  isRequestParameter('action', 'save')->

  // check form repopulation
  checkResponseElement('body form#sf_admin_edit_form input[name="article[online]"][id="article_online"][type="checkbox"][checked="checked"]', true)
;
