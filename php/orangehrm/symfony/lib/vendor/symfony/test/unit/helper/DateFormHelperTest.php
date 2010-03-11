<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/unit/sfContextMock.class.php');

class myController
{
  public function genUrl($parameters = array(), $absolute = false)
  {
    return 'module/action';
  }
}

class myUser
{
  public function getCulture()
  {
    return 'en';
  }
}

class myRequest
{
  public function getRelativeUrlRoot()
  {
    return '';
  }
}

$t = new lime_test(94, new lime_output_color());

$context = sfContext::getInstance(array('user' => 'myUser', 'request' => 'myRequest', 'controller' => 'myController'));

require_once(dirname(__FILE__).'/../../../lib/helper/HelperHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/TagHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/AssetHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/UrlHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/DateFormHelper.php');

// select_day_tag()
$t->diag('select_day_tag()');
$t->like(select_day_tag('day'), '/<select name="day" id="day">/', 'select_day_tag() outputs a select tag for days');
$t->like(select_day_tag('day'), '/<option value="'.date('j').'" selected="selected">/', 'select_day_tag() selects the current day by default');
$t->like(select_day_tag('day', 31), '/<option value="31" selected="selected">/', 'select_day_tag() takes a day as its second argument');
$t->like(select_day_tag('day', '01'), '/<option value="1" selected="selected">/', 'select_day_tag() takes a day as its second argument');

// options
$t->like(select_day_tag('day', null, array('include_custom' => 'test')), "/<option value=\"\">test<\/option>/", 'select_day_tag() can take an "include_custom" option');
$t->like(select_day_tag('day', null, array('include_blank' => true)), "/<option value=\"\"><\/option>/", 'select_day_tag() can take an "include_blank" option');
$t->like(select_day_tag('day', null, array(), array('class' => 'foo')), '<select name="day" id="day" class="foo">', 'select_day_tag() takes an array of attribute options as its fourth argument');
$t->like(select_day_tag('day', null, array(), array('id' => 'foo')), '<select name="day" id="foo">', 'select_day_tag() takes an array of attribute options as its fourth argument');

// select_month_tag()
$t->diag('select_month_tag()');
$t->like(select_month_tag('month'), '/<select name="month" id="month">/', 'select_month_tag() outputs a select tag for months');
$t->like(select_month_tag('month'), '/<option value="'.date('n').'" selected="selected">/', 'select_month_tag() selects the current month by default');
$t->like(select_month_tag('month', 12), '/<option value="12" selected="selected">/', 'select_month_tag() takes a month as its second argument');
$t->like(select_month_tag('month', '02'), '/<option value="1">January<\/option>/i', 'select_month_tag() displays month names by default');

// options
$t->like(select_month_tag('month', 2, array('use_short_month' => true)), '/<option value="1">Jan<\/option>/i', 'select_month_tag() displays short month names if passed a "use_short_month" options');
$t->like(select_month_tag('month', 2, array('use_month_numbers' => true)), '/<option value="1">01<\/option>/i', 'select_month_tag() displays numbers if passed a "use_month_numbers" options');
$t->like(select_month_tag('month', 2, array('culture' => 'fr')), '/<option value="1">janvier<\/option>/i', 'select_month_tag() takes a culture option');
$t->like(select_month_tag('month', 2, array('use_short_month' => true, 'culture' => 'fr')), '/<option value="1">janv.<\/option>/i', 'select_month_tag() displays short month names if passed a "use_short_month" options');
$t->like(select_month_tag('month', 2, array('use_short_month' => true, 'add_month_numbers' => true)), '/<option value="1">1 - Jan<\/option>/i', 'select_month_tag() displays month names and month number if passed a "add_month_numbers" options');
$t->like(select_month_tag('month', null, array('include_custom' => 'test')), "/<option value=\"\">test<\/option>/", 'select_month_tag() can take an "include_custom" option');
$t->like(select_month_tag('month', null, array('include_blank' => true)), "/<option value=\"\"><\/option>/", 'select_month_tag() can take an "include_blank" option');
$t->like(select_month_tag('month', null, array(), array('class' => 'foo')), '<select name="month" id="month" class="foo">', 'select_month_tag() takes an array of attribute options as its fourth argument');
$t->like(select_month_tag('month', null, array(), array('id' => 'foo')), '<select name="month" id="foo">', 'select_month_tag() takes an array of attribute options as its fourth argument');

