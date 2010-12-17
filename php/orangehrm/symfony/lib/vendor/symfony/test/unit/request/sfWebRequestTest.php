<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(71);

class myRequest extends sfWebRequest
{
  static protected $initialPathArrayKeys;

  public $languages = null;
  public $charsets = null;
  public $acceptableContentTypes = null;

  public function initialize(sfEventDispatcher $dispatcher, $parameters = array(), $attributes = array(), $options = array())
  {
    parent::initialize($dispatcher, $parameters, $attributes, $options);

    if (null === self::$initialPathArrayKeys)
    {
      self::$initialPathArrayKeys = array_keys($this->getPathInfoArray());
    }

    $this->resetPathInfoArray();
  }

  public function setOption($key, $value)
  {
    $this->options[$key] = $value;
  }

  public function resetPathInfoArray()
  {
    foreach (array_diff(array_keys($this->getPathInfoArray()), self::$initialPathArrayKeys) as $key)
    {
      unset($this->pathInfoArray[$key]);
    }
  }
}

$dispatcher = new sfEventDispatcher();
$request = new myRequest($dispatcher);

// ->getLanguages()
$t->diag('->getLanguages()');

$t->is($request->getLanguages(), array(), '->getLanguages() returns an empty array if the client do not send an ACCEPT_LANGUAGE header');

$request->languages = null;
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = '';
$t->is($request->getLanguages(), array(), '->getLanguages() returns an empty array if the client send an empty ACCEPT_LANGUAGE header');

$request->languages = null;
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-us,en;q=0.5,fr;q=0.3';
$t->is($request->getLanguages(), array('en_US', 'en', 'fr'), '->getLanguages() returns an array with all accepted languages');

// ->getPreferredCulture()
$t->diag('->getPreferredCulture()');

$request->languages = null;
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = '';
$t->is($request->getPreferredCulture(array('fr', 'en')), 'fr', '->getPreferredCulture() returns the first given culture if the client do not send an ACCEPT_LANGUAGE header');

$request->languages = null;
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-us,en;q=0.5,fr;q=0.3';
$t->is($request->getPreferredCulture(array('fr', 'en')), 'en', '->getPreferredCulture() returns the preferred culture');

$request->languages = null;
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-us,en;q=0.5,fr';
$t->is($request->getPreferredCulture(array('fr', 'en')), 'fr', '->getPreferredCulture() returns the preferred culture');

// ->getCharsets()
$t->diag('->getCharsets()');

$t->is($request->getCharsets(), array(), '->getCharsets() returns an empty array if the client do not send an ACCEPT_CHARSET header');

$request->charsets = null;
$_SERVER['HTTP_ACCEPT_CHARSET'] = '';
$t->is($request->getCharsets(), array(), '->getCharsets() returns an empty array if the client send an empty ACCEPT_CHARSET header');

$request->charsets = null;
$_SERVER['HTTP_ACCEPT_CHARSET'] = 'ISO-8859-1,utf-8;q=0.7,*;q=0.3';
$t->is($request->getCharsets(), array('ISO-8859-1', 'utf-8', '*'), '->getCharsets() returns an array with all accepted charsets');

// ->getAcceptableContentTypes()
$t->diag('->getAcceptableContentTypes()');

$t->is($request->getAcceptableContentTypes(), array(), '->getAcceptableContentTypes() returns an empty array if the client do not send an ACCEPT header');

$request->acceptableContentTypes = null;
$_SERVER['HTTP_ACCEPT'] = '';
$t->is($request->getAcceptableContentTypes(), array(), '->getAcceptableContentTypes() returns an empty array if the client send an empty ACCEPT header');

$request->acceptableContentTypes = null;
$_SERVER['HTTP_ACCEPT'] = 'text/xml,application/xhtml+xml,application/xml,text/html;q=0.9,text/plain;q=0.8,*/*;q=0.5';
$t->is($request->getAcceptableContentTypes(), array('text/xml', 'application/xml', 'application/xhtml+xml', 'text/html', 'text/plain', '*/*'), '->getAcceptableContentTypes() returns an array with all accepted content types');

// ->splitHttpAcceptHeader()
$t->diag('->splitHttpAcceptHeader()');

