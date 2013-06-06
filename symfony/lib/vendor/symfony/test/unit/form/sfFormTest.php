<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(163);

class FormTest extends sfForm
{
  public function getCSRFToken($secret = null)
  {
    return "*$secret*";
  }
}

class TestForm1 extends FormTest
{
  public function configure()
  {
    $this->disableCSRFProtection();
    $this->setWidgets(array(
      'a' => new sfWidgetFormInputText(),
      'b' => new sfWidgetFormInputText(),
      'c' => new sfWidgetFormInputText(),
    ));
    $this->setValidators(array(
      'a' => new sfValidatorString(array('min_length' => 2)),
      'b' => new sfValidatorString(array('max_length' => 3)),
      'c' => new sfValidatorString(array('max_length' => 1000)),
    ));
    $this->getWidgetSchema()->setLabels(array(
      'a' => '1_a',
      'b' => '1_b',
      'c' => '1_c',
    ));
    $this->getWidgetSchema()->setHelps(array(
      'a' => '1_a',
      'b' => '1_b',
      'c' => '1_c',
    ));
  }
}

class TestForm2 extends FormTest
{
  public function configure()
  {
    $this->disableCSRFProtection();
    $this->setWidgets(array(
      'c' => new sfWidgetFormTextarea(),
      'd' => new sfWidgetFormTextarea(),
    ));
    $this->setValidators(array(
      'c' => new sfValidatorPass(),
      'd' => new sfValidatorString(array('max_length' => 5)),
    ));
    $this->getWidgetSchema()->setLabels(array(
      'c' => '2_c',
      'd' => '2_d',
    ));
    $this->getWidgetSchema()->setHelps(array(
      'c' => '2_c',
      'd' => '2_d',
    ));
    $this->validatorSchema->setPreValidator(new sfValidatorPass());
    $this->validatorSchema->setPostValidator(new sfValidatorPass());
  }
}

class TestForm3 extends FormTest
{
  public function configure()
  {
    $this->disableLocalCSRFProtection();
  }
}

class TestForm4 extends FormTest
{
  public function configure()
  {
    $this->enableLocalCSRFProtection($this->getOption('csrf_secret'));
  }
}

class NumericFieldsForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      '5' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      '5' => new sfValidatorString(),
    ));

    $this->widgetSchema->setLabels(array('5' => 'label'.$this->getOption('salt')));
    $this->widgetSchema->setHelps(array('5' => 'help'.$this->getOption('salt')));
  }
}

sfForm::disableCSRFProtection();

// __construct()
$t->diag('__construct');
$f = new FormTest();
$t->ok($f->getValidatorSchema() instanceof sfValidatorSchema, '__construct() creates an empty validator schema');
$t->ok($f->getWidgetSchema() instanceof sfWidgetFormSchema, '__construct() creates an empty widget form schema');

$f = new sfForm(array('first_name' => 'Fabien'));
$t->is($f->getDefaults(), array('first_name' => 'Fabien'), '__construct() can take an array of default values as its first argument');

$f = new FormTest(array(), array(), 'secret');
$v = $f->getValidatorSchema();
$t->ok($f->isCSRFProtected(), '__construct() takes a CSRF secret as its second argument');
$t->is($v[sfForm::getCSRFFieldName()]->getOption('token'), '*secret*', '__construct() takes a CSRF secret as its second argument');

sfForm::enableCSRFProtection();
$f = new FormTest(array(), array(), false);
$t->ok(!$f->isCSRFProtected(), '__construct() can disable the CSRF protection by passing false as the second argument');

$f = new FormTest();
$t->ok($f->isCSRFProtected(), '__construct() uses CSRF protection if null is passed as the second argument and it\'s enabled globally');

// ->getOption() ->setOption() ->getOptions()
$t->diag('->getOption() ->setOption()');
$f = new FormTest(array(), array('foo' => 'bar'));
$t->is($f->getOption('foo'), 'bar', '__construct takes an option array as its second argument');
$f->setOption('bar', 'foo');
$t->is($f->getOption('bar'), 'foo', '->setOption() changes the value of an option');
$t->is_deeply($f->getOptions(), array('foo' => 'bar', 'bar' => 'foo'), '->getOptions() returns all options');

sfForm::disableCSRFProtection();

// ->setDefault() ->getDefault() ->hasDefault() ->setDefaults() ->getDefaults()
$t->diag('->setDefault() ->getDefault() ->hasDefault() ->setDefaults() ->getDefaults()');
$f = new FormTest();
$f->setDefaults(array('first_name' => 'Fabien'));
$t->is($f->getDefaults(), array('first_name' => 'Fabien'), 'setDefaults() sets the form default values');
$f->setDefault('last_name', 'Potencier');
$t->is($f->getDefaults(), array('first_name' => 'Fabien', 'last_name' => 'Potencier'), 'setDefault() sets a default value');
$t->is($f->hasDefault('first_name'), true, 'hasDefault() returns true if the form has a default value for the given field');
$t->is($f->hasDefault('name'), false, 'hasDefault() returns false if the form does not have a default value for the given field');
$t->is($f->getDefault('first_name'), 'Fabien', 'getDefault() returns a default value for a given field');
$t->is($f->getDefault('name'), null, 'getDefault() returns null if the form does not have a default value for a given field');

sfForm::enableCSRFProtection('*mygreatsecret*');
$f = new FormTest();
$f->setDefaults(array('first_name' => 'Fabien'));
$t->is($f->getDefault('_csrf_token'), $f->getCSRFToken('*mygreatsecret*'), '->getDefaults() keeps the CSRF token default value');

