<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(95);

$w1 = new sfWidgetFormInputText(array(), array('class' => 'foo1'));
$w2 = new sfWidgetFormInputText();

// __construct()
$t->diag('__construct()');
$w = new sfWidgetFormSchema();
$t->is($w->getFields(), array(), '__construct() can take no argument');
$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2));
$w1->setParent($w); $w2->setParent($w);
$t->ok($w->getFields() == array('w1' => $w1, 'w2' => $w2), '__construct() can take an array of named sfWidget objects');
try
{
  $w = new sfWidgetFormSchema('string');
  $t->fail('__construct() throws a exception when passing a non supported first argument');
}
catch (InvalidArgumentException $e)
{
  $t->pass('__construct() throws an exception when passing a non supported first argument');
}

$t->is($w->getFormFormatterName(), 'table', '__construct() sets "form_formatter" option to "table" by default');
$w = new sfWidgetFormSchema(array(), array('form_formatter' => 'list'));
$t->is($w->getFormFormatterName(), 'list', '__construct() can override the default value for the "form_formatter" option');

$t->is($w->getNameFormat(), '%s', '__construct() sets "name_format" option to "table" by default');
$w = new sfWidgetFormSchema(array(), array('name_format' => 'name_%s'));
$t->is($w->getNameFormat(), 'name_%s', '__construct() can override the default value for the "name_format" option');