$t->is($request->splitHttpAcceptHeader(''), array(), '->splitHttpAcceptHeader() returns an empty array if the header is empty');
$t->is($request->splitHttpAcceptHeader('a,b,c'), array('c', 'b', 'a'), '->splitHttpAcceptHeader() returns an array of values');
$t->is($request->splitHttpAcceptHeader('a,b;q=0.7,c;q=0.3'), array('a', 'b', 'c'), '->splitHttpAcceptHeader() strips the q value');
$t->is($request->splitHttpAcceptHeader('a;q=0.1,b,c;q=0.3'), array('b', 'c', 'a'), '->splitHttpAcceptHeader() sorts values by the q value');
$t->is($request->splitHttpAcceptHeader('a; q=0.1, b, c; q=0.3'), array('b', 'c', 'a'), '->splitHttpAcceptHeader() trims whitespaces');
$t->is($request->splitHttpAcceptHeader('a; q=0, b'), array('b'), '->splitHttpAcceptHeader() removes values when q = 0 (as per the RFC)');

// ->getRequestFormat() ->setRequestFormat()
$t->diag('->getRequestFormat() ->setRequestFormat()');

$t->ok(is_null($request->getRequestFormat()), '->getRequestFormat() returns null if the format is not defined in the request');
$request->setParameter('sf_format', 'js');
$t->is($request->getRequestFormat(), 'js', '->getRequestFormat() returns the request format');

$request->setRequestFormat('css');
$t->is($request->getRequestFormat(), 'css', '->setRequestFormat() sets the request format');

// ->getFormat() ->setFormat()
$t->diag('->getFormat() ->setFormat()');

$request->setFormat('js', 'application/x-javascript');
$t->is($request->getFormat('application/x-javascript'), 'js', '->getFormat() returns the format for the given mime type');
$request->setFormat('js', array('application/x-javascript', 'text/js'));
$t->is($request->getFormat('text/js'), 'js', '->setFormat() can take an array of mime types');
$t->is($request->getFormat('foo/bar'), null, '->getFormat() returns null if the mime type does not exist');

// ->getMimeType()
$t->diag('->getMimeType()');

$t->is($request->getMimeType('js'), 'application/x-javascript', '->getMimeType() returns the first mime type for the given format');
$t->is($request->getMimeType('foo'), null, '->getMimeType() returns null if the format does not exist');

// ->isSecure()
$t->diag('->isSecure()');

$t->is($request->isSecure(), false, '->isSecure() returns false if request is not secure');

$_SERVER['HTTPS'] = 'ON';
$t->is($request->isSecure(), true, '->isSecure() checks the "HTTPS" environment variable');
$_SERVER['HTTPS'] = 'on';
$t->is($request->isSecure(), true, '->isSecure() checks the "HTTPS" environment variable');
$_SERVER['HTTPS'] = '1';
$t->is($request->isSecure(), true, '->isSecure() checks the "HTTPS" environment variable');
$request->resetPathInfoArray();

$_SERVER['HTTP_SSL_HTTPS'] = 'ON';
$t->is($request->isSecure(), true, '->isSecure() checks the "HTTP_SSL_HTTPS" environment variable');
$_SERVER['HTTP_SSL_HTTPS'] = 'on';
$t->is($request->isSecure(), true, '->isSecure() checks the "HTTP_SSL_HTTPS" environment variable');
$_SERVER['HTTP_SSL_HTTPS'] = '1';
$t->is($request->isSecure(), true, '->isSecure() checks the "HTTP_SSL_HTTPS" environment variable');
$request->resetPathInfoArray();

$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
$t->is($request->isSecure(), true, '->isSecure() checks the "HTTP_X_FORWARDED_PROTO" environment variable');
$request->resetPathInfoArray();

// ->getUriPrefix()
$t->diag('->getUriPrefix()');

$request->resetPathInfoArray();
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTP_HOST'] = 'symfony-project.org:80';
$t->is($request->getUriPrefix(), 'http://symfony-project.org', '->getUriPrefix() returns no port for standard http port');
$_SERVER['HTTP_HOST'] = 'symfony-project.org';
$t->is($request->getUriPrefix(), 'http://symfony-project.org', '->getUriPrefix() works fine with no port in HTTP_HOST');
$_SERVER['HTTP_HOST'] = 'symfony-project.org:8088';
$t->is($request->getUriPrefix(), 'http://symfony-project.org:8088', '->getUriPrefix() works for nonstandard http ports');

$request->resetPathInfoArray();
$_SERVER['HTTPS'] = 'on';
$_SERVER['SERVER_PORT'] = '443';
$_SERVER['HTTP_HOST'] = 'symfony-project.org:443';
$t->is($request->getUriPrefix(), 'https://symfony-project.org', '->getUriPrefix() returns no port for standard https port');
$_SERVER['HTTP_HOST'] = 'symfony-project.org';
$t->is($request->getUriPrefix(), 'https://symfony-project.org', '->getUriPrefix() works fine with no port in HTTP_HOST');
$_SERVER['HTTP_HOST'] = 'symfony-project.org:8043';
$t->is($request->getUriPrefix(), 'https://symfony-project.org:8043', '->getUriPrefix() works for nonstandard https ports');

