<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/unit/sfContextMock.class.php');

class myController
{
  public function genUrl($parameters = array(), $absolute = false)
  {
    $url = is_array($parameters) && isset($parameters['sf_route']) ? $parameters['sf_route'] : 'module/action';
    return ($absolute ? '/' : '').$url;
  }
}

class myRequest
{
  public function getRelativeUrlRoot()
  {
    return '/public';
  }
  
  public function isSecure()
  {
    return true;
  }

  public function getHost()
  {
    return 'example.org';
  }
}

class BaseForm extends sfForm
{
  public function getCSRFToken($secret = null)
  {
    return '==TOKEN==';
  }
}

sfForm::enableCSRFProtection();

$t = new lime_test(44);

$context = sfContext::getInstance(array('controller' => 'myController', 'request' => 'myRequest'));

require_once(dirname(__FILE__).'/../../../lib/helper/AssetHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/UrlHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/TagHelper.php');

// url_for()
$t->diag('url_for()');
$t->is(url_for('@test'), 'module/action', 'url_for() converts an internal URI to a web URI');
$t->is(url_for('@test', true), '/module/action', 'url_for() can take an absolute boolean as its second argument');
$t->is(url_for('@test', false), 'module/action', 'url_for() can take an absolute boolean as its second argument');

// link_to()
$t->diag('link_to()');
$t->is(link_to('test', '@homepage'), '<a href="module/action">test</a>', 'link_to() returns an HTML "a" tag');
$t->is(link_to('test', '@homepage', array('absolute' => true)), '<a href="/module/action">test</a>', 'link_to() can take an "absolute" option');
$t->is(link_to('test', '@homepage', array('absolute' => false)), '<a href="module/action">test</a>', 'link_to() can take an "absolute" option');
$t->is(link_to('test', '@homepage', array('query_string' => 'foo=bar')), '<a href="module/action?foo=bar">test</a>', 'link_to() can take a "query_string" option');
$t->is(link_to('test', '@homepage', array('anchor' => 'bar')), '<a href="module/action#bar">test</a>', 'link_to() can take an "anchor" option');
$t->is(link_to('', '@homepage'), '<a href="module/action">module/action</a>', 'link_to() takes the url as the link name if the first argument is empty');
$t->like(link_to('test', '@homepage', array('method' => 'post')), '/==TOKEN==/', 'link_to() includes CSRF token from BaseForm');

// button_to()
$t->diag('button_to()');
$t->is(button_to('test', '@homepage'), '<input value="test" type="button" onclick="document.location.href=\'module/action\';" />', 'button_to() returns an HTML "input" tag');
$t->is(button_to('test', '@homepage', array('query_string' => 'foo=bar')), '<input value="test" type="button" onclick="document.location.href=\'module/action?foo=bar\';" />', 'button_to() returns an HTML "input" tag');
$t->is(button_to('test', '@homepage', array('anchor' => 'bar')), '<input value="test" type="button" onclick="document.location.href=\'module/action#bar\';" />', 'button_to() returns an HTML "input" tag');
$t->is(button_to('test', '@homepage', array('popup' => 'true', 'query_string' => 'foo=bar')), '<input value="test" type="button" onclick="var w=window.open(\'module/action?foo=bar\');w.focus();return false;" />', 'button_to() returns an HTML "input" tag');
$t->is(button_to('test', '@homepage', 'popup=true'), '<input value="test" type="button" onclick="var w=window.open(\'module/action\');w.focus();return false;" />', 'button_to() accepts options as string');
$t->is(button_to('test', '@homepage', 'confirm=really?'), '<input value="test" type="button" onclick="if (confirm(\'really?\')) { return document.location.href=\'module/action\';} else return false;" />', 'button_to() works with confirm option');
$t->is(button_to('test', '@homepage', 'popup=true confirm=really?'), '<input value="test" type="button" onclick="if (confirm(\'really?\')) { var w=window.open(\'module/action\');w.focus(); };return false;" />', 'button_to() works with confirm and popup option');
$t->like(button_to('test', '@homepage', array('method' => 'post')), '/==TOKEN==/', 'button_to() includes CSRF token from BaseForm');

class testObject
{
}

try
{
  $o1 = new testObject();
  link_to($o1, '@homepage');
  $t->fail('link_to() can take an object as its first argument if __toString() method is defined');
}
catch (sfException $e)
{
  $t->pass('link_to() can take an object as its first argument if __toString() method is defined');
}

class testObjectWithToString
{
  public function __toString()
  {
    return 'test';
  }
}
$o2 = new testObjectWithToString();
$t->is(link_to($o2, '@homepage'), '<a href="module/action">test</a>', 'link_to() can take an object as its first argument');

