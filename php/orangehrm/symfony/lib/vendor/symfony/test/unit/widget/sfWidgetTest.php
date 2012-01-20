<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(32);

class MyWidget extends sfWidget
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('foo');
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return $this->attributesToHtml(array_merge($this->attributes, $attributes));
  }
}

class MyWidgetWithRequired extends MyWidget
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('foo');
  }
}

// __construct()
$t->diag('__construct()');
$w = new MyWidget();
$t->is($w->getAttributes(), array(), '->__construct() can take no argument');
$w = new MyWidget(array(), array('class' => 'foo'));
$t->is($w->getAttributes(), array('class' => 'foo'), '->__construct() can take an array of default HTML attributes');

try
{
  new MyWidget(array('nonexistant' => false));
  $t->fail('__construct() throws an InvalidArgumentException if you pass some non existant options');
  $t->skip();
}
catch (InvalidArgumentException $e)
{
  $t->pass('__construct() throws an InvalidArgumentException if you pass some non existant options');
  $t->like($e->getMessage(), '/ \'nonexistant\'/', 'The exception contains the non existant option names');
}

$t->diag('getRequiredOptions');
$w = new MyWidgetWithRequired(array('foo' => 'bar'));
$t->is($w->getRequiredOptions(), array('foo'), '->getRequiredOptions() returns an array of required option names');

try
{
  new MyWidgetWithRequired();
  $t->fail('__construct() throws an RuntimeException if you don\'t pass a required option');
}
catch (RuntimeException $e)
{
  $t->pass('__construct() throws an RuntimeException if you don\'t pass a required option');
}

$w = new MyWidget();

// ->getOption() ->setOption() ->setOptions() ->getOptions() ->hasOption()
$t->diag('->getOption() ->setOption() ->setOptions() ->getOptions() ->hasOption()');
$w->setOption('foo', 'bar');
$t->is($w->getOption('foo'), 'bar', '->setOption() sets an option value');
$t->is($w->getOption('nonexistant'), null, '->getOption() returns null if the option does not exist');
$t->is($w->hasOption('foo'), true, '->hasOption() returns true if the option exist');
$t->is($w->hasOption('nonexistant'), false, '->hasOption() returns false if the option does not exist');
try
{
  $w->setOption('foobar', 'foo');
  $t->fail('->setOption() throws an InvalidArgumentException if the option is not registered');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->setOption() throws an InvalidArgumentException if the option is not registered');
}

// ->addOption()
$t->diag('->addOption()');
$w->addOption('foobar');
$w->setOption('foobar', 'bar');
$t->is($w->getOption('foobar'), 'bar', '->addOption() adds a new option');

$w = new MyWidget();
$w->setOptions(array('foo' => 'bar'));
$t->is($w->getOptions(), array('foo' => 'bar'), '->getOptions() returns an array of all options');

$w = new MyWidget();

// ->setAttribute() ->getAttribute()
$t->diag('->setAttribute() ->getAttribute()');
$w->setAttribute('foo', 'bar');
$t->is($w->getAttribute('foo'), 'bar', '->setAttribute() sets a new default attribute for the widget');

// ->getAttributes()
$t->diag('->getAttributes()');
$t->is($w->getAttributes(), array('foo' => 'bar'), '->getAttributes() returns an array of attributes');

// ->setAttributes()
$t->diag('->setAttributes()');
$w->setAttributes(array('foo' => 'bar'));
$t->is($w->getAttributes(), array('foo' => 'bar'), '->setAttributes() sets attributes');

// ->attributesToHtml()
$t->diag('->attributesToHtml()');
$w = new MyWidget(array(), array('foo' => 'bar', 'foobar' => '<strong>été</strong>'));
$t->is($w->render('foo', 'bar'), ' foo="bar" foobar="&lt;strong&gt;été&lt;/strong&gt;"', '->attributesToHtml() converts an attribute array to an HTML attribute string');

// ->renderTag()
$t->diag('->renderTag()');
$w = new MyWidget(array(), array('foo' => 'bar'));
$t->is($w->renderTag('input', array('bar' => 'foo')), '<input foo="bar" bar="foo" />', '->renderTag() renders a HTML tag with attributes');
$t->is($w->renderTag(''), '', '->renderTag() renders an empty string if the tag name is empty');

// ->renderContentTag()
$t->diag('->renderContentTag()');
$w = new MyWidget(array(), array('foo' => 'bar'));
$t->is($w->renderContentTag('textarea', 'content', array('bar' => 'foo')), '<textarea foo="bar" bar="foo">content</textarea>', '->renderContentTag() renders a HTML tag with content and attributes');
$t->is($w->renderContentTag(''), '', '->renderContentTag() renders an empty string if the tag name is empty');

// ::escapeOnce()
$t->diag('::escapeOnce()');
$t->is(sfWidget::escapeOnce('This a > text to "escape"'), 'This a &gt; text to &quot;escape&quot;', '::escapeOnce() escapes an HTML strings');
$t->is(sfWidget::escapeOnce(sfWidget::escapeOnce('This a > text to "escape"')), 'This a &gt; text to &quot;escape&quot;', '::escapeOnce() does not escape an already escaped string');
$t->is(sfWidget::escapeOnce('This a &gt; text to "escape"'), 'This a &gt; text to &quot;escape&quot;', '::escapeOnce() does not escape an already escaped string');

class MyClass
{
  public function __toString()
  {
    return 'mycontent';
  }
}
$t->is(sfWidget::escapeOnce(new MyClass()), 'mycontent', '::escapeOnce() converts objects to string');

// ::fixDoubleEscape()
$t->diag('::fixDoubleEscape()');
$t->is(sfWidget::fixDoubleEscape(htmlspecialchars(htmlspecialchars('This a > text to "escape"'), ENT_QUOTES, sfWidget::getCharset()), ENT_QUOTES, sfWidget::getCharset()), 'This a &gt; text to &quot;escape&quot;', '::fixDoubleEscape() fixes double escaped strings');

// ::getCharset() ::setCharset()
$t->diag('::getCharset() ::setCharset()');
$t->is(sfWidget::getCharset(), 'UTF-8', '::getCharset() returns the charset to use for widgets');
sfWidget::setCharset('ISO-8859-1');
$t->is(sfWidget::getCharset(), 'ISO-8859-1', '::setCharset() changes the charset to use for widgets');

// ::setXhtml() ::isXhtml()
$t->diag('::setXhtml() ::isXhtml()');
$w = new MyWidget();
$t->is(sfWidget::isXhtml(), true, '::isXhtml() return true if the widget must returns XHTML tags');
sfWidget::setXhtml(false);
$t->is($w->renderTag('input', array('value' => 'Test')), '<input value="Test">', '::setXhtml() changes the value of the XHTML tag');

// ->getJavaScripts() ->getStylesheets()
$t->diag('->getJavaScripts() ->getStylesheets()');
$w = new MyWidget();
$t->is($w->getJavaScripts(), array(), '->getJavaScripts() returns an array of stylesheets');
$t->is($w->getStylesheets(), array(), '->getStylesheets() returns an array of JavaScripts');