$f = new FormTest(array(), array(), false);
$f->setDefaults(array('first_name' => 'Fabien'));
$t->is(array_key_exists('_csrf_token', $f->getDefaults()), false, '->setDefaults() does not set the CSRF token if CSRF is disabled');
sfForm::disableCSRFProtection();

// ->getName()
$t->diag('->getName()');
$f = new FormTest();
$w = new sfWidgetFormSchema();
$f->setWidgetSchema($w);
$t->ok($f->getName() === false, '->getName() returns false if the name format is not an array');
$w->setNameFormat('foo_%s');
$t->ok($f->getName() === false, '->getName() returns false if the name format is not an array');
$w->setNameFormat('foo[%s]');
$t->is($f->getName(), 'foo', '->getName() returns the name under which user data can be retrieved');

// ::enableCSRFProtection() ::disableCSRFProtection() ->isCSRFProtected()
$t->diag('::enableCSRFProtection() ::disableCSRFProtection()');
sfForm::enableCSRFProtection();
$f1 = new FormTest();
$t->ok($f1->isCSRFProtected(),'::enableCSRFProtection() enabled CSRF protection for all future forms');
sfForm::disableCSRFProtection();
$f2 = new FormTest();
$t->ok(!$f2->isCSRFProtected(),'::disableCSRFProtection() disables CSRF protection for all future forms');
$t->ok($f1->isCSRFProtected(),'::enableCSRFProtection() enabled CSRF protection for all future forms');
sfForm::enableCSRFProtection();
$t->ok(!$f2->isCSRFProtected(),'::disableCSRFProtection() disables CSRF protection for all future forms');

$f = new FormTest(array(), array(), false);
$t->ok(!$f->isCSRFProtected(), '->isCSRFProtected() returns true if the form is CSRF protected');

sfForm::enableCSRFProtection('mygreatsecret');
$f = new FormTest();
$v = $f->getValidatorSchema();
$t->is($v[sfForm::getCSRFFieldName()]->getOption('token'), '*mygreatsecret*', '::enableCSRFProtection() can take a secret argument');

// ->enableLocalCSRFProtection() ->disableLocalCSRFProtection()
$t->diag('->enableLocalCSRFProtection() ->disableLocalCSRFProtection()');
$f = new TestForm3();
sfForm::disableCSRFProtection();
$t->ok(!$f->isCSRFProtected(),'->disableLocalCSRFProtection() disabled CSRF protection for the current form');
sfForm::enableCSRFProtection();
$t->ok(!$f->isCSRFProtected(),'->disableLocalCSRFProtection() disabled CSRF protection for the current form, even if the global CSRF protection is enabled');
$f = new TestForm3(array(), array(), 'foo');
$t->ok(!$f->isCSRFProtected(),'->disableLocalCSRFProtection() disabled CSRF protection for the current form, even a CSRF secret is provided in the constructor');
sfForm::disableCSRFProtection();
$f = new TestForm4();
$t->ok($f->isCSRFProtected(), '->enableLocalCSRFProtection() enables CSRF protection when passed null and global CSRF is disabled');
$f = new TestForm4(array(), array('csrf_secret' => '**localsecret**'));
$t->ok($f->isCSRFProtected(), '->enableLocalCSRFProtection() enables CSRF protection when passed a string global CSRF is disabled');

// ::getCSRFFieldName() ::setCSRFFieldName()
$t->diag('::getCSRFFieldName() ::setCSRFFieldName()');
sfForm::enableCSRFProtection();
sfForm::setCSRFFieldName('_token_');
$f = new FormTest();
$v = $f->getValidatorSchema();
$t->ok(isset($v['_token_']), '::setCSRFFieldName() changes the CSRF token field name');
$t->is(sfForm::getCSRFFieldName(), '_token_', '::getCSRFFieldName() returns the CSRF token field name');

// ->isMultipart()
$t->diag('->isMultipart()');
$f = new FormTest();
$t->ok(!$f->isMultipart(),'->isMultipart() returns false if the form does not need a multipart form');
$f->setWidgetSchema(new sfWidgetFormSchema(array('image' => new sfWidgetFormInputFile())));
$t->ok($f->isMultipart(),'->isMultipart() returns true if the form needs a multipart form');

// ->setValidators() ->setValidatorSchema() ->getValidatorSchema() ->setValidator() ->getValidator()
$t->diag('->setValidators() ->setValidatorSchema() ->getValidatorSchema() ->setValidator() ->getValidator()');
$f = new FormTest();
$validators = array(
  'first_name' => new sfValidatorPass(),
  'last_name' => new sfValidatorPass(),
);
$validatorSchema = new sfValidatorSchema($validators);
$f->setValidatorSchema($validatorSchema);
$t->is_deeply($f->getValidatorSchema(), $validatorSchema, '->setValidatorSchema() sets the current validator schema');
$f->setValidators($validators);
$schema = $f->getValidatorSchema();
$t->ok($schema['first_name'] == $validators['first_name'], '->setValidators() sets field validators');
$t->ok($schema['last_name'] == $validators['last_name'], '->setValidators() sets field validators');
$f->setValidator('name', $v3 = new sfValidatorPass());
$t->ok($f->getValidator('name') == $v3, '->setValidator() sets a validator for a field');

