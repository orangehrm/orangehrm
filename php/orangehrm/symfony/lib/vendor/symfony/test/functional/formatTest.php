<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$app = 'frontend';
if (!include(dirname(__FILE__).'/../bootstrap/functional.php'))
{
  return;
}

$b = new sfTestBrowser();

$b->
  get('/format_test.js')->
  isStatusCode(200)->
  isRequestParameter('module', 'format')->
  isRequestParameter('action', 'index')->
  isRequestFormat('js')->
  isResponseHeader('content-type', 'application/javascript')
;
$b->test()->unlike($b->getResponse()->getContent(), '/<body>/', 'response content is ok');
$b->test()->like($b->getResponse()->getContent(), '/Some js headers/', 'response content is ok');
$b->test()->like($b->getResponse()->getContent(), '/This is a js file/', 'response content is ok');

$b->
  get('/format_test.css')->
  isStatusCode(200)->
  isRequestParameter('module', 'format')->
  isRequestParameter('action', 'index')->
  isRequestFormat('css')->
  isResponseHeader('content-type', 'text/css; charset=utf-8')
;
$b->test()->is($b->getResponse()->getContent(), 'This is a css file', 'response content is ok');

$b->
  get('/format_test')->
  isStatusCode(200)->
  isRequestParameter('module', 'format')->
  isRequestParameter('action', 'index')->
  isRequestFormat('html')->
  isResponseHeader('content-type', 'text/html; charset=utf-8')->
  checkResponseElement('body #content', 'This is an HTML file')
;

$b->
  get('/format_test.xml')->
  isStatusCode(200)->
  isRequestParameter('module', 'format')->
  isRequestParameter('action', 'index')->
  isRequestFormat('xml')->
  isResponseHeader('content-type', 'text/xml; charset=utf-8')->
  checkResponseElement('sentences sentence:first', 'This is a XML file')
;

$b->
  get('/format_test.foo')->
  isStatusCode(200)->
  isRequestParameter('module', 'format')->
  isRequestParameter('action', 'index')->
  isRequestFormat('foo')->
  isResponseHeader('content-type', 'text/html; charset=utf-8')->
  isResponseHeader('x-foo', 'true')->
  checkResponseElement('body #content', 'This is an HTML file')
;

$b->
  get('/format/js')->
  isStatusCode(200)->
  isRequestParameter('module', 'format')->
  isRequestParameter('action', 'js')->
  isRequestFormat('js')->
  isResponseHeader('content-type', 'application/javascript')
;
$b->test()->is($b->getResponse()->getContent(), 'A js file', 'response content is ok');

$b->
  setHttpHeader('User-Agent', 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543a Safari/419.3')->
  get('/format/forTheIPhone')->
  isStatusCode(200)->
  isRequestParameter('module', 'format')->
  isRequestParameter('action', 'forTheIPhone')->
  isRequestFormat('iphone')->
  isResponseHeader('content-type', 'text/html; charset=utf-8')->
  checkResponseElement('#content', 'This is an HTML file for the iPhone')->
  checkResponseElement('link[href*="iphone.css"]')
;

$b->
  getAndCheck('format', 'throwsException', null, 500)->
  throwsException('Exception', '/message/')
;