// implements ArrayAccess
$t->diag('implements ArrayAccess');
$w = new sfWidgetFormSchema();
$w['w1'] = $w1;
$w['w2'] = $w2;
$w1->setParent($w); $w2->setParent($w);
$t->ok($w->getFields() == array('w1' => $w1, 'w2' => $w2), 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');
$t->is($w1->getParent(), $w, 'The widget schema is associated with the fields');
$t->is($w2->getParent(), $w, 'The widget schema is associated with the fields');

try
{
  $w['w1'] = 'string';
  $t->fail('sfWidgetFormSchema implements the ArrayAccess interface for the fields');
}
catch (LogicException $e)
{
  $t->pass('sfWidgetFormSchema implements the ArrayAccess interface for the fields');
}

$w = new sfWidgetFormSchema(array('w1' => $w1));
$t->is(isset($w['w1']), true, 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');
$t->is(isset($w['w2']), false, 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');

$w = new sfWidgetFormSchema(array('w1' => $w1));
$w1->setParent($w); $w2->setParent($w);
$t->ok($w['w1'] == $w1, 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');
$t->is($w['w2'], null, 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');

$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2));
unset($w['w1']);
$t->is($w['w1'], null, 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');
$t->is($w->getPositions(), array('w2'), 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');

// unset with numeric keys
$w = new sfWidgetFormSchema(array('0' => $w1, 'w2' => $w2));
unset($w['w2']);
$t->is($w['w2'], null, 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');
$t->is($w->getPositions(), array('0'), 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');

$w = new sfWidgetFormSchema(array('w1' => $w1, '0' => $w2));
unset($w[0]);
$t->is($w[0], null, 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');
$t->is($w->getPositions(), array('w1'), 'sfWidgetFormSchema implements the ArrayAccess interface for the fields');


// ->addFormFormatter() ->setFormFormatterName() ->getFormFormatterName() ->getFormFormatter() ->getFormFormatters()
$t->diag('->addFormFormatter() ->setFormFormatterName() ->getFormFormatterName() ->getFormFormatter() ->getFormFormatters()');
$w = new sfWidgetFormSchema(array('w1' => $w1));

$t->is(get_class($w->getFormFormatter()), 'sfWidgetFormSchemaFormatterTable', '->getFormFormatter() returns a sfWidgetSchemaFormatter object');

$w->addFormFormatter('custom', $customFormatter = new sfWidgetFormSchemaFormatterList($w));
$w->setFormFormatterName('custom');
$t->is(get_class($w->getFormFormatter()), 'sfWidgetFormSchemaFormatterList', '->addFormFormatter() associates a name with a sfWidgetSchemaFormatter object');

$w->setFormFormatterName('list');
$t->is(get_class($w->getFormFormatter()), 'sfWidgetFormSchemaFormatterList', '->setFormFormatterName() set the names of the formatter to use when rendering');

$w->setFormFormatterName('nonexistant');
try
{
  $w->getFormFormatter();
  $t->fail('->setFormFormatterName() throws a InvalidArgumentException when the form format name is not associated with a formatter');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->setFormFormatterName() throws a InvalidArgumentException when the form format name is not associated with a formatter');
}

$formatterNames = array_keys($w->getFormFormatters());
sort($formatterNames);
$t->is($formatterNames, array('custom', 'list', 'table'), '->getFormFormatters() returns an array of all formatter for this widget schema');

// ->setNameFormat() ->getNameFormat() ->generateName()
$t->diag('->setNameFormat() ->getNameFormat() ->generateName()');
$w = new sfWidgetFormSchema();
$t->is($w->generateName('foo'), 'foo', '->generateName() returns a HTML name attribute value for a given field name');
$w->setNameFormat('article[%s]');
$t->is($w->generateName('foo'), 'article[foo]', '->setNameFormat() changes the name format');
$t->is($w->getNameFormat(), 'article[%s]', '->getNameFormat() returns the name format');

$w->setNameFormat(false);
$t->is($w->generateName('foo'), 'foo', '->generateName() returns the name unchanged if the format is false');

try
{
  $w->setNameFormat('foo');
  $t->fail('->setNameFormat() throws an InvalidArgumentException if the format does not contain %s');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->setNameFormat() throws an InvalidArgumentException if the format does not contain %s');
}

$w = new sfWidgetFormSchema(array(
  'author' => new sfWidgetFormSchema(array(
    'first_name' => new sfWidgetFormInputText(),
    'company'    => new sfWidgetFormSchema(array(
      'name' => new sfWidgetFormInputText(),
    )),
  )),
));
$w->setNameFormat('article[%s]');
$t->is($w['author']->generateName('first_name'), 'article[author][first_name]', '->generateName() returns a HTML name attribute value for a given field name');
$t->is($w['author']['company']->generateName('name'), 'article[author][company][name]', '->generateName() returns a HTML name attribute value for a given field name');

// ->getParent() ->setParent()
$t->diag('->getParent() ->setParent()');
$author = new sfWidgetFormSchema(array('first_name' => new sfWidgetFormInputText()));
$company = new sfWidgetFormSchema(array('name' => new sfWidgetFormInputText()));
$t->is($company->getParent(), null, '->getParent() returns null if there is no parent widget schema');
$company->setParent($author);
$t->is($company->getParent(), $author, '->getParent() returns the parent widget schema');

// ->setLabels() ->setLabel() ->getLabels() ->getLabel() ->generateLabelName()
$t->diag('->setLabels() ->setLabel() ->getLabels() ->getLabel() ->generateLabelName()');
$w = new sfWidgetFormSchema(array('first_name' => new sfWidgetFormInputText()));
$w->setLabel('first_name', 'A first name');
$t->is($w->getLabels(), array('first_name' => 'A first name'), '->getLabels() returns all current labels');

$w->setLabels(array('first_name' => 'The first name'));
$t->is($w->getFormFormatter()->generateLabelName('first_name'), 'The first name', '->setLabels() changes all current labels');

$w->setLabel('first_name', 'A first name');
$t->is($w->getFormFormatter()->generateLabelName('first_name'), 'A first name', '->setLabel() sets a label value');

// ->setHelps() ->getHelps() ->setHelp() ->getHelp()
$t->diag('->setHelps() ->getHelps() ->setHelp() ->getHelp()');
$w = new sfWidgetFormSchema();
$w->setHelps(array('first_name', 'Please, provide your first name'));
$t->is($w->getHelps(), array('first_name', 'Please, provide your first name'), '->setHelps() changes all help messages');
$w->setHelp('last_name', 'Please, provide your last name');
$t->is($w->getHelp('last_name'), 'Please, provide your last name', '->setHelp() changes one help message');

// ->getLabel() ->setLabel() ->getLabels() ->setLabels()
$t->diag('->getLabel() ->setLabel() ->getLabels() ->setLabels()');
$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2));
$w->setLabels(array('w1' => 'foo'));
$t->is($w->getLabels(), array('w1' => 'foo', 'w2' => null), '->getLabels() returns the labels');
$t->is($w->getLabel('w1'), 'foo', '->getLabel() returns the label for a given field');
$w->setLabel('w2', 'foo');
$t->is($w->getLabels(), array('w1' => 'foo', 'w2' => 'foo'), '->setLabel() sets a label for a given field');
$w->setLabel('foo');
$t->is($w->getLabel(), 'foo', '->setLabel() can also set the label for the widget schema');

// ->getDefault() ->setDefault() ->getDefaults() ->setDefaults()
$t->diag('->getDefault() ->setDefault() ->getDefaults() ->setDefaults()');
$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2));
$w->setDefaults(array('w1' => 'foo'));
$t->is($w->getDefaults(), array('w1' => 'foo', 'w2' => null), '->getDefaults() returns the default values');
$t->is($w->getDefault('w1'), 'foo', '->getDefault() returns the default value for a given field');
$w->setDefault('w2', 'foo');
$t->is($w->getDefaults(), array('w1' => 'foo', 'w2' => 'foo'), '->setDefault() sets a default value for a given field');

// ->needsMultipartForm()
$t->diag('->needsMultipartForm()');
$w = new sfWidgetFormSchema(array('w1' => $w1));
$t->is($w->needsMultipartForm(), false, '->needsMultipartForm() returns false if the form schema does not have a widget that needs a multipart form');
$w['w2'] = new sfWidgetFormInputFile();
$t->is($w->needsMultipartForm(), true, '->needsMultipartForm() returns true if the form schema does not have a widget that needs a multipart form');

// ->renderField()
$t->diag('->renderField()');
$w = new sfWidgetFormSchema(array('first_name' => $w1));
$t->is($w->renderField('first_name', 'Fabien'), '<input class="foo1" type="text" name="first_name" value="Fabien" id="first_name" />', '->renderField() renders a field to HTML');

$ww = clone $w1;
$ww->setAttribute('id', 'foo');
$ww->setAttribute('style', 'color: blue');
$w = new sfWidgetFormSchema(array('first_name' => $ww));
$t->is($w->renderField('first_name', 'Fabien'), '<input class="foo1" id="foo" style="color: blue" type="text" name="first_name" value="Fabien" />', '->renderField() renders a field to HTML');

try
{
  $w->renderField('last_name', 'Potencier');
  $t->fail('->renderField() throws an InvalidArgumentException if the field does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->renderField() throws an InvalidArgumentException if the field does not exist');
}

// ->setPositions() ->getPositions()
$t->diag('->setPositions() ->getPositions()');
$w = new sfWidgetFormSchema();
$w['w1'] = $w1;
$w['w2'] = $w2;
$w->setPositions(array('w2', 'w1'));
$t->is($w->getPositions(), array('w2', 'w1'), '->setPositions() changes all field positions');
$w->setPositions(array('w1', 'w2'));
$t->is($w->getPositions(), array('w1', 'w2'), '->setPositions() changes all field positions');

$w = new sfWidgetFormSchema();
$w['w1'] = $w1;
$w['w2'] = $w2;
$w['w1'] = $w1;
$t->is($w->getPositions(), array('w1', 'w2'), '->setPositions() changes all field positions');

$w = new sfWidgetFormSchema();
$w['w1'] = $w1;
$w['w2'] = $w2;
$w->setPositions(array('w1', 'w2', 'w1'));
$t->is($w->getPositions(), array('w1', 'w2'), '->setPositions() does not repeat the fields');

try
{
  $w->setPositions(array('w1', 'w2', 'w3'));
  $t->fail('->setPositions() throws an InvalidArgumentException if you give it a non existant field name');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->setPositions() throws an InvalidArgumentException if you give it a non existant field name');
}

try
{
  $w->setPositions(array('w1'));
  $t->fail('->setPositions() throws an InvalidArgumentException if you miss a field name');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->setPositions() throws an InvalidArgumentException if you miss a field name');
}

// ->moveField()
$t->diag('->moveField()');
$w = new sfWidgetFormSchema();
$w['w1'] = $w1;
$w['w2'] = $w2;
$w['w3'] = $w1;
$w['w4'] = $w2;
$w->moveField('w1', sfWidgetFormSchema::BEFORE, 'w3');
$t->is($w->getPositions(), array('w2', 'w1', 'w3', 'w4'), '->moveField() can move a field before another one');
$w->moveField('w1', sfWidgetFormSchema::LAST);
$t->is($w->getPositions(), array('w2', 'w3', 'w4', 'w1'), '->moveField() can move a field to the end');
$w->moveField('w1', sfWidgetFormSchema::FIRST);
$t->is($w->getPositions(), array('w1', 'w2', 'w3', 'w4'), '->moveField() can move a field to the beginning');
$w->moveField('w1', sfWidgetFormSchema::AFTER, 'w3');
$t->is($w->getPositions(), array('w2', 'w3', 'w1', 'w4'), '->moveField() can move a field before another one');
try
{
  $w->moveField('w1', sfWidgetFormSchema::AFTER);
  $t->fail('->moveField() throws an LogicException if you don\'t pass a relative field name with AFTER');
}
catch (LogicException $e)
{
  $t->pass('->moveField() throws an LogicException if you don\'t pass a relative field name with AFTER');
}
try
{
  $w->moveField('w1', sfWidgetFormSchema::BEFORE);
  $t->fail('->moveField() throws an LogicException if you don\'t pass a relative field name with BEFORE');
}
catch (LogicException $e)
{
  $t->pass('->moveField() throws an LogicException if you don\'t pass a relative field name with BEFORE');
}
// this case is especially interesting because the numeric array keys are always
// converted to integers by array
// furthermore, (int)0 == (string)'w1' succeeds
$w = new sfWidgetFormSchema(array('w1' => $w1, '0' => $w2));
$w->moveField(0, sfWidgetFormSchema::FIRST);
$t->is($w->getPositions(), array('0', 'w1'), '->moveField() compares field names as strings');

$w = new sfWidgetFormSchema(array('w1' => $w1, '0' => $w2));
$w->moveField('0', sfWidgetFormSchema::FIRST);
$t->is($w->getPositions(), array('0', 'w1'), '->moveField() compares field names as strings');

$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2, '0' => $w1));
$w->moveField('w1', sfWidgetFormSchema::BEFORE, '0');
$t->is($w->getPositions(), array('w2', 'w1', '0'), '->moveField() compares field names as strings');

$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2, '0' => $w1));
$w->moveField('w1', sfWidgetFormSchema::BEFORE, 0);
$t->is($w->getPositions(), array('w2', 'w1', '0'), '->moveField() compares field names as strings');

