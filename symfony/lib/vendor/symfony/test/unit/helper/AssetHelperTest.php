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

require_once(dirname(__FILE__).'/../../../lib/helper/TagHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/UrlHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/AssetHelper.php');

$t = new lime_test(68);

class myRequest
{
  public $relativeUrlRoot = '';

  public function getRelativeUrlRoot()
  {
    return $this->relativeUrlRoot;
  }

  public function isSecure()
  {
    return false;
  }

  public function getHost()
  {
    return 'localhost';
  }
}

class myResponse extends sfWebResponse
{
  public function resetAssets()
  {
    $this->javascripts = array_combine($this->positions, array_fill(0, count($this->positions), array()));
    $this->stylesheets = array_combine($this->positions, array_fill(0, count($this->positions), array()));
  }
}

class myController
{
  public function genUrl($parameters = array(), $absolute = false)
  {
    return ($absolute ? '/' : '').$parameters;
  }
}

$context = sfContext::getInstance(array('request' => 'myRequest', 'response' => 'myResponse', 'controller' => 'myController'));

// _compute_public_path()
$t->diag('_compute_public_path');
$t->is(_compute_public_path('foo', 'css', 'css'), '/css/foo.css', '_compute_public_path() converts a string to a web path');
$t->is(_compute_public_path('foo', 'css', 'css', true), 'http://localhost/css/foo.css', '_compute_public_path() can create absolute links');
$t->is(_compute_public_path('foo.css2', 'css', 'css'), '/css/foo.css2', '_compute_public_path() does not add suffix if one already exists');
$context->request->relativeUrlRoot = '/bar';
$t->is(_compute_public_path('foo', 'css', 'css'), '/bar/css/foo.css', '_compute_public_path() takes into account the relative url root configuration');
$context->request->relativeUrlRoot = '';
$t->is(_compute_public_path('foo.css?foo=bar', 'css', 'css'), '/css/foo.css?foo=bar', '_compute_public_path() takes into account query strings');
$t->is(_compute_public_path('foo?foo=bar', 'css', 'css'), '/css/foo.css?foo=bar', '_compute_public_path() takes into account query strings');

// image_tag()
$t->diag('image_tag()');
$t->is(image_tag(''), '', 'image_tag() returns nothing when called without arguments');
$t->is(image_tag('test'), '<img src="/images/test.png" />', 'image_tag() takes an image name as its first argument');
$t->is(image_tag('test.png'), '<img src="/images/test.png" />', 'image_tag() can take an image name with an extension');
$t->is(image_tag('/images/test.png'), '<img src="/images/test.png" />', 'image_tag() can take an absolute image path');
$t->is(image_tag('/images/test'), '<img src="/images/test.png" />', 'image_tag() can take an absolute image path without extension');
$t->is(image_tag('test.jpg'), '<img src="/images/test.jpg" />', 'image_tag() can take an image name with an extension');
$t->is(image_tag('test', array('alt' => 'Foo')), '<img alt="Foo" src="/images/test.png" />', 'image_tag() takes an array of options as its second argument to override alt');
$t->is(image_tag('test', array('size' => '10x10')), '<img src="/images/test.png" height="10" width="10" />', 'image_tag() takes a size option');
$t->is(image_tag('test', array('absolute' => true)), '<img src="http://localhost/images/test.png" />', 'image_tag() can take an absolute parameter');
$t->is(image_tag('test', array('class' => 'bar')), '<img class="bar" src="/images/test.png" />', 'image_tag() takes whatever option you want');
$t->is(image_tag('test', array('alt_title' => 'Foo')), '<img src="/images/test.png" alt="Foo" title="Foo" />', 'image_tag() takes an array of options as its second argument to create alt and title');
$t->is(image_tag('test', array('alt_title' => 'Foo', 'title' => 'Bar')), '<img title="Bar" src="/images/test.png" alt="Foo" />', 'image_tag() takes an array of options as its second argument to create alt and title');

// stylesheet_tag()
$t->diag('stylesheet_tag()');
$t->is(stylesheet_tag('style'), 
  '<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css" />'."\n", 
  'stylesheet_tag() takes a stylesheet name as its first argument');
