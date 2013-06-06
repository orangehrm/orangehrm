<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(83);

class myWebResponse extends sfWebResponse
{
  public function getStatusText()
  {
    return $this->statusText;
  }

  public function normalizeHeaderName($name)
  {
    return parent::normalizeHeaderName($name);
  }
}

$dispatcher = new sfEventDispatcher();

// ->initialize()
$t->diag('->initialize()');
$response = new myWebResponse($dispatcher, array('charset' => 'ISO-8859-1'));
$t->is($response->getContentType(), 'text/html; charset=ISO-8859-1', '->initialize() takes a "charset" option');
$response = new myWebResponse($dispatcher, array('content_type' => 'text/plain'));
$t->is($response->getContentType(), 'text/plain; charset=utf-8', '->initialize() takes a "content_type" option');

$response = new myWebResponse($dispatcher);

// ->getStatusCode() ->setStatusCode()
$t->diag('->getStatusCode() ->setStatusCode()');
$t->is($response->getStatusCode(), 200, '->getStatusCode() returns 200 by default');
$response->setStatusCode(404);
$t->is($response->getStatusCode(), 404, '->setStatusCode() sets status code');
$t->is($response->getStatusText(), 'Not Found', '->setStatusCode() also sets the status text associated with the status code if no message is given');
$response->setStatusCode(404, 'my text');
$t->is($response->getStatusText(), 'my text', '->setStatusCode() takes a message as its second argument as the status text');
$response->setStatusCode(404, '');
$t->is($response->getStatusText(), '', '->setStatusCode() takes a message as its second argument as the status text');

// ->hasHttpHeader()
$t->diag('->hasHttpHeader()');
$t->is($response->hasHttpHeader('non-existant'), false, '->hasHttpHeader() returns false if http header is not set');
$response->setHttpHeader('My-Header', 'foo');
$t->is($response->hasHttpHeader('My-Header'), true, '->hasHttpHeader() returns true if http header is not set');
$t->is($response->hasHttpHeader('my-header'), true, '->hasHttpHeader() normalizes http header name');

// ->getHttpHeader()
$t->diag('->getHttpHeader()');
$response->setHttpHeader('My-Header', 'foo');
$t->is($response->getHttpHeader('My-Header'), 'foo', '->getHttpHeader() returns the current http header values');
$t->is($response->getHttpHeader('my-header'), 'foo', '->getHttpHeader() normalizes http header name');

// ->setHttpHeader()
$t->diag('->setHttpHeader()');
$response->setHttpHeader('My-Header', 'foo');
$response->setHttpHeader('My-Header', 'bar', false);
$response->setHttpHeader('my-header', 'foobar', false);
$t->is($response->getHttpHeader('My-Header'), 'foo, bar, foobar', '->setHttpHeader() takes a replace argument as its third argument');
$response->setHttpHeader('My-Other-Header', 'foo', false);
$t->is($response->getHttpHeader('My-Other-Header'), 'foo', '->setHttpHeader() takes a replace argument as its third argument');

$response->setHttpHeader('my-header', 'foo');
$t->is($response->getHttpHeader('My-Header'), 'foo', '->setHttpHeader() normalizes http header name');

// ->clearHttpHeaders()
$t->diag('->clearHttpHeaders()');
$response->setHttpHeader('my-header', 'foo');
$response->clearHttpHeaders();
$t->is($response->getHttpHeader('My-Header'), '', '->clearHttpHeaders() clears all current http headers');

// ->getHttpHeaders()
$t->diag('->getHttpHeaders()');
$response->clearHttpHeaders();
$response->setHttpHeader('my-header', 'foo');
$response->setHttpHeader('my-header', 'bar', false);
$response->setHttpHeader('another', 'foo');
$t->is($response->getHttpHeaders(), array('My-Header' => 'foo, bar', 'Another' => 'foo'), '->getHttpHeaders() return all current response http headers');