// ->getGlobalErrors()
$t->diag('->getGlobalErrors()');
$w = new sfWidgetFormSchema();
$w['w1'] = $w1;
$w['w2'] = new sfWidgetFormInputHidden();
$w['w3'] = new sfWidgetFormSchema();
$w['w3']['w1'] = $w1;
$w['w3']['w2'] = new sfWidgetFormInputHidden();
$errors = array(
  'global error',
  'w1' => 'error for w1',
  'w2' => 'error for w2',
  'w4' => array(
    'w1' => 'error for w4/w1',
    'w2' => 'error for w4/w2',
    'w3' => 'error for w4/w3',
  ),
  'w4' => 'error for w4',
);
$t->is($w->getGlobalErrors($errors), array('global error', 'error for w4', 'W2' => 'error for w2'), '->getGlobalErrors() returns an array of global errors, errors for hidden fields, and errors for non existent fields');

// ->render()
$t->diag('->render()');
$w = new sfWidgetFormSchema();

try
{
  $w->render(null, 'string');
  $t->fail('->render() throws an InvalidArgumentException if the second argument is not an array');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->render() throws an InvalidArgumentException if the second argument is not an array');
}

$w['first_name'] = $w1;
$w['last_name'] = $w2;
$w['id'] = new sfWidgetFormInputHidden();
$w->setAttribute('style', 'padding: 5px');
$w->setNameFormat('article[%s]');
$w->setIdFormat('id_%s');
$expected = <<<EOF
<tr><td colspan="2">
  <ul class="error_list">
    <li>Global error message</li>
    <li>Id: Required</li>
  </ul>