// select_year_tag()
$t->diag('select_year_tag()');
$t->like(select_year_tag('year'), '/<select name="year" id="year">/', 'select_year_tag() outputs a select tag for years');
$t->like(select_year_tag('year'), '/<option value="'.date('Y').'" selected="selected">/', 'select_year_tag() selects the current year by default');
$t->like(select_year_tag('year', 2006), '/<option value="2006" selected="selected">/', 'select_year_tag() takes a year as its second argument');

// options
$t->is(preg_match_all('/<option /', select_year_tag('year', 2006, array('year_start' => 2005, 'year_end' => 2007)), $matches), 3, 'select_year_tag() takes a "year_start" and a "year_end" options');
$t->like(select_year_tag('year', null, array('include_custom' => 'test')), "/<option value=\"\">test<\/option>/", 'select_year_tag() can take an "include_custom" option');
$t->like(select_year_tag('year', null, array('include_blank' => true)), "/<option value=\"\"><\/option>/", 'select_year_tag() can take an "include_blank" option');
$t->like(select_year_tag('year', null, array(), array('class' => 'foo')), '<select name="year" id="year" class="foo">', 'select_year_tag() takes an array of attribute options as its fourth argument');
$t->like(select_year_tag('year', null, array(), array('id' => 'foo')), '<select name="year" id="foo">', 'select_year_tag() takes an array of attribute options as its fourth argument');

// select_date_tag()
$t->diag('select_date_tag()');
$t->todo('select_date_tag()');

// select_second_tag()
$t->diag('select_second_tag()');
$t->like(select_second_tag('second'), '/<select name="second" id="second">/', 'select_second_tag() outputs a select tag for seconds');
$t->like(select_second_tag('second'), '/selected="selected">'.date('s').'/', 'select_second_tag() selects the current seconds by default');
$t->like(select_second_tag('second', 12), '/<option value="12" selected="selected">/', 'select_second_tag() takes a second number as its second argument');
$t->like(select_second_tag('second', '02'), '/<option value="2" selected="selected">/', 'select_second_tag() takes a second number as its second argument');

// options
$t->like(select_second_tag('second', null, array('include_custom' => 'test')), "/<option value=\"\">test<\/option>/", 'select_second_tag() can take an "include_custom" option');
$t->like(select_second_tag('second', null, array('include_blank' => true)), "/<option value=\"\"><\/option>/", 'select_second_tag() can take an "include_blank" option');
$t->like(select_second_tag('second', null, array(), array('class' => 'foo')), '<select name="second" id="second" class="foo">', 'select_second_tag() takes an array of attribute options as its fourth argument');
$t->like(select_second_tag('second', null, array(), array('id' => 'foo')), '<select name="second" id="foo">', 'select_second_tag() takes an array of attribute options as its fourth argument');
$t->is(preg_match_all("/<option value=\"/", select_second_tag('second', null, array('second_step' => 10)), $matches), 6, 'select_second_tag() can take an "second_step" option');

// select_minute_tag()
$t->diag('select_minute_tag()');
$t->like(select_minute_tag('minute'), '/<select name="minute" id="minute">/', 'select_minute_tag() outputs a select tag for minutes');
$t->like(select_minute_tag('minute'), '/selected="selected">'.date('i').'/', 'select_minute_tag() selects the current minutes by default');
$t->like(select_minute_tag('minute', 12), '/<option value="12" selected="selected">/', 'select_minute_tag() takes a minute number as its second argument');
$t->like(select_minute_tag('minute', '02'), '/<option value="2" selected="selected">/', 'select_minute_tag() takes a minute number as its second argument');

