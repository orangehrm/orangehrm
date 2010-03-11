<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
$debug = false;
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestBrowser();

// default main page (without cache)
$b->
  get('/')->
  isStatusCode(200)->
  isRequestParameter('module', 'default')->
  isRequestParameter('action', 'index')->
  checkResponseElement('body', '/congratulations/i')
;

// default main page (with cache)
$b->
  get('/')->
  isStatusCode(200)->
  isRequestParameter('module', 'default')->
  isRequestParameter('action', 'index')->
  checkResponseElement('body', '/congratulations/i')
;

// 404
$b->
  get('/nonexistant')->
  isStatusCode(404)->
  isForwardedTo('default', 'error404')->
  checkResponseElement('body', '!/congratulations/i')->
  checkResponseElement('link[href="/sf/sf_default/css/screen.css"]')
;

$b->
  get('/nonexistant/')->
  isStatusCode(404)->
  isForwardedTo('default', 'error404')->
  checkResponseElement('body', '!/congratulations/i')->
  checkResponseElement('link[href="/sf/sf_default/css/screen.css"]')
;

// unexistant action
$b->
  get('/default/nonexistantaction')->
  isStatusCode(404)->
  isForwardedTo('default', 'error404')->
  checkResponseElement('body', '!/congratulations/i')->
  checkResponseElement('link[href="/sf/sf_default/css/screen.css"]')
;