// ->setWidgets() ->setWidgetSchema() ->getWidgetSchema() ->getWidget() ->setWidget()
$t->diag('->setWidgets() ->setWidgetSchema() ->getWidgetSchema()');
$f = new FormTest();
$widgets = array(
  'first_name' => new sfWidgetFormInputText(),
  'last_name'  => new sfWidgetFormInputText(),
);
$widgetSchema = new sfWidgetFormSchema($widgets);
$f->setWidgetSchema($widgetSchema);
$t->ok($f->getWidgetSchema() == $widgetSchema, '->setWidgetSchema() sets the current widget schema');
$f->setWidgets($widgets);
$schema = $f->getWidgetSchema();
$widgets['first_name']->setParent($schema); $widgets['last_name']->setParent($schema);
$t->ok($schema['first_name'] == $widgets['first_name'], '->setWidgets() sets field widgets');
$t->ok($schema['last_name'] == $widgets['last_name'], '->setWidgets() sets field widgets');
$f->setWidget('name', $w3 = new sfWidgetFormInputText());
$w3->setParent($schema);
$t->ok($f->getWidget('name') == $w3, '->setWidget() sets a widget for a field');

// ArrayAccess interface
$t->diag('ArrayAccess interface');
$f = new FormTest();
$f->setWidgetSchema(new sfWidgetFormSchema(array(
  'first_name' => new sfWidgetFormInputText(array('default' => 'Fabien')),
  'last_name'  => new sfWidgetFormInputText(),
  'image'      => new sfWidgetFormInputFile(),
)));
$f->setValidatorSchema(new sfValidatorSchema(array(
  'first_name' => new sfValidatorPass(),
  'last_name'  => new sfValidatorPass(),
  'image'      => new sfValidatorPass(),
)));
$f->setDefaults(array(
  'image' => 'default.gif',
));
$f->embedForm('embedded', new sfForm());
$t->ok($f['first_name'] instanceof sfFormField, '"sfForm" implements the ArrayAccess interface');
$t->is($f['first_name']->render(), '<input type="text" name="first_name" value="Fabien" id="first_name" />', '"sfForm" implements the ArrayAccess interface');

try
{
  $f['image'] = 'image';
  $t->fail('"sfForm" ArrayAccess implementation does not permit to set a form field');
}
catch (LogicException $e)
{
  $t->pass('"sfForm" ArrayAccess implementation does not permit to set a form field');
}
$t->ok(isset($f['image']), '"sfForm" implements the ArrayAccess interface');
unset($f['image']);
$t->ok(!isset($f['image']), '"sfForm" implements the ArrayAccess interface');
$t->ok(!array_key_exists('image', $f->getDefaults()), '"sfForm" ArrayAccess implementation removes form defaults');
$v = $f->getValidatorSchema();
$t->ok(!isset($v['image']), '"sfForm" ArrayAccess implementation removes the widget and the validator');
$w = $f->getWidgetSchema();
$t->ok(!isset($w['image']), '"sfForm" ArrayAccess implementation removes the widget and the validator');
try
{
  $f['nonexistant'];
  $t->fail('"sfForm" ArrayAccess implementation throws a LogicException if the form field does not exist');
}
catch (LogicException $e)
{
  $t->pass('"sfForm" ArrayAccess implementation throws a LogicException if the form field does not exist');
}

unset($f['embedded']);
$t->ok(!array_key_exists('embedded', $f->getEmbeddedForms()), '"sfForm" ArrayAccess implementation removes embedded forms');

$f->bind(array(
  'first_name' => 'John',
  'last_name'  => 'Doe',
));
unset($f['first_name']);
$t->is_deeply($f->getValues(), array('last_name' => 'Doe'), '"sfForm" ArrayAccess implementation removes bound values');
$w['first_name'] = new sfWidgetFormInputText();
$t->is($f['first_name']->getValue(), '', '"sfForm" ArrayAccess implementation removes tainted values');

// Countable interface
$t->diag('Countable interface');
$f = new FormTest();
$f->setWidgetSchema(new sfWidgetFormSchema(array(
  'first_name' => new sfWidgetFormInputText(array('default' => 'Fabien')),
  'last_name'  => new sfWidgetFormInputText(),
  'image'      => new sfWidgetFormInputFile(),
)));
$t->is(count($f), 3, '"sfForm" implements the Countable interface');

// Iterator interface
$t->diag('Iterator interface');
$f = new FormTest();
$f->setWidgetSchema(new sfWidgetFormSchema(array(
  'first_name' => new sfWidgetFormInputText(array('default' => 'Fabien')),
  'last_name'  => new sfWidgetFormInputText(),
  'image'      => new sfWidgetFormInputFile(),
)));
foreach ($f as $name => $value)
{
  $values[$name] = $value;
}
$t->is(isset($values['first_name']), true, '"sfForm" implements the Iterator interface');
$t->is(isset($values['last_name']), true, '"sfForm" implements the Iterator interface');
$t->is_deeply(array_keys($values), array('first_name', 'last_name', 'image'), '"sfForm" implements the Iterator interface');

// ->useFields()
$t->diag('->useFields()');
$f = new FormTest();
$f->setWidgetSchema(new sfWidgetFormSchema(array(
  'first_name' => new sfWidgetFormInputText(),
  'last_name'  => new sfWidgetFormInputText(),
  'email'      => new sfWidgetFormInputText(),
)));
$f->useFields(array('first_name', 'last_name'));
$t->is($f->getWidgetSchema()->getPositions(), array('first_name', 'last_name'), '->useFields() removes all fields except the ones given as an argument');
$f->setWidgetSchema(new sfWidgetFormSchema(array(
  'first_name' => new sfWidgetFormInputText(),
  'last_name'  => new sfWidgetFormInputText(),
  'email'      => new sfWidgetFormInputText(),
)));
$f->useFields(array('email', 'first_name'));
$t->is($f->getWidgetSchema()->getPositions(), array('email', 'first_name'), '->useFields() reorders the fields');
$f->setWidgetSchema(new sfWidgetFormSchema(array(
  'first_name' => new sfWidgetFormInputText(),
  'last_name'  => new sfWidgetFormInputText(),
  'email'      => new sfWidgetFormInputText(),
)));
$f->useFields(array('email', 'first_name'), false);
$t->is($f->getWidgetSchema()->getPositions(), array('first_name', 'email'), '->useFields() does not reorder the fields if the second argument is false');
$f->setWidgetSchema(new sfWidgetFormSchema(array(
  'id'         => new sfWidgetFormInputHidden(),
  'first_name' => new sfWidgetFormInputText(),
  'last_name'  => new sfWidgetFormInputText(),
  'email'      => new sfWidgetFormInputText(),
)));
$f->useFields(array('first_name', 'last_name'));
$t->is($f->getWidgetSchema()->getPositions(), array('first_name', 'last_name', 'id'), '->useFields() does not remove hidden fields');

