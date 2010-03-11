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

try
{
  new sfValidatorDateRange();
  $t->fail('__construct() throws a sfValidatorError if you don\'t pass a from_date and a to_date option');
  $t->skip('', 1);
}
catch (RuntimeException $e)
{
  $t->pass('__construct() throws a sfValidatorError if you don\'t pass a from_date and a to_date option');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

$v = new sfValidatorDateRange(array(
  'from_date' => new sfValidatorDate(array('required' => false)),
  'to_date' => new sfValidatorDate(array('required' => false))
));

// ->clean()
$t->diag('->clean()');

$values = $v->clean(array('from' => '2008-01-01', 'to' => '2009-01-01'));
$t->is($values, array('from' => '2008-01-01', 'to' => '2009-01-01'), '->clean() returns the from and to values');

try
{
  $v->clean(array('from' => '2008-01-01', 'to' => '1998-01-01'));
  $t->fail('->clean() throws a sfValidatorError if the from date is after the to date');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the from date is after the to date');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}