$t->is(stylesheet_tag('random.styles', '/css/stylish'),
  '<link rel="stylesheet" type="text/css" media="screen" href="/css/random.styles" />'."\n".
  '<link rel="stylesheet" type="text/css" media="screen" href="/css/stylish.css" />'."\n", 
  'stylesheet_tag() can takes n stylesheet names as its arguments');
$t->is(stylesheet_tag('style', array('media' => 'all')), 
  '<link rel="stylesheet" type="text/css" media="all" href="/css/style.css" />'."\n", 
  'stylesheet_tag() can take a media option');
$t->is(stylesheet_tag('style', array('absolute' => true)), 
  '<link rel="stylesheet" type="text/css" media="screen" href="http://localhost/css/style.css" />'."\n", 
  'stylesheet_tag() can take an absolute option to output an absolute file name');
$t->is(stylesheet_tag('style', array('raw_name' => true)), 
  '<link rel="stylesheet" type="text/css" media="screen" href="style" />'."\n", 
  'stylesheet_tag() can take a raw_name option to bypass file name decoration');
$t->is(stylesheet_tag('style', array('condition' => 'IE 6')),
  '<!--[if IE 6]><link rel="stylesheet" type="text/css" media="screen" href="/css/style.css" /><![endif]-->'."\n",
  'stylesheet_tag() can take a condition option');

// javascript_include_tag()
$t->diag('javascript_include_tag()');
$t->is(javascript_include_tag('xmlhr'),
  '<script type="text/javascript" src="/js/xmlhr.js"></script>'."\n", 
  'javascript_include_tag() takes a javascript name as its first argument');
$t->is(javascript_include_tag('common.javascript', '/elsewhere/cools'),
  '<script type="text/javascript" src="/js/common.javascript"></script>'."\n".
  '<script type="text/javascript" src="/elsewhere/cools.js"></script>'."\n",
  'javascript_include_tag() can takes n javascript file names as its arguments');
$t->is(javascript_include_tag('xmlhr', array('absolute' => true)),
  '<script type="text/javascript" src="http://localhost/js/xmlhr.js"></script>'."\n", 
  'javascript_include_tag() can take an absolute option to output an absolute file name');
$t->is(javascript_include_tag('xmlhr', array('raw_name' => true)),
  '<script type="text/javascript" src="xmlhr"></script>'."\n", 
  'javascript_include_tag() can take a raw_name option to bypass file name decoration');
$t->is(javascript_include_tag('xmlhr', array('defer' => 'defer')),
  '<script type="text/javascript" src="/js/xmlhr.js" defer="defer"></script>'."\n", 
  'javascript_include_tag() can take additional html options like defer');
$t->is(javascript_include_tag('xmlhr', array('condition' => 'IE 6')),
  '<!--[if IE 6]><script type="text/javascript" src="/js/xmlhr.js"></script><![endif]-->'."\n",
  'javascript_include_tag() can take a condition option');

// javascript_path()
$t->diag('javascript_path()');
$t->is(javascript_path('xmlhr'), '/js/xmlhr.js', 'javascript_path() decorates a relative filename with js dir name and extension');
$t->is(javascript_path('/xmlhr'), '/xmlhr.js', 'javascript_path() does not decorate absolute file names with js dir name');
$t->is(javascript_path('xmlhr.foo'), '/js/xmlhr.foo', 'javascript_path() does not decorate file names with extension with .js');
$t->is(javascript_path('xmlhr.foo', true), 'http://localhost/js/xmlhr.foo', 'javascript_path() accepts a second parameter to output an absolute resource path');

// stylesheet_path()
$t->diag('stylesheet_path()');
$t->is(stylesheet_path('style'), '/css/style.css', 'stylesheet_path() decorates a relative filename with css dir name and extension');
$t->is(stylesheet_path('/style'), '/style.css', 'stylesheet_path() does not decorate absolute file names with css dir name');
$t->is(stylesheet_path('style.foo'), '/css/style.foo', 'stylesheet_path() does not decorate file names with extension with .css');
$t->is(stylesheet_path('style.foo', true), 'http://localhost/css/style.foo', 'stylesheet_path() accepts a second parameter to output an absolute resource path');

