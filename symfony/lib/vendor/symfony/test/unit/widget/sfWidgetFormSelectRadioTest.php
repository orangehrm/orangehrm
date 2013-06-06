<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

class FormFormatterStub extends sfWidgetFormSchemaFormatter
{
  public function __construct() {}

  public function translate($subject, $parameters = array())
  {
    return sprintf('translation[%s]', $subject);
  }
}

$t = new lime_test(12);

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
$output = <<< EOF
<ul class="radio_list"><li><input name="myname" type="radio" value="0" id="myname_0" checked="checked" />&nbsp;<label for="myname_0">bar</label></li>
<li><input name="myname" type="radio" value="1" id="myname_1" />&nbsp;<label for="myname_1">foo</label></li></ul>
EOF;
$t->is($w->render('myname', false), fix_linebreaks($output), '->render() considers false to be an integer 0');

$w = new sfWidgetFormSelectRadio(array('choices' => array('0' => 'bar', '1' => 'foo')));
$output = <<< EOF
<ul class="radio_list"><li><input name="myname" type="radio" value="0" id="myname_0" />&nbsp;<label for="myname_0">bar</label></li>
<li><input name="myname" type="radio" value="1" id="myname_1" checked="checked" />&nbsp;<label for="myname_1">foo</label></li></ul>
EOF;
$t->is($w->render('myname', true), fix_linebreaks($output), '->render() considers true to be an integer 1');

$w = new sfWidgetFormSelectRadio(array('choices' => array()));
$t->is($w->render('myname', array()), '', '->render() returns an empty HTML string if no choices');

// group support
$t->diag('group support');
$w = new sfWidgetFormSelectRadio(array('choices' => array('foo' => array('foo' => 'bar', 'bar' => 'foo'), 'bar' => array('foobar' => 'barfoo'))));
$output = <<<EOF
foo <ul class="radio_list"><li><input name="foo" type="radio" value="foo" id="foo_foo" checked="checked" />&nbsp;<label for="foo_foo">bar</label></li>
<li><input name="foo" type="radio" value="bar" id="foo_bar" />&nbsp;<label for="foo_bar">foo</label></li></ul>
bar <ul class="radio_list"><li><input name="foo" type="radio" value="foobar" id="foo_foobar" />&nbsp;<label for="foo_foobar">barfoo</label></li></ul>
EOF;
$t->is($w->render('foo', 'foo'), fix_linebreaks($output), '->render() has support for groups');

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

// choices are translated
$t->diag('choices are translated');

$ws = new sfWidgetFormSchema();
$ws->addFormFormatter('stub', new FormFormatterStub());
$ws->setFormFormatterName('stub');
$w = new sfWidgetFormSelectRadio(array('choices' => array('foo' => 'bar', 'foobar' => 'foo'), 'separator' => ''));
$w->setParent($ws);
$output = '<ul class="radio_list">'.
'<li><input name="foo" type="radio" value="foo" id="foo_foo" />&nbsp;<label for="foo_foo">translation[bar]</label></li>'.
'<li><input name="foo" type="radio" value="foobar" id="foo_foobar" />&nbsp;<label for="foo_foobar">translation[foo]</label></li>'.
'</ul>';
$t->is($w->render('foo'), $output, '->render() translates the options');

// choices as escaped
$t->diag('choices are escaped');

$w = new sfWidgetFormSelectRadio(array('choices' => array('<b>Hello world</b>')));
$t->is($w->render('foo'), '<ul class="radio_list"><li><input name="foo" type="radio" value="0" id="foo_0" />&nbsp;<label for="foo_0">&lt;b&gt;Hello world&lt;/b&gt;</label></li></ul>', '->render() escapes the choices');

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
