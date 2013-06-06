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

class TestBrowser extends sfTestBrowser
{
  public $events = array();
  public function listen(sfEvent $event)
  {
    $this->events[] = $event;
  }
}

$b = new TestBrowser();
$b->addListener('context.load_factories', array($b, 'listen'));

// listeners
$b->get('/');
$b->test()->is(count($b->events), 1, 'browser can connect to context.load_factories');

// exceptions
$b->
  get('/exception/noException')->
  with('request')->begin()->
    isParameter('module', 'exception')->
    isParameter('action', 'noException')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    matches('/foo/')->
  end()->

  get('/exception/throwsException')->
  with('request')->begin()->
    isParameter('module', 'exception')->
    isParameter('action', 'throwsException')->
  end()->
  with('response')->isStatusCode(500)->
  throwsException('Exception')->

  get('/exception/throwsException')->
  with('request')->begin()->
    isParameter('module', 'exception')->
    isParameter('action', 'throwsException')->
  end()->
  with('response')->isStatusCode(500)->
  throwsException('Exception', '/Exception message/')->

  get('/exception/throwsException')->
  with('request')->begin()->
    isParameter('module', 'exception')->
    isParameter('action', 'throwsException')->
  end()->
  with('response')->isStatusCode(500)->
  throwsException('Exception', '/message/')->

  get('/exception/throwsException')->
  with('request')->begin()->
    isParameter('module', 'exception')->
    isParameter('action', 'throwsException')->
  end()->
  with('response')->isStatusCode(500)->
  throwsException(null, '!/sfException/')->

  get('/exception/throwsSfException')->
  with('request')->begin()->
    isParameter('module', 'exception')->
    isParameter('action', 'throwsSfException')->
  end()->
  with('response')->isStatusCode(500)->
  throwsException('sfException')->

  get('/exception/throwsSfException')->
  with('request')->begin()->
    isParameter('module', 'exception')->
    isParameter('action', 'throwsSfException')->
  end()->
  with('response')->isStatusCode(500)->
  throwsException('sfException', 'sfException message')
;

$b->
  get('/browser')->
  with('response')->begin()->
    matches('/html/')->
    checkElement('h1', 'html')->
  end()->

  get('/browser/text')->
  with('response')->begin()->
    matches('/text/')->
  end()
;

try
{
  $b->with('response')->checkElement('h1', 'text');
  $b->test()->fail('The DOM is not accessible if the response content type is not HTML');
}
catch (LogicException $e)
{
  $b->test()->pass('The DOM is not accessible if the response content type is not HTML');
}

// check response headers
$b->
  get('/browser/responseHeader')->
  with('response')->begin()->
    isStatusCode()->
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
  with('response')->checkElement('p', 'bar.foo-')->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo')->
    isCookie('foo', 'bar')->
    isCookie('foo', '/a/')->
    isCookie('foo', '!/z/')->
  end()->
  with('response')->checkElement('p', 'bar.foo-')->
  removeCookie('foo')->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar')->
  end()->
  with('response')->checkElement('p', '.foo-')->
  clearCookies()->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar', false)->
  end()->
  with('response')->checkElement('p', '.-')
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
  with('response')->checkElement('p', 'bar.foo-barfoo')->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo')->
    isCookie('foo', 'bar')->
    isCookie('foo', '/a/')->
    isCookie('foo', '!/z/')->
  end()->
  with('response')->checkElement('p', 'bar.foo-barfoo')->
  removeCookie('foo')->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar')->
  end()->
  with('response')->checkElement('p', '.foo-barfoo')->

  get('/cookie/removeCookie')->

  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar')->
  end()->
  with('response')->checkElement('p', '.foo-')->

  get('/cookie/setCookie')->

  clearCookies()->
  get('/cookie')->
  with('request')->begin()->
    hasCookie('foo', false)->
    hasCookie('bar', false)->
  end()->
  with('response')->checkElement('p', '.-')
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
  with('response')->checkElement('#test', 'template')->

  get('/browser/templateCustom/custom/1')->
  with('response')->checkElement('#test', 'template 1')->

  get('/browser/templateCustom')->
  with('response')->checkElement('#test', 'template')
;

$b
  ->getAndCheck('browser', 'redirect1', null, 302)

  ->followRedirect()

  ->with('request')->begin()
    ->isParameter('module', 'browser')
    ->isParameter('action', 'redirectTarget1')
  ->end()

  ->with('response')->isStatusCode(200)

  ->getAndCheck('browser', 'redirect2', null, 302)

  ->followRedirect()

  ->with('request')->begin()
    ->isParameter('module', 'browser')
    ->isParameter('action', 'redirectTarget2')
  ->end()

  ->with('response')->isStatusCode(200)
;