</td></tr>
<tr>
  <th><label style="padding: 5px" for="id_article_first_name">First name</label></th>
  <td>  <ul class="error_list">
    <li>Too short</li>
  </ul>
<input class="foo" type="text" name="article[first_name]" value="Fabien" id="id_article_first_name" /></td>
</tr>
<tr>
  <th><label style="padding: 5px" for="id_article_last_name">Last name</label></th>
  <td><input type="text" name="article[last_name]" value="Potencier" class="bar" id="id_article_last_name" /><input type="hidden" name="article[id]" id="id_article_id" /></td>
</tr>

EOF;
$rendered = $w->render(null, array('first_name' => 'Fabien', 'last_name' => 'Potencier'), array('first_name' => array('class' => 'foo'), 'last_name' => array('class' => 'bar')), array('first_name' => 'Too short', 'Global error message', 'id' => 'Required'));
$t->is($rendered, fix_linebreaks($expected), '->render() renders a schema to HTML');

$t->diag('Widget schema with only hidden fields');
$w = new sfWidgetFormSchema(array('w1' => new sfWidgetFormInputHidden()));
$t->is($w->render(null), '<input type="hidden" name="w1" id="w1" />', '->render() is able to render widget schema that only contains hidden fields');

$t->diag('Widget schema with an embed form as the last field and hidden fields');
$w = new sfWidgetFormSchema();
$w['w1'] = new sfWidgetFormInputHidden();
$ew = new sfWidgetFormSchema(array('w3' => new sfWidgetFormInputText()));
$w['w4'] = new sfWidgetFormSchemaDecorator($ew, $w->getFormFormatter()->getDecoratorFormat());
$expected = <<<EOF
<tr>
  <th>W4</th>
  <td>
    <table>
      <tr>
        <th><label for="w4_w3">W3</label></th>
        <td><input type="text" name="w4[w3]" id="w4_w3" /></td>
      </tr>
    </table>
    <input type="hidden" name="w1" id="w1" />
  </td>