// options
$t->like(select_minute_tag('minute', null, array('include_custom' => 'test')), "/<option value=\"\">test<\/option>/", 'select_minute_tag() can take an "include_custom" option');
$t->like(select_minute_tag('minute', null, array('include_blank' => true)), "/<option value=\"\"><\/option>/", 'select_minute_tag() can take an "include_blank" option');
$t->like(select_minute_tag('minute', null, array(), array('class' => 'foo')), '<select name="minute" id="minute" class="foo">', 'select_minute_tag() takes an array of attribute options as its fourth argument');
$t->like(select_minute_tag('minute', null, array(), array('id' => 'foo')), '<select name="minute" id="foo">', 'select_minute_tag() takes an array of attribute options as its fourth argument');
$t->is(preg_match_all("/<option value=\"/", select_minute_tag('minute', null, array('minute_step' => 10)), $matches), 6, 'select_minute_tag() can take an "minute_step" option');

// select_hour_tag()
$t->diag('select_hour_tag()');
$t->like(select_hour_tag('hour'), '/<select name="hour" id="hour">/', 'select_hour_tag() outputs a select tag for hours');
$t->like(select_hour_tag('hour'), '/selected="selected">'.date('H').'/', 'select_hour_tag() selects the current hours by default');
$t->like(select_hour_tag('hour', 1), '/<option value="1" selected="selected">/', 'select_hour_tag() takes a hour number as its second argument');

// options
$t->like(select_hour_tag('hour', null, array('include_custom' => 'test')), "/<option value=\"\">test<\/option>/", 'select_hour_tag() can take an "include_custom" option');
$t->like(select_hour_tag('hour', null, array('include_blank' => true)), "/<option value=\"\"><\/option>/", 'select_hour_tag() can take an "include_blank" option');
$t->like(select_hour_tag('hour', null, array(), array('class' => 'foo')), '<select name="hour" id="hour" class="foo">', 'select_hour_tag() takes an array of attribute options as its fourth argument');
$t->like(select_hour_tag('hour', null, array(), array('id' => 'foo')), '<select name="hour" id="foo">', 'select_hour_tag() takes an array of attribute options as its fourth argument');
$t->is(preg_match_all("/<option value=\"/", select_hour_tag('hour'), $matches), 24, 'select_hour_tag() can take an "12hour_time" option');
$t->is(preg_match_all("/<option value=\"/", select_hour_tag('hour', null, array('12hour_time' => true)), $matches), 12, 'select_hour_tag() can take an "12hour_time" option');

// select_ampm_tag()
$t->diag('select_ampm_tag()');
$t->like(select_ampm_tag('ampm'), '/<select name="ampm" id="ampm">/', 'select_ampm_tag() outputs a select tag for ampm');
$t->like(select_ampm_tag('ampm'), '/<option value="'.date('A').'" selected="selected">/', 'select_ampm_tag() selects the current ampm by default');
$t->like(select_ampm_tag('ampm', 'AM'), '/<option value="AM" selected="selected">/', 'select_ampm_tag() takes a ampm as its second argument');

// options
$t->like(select_ampm_tag('ampm', null, array('include_custom' => 'test')), '/<option value="">test<\/option>/', 'select_ampm_tag() can take an "include_custom" option');
$t->like(select_ampm_tag('ampm', null, array('include_blank' => true)), '/<option value=""><\/option>/', 'select_ampm_tag() can take an "include_blank" option');
$t->like(select_ampm_tag('ampm', null, array(), array('class' => 'foo')), '<select name="ampm" id="ampm" class="foo">', 'select_ampm_tag() takes an array of attribute options as its fourth argument');
$t->like(select_ampm_tag('ampm', null, array(), array('id' => 'foo')), '<select name="ampm" id="foo">', 'select_ampm_tag() takes an array of attribute options as its fourth argument');

