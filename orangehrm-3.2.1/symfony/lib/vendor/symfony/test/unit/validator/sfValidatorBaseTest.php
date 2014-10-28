<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(47);

class ValidatorIdentity extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addOption('foo', 'bar');
    $this->addMessage('foo', 'bar');
  }

  public function testIsEmpty($value)
  {
    return $this->isEmpty($value);
  }

  protected function doClean($value)
  {
    return $value;
  }
}

class ValidatorIdentityWithRequired extends sfValidatorBase
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('foo');
  }

  protected function doClean($value)
  {
    return $value;
  }
}

// ->configure()
$t->diag('->configure()');
$v = new ValidatorIdentity();
$t->is($v->getOption('foo'), 'bar', '->configure() can add some options');
$v = new ValidatorIdentity(array('foo' => 'foobar'));
$t->is($v->getOption('foo'), 'foobar', '->configure() takes an options array as its first argument and values override default option values');
$v = new ValidatorIdentity();
$t->is($v->getMessage('foo'), 'bar', '->configure() can add some message');
$v = new ValidatorIdentity(array(), array('foo' => 'foobar'));
$t->is($v->getMessage('foo'), 'foobar', '->configure() takes a messages array as its second argument and values override default message values');

try
{
  new ValidatorIdentity(array('nonexistant' => false, 'foo' => 'foobar', 'anothernonexistant' => 'bar', 'required' => true));
  $t->fail('__construct() throws an InvalidArgumentException if you pass some non existant options');
  $t->skip();
}
catch (InvalidArgumentException $e)
{
  $t->pass('__construct() throws an InvalidArgumentException if you pass some non existant options');
  $t->like($e->getMessage(), '/ \'nonexistant\', \'anothernonexistant\'/', 'The exception contains the non existant option names');
}

try
{
  new ValidatorIdentity(array(), array('required' => 'This is required.', 'nonexistant' => 'foo', 'anothernonexistant' => false));
  $t->fail('__construct() throws an InvalidArgumentException if you pass some non existant error codes');
  $t->skip();
}
catch (InvalidArgumentException $e)
{
  $t->pass('__construct() throws an InvalidArgumentException if you pass some non existant error codes');
  $t->like($e->getMessage(), '/ \'nonexistant\', \'anothernonexistant\'/', 'The exception contains the non existant error codes');
}

// ->getRequiredOptions()
$t->diag('getRequiredOptions');
$v = new ValidatorIdentityWithRequired(array('foo' => 'bar'));
$t->is($v->getRequiredOptions(), array('foo'), '->getRequiredOptions() returns an array of required option names');

try
{
  new ValidatorIdentityWithRequired();
  $t->fail('__construct() throws an RuntimeException if you don\'t pass a required option');
}
catch (RuntimeException $e)
{
  $t->pass('__construct() throws an RuntimeException if you don\'t pass a required option');
}

$v = new ValidatorIdentity();

// ->clean()
$t->diag('->clean()');
$t->is($v->clean('foo'), 'foo', '->clean() returns a cleanup version of the data to validate');
try
{
  $t->is($v->clean(''), '');
  $t->fail('->clean() throws a sfValidatorError exception if the data does not validate');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError exception if the data does not validate');
  $t->is($e->getCode(), 'required', '->clean() throws a sfValidatorError');
}
$t->is($v->clean('  foo  '), '  foo  ', '->clean() does not trim whitespaces by default');

// ->isEmpty()
$t->diag('->isEmpty()');
$t->is($v->testIsEmpty(null), true, 'null value isEmpty()');
$t->is($v->testIsEmpty(''), true, 'empty string value isEmpty()');
$t->is($v->testIsEmpty(array()), true, 'empty array value isEmpty()');
$t->is($v->testIsEmpty(false), false, 'false value not isEmpty()');

// ->getEmptyValue()
$t->diag('->getEmptyValue()');
$v->setOption('required', false);
$v->setOption('empty_value', 'defaultnullvalue');
$t->is($v->clean(''), 'defaultnullvalue', '->getEmptyValue() returns the representation of an empty value for this validator');
$v->setOption('empty_value', null);

// ->setOption()
$t->diag('->setOption()');
$v->setOption('required', false);
$t->is($v->clean(''), null, '->setOption() changes options (required for example)');
$v->setOption('trim', true);
$t->is($v->clean('  foo  '), 'foo', '->setOption() can turn on whitespace trimming');
try
{
  $v->setOption('foobar', 'foo');
  $t->fail('->setOption() throws an InvalidArgumentException if the option is not registered');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->setOption() throws an InvalidArgumentException if the option is not registered');
}

// ->hasOption()
$t->diag('->hasOption()');
$t->ok($v->hasOption('required'), '->hasOption() returns true if the validator has the option');
$t->ok(!$v->hasOption('nonexistant'), '->hasOption() returns false if the validator does not have the option');