// ->normalizeHeaderName()
$t->diag('->normalizeHeaderName()');
foreach (array(
  array('header', 'Header'),
  array('HEADER', 'Header'),
  array('hEaDeR', 'Header'),
  array('my-header', 'My-Header'),
  array('my_header', 'My-Header'),
  array('MY_HEADER', 'My-Header'),
  array('my-header_is_very-long', 'My-Header-Is-Very-Long'),
  array('Content-Type', 'Content-Type'),
  array('content-type', 'Content-Type'),
) as $test)
{
  $t->is($response->normalizeHeaderName($test[0]), $test[1], '->normalizeHeaderName() normalizes http header name');
}

// ->getContentType() ->setContentType()
$t->diag('->getContentType() ->setContentType() ->getCharset()');

$response = new myWebResponse($dispatcher);
$t->is($response->getContentType(), 'text/html; charset=utf-8', '->getContentType() returns a sensible default value');
$t->is($response->getCharset(), 'utf-8', '->getCharset() returns the current charset of the response');

$response->setContentType('text/xml');
$t->is($response->getContentType(), 'text/xml; charset=utf-8', '->setContentType() adds a charset if none is given');

$response->setContentType('application/vnd.mozilla.xul+xml');
$t->is($response->getContentType(), 'application/vnd.mozilla.xul+xml; charset=utf-8', '->setContentType() adds a charset if none is given');
$t->is($response->getCharset(), 'utf-8', '->getCharset() returns the current charset of the response');

$response->setContentType('image/jpg');
$t->is($response->getContentType(), 'image/jpg', '->setContentType() does not add a charset if the content-type is not text/*');

$response->setContentType('text/xml; charset=ISO-8859-1');
$t->is($response->getContentType(), 'text/xml; charset=ISO-8859-1', '->setContentType() does nothing if a charset is given');
$t->is($response->getCharset(), 'ISO-8859-1', '->getCharset() returns the current charset of the response');

$response->setContentType('text/xml;charset = ISO-8859-1');
$t->is($response->getContentType(), 'text/xml;charset = ISO-8859-1', '->setContentType() does nothing if a charset is given');
$t->is($response->getCharset(), 'ISO-8859-1', '->getCharset() returns the current charset of the response');

$t->is($response->getContentType(), $response->getHttpHeader('content-type'), '->getContentType() is an alias for ->getHttpHeader(\'content-type\')');

$response->setContentType('text/xml');
$response->setContentType('text/html');
$t->is(count($response->getHttpHeader('content-type')), 1, '->setContentType() overrides previous content type if replace is true');

// ->getTitle() ->setTitle()
$t->diag('->getTitle() ->setTitle()');
$t->is($response->getTitle(), '', '->getTitle() returns an empty string by default');
$response->setTitle('my title');
$t->is($response->getTitle(), 'my title', '->setTitle() sets the title');
$response->setTitle('fööbäär');
$t->is($response->getTitle(), 'fööbäär', '->setTitle() will leave encoding intact');

// ->addHttpMeta()
$t->diag('->addHttpMeta()');
$response->clearHttpHeaders();
$response->addHttpMeta('My-Header', 'foo');
$response->addHttpMeta('My-Header', 'bar', false);
$response->addHttpMeta('my-header', 'foobar', false);
$metas = $response->getHttpMetas();
$t->is($metas['My-Header'], 'foo, bar, foobar', '->addHttpMeta() takes a replace argument as its third argument');
$t->is($response->getHttpHeader('My-Header'), 'foo, bar, foobar', '->addHttpMeta() also sets the corresponding http header');
$response->addHttpMeta('My-Other-Header', 'foo', false);
$metas = $response->getHttpMetas();
$t->is($metas['My-Other-Header'], 'foo', '->addHttpMeta() takes a replace argument as its third argument');
$response->addHttpMeta('my-header', 'foo');
$metas = $response->getHttpMetas();
$t->is($metas['My-Header'], 'foo', '->addHttpMeta() normalizes http header name');