$request->resetPathInfoArray();
$_SERVER['HTTP_HOST'] = 'symfony-project.org';
$_SERVER['SERVER_PORT'] = '8080';
$t->is($request->getUriPrefix(), 'http://symfony-project.org:8080', '->getUriPrefix() uses the "SERVER_PORT" environment variable');

$request->resetPathInfoArray();
$_SERVER['HTTPS'] = 'on';
$_SERVER['HTTP_HOST'] = 'symfony-project.org';
$_SERVER['SERVER_PORT'] = '8043';
$t->is($request->getUriPrefix(), 'https://symfony-project.org:8043', '->getUriPrefix() uses the "SERVER_PORT" environment variable');

$request->resetPathInfoArray();
$request->setOption('http_port', '8080');
$_SERVER['HTTP_HOST'] = 'symfony-project.org';
$t->is($request->getUriPrefix(), 'http://symfony-project.org:8080', '->getUriPrefix() uses the configured port');
$request->setOption('http_port', null);

$request->resetPathInfoArray();
$request->setOption('https_port', '8043');
$_SERVER['HTTPS'] = 'on';
$_SERVER['HTTP_HOST'] = 'symfony-project.org';
$t->is($request->getUriPrefix(), 'https://symfony-project.org:8043', '->getUriPrefix() uses the configured port');
$request->setOption('https_port', null);

$request->resetPathInfoArray();
$_SERVER['HTTP_HOST'] = 'symfony-project.org';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
$t->is($request->getUriPrefix(), 'https://symfony-project.org', '->getUriPrefix() works on secure requests forwarded as non-secure requests');

$request->resetPathInfoArray();
$request->setOption('https_port', '8043');
$_SERVER['HTTP_HOST'] = 'symfony-project.org';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
$t->is($request->getUriPrefix(), 'https://symfony-project.org:8043', '->getUriPrefix() uses the configured port on secure requests forwarded as non-secure requests');

$request->resetPathInfoArray();

// ->getRemoteAddress()
$t->diag('->getRemoteAddress()');

$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$t->is($request->getRemoteAddress(), '127.0.0.1', '->getRemoteAddress() returns the remote address');

// ->getForwardedFor()
$t->diag('->getForwardedFor()');

$t->is($request->getForwardedFor(), null, '->getForwardedFor() returns null if the request was not forwarded.');
$_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.1, 10.0.0.2';
$t->is_deeply($request->getForwardedFor(), array('10.0.0.1', '10.0.0.2'), '->getForwardedFor() returns the value from HTTP_X_FORWARDED_FOR');

// ->getMethod()
$t->diag('->getMethod()');

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['sf_method'] = 'PUT';
$request = new myRequest($dispatcher);
$t->is($request->getMethod(), 'PUT', '->getMethod() returns the "sf_method" parameter value if it exists and if the method is POST');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_POST['sf_method'] = 'PUT';
$request = new myRequest($dispatcher);
$t->is($request->getMethod(), 'GET', '->getMethod() returns the "sf_method" parameter value if it exists and if the method is POST');

$_SERVER['REQUEST_METHOD'] = 'POST';
unset($_POST['sf_method']);
$request = new myRequest($dispatcher);
$t->is($request->getMethod(), 'POST', '->getMethod() returns the "sf_method" parameter value if it exists and if the method is POST');

// ->getScriptName()
$t->diag('->getScriptName()');

$request = new myRequest($dispatcher);
$_SERVER['SCRIPT_NAME'] = '/frontend_test.php';
$_SERVER['ORIG_SCRIPT_NAME'] = '/frontend_test2.php';
$t->is($request->getScriptName(), '/frontend_test.php', '->getScriptName() returns the script name');

$request = new myRequest($dispatcher);
unset($_SERVER['SCRIPT_NAME']);
$_SERVER['ORIG_SCRIPT_NAME'] = '/frontend_test2.php';
$t->is($request->getScriptName(), '/frontend_test2.php', '->getScriptName() returns the script name if SCRIPT_NAME not set it use ORIG_SCRIPT_NAME');

