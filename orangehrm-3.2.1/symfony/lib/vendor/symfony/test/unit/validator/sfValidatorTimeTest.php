<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(27);

$v = new sfValidatorTime();

// ->clean()
$t->diag('->clean()');

$v->setOption('required', false);
$t->ok($v->clean(null) === null, '->clean() returns null if not required');

// validate strtotime formats
$t->diag('validate strtotime formats');
$t->is($v->clean('16:35:12'), '16:35:12', '->clean() accepts times parsable by strtotime');
$t->is($v->clean('+1 hour'), date('H:i:s', time() + 3600), '->clean() accepts times parsable by strtotime');

try
{
  $v->clean('This is not a time');
  $t->fail('->clean() throws a sfValidatorError if the time is a string and is not parsable by strtotime');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the time is a string and is not parsable by strtotime');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

// validate timestamp
$t->diag('validate timestamp');
$t->is($v->clean(time()), date('H:i:s', time()), '->clean() accepts timestamps as input');

// validate date array
$t->diag('validate date array');
$t->is($v->clean(array('hour' => 20, 'minute' => 10, 'second' => 15)), '20:10:15', '->clean() accepts an array as an input');
$t->is($v->clean(array('hour' => '20', 'minute' => '10', 'second' => '15')), '20:10:15', '->clean() accepts an array as an input');
$t->is($v->clean(array('hour' => '', 'minute' => '', 'second' => '')), null, '->clean() accepts an array as an input');
$t->is($v->clean(array('hour' => 0, 'minute' => 0, 'second' => 0)), '00:00:00', '->clean() accepts an array as an input'); 
$t->is($v->clean(array('hour' => '0', 'minute' => '0', 'second' => '0')), '00:00:00', '->clean() accepts an array as an input'); 

try
{
  $v->clean(array('hour' => '', 'minute' => 0, 'second' => 0));
  $t->fail('->clean() throws a sfValidatorError if time date is not valid');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the time is not valid');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

try
{
  $v->clean(array('hour' => '', 'minute' => 1, 'second' => 15));
  $t->fail('->clean() throws a sfValidatorError if time date is not valid');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the time is not valid');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

try
{
  $v->clean(array('hour' => -2, 'minute' => 1, 'second' => 15));
  $t->fail('->clean() throws a sfValidatorError if the time is not valid');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the time is not valid');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

// validate regex
$t->diag('validate regex');
$v->setOption('time_format', '~(?P<hour>\d{2})-(?P<minute>\d{2}).(?P<second>\d{2})~');
$t->is($v->clean('20-10.18'), '20:10:18', '->clean() accepts a regular expression to match times');

try
{
  $v->clean('20.10-18');
  $t->fail('->clean() throws a sfValidatorError if the time does not match the regex');
  $t->skip('', 2);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the time does not match the regex');
  $t->like($e->getMessage(), '/'.preg_quote(htmlspecialchars($v->getOption('time_format'), ENT_QUOTES, 'UTF-8'), '/').'/', '->clean() returns the expected time format in the error message');
  $t->is($e->getCode(), 'bad_format', '->clean() throws a sfValidatorError');
}

$v->setOption('time_format_error', 'hh/mm/ss');
try
{
  $v->clean('20.10-18');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->like($e->getMessage(), '/'.preg_quote('hh/mm/ss', '/').'/', '->clean() returns the expected time format error if provided');
}

$v->setOption('time_format', null);

// change date output
$t->diag('change date output');
$v->setOption('time_output', 'U');
$t->is($v->clean(time()), time(), '->clean() output format can be change with the time_output option');

// required
$v = new sfValidatorTime();
foreach (array(
  array('hour' => '', 'minute' => '', 'second' => ''),
  array('hour' => null, 'minute' => null, 'second' => null),
  '',
  null,
) as $input)
{
  try
  {
    $v->clean($input);
    $t->fail('->clean() throws an exception if the time is empty and required is true');
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an exception if the time is empty and required is true');
  }
}