// ->addVaryHttpHeader()
$t->diag('->addVaryHttpHeader()');
$response->clearHttpHeaders();
$response->addVaryHttpHeader('Cookie');
$t->is($response->getHttpHeader('Vary'), 'Cookie', '->addVaryHttpHeader() adds a new Vary header');
$response->addVaryHttpHeader('Cookie');
$t->is($response->getHttpHeader('Vary'), 'Cookie', '->addVaryHttpHeader() does not add the same header twice');
$response->addVaryHttpHeader('Accept-Language');
$t->is($response->getHttpHeader('Vary'), 'Cookie, Accept-Language', '->addVaryHttpHeader() respects ordering');

// ->addCacheControlHttpHeader()
$t->diag('->addCacheControlHttpHeader()');
$response->clearHttpHeaders();
$response->addCacheControlHttpHeader('max-age', 0);
$t->is($response->getHttpHeader('Cache-Control'), 'max-age=0', '->addCacheControlHttpHeader() adds a new Cache-Control header');
$response->addCacheControlHttpHeader('max-age', 12);
$t->is($response->getHttpHeader('Cache-Control'), 'max-age=12', '->addCacheControlHttpHeader() does not add the same header twice');
$response->addCacheControlHttpHeader('no-cache');
$t->is($response->getHttpHeader('Cache-Control'), 'max-age=12, no-cache', '->addCacheControlHttpHeader() respects ordering');

// ->copyProperties()
$t->diag('->copyProperties()');
$response1 = new myWebResponse($dispatcher);
$response2 = new myWebResponse($dispatcher);

$response1->setHttpHeader('symfony', 'foo');
$response1->setContentType('text/plain');
$response1->setTitle('My title');

$response2->copyProperties($response1);
$t->is($response1->getHttpHeader('symfony'), $response2->getHttpHeader('symfony'), '->copyProperties() merges http headers');
$t->is($response1->getContentType(), $response2->getContentType(), '->copyProperties() merges content type');
$t->is($response1->getTitle(), $response2->getTitle(), '->copyProperties() merges titles');

// ->addStylesheet()
$t->diag('->addStylesheet()');
$response = new myWebResponse($dispatcher);
$response->addStylesheet('test');
$t->ok(array_key_exists('test', $response->getStylesheets()), '->addStylesheet() adds a new stylesheet for the response');
$response->addStylesheet('foo', '');
$t->ok(array_key_exists('foo', $response->getStylesheets()), '->addStylesheet() adds a new stylesheet for the response');
$response->addStylesheet('first', 'first');
$t->ok(array_key_exists('first', $response->getStylesheets('first')), '->addStylesheet() takes a position as its second argument');
$response->addStylesheet('last', 'last');
$t->ok(array_key_exists('last', $response->getStylesheets('last')), '->addStylesheet() takes a position as its second argument');
$response->addStylesheet('bar', '', array('media' => 'print'));
$stylesheets = $response->getStylesheets();
$t->is($stylesheets['bar'], array('media' => 'print'), '->addStylesheet() takes an array of parameters as its third argument');

try
{
  $response->addStylesheet('last', 'none');
  $t->fail('->addStylesheet() throws an InvalidArgumentException if the position is not first, the empty string, or last');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->addStylesheet() throws an InvalidArgumentException if the position is not first, the empty string, or last');
}

// ->getStylesheets()
$t->diag('->getStylesheets()');
$t->is(array_keys($response->getStylesheets()), array('first', 'test', 'foo', 'bar', 'last'), '->getStylesheets() returns all current registered stylesheets ordered by position');
$t->is($response->getStylesheets(''), array('test' => array(), 'foo' => array(), 'bar' => array('media' => 'print')), '->getStylesheets() takes a position as its first argument');
$t->is($response->getStylesheets('first'), array('first' => array()), '->getStylesheets() takes a position as its first argument');
$t->is($response->getStylesheets('last'), array('last' => array()), '->getStylesheets() takes a position as its first argument');

