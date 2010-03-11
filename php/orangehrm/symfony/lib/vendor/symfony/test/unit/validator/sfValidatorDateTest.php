<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');

$t = new lime_test(44, new lime_output_color());

$v = new sfValidatorDate();

// ->clean()
$t->diag('->clean()');

$v->setOption('required', false);
$t->ok($v->clean(null) === null, '->clean() returns null if not required');

// validate strtotime formats
$t->diag('validate strtotime formats');
$t->is($v->clean('18 october 2005'), '2005-10-18', '->clean() accepts dates parsable by strtotime');
$t->is($v->clean('+1 day'), date('Y-m-d', time() + 86400), '->clean() accepts dates parsable by strtotime');

try
{
  $v->clean('This is not a date');
  $t->fail('->clean() throws a sfValidatorError if the date is a string and is not parsable by strtotime');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the date is a string and is not parsable by strtotime');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

// validate timestamp
$t->diag('validate timestamp');
$t->is($v->clean(time()), date('Y-m-d', time()), '->clean() accepts timestamps as input');

// validate date array
$t->diag('validate date array');
$t->is($v->clean(array('year' => 2005, 'month' => 10, 'day' => 15)), '2005-10-15', '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => '2005', 'month' => '10', 'day' => '15')), '2005-10-15', '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => '', 'month' => '', 'day' => '')), null, '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => 2008, 'month' => 02, 'day' => 29)), '2008-02-29', '->clean() recognises a leapyear');

try
{
  $v->clean(array('year' => '', 'month' => 1, 'day' => 15));
  $t->fail('->clean() throws a sfValidatorError if the date is not valid');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the date is not valid');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

try
{
  $v->clean(array('year' => -2, 'month' => 1, 'day' => 15));
  $t->fail('->clean() throws a sfValidatorError if the date is not valid');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the date is not valid');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

try
{
  $v->clean(array('year' => 2008, 'month' => 2, 'day' => 30));
  $t->fail('->clean() throws a sfValidatorError if the date is not valid');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the date is not valid');
  $t->is($e->getCode(), 'invalid', '->clean() throws a sfValidatorError');
}

// validate regex
$t->diag('validate regex');
$v->setOption('date_format', '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~');
$t->is($v->clean('18/10/2005'), '2005-10-18', '->clean() accepts a regular expression to match dates');

try
{
  $v->clean('2005-10-18');
  $t->fail('->clean() throws a sfValidatorError if the date does not match the regex');
  $t->skip('', 2);
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the date does not match the regex');
  $t->like($e->getMessage(), '/'.preg_quote(htmlspecialchars($v->getOption('date_format'), ENT_QUOTES, 'UTF-8'), '/').'/', '->clean() returns the expected date format in the error message');
  $t->is($e->getCode(), 'bad_format', '->clean() throws a sfValidatorError');
}

$v->setOption('date_format_error', 'dd/mm/YYYY');
try
{
  $v->clean('2005-10-18');
  $t->skip('', 1);
}
catch (sfValidatorError $e)
{
  $t->like($e->getMessage(), '/'.preg_quote('dd/mm/YYYY', '/').'/', '->clean() returns the expected date format error if provided');
}

$v->setOption('date_format', null);

// option with_time
$t->diag('option with_time');
$v->setOption('with_time', true);
$t->is($v->clean(array('year' => 2005, 'month' => 10, 'day' => 15, 'hour' => 12, 'minute' => 10, 'second' => 15)), '2005-10-15 12:10:15', '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => '2005', 'month' => '10', 'day' => '15', 'hour' => '12', 'minute' => '10', 'second' => '15')), '2005-10-15 12:10:15', '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => '', 'month' => '', 'day' => '', 'hour' => '', 'minute' => '', 'second' => '')), null, '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => 2005, 'month' => 10, 'day' => 15, 'hour' => 12, 'minute' => 10, 'second' => '')), '2005-10-15 12:10:00', '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => 2005, 'month' => 10, 'day' => 15, 'hour' => 12, 'minute' => 10)), '2005-10-15 12:10:00', '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => 2005, 'month' => 10, 'day' => 15, 'hour' => 0, 'minute' => 10)), '2005-10-15 00:10:00', '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => 2005, 'month' => 10, 'day' => 15, 'hour' => '0', 'minute' => 10)), '2005-10-15 00:10:00', '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => 2005, 'month' => 10, 'day' => 15, 'hour' => 10)), '2005-10-15 10:00:00', '->clean() accepts an array as an input');
$t->is($v->clean(array('year' => 2005, 'month' => 10, 'day' => 15, 'hour' => 0)), '2005-10-15 00:00:00', '->clean() accepts an array as an input');
try
{
  $v->clean(array('year' => 2005, 'month' => 1, 'day' => 15, 'hour' => 12, 'minute' => '', 'second' => 12));
  $t->fail('->clean() throws a sfValidatorError if the time is not valid');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws a sfValidatorError if the time is not valid');
}

$t->is($v->clean('18 october 2005 12:30'), '2005-10-18 12:30:00', '->clean() can accept date time with the with_time option');
$t->is($v->clean(time()), date('Y-m-d H:i:s', time()), '->clean() can accept date time with the with_time option');
$v->setOption('date_format', '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~');
$t->is($v->clean('18/10/2005'), '2005-10-18 00:00:00', '->clean() can accept date time with the with_time option');
$v->setOption('date_format', '~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4}) (?P<hour>\d{2})\:(?P<minute>\d{2})~');
$t->is($v->clean('18/10/2005 12:30'), '2005-10-18 12:30:00', '->clean() can accept date time with the with_time option');
$v->setOption('date_format', null);

// change date output
$t->diag('change date output');
$v->setOption('with_time', false);
$v->setOption('date_output', 'U');
$t->is($v->clean(time()), time(), '->clean() output format can be change with the date_output option');
$v->setOption('datetime_output', 'U');
$v->setOption('with_time', true);
$t->is($v->clean(time()), time(), '->clean() output format can be change with the date_output option');

// required
$v = new sfValidatorDate();
foreach (array(
  array('year' => '', 'month' => '', 'day' => ''),
  array('year' => null, 'month' => null, 'day' => null),
  '',
  null,
) as $input)
{
  try
  {
    $v->clean($input);
    $t->fail('->clean() throws an exception if the date is empty and required is true');
  }
  catch (sfValidatorError $e)
  {
    $t->pass('->clean() throws an exception if the date is empty and required is true');
  }
}

// max and min options
$t->diag('max and min options');
$v->setOption('min', strtotime('1 Jan 2005'));
$v->setOption('max', strtotime('31 Dec 2007'));
$t->is($v->clean('18 october 2005'), '2005-10-18', '->clean() can accept a max/min option');
try
{
  $v->clean('18 october 2004');
  $t->fail('->clean() throws an exception if the date is not within the range provided by the min/max options');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an exception if the date is not within the range provided by the min/max options');
}
try
{
  $v->clean('18 october 2008');
  $t->fail('->clean() throws an exception if the date is not within the range provided by the min/max options');
}
catch (sfValidatorError $e)
{
  $t->pass('->clean() throws an exception if the date is not within the range provided by the min/max options');
}