// image_path()
$t->diag('image_path()');
$t->is(image_path('img'), '/images/img.png', 'image_path() decorates a relative filename with images dir name and png extension');
$t->is(image_path('/img'), '/img.png', 'image_path() does not decorate absolute file names with images dir name');
$t->is(image_path('img.jpg'), '/images/img.jpg', 'image_path() does not decorate file names with extension with .png');
$t->is(image_path('img.jpg', true), 'http://localhost/images/img.jpg', 'image_path() accepts a second parameter to output an absolute resource path');

// use_javascript() get_javascripts()
$t->diag('use_javascript() get_javascripts()');
use_javascript('xmlhr');
$t->is(get_javascripts(),
  '<script type="text/javascript" src="/js/xmlhr.js"></script>'."\n", 
  'get_javascripts() returns a javascript previously added by use_javascript()');
use_javascript('xmlhr', '', array('raw_name' => true));
$t->is(get_javascripts(),
  '<script type="text/javascript" src="xmlhr"></script>'."\n", 
  'use_javascript() accepts an array of options as a third parameter');
use_javascript('xmlhr', '', array('absolute' => true));
$t->is(get_javascripts(),
  '<script type="text/javascript" src="http://localhost/js/xmlhr.js"></script>'."\n", 
  'use_javascript() accepts an array of options as a third parameter');
use_javascript('xmlhr');
use_javascript('xmlhr2');
$t->is(get_javascripts(),
  '<script type="text/javascript" src="/js/xmlhr.js"></script>'."\n".'<script type="text/javascript" src="/js/xmlhr2.js"></script>'."\n", 
  'get_javascripts() returns all the javascripts previously added by use_javascript()');

// use_stylesheet() get_stylesheets()
$t->diag('use_stylesheet() get_stylesheets()');
use_stylesheet('style');
$t->is(get_stylesheets(),
  '<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css" />'."\n", 
  'get_stylesheets() returns a stylesheet previously added by use_stylesheet()');
use_stylesheet('style', '', array('raw_name' => true));
$t->is(get_stylesheets(),
  '<link rel="stylesheet" type="text/css" media="screen" href="style" />'."\n", 
  'use_stylesheet() accepts an array of options as a third parameter');
use_stylesheet('style', '', array('absolute' => true));
$t->is(get_stylesheets(),
  '<link rel="stylesheet" type="text/css" media="screen" href="http://localhost/css/style.css" />'."\n", 
  'use_stylesheet() accepts an array of options as a third parameter');
use_stylesheet('style');
use_stylesheet('style2');
$t->is(get_stylesheets(),
  '<link rel="stylesheet" type="text/css" media="screen" href="/css/style.css" />'."\n".'<link rel="stylesheet" type="text/css" media="screen" href="/css/style2.css" />'."\n",
  'get_stylesheets() returns all the stylesheets previously added by use_stylesheet()');

// _dynamic_path()
$t->diag('_dynamic_path()');
$t->is(_dynamic_path('module/action', 'js'), 'module/action?sf_format=js', '_dynamic_path() converts an internal URI to a URL');
$t->is(_dynamic_path('module/action?key=value', 'js'), 'module/action?key=value&sf_format=js', '_dynamic_path() converts an internal URI to a URL');
$t->is(_dynamic_path('module/action', 'js', true), '/module/action?sf_format=js', '_dynamic_path() converts an internal URI to a URL');

// dynamic_javascript_include_tag()
$t->diag('dynamic_javascript_include_tag()');
$t->is(dynamic_javascript_include_tag('module/action'), '<script type="text/javascript" src="module/action?sf_format=js"></script>'."\n", 'dynamic_javascript_include_tag() returns a tag relative to the given action');
$t->is(dynamic_javascript_include_tag('module/action', true), '<script type="text/javascript" src="/module/action?sf_format=js"></script>'."\n", 'dynamic_javascript_include_tag() takes an absolute boolean as its second argument');
$t->is(dynamic_javascript_include_tag('module/action', true, array('class' => 'foo')), '<script type="text/javascript" src="/module/action?sf_format=js" class="foo"></script>'."\n", 'dynamic_javascript_include_tag() takes an array of HTML attributes as its third argument');

