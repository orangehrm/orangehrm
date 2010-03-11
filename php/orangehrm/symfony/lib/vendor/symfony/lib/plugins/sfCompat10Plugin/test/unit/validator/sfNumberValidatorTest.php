<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/../../../../test/unit/sfContextMock.class.php');
require_once(dirname(__FILE__).'/sfValidatorTestHelper.class.php');

$t = new lime_test(53, new lime_output_color());

$context = sfContext::getInstance();
$v = new sfNumberValidator($context);

// ->execute()
$t->diag('->execute()');
$number = 12;
$error = null;
$t->ok($v->execute($number, $error), '->execute() returns true if you don\'t define any parameter');

foreach (array('not a number', '0xFE') as $number)
{
  $error = null;
  $t->ok(!$v->execute($number, $error), '->execute() returns "nan_error" if value is not a number');
  $t->is($error, 'Input is not a number', '->execute() changes "$error" with a default message if it returns false');
}

foreach (array('any', 'decimal', 'float', 'int', 'integer') as $type)
{
  $t->ok($v->initialize($context, array('type' => $type)), sprintf('->execute() can take "%s" as a type argument', $type));
}

try
{
  $v->initialize($context, array('type' => 'another type'));
  $t->fail('->initialize() throws an sfValidatorException if "type" is invalid');
}
catch (sfValidatorException $e)
{
  $t->pass('->initialize() throws an sfValidatorException if "type" is invalid');
}

$h = new sfValidatorTestHelper($context, $t);

// min
$t->diag('->execute() - min parameter');
$h->launchTests($v, 6, true, 'min', null, array('min' => 5));
$h->launchTests($v, 5, true, 'min', null, array('min' => 5));
$h->launchTests($v, 4, false, 'min', 'min_error', array('min' => 5));

// max
$t->diag('->execute() - max parameter');
$h->launchTests($v, 4, true, 'max', null, array('max' => 5));
$h->launchTests($v, 5, true, 'max', null, array('max' => 5));
$h->launchTests($v, 6, false, 'max', 'max_error', array('max' => 5));

// type is integer
$t->diag('->execute() - type is integer');
$h->launchTests($v, 4, true, 'type', null, array('type' => 'integer'));
$h->launchTests($v, 4.1, false, 'type', 'type_error', array('type' => 'integer'));

// type is int
$t->diag('->execute() - type is int');
$h->launchTests($v, 4, true, 'type', null, array('type' => 'int'));
$h->launchTests($v, 4.1, false, 'type', 'type_error', array('type' => 'int'));

// type is float
$t->diag('->execute() - type is float');
$h->launchTests($v, 4.1, true, 'type', null, array('type' => 'float'));
$h->launchTests($v, 4, false, 'type', 'type_error', array('type' => 'float'));

// type is decimal
$t->diag('->execute() - type is decimal');
$h->launchTests($v, 4.1, true, 'type', null, array('type' => 'decimal'));
$h->launchTests($v, 4, false, 'type', 'type_error', array('type' => 'decimal'));

// type is any
$t->diag('->execute() - type is any');
$h->launchTests($v, 4.1, true, 'type', null, array('type' => 'any'));
$h->launchTests($v, 4, true, 'type', 'type_error', array('type' => 'any'));

// number is negative
$t->diag('->execute() - number is negative');
$h->launchTests($v, -4, true, 'type', 'type_error', array('type' => 'any'));

// conversion of value
$t->diag('->execute() - conversion of value');
$v->initialize($context, array('type' => 'integer'));
$value = '4';
$v->execute($value, $error);
$t->isa_ok($value, 'integer', '->excute() converts to integer if type is integer');

$v->initialize($context, array('type' => 'float'));
$value = '4.1';
$v->execute($value, $error);
$t->isa_ok($value, 'double', '->excute() converts to float if type is float');
