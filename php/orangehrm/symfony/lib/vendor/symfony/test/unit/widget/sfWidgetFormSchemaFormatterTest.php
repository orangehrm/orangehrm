<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(28);

class MyFormatter extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<li>\n  %error%%label%\n  %field%%help%\n%hidden_fields%</li>\n",
    $errorRowFormat  = "<li>\n%errors%</li>\n",
    $decoratorFormat = "<ul>\n  %content%</ul>";

  public function unnestErrors($errors, $prefix = '')
  {
    return parent::unnestErrors($errors, $prefix);
  }
  
  static public function dropTranslationCallable()
  {
    self::$translationCallable = null;
  }
}

$w1 = new sfWidgetFormInputText();
$w2 = new sfWidgetFormInputText();
$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2));
$f = new MyFormatter($w);

// ->formatRow()
$t->diag('->formatRow()');
$output = <<<EOF
<li>
  <label>label</label>
  <input /><p>help</p>
</li>

EOF;
$t->is($f->formatRow('<label>label</label>', '<input />', array(), '<p>help</p>', ''), fix_linebreaks($output), '->formatRow() formats a field in a row');

// ->formatErrorRow()
$t->diag('->formatErrorRow()');
$output = <<<EOF
<li>
  <ul class="error_list">
    <li>Global error</li>
    <li>id: required</li>
    <li>1 > sub_id: required</li>
  </ul>
</li>

EOF;
$t->is($f->formatErrorRow(array('Global error', 'id' => 'required', array('sub_id' => 'required'))), fix_linebreaks($output), '->formatErrorRow() formats an array of errors in a row');

// ->unnestErrors()
$t->diag('->unnestErrors()');
$f->setErrorRowFormatInARow("<li>%error%</li>");
$f->setNamedErrorRowFormatInARow("<li>%name%: %error%</li>");
$errors = array('foo', 'bar', 'foobar' => 'foobar');
$t->is($f->unnestErrors($errors), array('<li>foo</li>', '<li>bar</li>', '<li>foobar: foobar</li>'), '->unnestErrors() returns an array of formatted errors');
$errors = array('foo', 'bar' => array('foo', 'foobar' => 'foobar'));
$t->is($f->unnestErrors($errors), array('<li>foo</li>', '<li>foo</li>', '<li>bar > foobar: foobar</li>'), '->unnestErrors() unnests errors');

foreach (array('RowFormat', 'ErrorRowFormat', 'ErrorListFormatInARow', 'ErrorRowFormatInARow', 'NamedErrorRowFormatInARow', 'DecoratorFormat') as $method)
{
  $getter = sprintf('get%s', $method);
  $setter = sprintf('set%s', $method);
  $t->diag(sprintf('->%s() ->%s()', $getter, $setter));
  $f->$setter($value = rand(1, 99999));
  $t->is($f->$getter(), $value, sprintf('->%s() ->%s()', $getter, $setter));
}

$t->diag('::setTranslationCallable() ::getTranslationCallable()');
function my__($string)
{
  return sprintf('[%s]', $string);
}

class myI18n
{
  static public function __($string)
  {
    return my__($string);
  }
}
MyFormatter::setTranslationCallable('my__');

$t->is(MyFormatter::getTranslationCallable(), 'my__', 'get18nCallable() retrieves i18n callable correctly');

MyFormatter::setTranslationCallable(new sfCallable('my__'));
$t->isa_ok(MyFormatter::getTranslationCallable(), 'sfCallable', 'get18nCallable() retrieves i18n sfCallable correctly');

try
{
  $f->setTranslationCallable('foo');
  $t->fail('setTranslationCallable() does not throw InvalidException when i18n callable is invalid');
}
catch (InvalidArgumentException $e)
{
  $t->pass('setTranslationCallable() throws InvalidException if i18n callable is not a valid callable');
}
catch (Exception $e)
{
  $t->fail('setTranslationCallable() throws unexpected exception');
}

$t->diag('->translate()');
$f = new MyFormatter(new sfWidgetFormSchema());
$t->is($f->translate('label'), '[label]', 'translate() call i18n sfCallable as expected');

MyFormatter::setTranslationCallable(array('myI18n', '__'));
$t->is($f->translate('label'), '[label]', 'translate() call i18n callable as expected');

$t->diag('->generateLabel() ->generateLabelName() ->setLabel() ->setLabels()');
MyFormatter::dropTranslationCallable();
$w = new sfWidgetFormSchema(array(
  'author_id'  => new sfWidgetFormInputText(),
  'first_name' => new sfWidgetFormInputText(),
  'last_name'  => new sfWidgetFormInputText(),
));
$f = new MyFormatter($w);
$t->is($f->generateLabelName('first_name'), 'First name', '->generateLabelName() generates a label value from a label name');
$t->is($f->generateLabelName('author_id'), 'Author', '->generateLabelName() removes _id from auto-generated labels');

$w->setLabels(array('first_name' => 'The first name'));
$t->is($f->generateLabelName('first_name'), 'The first name', '->setLabels() changes all current labels');

$w->setLabel('first_name', 'A first name');
$t->is($f->generateLabelName('first_name'), 'A first name', '->setLabel() sets a label value');

$w->setLabel('first_name', false);
$t->is($f->generateLabel('first_name'), '', '->generateLabel() returns an empty string if the label is false');

$w->setLabel('first_name', 'Your First Name');
$t->is($f->generateLabel('first_name'), '<label for="first_name">Your First Name</label>', '->generateLabelName() returns a label tag');
$t->is($f->generateLabel('first_name', array('class' => 'foo')), '<label class="foo" for="first_name">Your First Name</label>', '->generateLabelName() returns a label tag with optional HTML attributes');
$t->is($f->generateLabel('first_name', array('for' => 'myid')), '<label for="myid">Your First Name</label>', '->generateLabelName() returns a label tag with specified for-id');

$w->setLabel('last_name', 'Your Last Name');
$t->is($f->generateLabel('last_name'), '<label for="last_name">Your Last Name</label>', '->generateLabelName() returns a label tag');
MyFormatter::setTranslationCallable('my__');
$t->is($f->generateLabel('last_name'), '<label for="last_name">[Your Last Name]</label>', '->generateLabelName() returns a i18ned label tag');

// ->setTranslationCatalogue() ->getTranslationCatalogue()
class MyFormatter2 extends sfWidgetFormSchemaFormatter
{
  
}

$f = new MyFormatter2(new sfWidgetFormSchema(array()));
$f->setTranslationCatalogue('foo');
$t->is($f->getTranslationCatalogue(), 'foo', 'setTranslationCatalogue() has set the i18n catalogue correctly');
$t->diag('->setTranslationCatalogue() ->getTranslationCatalogue()');
try
{
  $f->setTranslationCatalogue(array('foo'));
  $t->fail('setTranslationCatalogue() does not throw an exception when catalogue name is incorrectly typed');
}
catch (InvalidArgumentException $e)
{
  $t->pass('setTranslationCatalogue() throws an exception when catalogue name is incorrectly typed');
}

function ___my($s, $p, $c)
{
  return $c;
}

$f = new MyFormatter2(new sfWidgetFormSchema());
$f->setTranslationCallable('___my');
$f->setTranslationCatalogue('bar');
$t->is($f->translate('foo', array()), 'bar', 'translate() passes back the catalogue to the translation callable');
