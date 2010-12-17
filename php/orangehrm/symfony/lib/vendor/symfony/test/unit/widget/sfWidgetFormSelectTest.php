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

$t = new lime_test(22);

$dom = new DomDocument('1.0', 'utf-8');
$dom->validateOnParse = true;

// ->render()
$t->diag('->render()');
$w = new sfWidgetFormSelect(array('choices' => array('foo' => 'bar', 'foobar' => 'foo')));
$dom->loadHTML($w->render('foo', 'foobar'));
$css = new sfDomCssSelector($dom);

$t->is($css->matchSingle('#foo option[value="foobar"][selected="selected"]')->getValue(), 'foo', '->render() renders a select tag with the value selected');
$t->is(count($css->matchAll('#foo option')->getNodes()), 2, '->render() renders all choices as option tags');

// value attribute is always mandatory
$w = new sfWidgetFormSelect(array('choices' => array('' => 'bar')));
$t->like($w->render('foo', 'foobar'), '/<option value="">/', '->render() always generate a value attribute, even for empty keys');

// other attributes are removed is empty
$w = new sfWidgetFormSelect(array('choices' => array('' => 'bar')));
$t->like($w->render('foo', 'foobar', array('class' => '', 'style' => null)), '/<option value="">/', '->render() always generate a value attribute, even for empty keys');

// multiple select
$t->diag('multiple select');
$w = new sfWidgetFormSelect(array('multiple' => true, 'choices' => array('foo' => 'bar', 'foobar' => 'foo')));
$dom->loadHTML($w->render('foo', array('foo', 'foobar')));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('select[multiple="multiple"]')->getNodes()), 1, '->render() automatically adds a multiple HTML attributes if multiple is true');
$t->is(count($css->matchAll('select[name="foo[]"]')->getNodes()), 1, '->render() automatically adds a [] at the end of the name if multiple is true');
$t->is($css->matchSingle('#foo option[value="foobar"][selected="selected"]')->getValue(), 'foo', '->render() renders a select tag with the value selected');
$t->is($css->matchSingle('#foo option[value="foo"][selected="selected"]')->getValue(), 'bar', '->render() renders a select tag with the value selected');

$dom->loadHTML($w->render('foo[]', array('foo', 'foobar')));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('select[name="foo[]"]')->getNodes()), 1, '->render() automatically does not add a [] at the end of the name if multiple is true and the name already has one');

// optgroup support
$t->diag('optgroup support');
$w = new sfWidgetFormSelect(array('choices' => array('foo' => array('foo' => 'bar', 'bar' => 'foo'), 'foobar' => 'foo')));

$dom->loadHTML($w->render('foo', array('foo', 'foobar')));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('#foo optgroup[label="foo"] option')->getNodes()), 2, '->render() has support for optgroups tags');

try
{
  $w = new sfWidgetFormSelect();
  $t->fail('__construct() throws an RuntimeException if you don\'t pass a choices option');
}
catch (RuntimeException $e)
{
  $t->pass('__construct() throws an RuntimeException if you don\'t pass a choices option');
}

// choices are translated
$t->diag('choices are translated');

$ws = new sfWidgetFormSchema();
$ws->addFormFormatter('stub', new FormFormatterStub());
$ws->setFormFormatterName('stub');
$w = new sfWidgetFormSelect(array('choices' => array('foo' => 'bar', 'foobar' => 'foo')));
$w->setParent($ws);
$dom->loadHTML($w->render('foo'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo option[value="foo"]')->getValue(), 'translation[bar]', '->render() translates the options');
$t->is($css->matchSingle('#foo option[value="foobar"]')->getValue(), 'translation[foo]', '->render() translates the options');

// optgroup support with translated choices
$t->diag('optgroup support with translated choices');

$ws = new sfWidgetFormSchema();
$ws->addFormFormatter('stub', new FormFormatterStub());
$ws->setFormFormatterName('stub');
$w = new sfWidgetFormSelect(array('choices' => array('group' => array('foo' => 'bar', 'foobar' => 'foo'))));
$w->setParent($ws);
$dom->loadHTML($w->render('foo'));
$css = new sfDomCssSelector($dom);
$t->is($css->matchSingle('#foo optgroup[label="translation[group]"] option[value="foo"]')->getValue(), 'translation[bar]', '->render() translates the options');
$t->is($css->matchSingle('#foo optgroup[label="translation[group]"] option[value="foobar"]')->getValue(), 'translation[foo]', '->render() translates the options');

// choices as a callable
$t->diag('choices as a callable');

function choice_callable()
{
  return array(1, 2, 3);
}
$w = new sfWidgetFormSelect(array('choices' => new sfCallable('choice_callable')));
$dom->loadHTML($w->render('foo'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('#foo option')->getNodes()), 3, '->render() accepts a sfCallable as a choices option');

// attributes
$t->diag('attributes');
$w = new sfWidgetFormSelect(array('choices' => array(0, 1, 2)));
$dom->loadHTML($w->render('foo', null, array('disabled' => 'disabled')));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('select[disabled="disabled"]')->getNodes()), 1, '->render() does not pass the select HTML attributes to the option tag');
$t->is(count($css->matchAll('option[disabled="disabled"]')->getNodes()), 0, '->render() does not pass the select HTML attributes to the option tag');

$w = new sfWidgetFormSelect(array('choices' => array(0, 1, 2)), array('disabled' => 'disabled'));
$dom->loadHTML($w->render('foo'));
$css = new sfDomCssSelector($dom);
$t->is(count($css->matchAll('select[disabled="disabled"]')->getNodes()), 1, '->render() does not pass the select HTML attributes to the option tag');
$t->is(count($css->matchAll('option[disabled="disabled"]')->getNodes()), 0, '->render() does not pass the select HTML attributes to the option tag');

// __clone()
$t->diag('__clone()');
$w = new sfWidgetFormSelect(array('choices' => new sfCallable(array($w, 'foo'))));
$w1 = clone $w;
$callable = $w1->getOption('choices')->getCallable();
$t->is(spl_object_hash($callable[0]), spl_object_hash($w1), '__clone() changes the choices is a callable and the object is an instance of the current object');

$w = new sfWidgetFormSelect(array('choices' => new sfCallable(array($a = new stdClass(), 'foo'))));
$w1 = clone $w;
$callable = $w1->getOption('choices')->getCallable();
$t->is(spl_object_hash($callable[0]), spl_object_hash($a), '__clone() changes nothing if the choices is a callable and the object is not an instance of the current object');
