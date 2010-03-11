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

include(dirname(__FILE__).'/backendTestBrowser.class.php');

$b = new backendTestBrowser();

$b->
  post('/article/edit/id/1', array('article' => array('end_date' => 'not a date')))->
  isStatusCode(302)->
  isRequestParameter('module', 'article')->
  isRequestParameter('action', 'edit')->

  isRedirected(true)->
  followRedirect()->
  checkResponseElement('input[name="article[end_date]"][value=""]')
;

// non rich date (without time)
$tomorrow = time() + 86400 + 3600;
$b->
  customizeGenerator(array('edit' => array('display' => array('title', 'end_date'), 'fields' => array('end_date' => array('params' => 'withtime=false rich=false')))))->

  post('/article/edit/id/1', array('article' => array('end_date' => array('day' => date('d', $tomorrow), 'month' => date('m', $tomorrow), 'year' => date('Y', $tomorrow)))))->
  isStatusCode(302)->
  isRequestParameter('module', 'article')->
  isRequestParameter('action', 'edit')->

  isRedirected(true)->
  followRedirect()->
  checkResponseElement(sprintf('select[name="article[end_date][day]"] option[value="%s"][selected="selected"]', date('d', $tomorrow)))->
  checkResponseElement(sprintf('select[name="article[end_date][month]"] option[value="%s"][selected="selected"]', date('m', $tomorrow)))->
  checkResponseElement(sprintf('select[name="article[end_date][year]"] option[value="%s"][selected="selected"]', date('Y', $tomorrow)))->
  checkResponseElement('select[name="article[end_date][hour]"]', false)->
  checkResponseElement('select[name="article[end_date][minute]"]', false)->

  checkResponseElement('script[src*="calendar"]', false)->
  checkResponseElement('script[src]', false)->
  checkResponseElement('link[href*="calendar"]', false)->
  checkResponseElement('link[href][media]', 2)
;

// non rich date (with time)
$b->
  customizeGenerator(array('edit' => array('fields' => array('end_date' => array('params' => 'withtime=true rich=false')))))->

  post('/article/edit/id/1', array('article' => array('end_date' => array('day' => date('d', $tomorrow), 'month' => date('m', $tomorrow), 'year' => date('Y', $tomorrow), 'hour' => date('G', $tomorrow), 'minute' => date('i', $tomorrow)))))->
  isStatusCode(302)->
  isRequestParameter('module', 'article')->
  isRequestParameter('action', 'edit')->

  isRedirected(true)->
  followRedirect()->
  checkResponseElement(sprintf('select[name="article[end_date][day]"] option[value="%s"][selected="selected"]', date('d', $tomorrow)))->
  checkResponseElement(sprintf('select[name="article[end_date][month]"] option[value="%s"][selected="selected"]', date('m', $tomorrow)))->
  checkResponseElement(sprintf('select[name="article[end_date][year]"] option[value="%s"][selected="selected"]', date('Y', $tomorrow)))->
  checkResponseElement(sprintf('select[name="article[end_date][hour]"] option[value="%s"][selected="selected"]', date('G', $tomorrow)))->
  checkResponseElement(sprintf('select[name="article[end_date][minute]"] option[value="%s"][selected="selected"]', date('i', $tomorrow)))
;
