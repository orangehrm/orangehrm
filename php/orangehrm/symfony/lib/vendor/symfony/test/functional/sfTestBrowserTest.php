<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
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

// exceptions
$b->
  get('/exception/noException')->
  isStatusCode(200)->
  isRequestParameter('module', 'exception')->
  isRequestParameter('action', 'noException')->
  responseContains('foo')->

  get('/exception/throwsException')->
  isStatusCode(500)->
  isRequestParameter('module', 'exception')->
  isRequestParameter('action', 'throwsException')->
  throwsException('Exception')->

  get('/exception/throwsException')->
  isStatusCode(500)->
  isRequestParameter('module', 'exception')->
  isRequestParameter('action', 'throwsException')->
  throwsException('Exception', '/Exception message/')->

  get('/exception/throwsException')->
  isStatusCode(500)->
  isRequestParameter('module', 'exception')->
  isRequestParameter('action', 'throwsException')->
  throwsException('Exception', '/message/')->

  get('/exception/throwsException')->
  isStatusCode(500)->
  isRequestParameter('module', 'exception')->
  isRequestParameter('action', 'throwsException')->
  throwsException(null, '!/sfException/')->

  get('/exception/throwsSfException')->
  isStatusCode(500)->
  isRequestParameter('module', 'exception')->
  isRequestParameter('action', 'throwsSfException')->
  throwsException('sfException')->

  get('/exception/throwsSfException')->
  isStatusCode(500)->
  isRequestParameter('module', 'exception')->
  isRequestParameter('action', 'throwsSfException')->
  throwsException('sfException', 'sfException message')
;

$b->
  get('/browser')->
  responseContains('html')->
  checkResponseElement('h1', 'html')->

  get('/browser/text')->
  responseContains('text')
;

try
{
  $b->checkResponseElement('h1', 'text');
  $b->test()->fail('The DOM is not accessible if the response content type is not HTML');
}
catch (LogicException $e)
{
  $b->test()->pass('The DOM is not accessible if the response content type is not HTML');
}

// check response headers
$b->
  get('/browser/responseHeader')->
  isStatusCode()->
  with('response')->begin()->
    isHeader('content-type', 'text/plain; charset=utf-8')->
    isHeader('content-type', '#text/plain#')->
    isHeader('content-type', '!#text/html#')->
    isHeader('foo', 'bar')->
    isHeader('foo', 'foobar')->
  end()
;

// cookies
$b->
  setCookie('foo', 'bar')->
  setCookie('bar', 'foo')->
  setCookie('foofoo', 'foo', time() - 10)->

  get('/cookie')->
  with('request')->begin()->
    hasCookie('foofoo', false)->
    hasCookie('foo')->
    isCookie('foo', 'bar')->
    isCookie('foo', '/a/')->
    isCookie('foo', '!/z/')->
  end()->
  checkResponseElement('p', 'bar.foo-')->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo')->
    isCookie('foo', 'bar')->
    isCookie('foo', '/a/')->
    isCookie('foo', '!/z/')->
  end()->
  checkResponseElement('p', 'bar.foo-')->
  removeCookie('foo')->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar')->
  end()->
  checkResponseElement('p', '.foo-')->
  clearCookies()->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar', false)->
  end()->
  checkResponseElement('p', '.-')
;

$b->
  setCookie('foo', 'bar')->
  setCookie('bar', 'foo')->

  get('/cookie/setCookie')->

  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo')->
    isCookie('foo', 'bar')->
    isCookie('foo', '/a/')->
    isCookie('foo', '!/z/')->
  end()->
  checkResponseElement('p', 'bar.foo-barfoo')->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo')->
    isCookie('foo', 'bar')->
    isCookie('foo', '/a/')->
    isCookie('foo', '!/z/')->
  end()->
  checkResponseElement('p', 'bar.foo-barfoo')->
  removeCookie('foo')->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar')->
  end()->
  checkResponseElement('p', '.foo-barfoo')->

  get('/cookie/removeCookie')->

  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar')->
  end()->
  checkResponseElement('p', '.foo-')->

  get('/cookie/setCookie')->

  clearCookies()->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar', false)->
  end()->
  checkResponseElement('p', '.-')
;

$b->
  get('/browser')->
  with('request')->isMethod('get')->
  post('/browser')->
  with('request')->isMethod('post')->
  call('/browser', 'put')->
  with('request')->isMethod('put')
;

// sfBrowser: clean the custom view templates
$b->
  get('/browser/templateCustom')->
  checkResponseElement('#test', 'template')->

  get('/browser/templateCustom/custom/1')->
  checkResponseElement('#test', 'template 1')->

  get('/browser/templateCustom')->
  checkResponseElement('#test', 'template')
;