$request = new myRequest($dispatcher);
unset($_SERVER['SCRIPT_NAME']);
$t->is($request->getScriptName(), '', '->getScriptName() returns the script name if SCRIPT_NAME and ORIG_SCRIPT_NAME not set it return empty');

// ->getPathInfo()
$t->diag('->getPathInfo()');

$request = new myRequest($dispatcher);
$options = $request->getOptions();
$t->is($options['path_info_key'], 'PATH_INFO', 'check if default path_info_key is PATH_INFO');

$request = new myRequest($dispatcher);
$_SERVER['PATH_INFO'] = '/test/klaus';
$_SERVER['REQUEST_URI'] = '/test/klaus2';
$t->is($request->getPathInfo(), '/test/klaus', '->getPathInfo() returns the url path value');

$request = new myRequest($dispatcher, array(), array(), array('path_info_key' => 'SPECIAL'));
$_SERVER['SPECIAL'] = '/special';
$t->is($request->getPathInfo(), '/special', '->getPathInfo() returns the url path value use path_info_key');
$request->resetPathInfoArray();

$request->resetPathInfoArray();
$request = new myRequest($dispatcher);
$_SERVER['SCRIPT_NAME'] = '/frontend_test.php';
$_SERVER['REQUEST_URI'] = '/frontend_test.php/test/klaus2';
$_SERVER['QUERY_STRING'] = '';
$t->is($request->getPathInfo(), '/test/klaus2', '->getPathInfo() returns the url path value if it not exists use default REQUEST_URI');

$request = new myRequest($dispatcher);
$_SERVER['QUERY_STRING'] = 'test';
$_SERVER['REQUEST_URI']  = '/frontend_test.php/test/klaus2?test';
$t->is($request->getPathInfo(), '/test/klaus2', '->getPathInfo() returns the url path value if it not exists use default REQUEST_URI without query');

$request->resetPathInfoArray();
$request = new myRequest($dispatcher);
$t->is($request->getPathInfo(), '/', '->getPathInfo() returns the url path value if it not exists use default /');

// ->addRequestParameters() ->getRequestParameters() ->fixParameters()
$t->diag('getPathInfo');

$request = new myRequest($dispatcher);
$t->is($request->getRequestParameters(), array(), '->getRequestParameters() returns the request parameters default array');

$request->addRequestParameters(array('test' => 'test'));
$t->is($request->getRequestParameters(), array('test' => 'test'), '->getRequestParameters() returns the request parameters');

$request->addRequestParameters(array('test' => 'test'));
$t->is($request->getRequestParameters(), array('test' => 'test'), '->getRequestParameters() returns the request parameters allready exists');

$request->addRequestParameters(array('_sf_ignore_cache' => 1, 'test2' => 'test2'));
$t->is($request->getRequestParameters(), array('test' => 'test', 'test2' => 'test2', '_sf_ignore_cache' => 1), '->getRequestParameters() returns the request parameters check fixParameters call for special _sf_ params');
$t->is($request->getAttribute('sf_ignore_cache'), 1, '->getAttribute() check special param is set as attribute');

// ->checkCSRFProtection()
$t->diag('->checkCSRFProtection()');

class BaseForm extends sfForm
{
  public function getCSRFToken($secret = null)
  {
    return '==TOKEN==';
  }
}

sfForm::enableCSRFProtection();

$request = new myRequest($dispatcher);
try
{
  $request->checkCSRFProtection();
  $t->fail('->checkCSRFProtection() throws a validator error if CSRF protection fails');
}
catch (sfValidatorErrorSchema $error)
{
  $t->pass('->checkCSRFProtection() throws a validator error if CSRF protection fails');
}

$request = new myRequest($dispatcher);
$request->setParameter('_csrf_token', '==TOKEN==');
try
{
  $request->checkCSRFProtection();
  $t->pass('->checkCSRFProtection() checks token from BaseForm');
}
catch (sfValidatorErrorSchema $error)
{
  $t->fail('->checkCSRFProtection() checks token from BaseForm');
}

// ->getContentType()
$t->diag('->getContentType()');

$request = new myRequest($dispatcher);
$_SERVER['CONTENT_TYPE'] = 'text/html';
$t->is($request->getContentType(), 'text/html', '->getContentType() returns the content type');
$request = new myRequest($dispatcher);
$_SERVER['CONTENT_TYPE'] = 'text/html; charset=UTF-8';
$t->is($request->getContentType(), 'text/html', '->getContentType() strips the charset information by default');
$t->is($request->getContentType(false), 'text/html; charset=UTF-8', '->getContentType() does not strip the charset information by defaultif you pass false as the first argument');