$context->response = new myResponse($context->getEventDispatcher());

// use_dynamic_javascript()
$t->diag('use_dynamic_javascript()');
use_dynamic_javascript('module/action');
$t->is(get_javascripts(),
  '<script type="text/javascript" src="module/action?sf_format=js"></script>'."\n",
  'use_dynamic_javascript() register a dynamic javascript in the response'
);

// use_dynamic_stylesheet()
$t->diag('use_dynamic_stylesheet()');
use_dynamic_stylesheet('module/action');
$t->is(get_stylesheets(),
  '<link rel="stylesheet" type="text/css" media="screen" href="module/action?sf_format=css" />'."\n", 
  'use_dynamic_stylesheet() register a dynamic stylesheet in the response'
);

class MyForm extends sfForm
{
  public function getStylesheets()
  {
    return array('/path/to/a/foo.css' => 'all', '/path/to/a/bar.css' => 'print');
  }

  public function getJavaScripts()
  {
    return array('/path/to/a/foo.js', '/path/to/a/bar.js');
  }
}

// get_javascripts_for_form() get_stylesheets_for_form()
$t->diag('get_javascripts_for_form() get_stylesheets_for_form()');
$form = new MyForm();
$output = <<<EOF
<script type="text/javascript" src="/path/to/a/foo.js"></script>
<script type="text/javascript" src="/path/to/a/bar.js"></script>

EOF;
$t->is(get_javascripts_for_form($form), fix_linebreaks($output), 'get_javascripts_for_form() returns script tags');
$output = <<<EOF
<link rel="stylesheet" type="text/css" media="all" href="/path/to/a/foo.css" />
<link rel="stylesheet" type="text/css" media="print" href="/path/to/a/bar.css" />

EOF;
$t->is(get_stylesheets_for_form($form), fix_linebreaks($output), 'get_stylesheets_for_form() returns link tags');

// use_javascripts_for_form() use_stylesheets_for_form()
$t->diag('use_javascripts_for_form() use_stylesheets_for_form()');

$response = sfContext::getInstance()->getResponse();
$form = new MyForm();

$response->resetAssets();
use_stylesheets_for_form($form);
$t->is_deeply($response->getStylesheets(), array('/path/to/a/foo.css' => array('media' => 'all'), '/path/to/a/bar.css' => array('media' => 'print')), 'use_stylesheets_for_form() adds stylesheets to the response');

$response->resetAssets();
use_javascripts_for_form($form);
$t->is_deeply($response->getJavaScripts(), array('/path/to/a/foo.js' => array(), '/path/to/a/bar.js' => array()), 'use_javascripts_for_form() adds javascripts to the response');

// custom web paths
$t->diag('Custom asset path handling');

sfConfig::set('sf_web_js_dir_name', 'static/js');
$t->is(javascript_path('xmlhr'), '/static/js/xmlhr.js', 'javascript_path() decorates a relative filename with js dir name and extension with custom js dir');
$t->is(javascript_include_tag('xmlhr'),
  '<script type="text/javascript" src="/static/js/xmlhr.js"></script>'."\n", 
  'javascript_include_tag() takes a javascript name as its first argument');

sfConfig::set('sf_web_css_dir_name', 'static/css');
$t->is(stylesheet_path('style'), '/static/css/style.css', 'stylesheet_path() decorates a relative filename with css dir name and extension with custom css dir');
$t->is(stylesheet_tag('style'), 
  '<link rel="stylesheet" type="text/css" media="screen" href="/static/css/style.css" />'."\n", 
  'stylesheet_tag() takes a stylesheet name as its first argument');

sfConfig::set('sf_web_images_dir_name', 'static/img');
$t->is(image_path('img'), '/static/img/img.png', 'image_path() decorates a relative filename with images dir name and png extension with custom images dir');
$t->is(image_tag('test'), '<img src="/static/img/test.png" />', 'image_tag() takes an image name as its first argument');