// ->bind() ->isValid() ->hasErrors() ->getValues() ->getValue() ->isBound() ->getErrorSchema()
$t->diag('->bind() ->isValid() ->getValues() ->isBound() ->getErrorSchema()');
$f = new FormTest();
$f->setValidatorSchema(new sfValidatorSchema(array(
  'first_name' => new sfValidatorString(array('min_length' => 2)),
  'last_name' => new sfValidatorString(array('min_length' => 2)),
)));
$t->ok(!$f->isBound(), '->isBound() returns false if the form is not bound');
$t->is($f->getValues(), array(), '->getValues() returns an empty array if the form is not bound');
$t->ok(!$f->isValid(), '->isValid() returns false if the form is not bound');
$t->ok(!$f->hasErrors(), '->hasErrors() returns false if the form is not bound');

$t->is($f->getValue('first_name'), null, '->getValue() returns null if the form is not bound');
$f->bind(array('first_name' => 'Fabien', 'last_name' => 'Potencier'));
$t->ok($f->isBound(), '->isBound() returns true if the form is bound');
$t->is($f->getValues(), array('first_name' => 'Fabien', 'last_name' => 'Potencier'), '->getValues() returns an array of cleaned values if the form is bound');
$t->ok($f->isValid(), '->isValid() returns true if the form passes the validation');
$t->ok(!$f->hasErrors(), '->hasErrors() returns false if the form passes the validation');
$t->is($f->getValue('first_name'), 'Fabien', '->getValue() returns the cleaned value for a field name if the form is bound');
$t->is($f->getValue('nonsense'), null, '->getValue() returns null when non-existant param is requested');

$f->bind(array());
$t->ok(!$f->isValid(), '->isValid() returns false if the form does not pass the validation');
$t->ok($f->hasErrors(), '->isValid() returns true if the form does not pass the validation');
$t->is($f->getValues(), array(), '->getValues() returns an empty array if the form does not pass the validation');
$t->is($f->getErrorSchema()->getMessage(), 'first_name [Required.] last_name [Required.]', '->getErrorSchema() returns an error schema object with all errors');

$t->diag('bind when field names are numeric');
$f = new FormTest();
$f->setValidatorSchema(new sfValidatorSchema(array(
  1 => new sfValidatorString(array('min_length' => 2)),
  2 => new sfValidatorString(array('min_length' => 2)),
)));
$f->bind(array(1 => 'fabien', 2 => 'potencier'));
$t->ok($f->isValid(), '->bind() behaves correctly when field names are numeric');

$t->diag('bind with files');
$f = new FormTest();
$f->setValidatorSchema(new sfValidatorSchema(array(
  1 => new sfValidatorString(array('min_length' => 2)),
  2 => new sfValidatorString(array('min_length' => 2)),
  'file' => new sfValidatorFile(array('max_size' => 2)),
)));
$f->setWidgetSchema(new sfWidgetFormSchema(array('file' => new sfWidgetFormInputFile())));
$f->bind(array(1 => 'f', 2 => 'potencier'), array(
  'file' => array('name' => 'test1.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/test1.txt', 'error' => 0, 'size' => 100)
));
$t->is($f->getErrorSchema()->getCode(), '1 [min_length] file [max_size]', '->bind() behaves correctly with files');

try
{
  $f->bind(array(1 => 'f', 2 => 'potencier'));
  $t->fail('->bind() second argument is mandatory if the form is multipart');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->bind() second argument is mandatory if the form is multipart');
}

$t->diag('bind with files in embed form');
$pf = new FormTest(); //parent form
$pf->setValidatorSchema(new sfValidatorSchema()); //cleaning sfValidatorSchema to silence `_token_`

$ef = new FormTest(); //embed form

$ef->setValidatorSchema(new sfValidatorSchema(array(
  1 => new sfValidatorString(array('min_length' => 2)),
  2 => new sfValidatorString(array('min_length' => 2)),
  'file' => new sfValidatorFile(array('max_size' => 2)),
)));
$ef->setWidgetSchema(new sfWidgetFormSchema(array('file' => new sfWidgetFormInputFile())));
$pf->embedForm('ef', $ef);
$pf->bind(array('ef' => array(1 => 'f', 2 => 'potencier')), array('ef' => array(
  'file' => array('name' => 'test1.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/test1.txt', 'error' => 0, 'size' => 100)
)));
$t->is($pf->getErrorSchema()->getCode(), 'ef [1 [min_length] file [max_size]]', '->bind() behaves correctly with files in embed form');


// ->renderGlobalErrors()
$t->diag('->renderGlobalErrors()');
$f = new FormTest();
$f->setValidatorSchema(new sfValidatorSchema(array(
  'id'         => new sfValidatorInteger(),
  'first_name' => new sfValidatorString(array('min_length' => 2)),
  'last_name'  => new sfValidatorString(array('min_length' => 2)),
)));
$f->setWidgetSchema(new sfWidgetFormSchema(array(
  'id'         => new sfWidgetFormInputHidden(),
  'first_name' => new sfWidgetFormInputText(),
  'last_name'  => new sfWidgetFormInputText(),
)));
$f->bind(array(
  'id'         => 'dddd',
  'first_name' => 'f',
  'last_name'  => 'potencier',
));
$output = <<<EOF
  <ul class="error_list">
    <li>Id: "dddd" is not an integer.</li>
  </ul>