// ->getOption()
$t->diag('->getOption()');
$t->is($v->getOption('required'), false, '->getOption() returns the value of an option');
$t->is($v->getOption('nonexistant'), null, '->getOption() returns null if the option does not exist');

// ->addOption()
$t->diag('->addOption()');
$v->addOption('foobar');
$v->setOption('foobar', 'foo');
$t->is($v->getOption('foobar'), 'foo', '->addOption() adds a new option to a validator');

// ->getOptions() ->setOptions()
$t->diag('->getOptions() ->setOptions()');
$v->setOptions(array('required' => true, 'trim' => false));
$t->is($v->getOptions(), array('required' => true, 'trim' => false, 'empty_value' => null), '->setOptions() changes all options');

// ->getMessages()
$t->diag('->getMessages()');
$t->is($v->getMessages(), array('required' => 'Required.', 'invalid' => 'Invalid.', 'foo' => 'bar'), '->getMessages() returns an array of all error messages');

// ->getMessage()
$t->diag('->getMessage()');
$t->is($v->getMessage('required'), 'Required.', '->getMessage() returns an error message string');
$t->is($v->getMessage('nonexistant'), '', '->getMessage() returns an empty string if the message does not exist');

// ->setMessage()
$t->diag('->setMessage()');
$v->setMessage('required', 'The field is required.');
try
{
  $v->clean('');
  $t->isnt($e->getMessage(), 'The field is required.', '->setMessage() changes the default error message string');
}
catch (sfValidatorError $e)
{
  $t->is($e->getMessage(), 'The field is required.', '->setMessage() changes the default error message string');
}

try
{
  $v->setMessage('foobar', 'foo');
  $t->fail('->setMessage() throws an InvalidArgumentException if the message is not registered');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->setMessage() throws an InvalidArgumentException if the message is not registered');
}

// ->setMessages()
$t->diag('->setMessages()');
$v->setMessages(array('required' => 'This is required!'));
$t->is($v->getMessages(), array('invalid' => 'Invalid.', 'required' => 'This is required!'), '->setMessages() changes all error messages');

// ->addMessage()
$t->diag('->addMessage()');
$v->addMessage('foobar', 'foo');
$v->setMessage('foobar', 'bar');
$t->is($v->getMessage('foobar'), 'bar', '->addMessage() adds a new error code');

// ->getErrorCodes()
$t->diag('->getErrorCodes()');
$t->is($v->getErrorCodes(), array('required', 'invalid', 'foo'), '->getErrorCodes() returns an array of error codes the validator can use');

// ::getCharset() ::setCharset()
$t->diag('::getCharset() ::setCharset()');
$t->is(sfValidatorBase::getCharset(), 'UTF-8', '::getCharset() returns the charset to use for validators');
sfValidatorBase::setCharset('ISO-8859-1');
$t->is(sfValidatorBase::getCharset(), 'ISO-8859-1', '::setCharset() changes the charset to use for validators');

// ->asString()
$t->diag('->asString()');
$v = new ValidatorIdentity();
$t->is($v->asString(), 'ValidatorIdentity()', '->asString() returns a string representation of the validator');
$v->setOption('required', false);
$v->setOption('foo', 'foo');
$t->is($v->asString(), 'ValidatorIdentity({ required: false, foo: foo })', '->asString() returns a string representation of the validator');

$v->setMessage('required', 'This is required.');
$t->is($v->asString(), 'ValidatorIdentity({ required: false, foo: foo }, { required: \'This is required.\' })', '->asString() returns a string representation of the validator');

$v = new ValidatorIdentity();
$v->setMessage('required', 'This is required.');
$t->is($v->asString(), 'ValidatorIdentity({}, { required: \'This is required.\' })', '->asString() returns a string representation of the validator');

// ::setDefaultMessage()
$t->diag('::setDefaultMessage()');
ValidatorIdentity::setDefaultMessage('required', 'This field is required.');
ValidatorIdentity::setDefaultMessage('invalid', 'This field is invalid.');
ValidatorIdentity::setDefaultMessage('foo', 'Foo bar.');
$v = new ValidatorIdentity();
$t->is($v->getMessage('required'), 'This field is required.', '::setDefaultMessage() sets the default message for an error');
$t->is($v->getMessage('invalid'), 'This field is invalid.', '::setDefaultMessage() sets the default message for an error');
$t->is($v->getMessage('foo'), 'Foo bar.', '::setDefaultMessage() sets the default message for an error');

$v = new ValidatorIdentity(array(), array('required' => 'Yep, this is required!', 'foo' => 'Yep, this is a foo error!'));
$t->is($v->getMessage('required'), 'Yep, this is required!', '::setDefaultMessage() is ignored if the validator explicitly overrides the message');
$t->is($v->getMessage('foo'), 'Yep, this is a foo error!', '::setDefaultMessage() is ignored if the validator explicitly overrides the message');
