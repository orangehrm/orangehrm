<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(25, new lime_output_color());

$v1 = new sfValidatorString(array('max_length' => 3));
$v2 = new sfValidatorString(array('min_length' => 3));

$v = new sfValidatorAnd(array($v1, $v2));

// __construct()
$t->diag('__construct()');
$v = new sfValidatorAnd();
$t->is($v->getValidators(), array(), '->__construct() can take no argument');
$v = new sfValidatorAnd($v1);
$t->is($v->getValidators(), array($v1), '->__construct() can take a validator as its first argument');
$v = new sfValidatorAnd(array($v1, $v2));
$t->is($v->getValidators(), array($v1, $v2), '->__construct() can take an array of validators as its first argument');
try
{
  $v = new sfValidatorAnd('string');
  $t->fail('__construct() throws an exception when passing a non supported first argument');
}
catch (InvalidArgumentException $e)
{
  $t->pass('__construct() throws an exception when passing a non supported first argument');
}

// ->addValidator()
$t->diag('->addValidator()');
$v = new sfValidatorAnd();
$v->addValidator($v1);
$v->addValidator($v2);
$t->is($v->getValidators(), array($v1, $v2), '->addValidator() adds a validator');

// ->clean()
$t->diag('->clean()');
$t->is($v->clean('foo'), 'foo', '->clean() returns the string unmodified');

try
{
  $v->setOption('required', true);
  $v->clean(null);
  $t->fail('->clean() throws an sfValidatorError exception if the input value is required');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an sfValidatorError exception if the input value is required');
  $t->is($e->getCode(), 'required', '->clean() throws a sfValidatorError');
}

$v2->setOption('max_length', 2);
try
{
  $v->clean('foo');
  $t->fail('->clean() throws an sfValidatorError exception if one of the validators fails');
  $t->skip('', 2);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an sfValidatorError exception if one of the validators fails');
  $t->is($e[0]->getCode(), 'max_length', '->clean() throws a sfValidatorSchemaError');
  $t->is($e instanceof sfValidatorErrorSchema, 'max_length', '->clean() throws a sfValidatorSchemaError');
}

$v1->setOption('max_length', 2);
try
{
  $v->clean('foo');
  $t->fail('->clean() throws an sfValidatorError exception if one of the validators fails');
  $t->skip('', 4);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an sfValidatorError exception if one of the validators fails');
  $t->is(count($e), 2, '->clean() throws an error for every error');
  $t->is($e[0]->getCode(), 'max_length', '->clean() throws a sfValidatorSchemaError');
  $t->is($e[1]->getCode(), 'max_length', '->clean() throws a sfValidatorSchemaError');
  $t->is($e instanceof sfValidatorErrorSchema, 'max_length', '->clean() throws a sfValidatorSchemaError');
}

$v->setOption('halt_on_error', true);
try
{
  $v->clean('foo');
  $t->fail('->clean() throws an sfValidatorError exception if one of the validators fails');
  $t->skip('', 3);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an sfValidatorError exception if one of the validators fails');
  $t->is(count($e), 1, '->clean() only returns the first error if halt_on_error option is true');
  $t->is($e[0]->getCode(), 'max_length', '->clean() throws a sfValidatorSchemaError');
  $t->is($e instanceof sfValidatorErrorSchema, 'max_length', '->clean() throws a sfValidatorSchemaError');
}

try
{
  $v->setMessage('invalid', 'Invalid.');
  $v->clean('foo');
  $t->fail('->clean() throws an sfValidatorError exception if one of the validators fails');
  $t->skip('', 2);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an sfValidatorError exception if one of the validators fails');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError if invalid message is not empty');
  $t->is(!$e instanceof sfValidatorErrorSchema, 'max_length', '->clean() throws a sfValidatorError if invalid message is not empty');
}

// ->asString()
$t->diag('->asString()');
$v1 = new sfValidatorString(array('max_length' => 3));
$v2 = new sfValidatorString(array('min_length' => 3));
$v = new sfValidatorAnd(array($v1, $v2));
$t->is($v->asString(), "(\n  String({ max_length: 3 })\n  and\n  String({ min_length: 3 })\n)"
, '->asString() returns a string representation of the validator');

$v = new sfValidatorAnd(array($v1, $v2), array(), array('required' => 'This is required.'));
$t->is($v->asString(), "(\n  String({ max_length: 3 })\n  and({}, { required: 'This is required.' })\n  String({ min_length: 3 })\n)" 
, '->asString() returns a string representation of the validator');