EOF;
$t->is($f->renderGlobalErrors(), fix_linebreaks($output), '->renderGlobalErrors() renders global errors as an HTML list');

// ->render()
$t->diag('->render()');
$f = new FormTest(array('first_name' => 'Fabien', 'last_name' => 'Potencier'));
$f->setValidators(array(
  'id'         => new sfValidatorInteger(),
  'first_name' => new sfValidatorString(array('min_length' => 2)),
  'last_name'  => new sfValidatorString(array('min_length' => 2)),
));
$f->setWidgets(array(
  'id'         => new sfWidgetFormInputHidden(array('default' => 3)),
  'first_name' => new sfWidgetFormInputText(array('default' => 'Thomas')),
  'last_name'  => new sfWidgetFormInputText(),
));

// unbound
$output = <<<EOF
<tr>
  <th><label for="first_name">First name</label></th>
  <td><input type="text" name="first_name" value="Fabien" id="first_name" /></td>
</tr>
<tr>
  <th><label for="last_name">Last name</label></th>
  <td><input type="text" name="last_name" value="Potencier" id="last_name" /><input type="hidden" name="id" value="3" id="id" /></td>
</tr>

EOF;
$t->is($f->__toString(), fix_linebreaks($output), '->__toString() renders the form as HTML');
$output = <<<EOF
<tr>
  <th><label for="first_name">First name</label></th>
  <td><input type="text" name="first_name" value="Fabien" class="foo" id="first_name" /></td>
</tr>
<tr>
  <th><label for="last_name">Last name</label></th>
  <td><input type="text" name="last_name" value="Potencier" id="last_name" /><input type="hidden" name="id" value="3" id="id" /></td>
</tr>

EOF;
$t->is($f->render(array('first_name' => array('class' => 'foo'))), fix_linebreaks($output), '->render() renders the form as HTML');
$t->is((string) $f['id'], '<input type="hidden" name="id" value="3" id="id" />', '->offsetGet() returns a sfFormField');
$t->is((string) $f['first_name'], '<input type="text" name="first_name" value="Fabien" id="first_name" />', '->offsetGet() returns a sfFormField');
$t->is((string) $f['last_name'], '<input type="text" name="last_name" value="Potencier" id="last_name" />', '->offsetGet() returns a sfFormField');

// bound
$f->bind(array(
  'id'         => '1',
  'first_name' => 'Fabien',
  'last_name'  => 'Potencier',
));
$output = <<<EOF
<tr>
  <th><label for="first_name">First name</label></th>
  <td><input type="text" name="first_name" value="Fabien" id="first_name" /></td>
</tr>
<tr>
  <th><label for="last_name">Last name</label></th>
  <td><input type="text" name="last_name" value="Potencier" id="last_name" /><input type="hidden" name="id" value="1" id="id" /></td>
</tr>

EOF;
$t->is($f->__toString(), fix_linebreaks($output), '->__toString() renders the form as HTML');
$output = <<<EOF
<tr>
  <th><label for="first_name">First name</label></th>
  <td><input type="text" name="first_name" value="Fabien" class="foo" id="first_name" /></td>
</tr>
<tr>
  <th><label for="last_name">Last name</label></th>
  <td><input type="text" name="last_name" value="Potencier" id="last_name" /><input type="hidden" name="id" value="1" id="id" /></td>
</tr>

EOF;
$t->is($f->render(array('first_name' => array('class' => 'foo'))), fix_linebreaks($output), '->render() renders the form as HTML');
$t->is((string) $f['id'], '<input type="hidden" name="id" value="1" id="id" />', '->offsetGet() returns a sfFormField');
$t->is((string) $f['first_name'], '<input type="text" name="first_name" value="Fabien" id="first_name" />', '->offsetGet() returns a sfFormField');
$t->is((string) $f['last_name'], '<input type="text" name="last_name" value="Potencier" id="last_name" />', '->offsetGet() returns a sfFormField');

// renderUsing()
$t->diag('->renderUsing()');
$f = new sfForm();
$f->setWidgets(array('name' => new sfWidgetFormInputText()));
$output = <<<EOF
<li>
  <label for="name">Name</label>
  <input type="text" name="name" id="name" />
</li>

EOF;
$t->is($f->renderUsing('list'), fix_linebreaks($output), 'renderUsing() renders the widget schema using the given form formatter');
$t->is($f->getWidgetSchema()->getFormFormatterName(), 'table', 'renderUsing() does not persist form formatter name for the current form instance');

$w = $f->getWidgetSchema();
$w->addFormFormatter('custom', new sfWidgetFormSchemaFormatterList($w));
$t->is($f->renderUsing('custom'), fix_linebreaks($output), 'renderUsing() renders a custom form formatter');