// select_time_tag()
$t->diag('select_time_tag()');
$t->like(select_time_tag('time'), '/<select name="time\[hour\]" id="time_hour">/', 'select_time_tag() outputs a select tag for hours');
$t->like(select_time_tag('time'), '/selected="selected">'.date('H').'/', 'select_time_tag() selects the current hours by default');
$t->like(select_time_tag('time'), '/<select name="time\[minute\]" id="time_minute">/', 'select_time_tag() outputs a select tag for minutes');
$t->like(select_time_tag('time'), '/selected="selected">'.date('i').'/', 'select_time_tag() selects the current minutes by default');
$t->like(select_time_tag('time','09:01:05'), '/<option value="9" selected="selected">/', 'select_time_tag() selects hours for one digit correctly');
$t->like(select_time_tag('time','09:01:05'), '/<option value="1" selected="selected">/', 'select_time_tag() selects minutes for one digit correctly');
$t->like(select_time_tag('time','09:01:05', array('include_second' => true)), '/<option value="5" selected="selected">/', 'select_time_tag() selects seconds for one digit correctly');
$t->todo('select_time_tag()');

// select_timezone_tag()
$t->diag('select_timezone_tag()');
$t->like(select_timezone_tag('timezone'), '/<select name="timezone" id="timezone">/', 'select_timezone_tag() outputs a select tag for timezones');
$t->like(select_timezone_tag('timezone'), '/<option value="America\/Los_Angeles">America\/Los_Angeles<\/option>/', 'select_timezone_tag() outputs a select tag for timezones');
$t->like(select_timezone_tag('timezone', null, array('display' => 'city')), '/<option value="America\/Los_Angeles">Los Angeles<\/option>/', 'select_timezone_tag() respects the display option');
$t->like(select_timezone_tag('timezone', null, array('display' => 'timezone')), '/<option value="America\/Los_Angeles">Pacific Standard Time<\/option>/', 'select_timezone_tag() respects the display option');
$t->like(select_timezone_tag('timezone', null, array('display' => 'timezone_abbr')), '/<option value="America\/Los_Angeles">PST<\/option>/', 'select_timezone_tag() respects the display option');
$t->like(select_timezone_tag('timezone', null, array('display' => 'timezone_dst')), '/<option value="America\/Los_Angeles">Pacific Daylight Time<\/option>/', 'select_timezone_tag() respects the display option');
$t->like(select_timezone_tag('timezone', null, array('display' => 'timezone_dst_abbr')), '/<option value="America\/Los_Angeles">PDT<\/option>/', 'select_timezone_tag() respects the display option');

// select_datetime_tag()
$t->diag('select_datetime_tag()');
$t->todo('select_datetime_tag()');

// select_number_tag()
$t->diag('select_number_tag()');
$t->like(select_number_tag('number', 3), '/<select name="number" id="number">/', 'select_number_tag() outputs a select tag for a range of numbers');

// options
$t->like(select_number_tag('number', null, array('include_custom' => 'test')), "/<option value=\"\">test<\/option>/", 'select_number_tag() can take an "include_custom" option');
$t->like(select_number_tag('number', null, array('include_blank' => true)), "/<option value=\"\"><\/option>/", 'select_number_tag() can take an "include_blank" option');
$t->like(select_number_tag('number', null, array(), array('class' => 'foo')), '<select name="number" id="number" class="foo">', 'select_number_tag() takes an array of attribute options as its fourth argument');
$t->like(select_number_tag('number', null, array(), array('id' => 'foo')), '<select name="number" id="foo">', 'select_number_tag() takes an array of attribute options as its fourth argument');
$t->is(preg_match_all('/<option/', select_number_tag('number', 3, array('increment' => 4)), $matches), 4, 'select_number_tag() can take an "increment" option');
foreach (array(1, 5, 9, 13) as $number)
{
  $t->like(select_number_tag('number', 3, array('increment' => 4)), '/<option value="'.$number.'"/', 'select_number_tag() can take an "increment" option');
}
$t->is(preg_match_all('/<option/', select_number_tag('number', 40, array('start' => 38, 'end' => 40)), $matches), 3, 'select_number_tag() can take a "start" and "end" options');
foreach (array(38, 39, 40) as $number)
{
  $t->like(select_number_tag('number', 40, array('start' => 38, 'end' => 40)), '/<option value="'.$number.'"/', 'select_number_tag() can take a "start" and an "end" option');
}
