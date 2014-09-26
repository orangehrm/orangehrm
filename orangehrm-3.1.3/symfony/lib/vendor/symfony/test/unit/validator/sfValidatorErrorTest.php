<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(14);

$v = new sfValidatorString();

$e = new sfValidatorError($v, 'max_length', array('value' => 'foo<br />', 'max_length' => 1));

// ->getValue()
$t->diag('->getValue()');
$t->is($e->getValue(), 'foo<br />', '->getValue() returns the value that has been validated with the validator');

$e1 = new sfValidatorError($v, 'max_length', array('max_length' => 1));
$t->is($e1->getValue(), null, '->getValue() returns null if there is no value key in arguments');

// ->getValidator()
$t->diag('->getValidator()');
$t->is($e->getValidator(), $v, '->getValidator() returns the validator that triggered this exception');

// ->getArguments()
$t->diag('->getArguments()');
$t->is($e->getArguments(), array('%value%' => 'foo&lt;br /&gt;', '%max_length%' => 1), '->getArguments() returns the arguments needed to format the error message, escaped according to the current charset');
$t->is($e->getArguments(true), array('value' => 'foo<br />', 'max_length' => 1), '->getArguments() takes a Boolean as its first argument to return the raw arguments');

// ->getMessageFormat()
$t->diag('->getMessageFormat()');
$t->is($e->getMessageFormat(), $v->getMessage($e->getCode()), '->getMessageFormat()');

// ->getMessage()
$t->diag('->getMessage()');
$t->is($e->getMessage(), '"foo&lt;br /&gt;" is too long (1 characters max).', '->getMessage() returns the error message string');

// ->getCode()
$t->diag('->getCode()');
$t->is($e->getCode(), 'max_length', '->getCode() returns the error code');

// ->__toString()
$t->diag('__toString()');
$t->is($e->__toString(), $e->getMessage(), '->__toString() returns the error message string');

// implements Serializable
$t->diag('implements Serializable');

// we test with non serializable objects
// to ensure that the errors are always serializable
// even if you use PDO as a session handler
class NotSerializable implements Serializable
{
  public function serialize()
  {
    throw new Exception('Not serializable');
  }

  public function unserialize($serialized)
  {
    throw new Exception('Not serializable');
  }
}

function will_crash($a)
{
  return serialize(new sfValidatorError(new sfValidatorString(), 'max_length', array('value' => 'foo<br />', 'max_length' => 1)));
}

$a = new NotSerializable();

try
{
  $serialized = will_crash($a);
  $t->pass('sfValidatorError implements Serializable');
}
catch (Exception $e)
{
  $t->fail('sfValidatorError implements Serializable');
}

$e1 = unserialize($serialized);
$t->is($e1->getMessage(), $e->getMessage(), 'sfValidatorError implements Serializable');
$t->is($e1->getCode(), $e->getCode(), 'sfValidatorError implements Serializable');
$t->is(get_class($e1->getValidator()), get_class($e->getValidator()), 'sfValidatorError implements Serializable');
$t->is($e1->getArguments(), $e->getArguments(), 'sfValidatorError implements Serializable');
