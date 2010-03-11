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

function choice_callable()
{
  return array(1, 2, 3);
}

// __construct()
$t->diag('__construct()');
try
{
  new sfValidatorChoice();
  $t->fail('__construct() throws an RuntimeException if you don\'t pass an expected option');
}
catch (RuntimeException $e)
{
  $t->pass('__construct() throws an RuntimeException if you don\'t pass an expected option');
}

$v = new sfValidatorChoice(array('choices' => array('foo', 'bar')));

// ->clean()
$t->diag('->clean()');
$t->is($v->clean('foo'), 'foo', '->clean() checks that the value is an expected value');
$t->is($v->clean('bar'), 'bar', '->clean() checks that the value is an expected value');

try
{
  $v->clean('foobar');
  $t->fail('->clean() throws an sfValidatorError if the value is not an expected value');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an sfValidatorError if the value is not an expected value');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

// ->asString()
$t->diag('->asString()');
$t->is($v->asString(), 'Choice({ choices: [foo, bar] })', '->asString() returns a string representation of the validator');

// choices as a callable
$t->diag('choices as a callable');
$v = new sfValidatorChoice(array('choices' => new sfCallable('choice_callable')));
$t->is($v->clean('2'), '2', '__construct() can take a sfCallable object as a choices option');

// see bug #4212
$v = new sfValidatorChoice(array('choices' => array(0, 1, 2)));
try
{
  $v->clean('xxx');
  $t->fail('->clean() throws an sfValidatorError if the value is not strictly an expected value');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an sfValidatorError if the value is not strictly an expected value');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}