$t->diag('->removeStylesheet()');
$response->removeStylesheet('foo');
$t->is(array_keys($response->getStylesheets()), array('first', 'test', 'bar', 'last'), '->getStylesheets() removes a stylesheet from the response');

$response->removeStylesheet('first');
$t->is(array_keys($response->getStylesheets()), array('test', 'bar', 'last'), '->getStylesheets() removes a stylesheet from the response');

// ->addJavascript()
$t->diag('->addJavascript()');
$response = new myWebResponse($dispatcher);
$response->addJavascript('test');
$t->ok(array_key_exists('test', $response->getJavascripts()), '->addJavascript() adds a new javascript for the response');
$response->addJavascript('foo', '', array('raw_name' => true));
$t->ok(array_key_exists('foo', $response->getJavascripts()), '->addJavascript() adds a new javascript for the response');
$response->addJavascript('first_js', 'first');
$t->ok(array_key_exists('first_js', $response->getJavascripts('first')), '->addJavascript() takes a position as its second argument');
$response->addJavascript('last_js', 'last');
$t->ok(array_key_exists('last_js', $response->getJavascripts('last')), '->addJavascript() takes a position as its second argument');

try
{
  $response->addJavascript('last_js', 'none');
  $t->fail('->addJavascript() throws an InvalidArgumentException if the position is not first, the empty string, or last');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->addJavascript() throws an InvalidArgumentException if the position is not first, the empty string, or last');
}

// ->getJavascripts()
$t->diag('->getJavascripts()');
$t->is(array_keys($response->getJavascripts()), array('first_js', 'test', 'foo', 'last_js'), '->getJavascripts() returns all current registered javascripts ordered by position');
$t->is($response->getJavascripts(''), array('test' => array(), 'foo' => array('raw_name' => true)), '->getJavascripts() takes a position as its first argument');
$t->is($response->getJavascripts('first'), array('first_js' => array()), '->getJavascripts() takes a position as its first argument');
$t->is($response->getJavascripts('last'), array('last_js' => array()), '->getJavascripts() takes a position as its first argument');

$t->diag('->removeJavascript()');
$response->removeJavascript('test');
$t->is(array_keys($response->getJavascripts()), array('first_js', 'foo', 'last_js'), '->removeJavascripts() removes a javascript file');

$response->removeJavascript('first_js');
$t->is(array_keys($response->getJavascripts()), array('foo', 'last_js'), '->removeJavascripts() removes a javascript file');

// ->setCookie() ->getCookies()
$t->diag('->setCookie() ->getCookies()');
$response->setCookie('foo', 'bar');
$t->is($response->getCookies(), array('foo' => array('name' => 'foo', 'value' => 'bar', 'expire' => null, 'path' => '/', 'domain' => '', 'secure' => false, 'httpOnly' => false)), '->setCookie() adds a cookie for the response');

// ->setHeaderOnly() ->getHeaderOnly()
$t->diag('->setHeaderOnly() ->isHeaderOnly()');
$response = new myWebResponse($dispatcher);
$t->is($response->isHeaderOnly(), false, '->isHeaderOnly() returns false if the content must be send to the client');
$response->setHeaderOnly(true);
$t->is($response->isHeaderOnly(), true, '->setHeaderOnly() changes the current value of header only');

// ->sendContent()
$t->diag('->sendContent()');
$response->setHeaderOnly(true);
$response->setContent('foo');
ob_start();
$response->sendContent();
$t->is(ob_get_clean(), '', '->sendContent() returns nothing if headerOnly is true');

$response->setHeaderOnly(false);
$response->setContent('foo');
ob_start();
$response->sendContent();
$t->is(ob_get_clean(), 'foo', '->sendContent() returns the response content if headerOnly is false');

// ->serialize() ->unserialize()
$t->diag('->serialize() ->unserialize()');
$resp = unserialize(serialize($response));
$resp->initialize($dispatcher);
$t->ok($response == $resp, 'sfWebResponse implements the Serializable interface');