</tr>

EOF;
$t->is(str_replace("\n", '', preg_replace('/^ +/m', '', $w->render(null))), str_replace("\n", '', preg_replace('/^ +/m', '', fix_linebreaks($expected))), '->render() is able to render widget schema that only contains hidden fields when the last field is a form');

// __clone()
$t->diag('__clone()');
$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2));
$w1->setParent($w); $w2->setParent($w);
$format1 = new sfWidgetFormSchemaFormatterList($w);
$format1->setTranslationCatalogue('english');
$w->addFormFormatter('testFormatter', $format1);
$w1 = clone $w;
$f1 = $w1->getFields();
$f = $w->getFields();
$t->is(array_keys($f1), array_keys($f), '__clone() clones embedded widgets');
foreach ($f1 as $name => $widget)
{
  $t->ok($widget !== $f[$name], '__clone() clones embedded widgets');
  $t->ok($widget->getParent() === $w1, 'The parents hafe been changed');
  // avoid recursive dependencies at comparing
  $widget->setParent(null); $f[$name]->setParent(null);
  $t->ok($widget == $f[$name], '__clone() clones embedded widgets');
}
$format1->setTranslationCatalogue('french');
$formatters = $w1->getFormFormatters();
$t->is(count($formatters), 1 , '__clone() returns a sfWidgetFormSchema that has the Formatters attached');
$t->is($formatters['testFormatter']->getTranslationCatalogue(), 'english', '__clone() clones formatters, so that changes to the original one have no effect to the cloned formatter.');

$w = new sfWidgetFormSchema();
$w->addFormFormatter('table', new sfWidgetFormSchemaFormatterTable($w));
$w->addFormFormatter('list', new sfWidgetFormSchemaFormatterList($w));
$w1 = clone $w;
$f1 = $w1->getFormFormatters();
$f = $w->getFormFormatters();
$t->is(array_keys($f1), array_keys($f), '__clone() clones form formatters');
foreach ($f1 as $key => $formFormatter)
{
  $t->ok($formFormatter !== $f[$key], '__clone() clones form formatters');
  $t->is(get_class($formFormatter), get_class($f[$key]), '__clone() clones form formatters');

  $t->ok($formFormatter->getWidgetSchema() !== $f[$key]->getWidgetSchema(), '__clone() clones form formatters');
  $t->is(get_class($formFormatter->getWidgetSchema()), get_class($f[$key]->getWidgetSchema()), '__clone() clones form formatters');
}

// setDefaultFormFormatterName()
$t->diag('setDefaultFormFormatterName()');
$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2));
$t->isa_ok($w->getFormFormatter(), 'sfWidgetFormSchemaFormatterTable', 'setDefaultFormFormatterName() has the "sfWidgetFormSchemaFormatterTable" form formatter by default');

sfWidgetFormSchema::setDefaultFormFormatterName('list');
$w = new sfWidgetFormSchema(array('w1' => $w1, 'w2' => $w2));
$t->isa_ok($w->getFormFormatter(), 'sfWidgetFormSchemaFormatterList', 'setDefaultFormFormatterName() changes the default form formatter name correctly');

class MyWidget extends sfWidgetForm
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('name');
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    return null;
  }

  public function getJavaScripts()
  {
    return array('/path/to/a/'.$this->getOption('name').'.js', '/path/to/foo.js');
  }

  public function getStylesheets()
  {
    return array('/path/to/a/'.$this->getOption('name').'.css' => 'all', '/path/to/foo.css' => 'all');
  }
}

// ->getJavaScripts() ->getStylesheets()
$t->diag('->getJavaScripts() ->getStylesheets()');
$w = new sfWidgetFormSchema(array(
  'foo' => new MyWidget(array('name' => 'foo')),
  'bar' => new MyWidget(array('name' => 'bar')),
));
$t->is($w->getJavaScripts(), array('/path/to/a/foo.js', '/path/to/foo.js', '/path/to/a/bar.js'), '->getJavaScripts() returns an array of stylesheets');
$t->is($w->getStylesheets(), array('/path/to/a/foo.css' => 'all', '/path/to/foo.css' => 'all', '/path/to/a/bar.css' => 'all'), '->getStylesheets() returns an array of JavaScripts');
