<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(114);

$v = new sfValidatorSchemaCompare('left', sfValidatorSchemaCompare::EQUAL, 'right');

// ->clean()
$t->diag('->clean()');
foreach (array(
  array(array('left' => 'foo', 'right' => 'foo'), sfValidatorSchemaCompare::EQUAL),
  array(array(), sfValidatorSchemaCompare::EQUAL),
  array(null, sfValidatorSchemaCompare::EQUAL),
  array(array('left' => 1, 'right' => 2), sfValidatorSchemaCompare::LESS_THAN),
  array(array('left' => 2, 'right' => 2), sfValidatorSchemaCompare::LESS_THAN_EQUAL),
  array(array('left' => 2, 'right' => 1), sfValidatorSchemaCompare::GREATER_THAN),
  array(array('left' => 2, 'right' => 2), sfValidatorSchemaCompare::GREATER_THAN_EQUAL),
  array(array('left' => 'foo', 'right' => 'bar'), sfValidatorSchemaCompare::NOT_EQUAL),
  array(array('left' => '0000', 'right' => '0'), sfValidatorSchemaCompare::NOT_IDENTICAL),
  array(array('left' => '0000', 'right' => '0'), sfValidatorSchemaCompare::EQUAL),
  array(array('left' => '0000', 'right' => '0000'), sfValidatorSchemaCompare::IDENTICAL),

  array(array('left' => 'foo', 'right' => 'foo'), '=='),
  array(array(), '=='),
  array(null, '=='),
  array(array('left' => 1, 'right' => 2), '<'),
  array(array('left' => 2, 'right' => 2), '<='),
  array(array('left' => 2, 'right' => 1), '>'),
  array(array('left' => 2, 'right' => 2), '>='),
  array(array('left' => 'foo', 'right' => 'bar'), '!='),
  array(array('left' => '0000', 'right' => '0'), '!=='),
  array(array('left' => '0000', 'right' => '0'), '=='),
  array(array('left' => '0000', 'right' => '0000'), '==='),
) as $values)
{
  $v->setOption('operator', $values[1]);
  $t->is($v->clean($values[0]), $values[0], '->clean() checks that the values match the comparison');
}

foreach (array(
  array(array('left' => 'foo', 'right' => 'foo'), sfValidatorSchemaCompare::NOT_EQUAL),
  array(array(), sfValidatorSchemaCompare::NOT_EQUAL),
  array(null, sfValidatorSchemaCompare::NOT_EQUAL),
  array(array('left' => 1, 'right' => 2), sfValidatorSchemaCompare::GREATER_THAN),
  array(array('left' => 2, 'right' => 3), sfValidatorSchemaCompare::GREATER_THAN_EQUAL),
  array(array('left' => 2, 'right' => 1), sfValidatorSchemaCompare::LESS_THAN),
  array(array('left' => 3, 'right' => 2), sfValidatorSchemaCompare::LESS_THAN_EQUAL),
  array(array('left' => 'foo', 'right' => 'bar'), sfValidatorSchemaCompare::EQUAL),
  array(array('left' => '0000', 'right' => '0'), sfValidatorSchemaCompare::IDENTICAL),
  array(array('left' => '0000', 'right' => '0'), sfValidatorSchemaCompare::NOT_EQUAL),
  array(array('left' => '0000', 'right' => '0000'), sfValidatorSchemaCompare::NOT_IDENTICAL),

  array(array('left' => 'foo', 'right' => 'foo'), '!='),
  array(array(), '!='),
  array(null, '!='),
  array(array('left' => 1, 'right' => 2), '>'),
  array(array('left' => 2, 'right' => 3), '>='),
  array(array('left' => 2, 'right' => 1), '<'),
  array(array('left' => 3, 'right' => 2), '<='),
  array(array('left' => 'foo', 'right' => 'bar'), '=='),
  array(array('left' => '0000', 'right' => '0'), '==='),
  array(array('left' => '0000', 'right' => '0'), '!='),
  array(array('left' => '0000', 'right' => '0000'), '!=='),
) as $values)
{
  $v->setOption('operator', $values[1]);

  foreach (array(true, false) as $globalError)
  {
    $v->setOption('throw_global_error', $globalError);
    try
    {
      $v->clean($values[0]);
      $t->fail('->clean() throws an sfValidatorError if the value is the comparison failed');
      $t->skip('', 1);
    }
    catch (sfValidatorError $e)
    {
      $t->pass('->clean() throws an sfValidatorError if the value is the comparison failed');
      $t->is($e->getCode(), $globalError ? 'invalid' : 'left [invalid]', '->clean() throws a sfValidatorError');
    }
  }
}

try
{
  $v->clean('foo');
  $t->fail('->clean() throws an InvalidArgumentException exception if the first argument is not an array of value');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->clean() throws an InvalidArgumentException exception if the first argument is not an array of value');
}

$v = new sfValidatorSchemaCompare('left', 'foo', 'right');
try
{
  $v->clean(array());
  $t->fail('->clean() throws an InvalidArgumentException exception if the operator does not exist');
}
catch (InvalidArgumentException $e)
{
  $t->pass('->clean() throws an InvalidArgumentException exception if the operator does not exist');
}

// ->asString()
$t->diag('->asString()');
$v = new sfValidatorSchemaCompare('left', sfValidatorSchemaCompare::EQUAL, 'right');
$t->is($v->asString(), 'left == right', '->asString() returns a string representation of the validator');

$v = new sfValidatorSchemaCompare('left', sfValidatorSchemaCompare::EQUAL, 'right', array(), array('required' => 'This is required.'));
$t->is($v->asString(), 'left ==({}, { required: \'This is required.\' }) right', '->asString() returns a string representation of the validator');