// link_to_if()
$t->diag('link_to_if()');
$t->is(link_to_if(true, 'test', '@homepage'), '<a href="module/action">test</a>', 'link_to_if() returns an HTML "a" tag if the condition is true');
$t->is(link_to_if(false, 'test', '@homepage'), '<span>test</span>', 'link_to_if() returns an HTML "span" tag by default if the condition is false');
$t->is(link_to_if(false, 'test', '@homepage', array('tag' => 'div')), '<div>test</div>', 'link_to_if() takes a "tag" option');
$t->is(link_to_if(true, 'test', '@homepage', 'tag=div'), '<a href="module/action">test</a>', 'link_to_if() removes "tag" option (given as string) in true case');
$t->is(link_to_if(true, 'test', '@homepage', array('tag' => 'div')), '<a href="module/action">test</a>', 'link_to_if() removes "tag" option (given as array) in true case');
$t->is(link_to_if(false, 'test', '@homepage', array('query_string' => 'foo=bar', 'absolute' => true, 'absolute_url' => 'http://www.google.com/')), '<span>test</span>', 'link_to_if() returns an HTML "span" tag by default if the condition is false');
$t->is(link_to_if(true, 'test', 'homepage', array(), array('class' => 'test')), '<a class="test" href="homepage">test</a>', 'link_to_if() accepts link_to2 compatible usage');
$t->is(link_to_if(false, 'test', 'homepage', array(), array('class' => 'test')), '<span class="test">test</span>', 'link_to_if() accepts link_to2 compatible usage');

// link_to_unless()
$t->diag('link_to_unless()');
$t->is(link_to_unless(false, 'test', '@homepage'), '<a href="module/action">test</a>', 'link_to_unless() returns an HTML "a" tag if the condition is false');
$t->is(link_to_unless(true, 'test', '@homepage'), '<span>test</span>', 'link_to_unless() returns an HTML "span" tag by default if the condition is true');
$t->is(link_to_unless(true, 'test', 'homepage', array(), array('class' => 'test')), '<span class="test">test</span>', 'link_to_unless() accepts link_to2 compatible usage');
$t->is(link_to_unless(false, 'test', 'homepage', array(), array('class' => 'test')), '<a class="test" href="homepage">test</a>', 'link_to_unless() accepts link_to2 compatible usage');

// public_path()
$t->diag('public_path()');
$t->is(public_path('pdf/download.pdf'), '/public/pdf/download.pdf', 'public_path() returns the public path');
$t->is(public_path('/pdf/download.pdf'), '/public/pdf/download.pdf', 'public_path() returns the public path if starting with slash');
$t->is(public_path('pdf/download.pdf', true), 'https://example.org/public/pdf/download.pdf', 'public_path() returns the public path');

// mail_to()
$t->diag('mail_to()');
$t->is(mail_to('fabien.potencier@symfony-project.com'), '<a href="mailto:fabien.potencier@symfony-project.com">fabien.potencier@symfony-project.com</a>', 'mail_to() creates a mailto a tag');
$t->is(mail_to('fabien.potencier@symfony-project.com', 'fabien'), '<a href="mailto:fabien.potencier@symfony-project.com">fabien</a>', 'mail_to() creates a mailto a tag');
preg_match('/href="(.+?)"/', mail_to('fabien.potencier@symfony-project.com', 'fabien', array('encode' => true)), $matches);
$t->is(html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8'), 'mailto:fabien.potencier@symfony-project.com', 'mail_to() can encode the email address');

$t->diag('mail_to test');
$t->is(mail_to('webmaster@example.com'),'<a href="mailto:webmaster@example.com">webmaster@example.com</a>','mail_to with only given email works');
$t->is(mail_to('webmaster@example.com', 'send us an email'),'<a href="mailto:webmaster@example.com">send us an email</a>','mail_to with given email and title works');
$t->isnt(mail_to('webmaster@example.com', 'encoded', array('encode' => true)),'<a href="mailto:webmaster@example.com">encoded</a>','mail_to with encoding works');

$t->is(mail_to('webmaster@example.com', '', array(), array('subject' => 'test subject', 'body' => 'test body')),'<a href="mailto:webmaster@example.com?subject=test+subject&amp;body=test+body">webmaster@example.com</a>', 'mail_to() works with given default values in array form');
$t->is(mail_to('webmaster@example.com', '', array(), 'subject=test subject body=test body'),'<a href="mailto:webmaster@example.com?subject=test+subject&amp;body=test+body">webmaster@example.com</a>', 'mail_to() works with given default values in string form');
$t->is(mail_to('webmaster@example.com', '', array(), 'subject=Hello World and more'),'<a href="mailto:webmaster@example.com?subject=Hello+World+and+more">webmaster@example.com</a>', 'mail_to() works with given default value with spaces');
