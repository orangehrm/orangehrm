<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(9, new lime_output_color());

$dom = new DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;

// ->render()
$t->diag('->render()');
$w = new sfWidgetFormSelectRadio(array('choices' => array('foo' => 'bar', 'foobar' => 'foo'), 'separator' => ''));
$output = '<ul class="radio_list">'.
'<li><input name="foo" type="radio" value="foo" id="foo_foo" />&nbsp;<label for="foo_foo">bar</label></li>'.
'<li><input name="foo" type="radio" value="foobar" id="foo_foobar" checked="checked" />&nbsp;<label for="foo_foobar">foo</label></li>'.
'</ul>';
$t->is($w->render('foo', 'foobar'), $output, '->render() renders a radio tag with the value checked');

// regression for ticket #3528
$onChange = '<ul class="radio_list">'.
'<li><input name="foo" type="radio" value="foo" id="foo_foo" onChange="alert(42)" />'.
'&nbsp;<label for="foo_foo">bar</label></li>'.
'<li><input name="foo" type="radio" value="foobar" id="foo_foobar" checked="checked" onChange="alert(42)" />'.
'&nbsp;<label for="foo_foobar">foo</label></li>'.
'</ul>';
$t->is($w->render('foo', 'foobar', array('onChange' => 'alert(42)')), $onChange, '->render() renders a radio tag using extra attributes');

$w = new sfWidgetFormSelectRadio(array('choices' => array('0' => 'bar', '1' => 'foo')));
$output = '<ul class="radio_list">'.
'<li><input name="myname" type="radio" value="0" id="myname_0" checked="checked" />&nbsp;<label for="myname_0">bar</label></li>'."\n".
'<li><input name="myname" type="radio" value="1" id="myname_1" />&nbsp;<label for="myname_1">foo</label></li>'.
'</ul>';
$t->is($w->render('myname', false), $output, '->render() considers false to be an integer 0');

$w = new sfWidgetFormSelectRadio(array('choices' => array('0' => 'bar', '1' => 'foo')));
$output = '<ul class="radio_list">'.
'<li><input name="myname" type="radio" value="0" id="myname_0" />&nbsp;<label for="myname_0">bar</label></li>'."\n".
'<li><input name="myname" type="radio" value="1" id="myname_1" checked="checked" />&nbsp;<label for="myname_1">foo</label></li>'.
'</ul>';
$t->is($w->render('myname', true), $output, '->render() considers true to be an integer 1');

// group support
$t->diag('group support');
$w = new sfWidgetFormSelectRadio(array('choices' => array('foo' => array('foo' => 'bar', 'bar' => 'foo'), 'bar' => array('foobar' => 'barfoo'))));
$output = 'foo <ul class="radio_list"><li><input name="foo" type="radio" value="foo" id="foo_foo" checked="checked" />&nbsp;<label for="foo_foo">bar</label></li>
<li><input name="foo" type="radio" value="bar" id="foo_bar" />&nbsp;<label for="foo_bar">foo</label></li></ul>
bar <ul class="radio_list"><li><input name="foo" type="radio" value="foobar" id="foo_foobar" />&nbsp;<label for="foo_foobar">barfoo</label></li></ul>';
$t->is($w->render('foo', 'foo'), $output, '->render() has support for groups');

try
{
  $w = new sfWidgetFormSelectRadio();
  $t->fail('__construct() throws an RuntimeException if you don\'t pass a choices option');
}
catch (RuntimeException $e)
{
  $t->pass('__construct() throws an RuntimeException if you don\'t pass a choices option');
}

// choices as a callable
$t->diag('choices as a callable');

function choice_callable()
{
  return array(1, 2, 3);
}
$w = new sfWidgetFormSelectRadio(array('choices' => new sfCallable('choice_callable')));
$dom->loadHTML($w->render('foo'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('input[type="radio"]')->getNodes()), 3, '->render() accepts a sfCallable as a choices option');

// __clone()
$t->diag('__clone()');
$w = new sfWidgetFormSelectRadio(array('choices' => new sfCallable(array($w, 'foo'))));
$w1 = clone $w;
$callable = $w1->getOption('choices')->getCallable();
$t->is(spl_object_hash($callable[0]), spl_object_hash($w1), '__clone() changes the choices is a callable and the object is an instance of the current object');

$w = new sfWidgetFormSelectRadio(array('choices' => new sfCallable(array($a = new stdClass(), 'foo'))));
$w1 = clone $w;
$callable = $w1->getOption('choices')->getCallable();
$t->is(spl_object_hash($callable[0]), spl_object_hash($a), '__clone() changes nothing if the choices is a callable and the object is not an instance of the current object');
