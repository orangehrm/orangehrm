<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(5, new lime_output_color());

// __construct()
$t->diag('__construct()');
try
{
  new sfValidatorRegex();
  $t->fail('__construct() throws an RuntimeException if you don\'t pass a pattern option');
}
catch (RuntimeException $e)
{
  $t->pass('__construct() throws an RuntimeException if you don\'t pass a pattern option');
}

$v = new sfValidatorRegex(array('pattern' => '/^[0-9]+$/'));

// ->clean()
$t->diag('->clean()');
$t->is($v->clean(12), '12', '->clean() checks that the value match the regex');

try
{
  $v->clean('symfony');
  $t->fail('->clean() throws an sfValidatorError if the value does not match the pattern');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an sfValidatorError if the value does not match the pattern');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

// ->asString()
$t->diag('->asString()');
$t->is($v->asString(), 'Regex({ pattern: \'/^[0-9]+$/\' })', '->asString() returns a string representation of the validator');
