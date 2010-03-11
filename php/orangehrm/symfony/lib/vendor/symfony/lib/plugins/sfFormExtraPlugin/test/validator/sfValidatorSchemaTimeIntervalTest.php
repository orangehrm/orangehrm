<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/../bootstrap.php';
require_once dirname(__FILE__).'/../../lib/validator/sfValidatorSchemaTimeInterval.class.php';

$t = new lime_test(14, new lime_output_color());

// __construct()
$t->diag('__construct()');
$v = new sfValidatorSchemaTimeInterval('start', 'end');
$t->is($v->getOption('date_start_field'), 'start', '__construct() assigns date start field option');
$t->is($v->getOption('date_end_field'), 'end', '__construct() assigns date end field option');

// clean()
$t->diag('clean()');
$v = new sfValidatorSchemaTimeInterval('start', 'end', array('disallow_future_dates' => false, 'throw_global_error' => false));
try
{
  $v->clean(array('start' => null, 'end' => null));
  $t->pass('clean() validates if no dates are set');
}
catch (sfValidatorErrorSchema $e)
{
  $t->fail('clean() validates if no dates are set');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('disallow_future_dates' => false, 'throw_global_error' => false));
try
{
  $v->clean(array('start' => '2008-01-01', 'end' => '2007-01-01'));
  $t->fail('clean() throws a sfValidatorError');
  $t->fail('clean() detects start date greater than end date');
}
catch (sfValidatorErrorSchema $e)
{
  $t->isa_ok($e, 'sfValidatorErrorSchema', 'clean() throws a sfValidatorErrorSchema');
  $t->is($e->getCode(), 'start [start_not_prior]', 'clean() detects start date greater than end date');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('disallow_future_dates' => true, 'throw_global_error' => false));
try
{
  $v->clean(array('start' => date('Y-m-d', time() + (365*24*3600)), 'end' => date('Y-m-d', time() + (366*24*3600))));
  $t->fail('clean() detects future date');
}
catch (sfValidatorErrorSchema $e)
{
  $t->is($e->getCode(), 'start [future_date]', 'clean() detects future date');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('disallow_past_dates' => true, 'throw_global_error' => false));
try
{
  $v->clean(array('start' => date('Y-m-d', time() - (365*24*3600)), 'end' => date('Y-m-d', time() - (366*24*3600))));
  $t->fail('clean() detects past date');
}
catch (sfValidatorErrorSchema $e)
{
  $t->is($e->getCode(), 'start [past_date]', 'clean() detects past date');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('disallow_future_dates' => false, 'throw_global_error' => false));
try
{
  $v->clean(array('start' => date('Y-m-d', time() + (365*24*3600)), 'end' => date('Y-m-d', time() + (366*24*3600))));
  $t->pass('clean() validates future dates if option allows it');
}
catch (sfValidatorErrorSchema $e)
{
  $t->fail('clean() validates future dates if option allows it');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('disallow_future_dates' => false, 'throw_global_error' => true));
try
{
  $v->clean(array('start' => '2008-01-01', 'end' => '2007-01-01'));
  $t->fail('clean() throws a gobal error if option is set');
}
catch (sfValidatorError $e)
{
  $t->isa_ok($e, 'sfValidatorError', 'clean() throws a gobal error if option is set');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('disallow_future_dates' => false, 'throw_global_error' => true));
try
{
  $v->clean(array('start' => '2008-01-01', 'end' => null));
  $t->pass('clean() validates if end date is not set');
}
catch (sfValidatorError $e)
{
  $t->fail('clean() validates if end date is not set');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('min_duration' => 86400 * 10, 'disallow_future_dates' => false, 'throw_global_error' => false));
try
{
  $v->clean(array('start' => '2008-01-01', 'end' => '2008-01-09'));
  $t->fail('clean() detects a too short duration');
}
catch (sfValidatorError $e)
{
  $t->is($e->getCode(), 'end [too_short]', 'clean() detects a too short duration');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('max_duration' => 86400 * 10, 'disallow_future_dates' => false, 'throw_global_error' => false));
try
{
  $v->clean(array('start' => '2008-01-01', 'end' => '2008-01-12'));
  $t->fail('clean() detects an exceeded duration');
}
catch (sfValidatorError $e)
{
  $t->is($e->getCode(), 'end [too_long]', 'clean() detects an exceeded duration');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('disallow_future_dates' => true, 'throw_global_error' => false));
try
{
  $v->clean(array('start' => null, 'end' => date('Y-m-d', time() + (10*86400))));
  $t->fail('clean() detects an unique future date');
}
catch (sfValidatorError $e)
{
  $t->pass('clean() detects an unique future date');
}

$v = new sfValidatorSchemaTimeInterval('start', 'end', array('disallow_past_dates' => true, 'throw_global_error' => false));
try
{
  $v->clean(array('start' => null, 'end' => date('Y-m-d', time() - (10*86400))));
  $t->fail('clean() detects an unique past date');
}
catch (sfValidatorError $e)
{
  $t->pass('clean() detects an unique past date');
}