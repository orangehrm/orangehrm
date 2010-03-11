<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(6, new lime_output_color());

$v1 = new sfValidatorString(array('min_length' => 2, 'trim' => true));

$v = new sfValidatorSchemaFilter('first_name', $v1);

// ->clean()
$t->diag('->clean()');
$t->is($v->clean(array('first_name' => '  foo  ')), array('first_name' => 'foo'), '->clean() executes the embedded validator');

try
{
  $v->clean('string');
  $t->fail('->clean() throws a InvalidArgumentException if the input value is not an array');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->clean() throws a InvalidArgumentException if the input value is not an array');
}

try
{
  $v->clean(null);
  $t->fail('->clean() throws a sfValidatorError if the embedded validator failed');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the embedded validator failed');
  $t->is($e->getCode(), 'first_name [required]', '->clean() throws a sfValidatorError');
}

try
{
  $v->clean(array('first_name' => 'f'));
  $t->fail('->clean() throws a sfValidatorError if the embedded validator failed');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the embedded validator failed');
  $t->is($e->getCode(), 'first_name [min_length]', '->clean() throws a sfValidatorError');
}
