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

$t = new lime_test(21, new lime_output_color());

$context = sfContext::getInstance();

require_once(dirname(__FILE__).'/../../../lib/helper/TagHelper.php');

// tag()
$t->diag('tag()');
$t->is(tag(''), '', 'tag() returns an empty string with empty input');
$t->is(tag('br'), '<br />', 'tag() takes a tag as its first parameter');
$t->is(tag('p', null, true), '<p>', 'tag() takes a boolean parameter as its third parameter');
$t->is(tag('br', array('class' => 'foo'), false), '<br class="foo" />', 'tag() takes an array of options as its second parameters');
$t->is(tag('br', 'class=foo', false), '<br class="foo" />', 'tag() takes a string of options as its second parameters');
$t->is(tag('p', array('class' => 'foo', 'id' => 'bar'), true), '<p class="foo" id="bar">', 'tag() takes a boolean parameter as its third parameter');
//$t->is(tag('br', array('class' => '"foo"')), '<br class="&quot;foo&quot;" />');

// content_tag()
$t->diag('content_tag()');
$t->is(content_tag(''), '', 'content_tag() returns an empty string with empty input');
$t->is(content_tag('', ''), '', 'content_tag() returns an empty string with empty input');
$t->is(content_tag('p', 'Toto'), '<p>Toto</p>', 'content_tag() takes a content as its second parameter');
$t->is(content_tag('p', ''), '<p></p>', 'content_tag() takes a tag as its first parameter');

// cdata_section()
$t->diag('cdata_section()');
$t->is(cdata_section(''), '<![CDATA[]]>', 'cdata_section() returns a string wrapped into a CDATA section');
$t->is(cdata_section('foobar'), '<![CDATA[foobar]]>', 'cdata_section() returns a string wrapped into a CDATA section');

// escape_javascript()
$t->diag('escape_javascript()');
$t->is(escape_javascript("alert('foo');\nalert(\"bar\");"), 'alert(\\\'foo\\\');\\nalert(\\"bar\\");', 'escape_javascript() escapes JavaScript scripts');

// _get_option()
$t->diag('_get_option()');
$options = array(
  'foo' => 'bar',
  'bar' => 'foo',
);
$t->is(_get_option($options, 'foo'), 'bar', '_get_option() returns the value for the given key');
$t->ok(!isset($options['foo']), '_get_option() removes the key from the original array');
$t->is(_get_option($options, 'nofoo', 'nobar'), 'nobar', '_get_option() returns the default value if the key does not exist');

// escape_once()
$t->diag('escape_once()');
$t->is(escape_once('This a > text to "escape"'), 'This a &gt; text to &quot;escape&quot;', 'escape_once() escapes an HTML strings');
$t->is(escape_once(escape_once('This a > text to "escape"')), 'This a &gt; text to &quot;escape&quot;', 'escape_once() does not escape an already escaped string');
$t->is(escape_once('This a &gt; text to "escape"'), 'This a &gt; text to &quot;escape&quot;', 'escape_once() does not escape an already escaped string');
$t->is(escape_once("This a &gt; \"text\" to 'escape'"), "This a &gt; &quot;text&quot; to 'escape'", 'escape_once() does not escape simple quotes but escape double quotes');

// fix_double_escape()
$t->diag('fix_double_escape()');
$t->is(fix_double_escape(htmlspecialchars(htmlspecialchars('This a > text to "escape"'), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'), 'This a &gt; text to &quot;escape&quot;', 'fix_double_escape() fixes double escaped strings');
