<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(15, new lime_output_color());

$v = new sfValidatorInteger();

// ->clean()
$t->diag('->clean()');
$t->is($v->clean(12), 12, '->clean() returns the numbers unmodified');
$t->is($v->clean('12'), 12, '->clean() converts strings to integers');

try
{
  $v->clean('not an integer');
  $t->fail('->clean() throws a sfValidatorError if the value is not an integer');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the value is not an integer');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

try
{
  $v->clean(12.3);
  $t->fail('->clean() throws a sfValidatorError if the value is not an integer');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the value is not an integer');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

$v->setOption('required', false);
$t->ok($v->clean(null) === null, '->clean() returns null for null values');

$v->setOption('max', 2);
$t->is($v->clean(1), 1, '->clean() checks the maximum number allowed');
try
{
  $v->clean(3);
  $t->fail('"max" option set the maximum number allowed');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('"max" option set the maximum number allowed');
  $t->is($e->getCode(), 'max', '->clean() throws a sfValidatorError');
}

$v->setMessage('max', 'Too large');
try
{
  $v->clean(5);
  $t->fail('"max" error message customization');
}
catch (sfValidatorError $e)
{
  $t->is($e->getMessage(), 'Too large', '"max" error message customization');
}

$v->setOption('max', null);

$v->setOption('min', 3);
$t->is($v->clean(5), 5, '->clean() checks the minimum number allowed');
try
{
  $v->clean('1');
  $t->fail('"min" option set the minimum number allowed');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('"min" option set the minimum number allowed');
  $t->is($e->getCode(), 'min', '->clean() throws a sfValidatorError');
}

$v->setMessage('min', 'Too small');
try
{
  $v->clean(1);
  $t->fail('"min" error message customization');
}
catch (sfValidatorError $e)
{
  $t->is($e->getMessage(), 'Too small', '"min" error message customization');
}

$v->setOption('min', null);