try
{
  $f->renderUsing('nonexistant');
  $t->fail('renderUsing() throws an exception if formatter name does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('renderUsing() throws an exception if formatter name does not exist');
}

// renderHiddenFields()
$t->diag('->renderHiddenFields()');
$f = new sfForm();
$f->setWidgets(array(
  'id' => new sfWidgetFormInputHidden(),
  'name' => new sfWidgetFormInputText(),
  'is_admin' => new sfWidgetFormInputHidden(),
));
$output = '<input type="hidden" name="id" id="id" /><input type="hidden" name="is_admin" id="is_admin" />';
$t->is($f->renderHiddenFields(), $output, 'renderHiddenFields() renders all hidden fields, no visible fields');
$t->is(count($f->getFormFieldSchema()), 3, 'renderHiddenFields() does not modify the form fields');

$author = new sfForm();
$author->setWidgets(array('id' => new sfWidgetFormInputHidden(), 'name' => new sfWidgetFormInputText()));

$company = new sfForm();
$company->setWidgets(array('id' => new sfWidgetFormInputHidden(), 'name' => new sfWidgetFormInputText()));

$author->embedForm('company', $company);

$output = '<input type="hidden" name="id" id="id" /><input type="hidden" name="company[id]" id="company_id" />';
$t->is($author->renderHiddenFields(), $output, 'renderHiddenFields() renders hidden fields from embedded forms');

$output = '<input type="hidden" name="id" id="id" />';
$t->is($author->renderHiddenFields(false), $output, 'renderHiddenFields() does not render hidden fields from embedded forms if the first parameter is "false"');

// ->embedForm()
$t->diag('->embedForm()');

$author = new FormTest(array('first_name' => 'Fabien'));
$author->setWidgetSchema($author_widget_schema = new sfWidgetFormSchema(array('first_name' => new sfWidgetFormInputText())));
$author->setValidatorSchema($author_validator_schema = new sfValidatorSchema(array('first_name' => new sfValidatorString(array('min_length' => 2)))));

$company = new FormTest();
$company->setWidgetSchema($company_widget_schema = new sfWidgetFormSchema(array('name' => new sfWidgetFormInputText())));
$company->setValidatorSchema($company_validator_schema = new sfValidatorSchema(array('name' => new sfValidatorString(array('min_length' => 2)))));

$article = new FormTest();
$article->setWidgetSchema($article_widget_schema = new sfWidgetFormSchema(array('title' => new sfWidgetFormInputText())));
$article->setValidatorSchema($article_validator_schema = new sfValidatorSchema(array('title' => new sfValidatorString(array('min_length' => 2)))));

$author->embedForm('company', $company);
$article->embedForm('author', $author);
$v = $article->getValidatorSchema();
$w = $article->getWidgetSchema();
$d = $article->getDefaults();

$w->setNameFormat('article[%s]');

$t->ok($v['author']['first_name'] == $author_validator_schema['first_name'], '->embedForm() embeds the validator schema');
// ignore parents in comparison
$w['author']['first_name']->setParent(null); $author_widget_schema['first_name']->setParent(null);
$t->ok($w['author']['first_name'] == $author_widget_schema['first_name'], '->embedForm() embeds the widget schema');
$t->is($d['author']['first_name'], 'Fabien', '->embedForm() merges default values from the embedded form');
$t->is($v['author'][sfForm::getCSRFFieldName()], null, '->embedForm() removes the CSRF token for the embedded form');
$t->is($w['author'][sfForm::getCSRFFieldName()], null, '->embedForm() removes the CSRF token for the embedded form');

$t->is($w['author']->generateName('first_name'), 'article[author][first_name]', '->embedForm() changes the name format to reflect the embedding');
$t->is($w['author']['company']->generateName('name'), 'article[author][company][name]', '->embedForm() changes the name format to reflect the embedding');

// tests for ticket #4754
$f1 = new TestForm1();
$f2 = new TestForm2();
$f1->embedForm('f2', $f2);
$t->is($f1['f2']['c']->render(), '<textarea rows="4" cols="30" name="f2[c]" id="f2_c"></textarea>', '->embedForm() generates a correct id in embedded form fields');
$t->is($f1['f2']['c']->renderLabel(), '<label for="f2_c">2_c</label>', '->embedForm() generates a correct label id correctly in embedded form fields');

// ->embedFormForEach()
$t->diag('->embedFormForEach()');
$article->embedFormForEach('authors', $author, 2, null, null, array('id_format' => '%s_id'), array('class' => 'embedded'));
$v = $article->getValidatorSchema();
$w = $article->getWidgetSchema();
$d = $article->getDefaults();
$w->setNameFormat('article[%s]');

for ($i = 0; $i < 2; $i++)
{
  $t->ok($v['authors'][$i]['first_name'] == $author_validator_schema['first_name'], '->embedFormForEach() embeds the validator schema');
  // ignore the parents in comparison
  $w['authors'][$i]['first_name']->setParent(null); $author_widget_schema['first_name']->setParent(null);
  $t->ok($w['authors'][$i]['first_name'] == $author_widget_schema['first_name'], '->embedFormForEach() embeds the widget schema');
  $t->is($d['authors'][$i]['first_name'], 'Fabien', '->embedFormForEach() merges default values from the embedded forms');
  $t->is($v['authors'][$i][sfForm::getCSRFFieldName()], null, '->embedFormForEach() removes the CSRF token for the embedded forms');
  $t->is($w['authors'][$i][sfForm::getCSRFFieldName()], null, '->embedFormForEach() removes the CSRF token for the embedded forms');
}

$t->is($w['authors'][0]->generateName('first_name'), 'article[authors][0][first_name]', '->embedFormForEach() changes the name format to reflect the embedding');

// bind too many values for embedded forms
$t->diag('bind too many values for embedded forms');
$list = new FormTest();
$list->setWidgets(array('title' => new sfWidgetFormInputText()));
$list->setValidators(array('title' => new sfValidatorString()));
$list->embedFormForEach('items', clone $list, 2);
$list->bind(array(
  'title' => 'list title',
  'items' => array(
    array('title' => 'item 1'),
    array('title' => 'item 2'),
    array('title' => 'extra item'),
  ),
));

$t->isa_ok($list['items'][0]->getError(), 'sfValidatorErrorSchema', '"sfFormFieldSchema" is given an error schema when an extra embedded form is bound');

// does this trigger a fatal error?
$list['items']->render();
$t->pass('"sfFormFieldSchema" renders when an extra embedded form is bound');

// ->getEmbeddedForms()
$t->diag('->getEmbeddedForms()');
$article = new FormTest();
$company = new FormTest();
$author = new FormTest();
$article->embedForm('company', $company);
$article->embedForm('author', $author);
$forms = $article->getEmbeddedForms();
$t->is(array_keys($forms), array('company', 'author'), '->getEmbeddedForms() returns the embedded forms');
$t->is($forms['company'], $company, '->getEmbeddedForms() returns the embedded forms');
$t->isa_ok($article->getEmbeddedForm('company'), 'FormTest', '->getEmbeddedForm() return an embedded form');
try
{
  $article->getEmbeddedForm('nonexistant');
  $t->fail('->getEmbeddedForm() throws an exception if the embedded form does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->getEmbeddedForm() throws an exception if the embedded form does not exist');
}

// ::convertFileInformation()
$t->diag('::convertFileInformation()');
$input = array(
  'file' => array(
    'name' => 'test1.txt',
    'type' => 'text/plain',
    'tmp_name' => '/tmp/test1.txt',
    'error' => 0,
    'size' => 100,
  ),
  'file1' => array(
    'name' => 'test2.txt',
    'type' => 'text/plain',
    'tmp_name' => '/tmp/test1.txt',
    'error' => 0,
    'size' => 200,
  ),
);
$t->is_deeply(sfForm::convertFileInformation($input), $input, '::convertFileInformation() converts $_FILES to be coherent with $_GET and $_POST naming convention');

$input = array(
  'article' => array(
    'name' => array(
      'file1' => 'test1.txt',
      'file2' => 'test2.txt',
    ),
    'type' => array(
      'file1' => 'text/plain',
      'file2' => 'text/plain',
    ),
    'tmp_name' => array(
      'file1' => '/tmp/test1.txt',
      'file2' => '/tmp/test2.txt',
    ),
    'error' => array(
      'file1' => 0,
      'file2' => 0,
    ),
    'size' => array(
      'file1' => 100,
      'file2' => 200,
    ),
  ),
);
$expected = array(
  'article' => array(
    'file1' => array(
      'name' => 'test1.txt',
      'type' => 'text/plain',
      'tmp_name' => '/tmp/test1.txt',
      'error' => 0,
      'size' => 100,
    ),
    'file2' => array(
      'name' => 'test2.txt',
      'type' => 'text/plain',
      'tmp_name' => '/tmp/test2.txt',
      'error' => 0,
      'size' => 200,
    ),
  ),
);
$t->is_deeply(sfForm::convertFileInformation($input), $expected, '::convertFileInformation() converts $_FILES to be coherent with $_GET and $_POST naming convention');
$t->is_deeply(sfForm::convertFileInformation($expected), $expected, '::convertFileInformation() only changes the input array if needed');

$input = array(
  'file' => array(
    'name' => 'test.txt',
    'type' => 'text/plain',
    'tmp_name' => '/tmp/test.txt',
    'error' => 0,
    'size' => 100,
  ),
  'article' => array(
    'name' => array(
      'name' => array(
        'name' => 'test1.txt',
        'another' => array('file2' => 'test2.txt'),
      ),
    ),
    'type' => array(
      'name' => array(
        'name' => 'text/plain',
        'another' => array('file2' => 'text/plain'),
      ),
    ),
    'tmp_name' => array(
      'name' => array(
        'name' => '/tmp/test1.txt',
        'another' => array('file2' => '/tmp/test2.txt'),
      ),
    ),
    'error' => array(
      'name' => array(
        'name' => 0,
        'another' => array('file2' => 0),
      ),
    ),
    'size' => array(
      'name' => array(
        'name' => 100,
        'another' => array('file2' => 200),
      ),
    ),
  ),
);
$expected = array(
  'file' => array(
    'name' => 'test.txt',
    'type' => 'text/plain',
    'tmp_name' => '/tmp/test.txt',
    'error' => 0,
    'size' => 100,
  ),
  'article' => array(
    'name' => array(
      'name' => array(
        'name' => 'test1.txt',
        'type' => 'text/plain',
        'tmp_name' => '/tmp/test1.txt',
        'error' => 0,
        'size' => 100,
      ),
      'another' => array(
        'file2' => array(
          'name' => 'test2.txt',
          'type' => 'text/plain',
          'tmp_name' => '/tmp/test2.txt',
          'error' => 0,
          'size' => 200,
        ),
      ),
    )
  ),
);
$t->is_deeply(sfForm::convertFileInformation($input), $expected, '::convertFileInformation() converts $_FILES to be coherent with $_GET and $_POST naming convention');
$t->is_deeply(sfForm::convertFileInformation($expected), $expected, '::convertFileInformation() converts $_FILES to be coherent with $_GET and $_POST naming convention');

// ->renderFormTag()
$t->diag('->renderFormTag()');
$f = new FormTest();
$t->is($f->renderFormTag('/url'), '<form action="/url" method="post">', '->renderFormTag() renders the form tag');
$t->is($f->renderFormTag('/url', array('method' => 'put')), '<form method="post" action="/url"><input type="hidden" name="sf_method" value="put" />', '->renderFormTag() adds a hidden input tag if the method is not GET or POST');
$f->setWidgetSchema(new sfWidgetFormSchema(array('image' => new sfWidgetFormInputFile())));
$t->is($f->renderFormTag('/url'), '<form action="/url" method="post" enctype="multipart/form-data">', '->renderFormTag() adds the enctype attribute if the form is multipart');

// __clone()
$t->diag('__clone()');
$a = new FormTest();
$a->setValidatorSchema(new sfValidatorSchema(array(
  'first_name' => new sfValidatorString(array('min_length' => 2)),
)));
$a->bind(array('first_name' => 'F'));
$a1 = clone $a;

$t->ok($a1->getValidatorSchema() !== $a->getValidatorSchema(), '__clone() clones the validator schema');
$t->ok($a1->getValidatorSchema() == $a->getValidatorSchema(), '__clone() clones the validator schema');

$t->ok($a1->getWidgetSchema() !== $a->getWidgetSchema(), '__clone() clones the widget schema');
$t->ok($a1->getWidgetSchema() == $a->getWidgetSchema(), '__clone() clones the widget schema');

$t->ok($a1->getErrorSchema() !== $a->getErrorSchema(), '__clone() clones the error schema');
$t->ok($a1->getErrorSchema()->getMessage() == $a->getErrorSchema()->getMessage(), '__clone() clones the error schema');

// mergeForm()
$t->diag('mergeForm()');

$f1 = new TestForm1();
$f2 = new TestForm2();
$f1->mergeForm($f2);

$widgetSchema = $f1->getWidgetSchema();
$validatorSchema = $f1->getValidatorSchema();
$t->is(count($widgetSchema->getFields()), 4, 'mergeForm() merges a widget form schema');
$t->is(count($validatorSchema->getFields()), 4, 'mergeForm() merges a validator schema');
$t->is(array_keys($widgetSchema->getFields()), array('a', 'b', 'c', 'd'), 'mergeForms() merges the correct widgets');
$t->is(array_keys($validatorSchema->getFields()), array('a', 'b', 'c', 'd'), 'mergeForms() merges the correct validators');
$t->is($widgetSchema->getLabels(), array('a' => '1_a', 'b' => '1_b', 'c' => '2_c', 'd' => '2_d'), 'mergeForm() merges labels correctly');
$t->is($widgetSchema->getHelps(), array('a' => '1_a', 'b' => '1_b', 'c' => '2_c', 'd' => '2_d'), 'mergeForm() merges helps correctly');
$t->isa_ok($widgetSchema['c'], 'sfWidgetFormTextarea', 'mergeForm() overrides original form widget');
$t->isa_ok($validatorSchema['c'], 'sfValidatorPass', 'mergeForm() overrides original form validator');
$t->isa_ok($validatorSchema->getPreValidator(), 'sfValidatorPass', 'mergeForm() merges pre validator');
$t->isa_ok($validatorSchema->getPostValidator(), 'sfValidatorPass', 'mergeForm() merges post validator');

try
{
  $f1->bind(array('a' => 'foo', 'b' => 'bar', 'd' => 'far_too_long_value'));
  $f1->mergeForm($f2);
  $t->fail('mergeForm() disallows merging already bound forms');
}
catch (LogicException $e)
{
  $t->pass('mergeForm() disallows merging already bound forms');
}

$errorSchema = $f1->getErrorSchema();
$t->ok(array_key_exists('d', $errorSchema->getErrors()), 'mergeForm() merges errors after having been bound');

$f1 = new TestForm1();
$f1->getWidgetSchema()->moveField('a', 'last');

// is moved field well positioned when accessed with iterator interface? (#5551)
foreach($f1 as $f1name => $f1field)
{
  $t->is ($f1name, 'b', 'iterating on form takes in account ->moveField() operations.');
  break;
}

$f2 = new TestForm2();
$f2->mergeForm($f1);

$t->is_deeply(array_keys($f2->getWidgetSchema()->getFields()), array('c', 'd', 'b', 'a'), 'mergeForm() merges fields in the correct order');

$f1 = new NumericFieldsForm(array('5' => 'default1'), array('salt' => '1'));
$f2 = new NumericFieldsForm(array('5' => 'default2'), array('salt' => '2'));
$f1->mergeForm($f2);

$t->is_deeply($f1->getDefaults(), array('5' => 'default2'), '->mergeForm() merges numeric defaults');
$t->is_deeply($f1->getWidgetSchema()->getLabels(), array('5' => 'label2'), '->mergeForm() merges numeric labels');
$t->is_deeply($f1->getWidgetSchema()->getHelps(), array('5' => 'help2'), '->mergeForm() merges numeric helps');

// ->getJavaScripts() ->getStylesheets()
$t->diag('->getJavaScripts() ->getStylesheets()');

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
    return array('/path/to/a/'.$this->getOption('name').'.js');
  }

  public function getStylesheets()
  {
    return array('/path/to/a/'.$this->getOption('name').'.css' => 'all');
  }
}

$f = new FormTest();
$f->setWidgets(array(
  'foo' => new MyWidget(array('name' => 'foo')),
  'bar' => new MyWidget(array('name' => 'bar')),
));
$t->is($f->getJavaScripts(), array('/path/to/a/foo.js', '/path/to/a/bar.js'), '->getJavaScripts() returns the stylesheets of all widgets');
$t->is($f->getStylesheets(), array('/path/to/a/foo.css' => 'all', '/path/to/a/bar.css' => 'all'), '->getStylesheets() returns the JavaScripts of all widgets');

// ->getFormFieldSchema()
$t->diag('->getFormFieldSchema()');

$f = new NumericFieldsForm(array('5' => 'default'));
$t->is_deeply($f->getFormFieldSchema()->getValue(), array('5' => 'default'), '->getFormFieldSchema() includes default numeric fields');
$f->bind(array('5' => 'bound'));
$t->is_deeply($f->getFormFieldSchema()->getValue(), array('5' => 'bound'), '->getFormFieldSchema() includes bound numeric fields');
