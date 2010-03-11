<?php

require_once dirname(__FILE__).'/FormHelper.php';

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * DateFormHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: DateFormHelper.php 17858 2009-05-01 21:22:50Z FabianLange $
 */

/**
 * Returns a <select> tag populated with all the days of the month (1 - 31).
 *
 * By default, the <i>$value</i> parameter is set to today's day. To override this, simply pass an integer
 * (1 - 31) to the <i>$value</i> parameter. You can also set the <i>$value</i> parameter to null which will disable
 * the <i>$value</i>, however this will only be useful if you pass 'include_blank' or 'include_custom' to the <i>$options</i>
 * parameter. For convenience, symfony also offers the select_date_tag helper function which combines the
 * select_year_tag, select_month_tag, and select_day_tag functions into a single helper.
 *
 * <b>Options:</b>
 * - include_blank  - Includes a blank <option> tag at the beginning of the string with an empty value.
 * - include_custom - Includes an <option> tag with a custom display title at the beginning of the string with an empty value.
 *
 * <b>Examples:</b>
 * <code>
 *  echo select_day_tag('day', 14);
 * </code>
 *
 * @param string $name         field name
 * @param int    $value        selected value (1 - 31)
 * @param array  $options      special options for the select tag
 * @param array  $html_options additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with all the days of the month (1 - 31).
 * @see    select_date_tag, select datetime_tag
 */
function select_day_tag($name, $value = null, $options = array(), $html_options = array())
{
  if ($value === null)
  {
    $value = date('j');
  }

  $options = _parse_attributes($options);

  $select_options = array();
  _convert_include_custom_for_select($options, $select_options);

  for ($x = 1; $x < 32; $x++)
  {
    $select_options[$x] = str_pad($x, 2, '0', STR_PAD_LEFT);
  }

  return select_tag($name, options_for_select($select_options, (int) $value), $html_options);
}

/**
 * Returns a <select> tag populated with all the months of the year (1 - 12).
 *
 * By default, the <i>$value</i> parameter is set to today's month. To override this, simply pass an integer 
 * (1 - 12) to the <i>$value</i> parameter. You can also set the <i>$value</i> parameter to null which will disable
 * the <i>$value</i>, however this will only be useful if you pass 'include_blank' or 'include_custom' to the <i>$options</i>
 * parameter. Also, the each month's display title is set to return its respective full month name, which can be easily 
 * overridden by passing the 'use_short_names' or 'use_month_numbers' options to the <i>$options</i> parameter.
 * For convenience, Symfony also offers the select_date_tag helper function which combines the 
 * select_year_tag, select_month_tag, and select_day_tag functions into a single helper.
 *
 * <b>Options:</b>
 * - include_blank     - Includes a blank <option> tag at the beginning of the string with an empty value
 * - include_custom    - Includes an <option> tag with a custom display title at the beginning of the string with an empty value
 * - use_month_numbers - If set to true, will show the month's numerical value (1 - 12) instead of the months full name.
 * - use_short_month   - If set to true, will show the month's short name (i.e. Jan, Feb, Mar) instead of its full name.
 *  
 * <b>Examples:</b>
 * <code>
 *  echo select_month_tag('month', 5, array('use_short_month' => true));
 * </code>
 *
 * <code>
 *  echo select_month_tag('month', null, array('use_month_numbers' => true, 'include_blank' => true));
 * </code>
 *
 * @param string $name         field name
 * @param int    $value        selected value (1 - 12)
 * @param array  $options      special options for the select tag
 * @param array  $html_options additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with all the months of the year (1 - 12).
 * @see select_date_tag, select datetime_tag
 */
function select_month_tag($name, $value = null, $options = array(), $html_options = array())
{
  if ($value === null)
  {
    $value = date('n');
  }

  $options = _parse_attributes($options);

  $select_options = array();
  _convert_include_custom_for_select($options, $select_options);

  if (_get_option($options, 'use_month_numbers'))
  {
    for ($k = 1; $k < 13; $k++) 
    {
      $select_options[$k] = str_pad($k, 2, '0', STR_PAD_LEFT);
    }
  }
  else
  {
    $culture = _get_option($options, 'culture', sfContext::getInstance()->getUser()->getCulture());
    $I18n_arr = _get_I18n_date_locales($culture);

    if (_get_option($options, 'use_short_month'))
    {
      $month_names = $I18n_arr['dateFormatInfo']->getAbbreviatedMonthNames();
    }
    else
    {
      $month_names = $I18n_arr['dateFormatInfo']->getMonthNames();
    }

    $add_month_numbers = _get_option($options, 'add_month_numbers');
    foreach ($month_names as $k => $v) 
    {
      $select_options[$k + 1] = $add_month_numbers ? ($k + 1).' - '.$v : $v;
    }
  }

  return select_tag($name, options_for_select($select_options, (int) $value), $html_options);
}

/**
 * Returns a <select> tag populated with a range of years.
 *
 * By default, the <i>$value</i> parameter is set to today's year. To override this, simply pass a four-digit integer (YYYY)
 * to the <i>$value</i> parameter. You can also set the <i>$value</i> parameter to null which will disable
 * the <i>$value</i>, however this will only be useful if you pass 'include_blank' or 'include_custom' to the <i>$options</i>
 * parameter. Also, the default selectable range of years is set to show five years back and five years forward from today's year.
 * For instance, if today's year is 2006, the default 'year_start' option will be set to 2001 and the 'year_end' option will be set
 * to 2011.  These start and end dates can easily be overwritten by setting the 'year_start' and 'year_end' options in the <i>$options</i>
 * parameter. For convenience, Symfony also offers the select_date_tag helper function which combines the 
 * select_year_tag, select_month_tag, and select_day_tag functions into a single helper.
 *
 * <b>Options:</b>
 * - include_blank  - Includes a blank <option> tag at the beginning of the string with an empty value
 * - include_custom - Includes an <option> tag with a custom display title at the beginning of the string with an empty value
 * - year_start     - If set, the range of years will begin at this four-digit date (i.e. 1979)
 * - year_end       - If set, the range of years will end at this four-digit date (i.e. 2025)
 *  
 * <b>Examples:</b>
 * <code>
 *  echo select_year_tag('year');
 * </code>
 *
 * <code>
 *  $year_start = date('Y', strtotime('-10 years'));
 *  $year_end = date('Y', strtotime('+10 years'));
 *  echo select_year_tag('year', null, array('year_start' => $year_start, 'year_end' => $year_end));
 * </code>
 *
 * @param string $name         field name
 * @param int    $value        selected value within the range of years.
 * @param array  $options      special options for the select tag
 * @param array  $html_options additional HTML compliant <select> tag parameters
 * 
 * @return string <select> tag populated with a range of years.
 * @see select_date_tag, select datetime_tag
 */
function select_year_tag($name, $value = null, $options = array(), $html_options = array())
{
  if ($value === null)
  {
    $value = date('Y');
  }
    
  $options = _parse_attributes($options);

  $select_options = array();
  _convert_include_custom_for_select($options, $select_options);

  if (strlen($value) > 0 && is_numeric($value))
  {
    $year_origin = $value;
  }
  else
  {
    $year_origin = date('Y');
  }

  $year_start = _get_option($options, 'year_start', $year_origin - 5);
  $year_end   = _get_option($options, 'year_end', $year_origin + 5);

  $ascending  = ($year_start < $year_end);
  $until_year = ($ascending) ? $year_end + 1 : $year_end - 1;

  for ($x = $year_start; $x != $until_year; ($ascending) ? $x++ : $x--)
  {
    $select_options[$x] = $x;
  }

  return select_tag($name, options_for_select($select_options, $value), $html_options);
}

/**
 * Returns three <select> tags populated with a range of months, days, and years.
 *
 * By default, the <i>$value</i> parameter is set to today's month, day and year. To override this, simply pass a valid date
 * or a correctly formatted date array (see example) to the <i>$value</i> parameter. You can also set the <i>$value</i> 
 * parameter to null which will disable the <i>$value</i>, however this will only be useful if you pass 'include_blank' or 
 * 'include_custom' to the <i>$options</i> parameter. Also, the default selectable range of years is set to show five years 
 * back and five years forward from today's year. For instance, if today's year is 2006, the default 'year_start' option will 
 * be set to 2001 and the 'year_end' option will be set to 2011.  These start and end dates can easily be overwritten by 
 * setting the 'year_start' and 'year_end' options in the <i>$options</i> parameter. 
 *
 * <b>Note:</b> The <i>$name</i> parameter will automatically converted to array names. For example, a <i>$name</i> of "date" becomes:
 * <samp>
 *  <select name="date[month]">...</select>
 *  <select name="date[day]">...</select>
 *  <select name="date[year]">...</select>
 * </samp>
 *  
 * <b>Options:</b>
 * - include_blank     - Includes a blank <option> tag at the beginning of the string with an empty value.
 * - include_custom    - Includes an <option> tag with a custom display title at the beginning of the string with an empty value.
 * - discard_month     - If set to true, will only return select tags for day and year.
 * - discard_day       - If set to true, will only return select tags for month and year.
 * - discard_year      - If set to true, will only return select tags for month and day.
 * - use_month_numbers - If set to true, will show the month's numerical value (1 - 12) instead of the months full name.
 * - use_short_month   - If set to true, will show the month's short name (i.e. Jan, Feb, Mar) instead of its full name.
 * - year_start        - If set, the range of years will begin at this four-digit date (i.e. 1979)
 * - year_end          - If set, the range of years will end at this four-digit date (i.e. 2025)
 * - date_seperator    - Includes a string of defined text between each generated select tag
 *  
 * <b>Examples:</b>
 * <code>
 *  echo select_date_tag('date');
 * </code>
 *
 * <code>
 *  echo select_date_tag('date', '2006-10-30');
 * </code>
 *
 * <code>
 *  $date = array('year' => '1979', 'month' => 10, 'day' => 30);
 *  echo select_date_tag('date', $date, array('year_start' => $date['year'] - 10, 'year_end' => $date['year'] + 10));
 * </code>
 *
 * @param string $name         field name (automatically becomes an array of parts: name[year], name[month], year[day])
 * @param mixed  $value        accepts a valid date string or properly formatted date array
 * @param array  $options      special options for the select tags
 * @param array  $html_options additional HTML compliant <select> tag parameters
 *
 * @return string three <select> tags populated with a months, days and years
 * @see select datetime_tag, select_month_tag, select_date_tag, select_year_tag
 */
function select_date_tag($name, $value = null, $options = array(), $html_options = array())
{
  $options = _parse_attributes($options);

  $culture = _get_option($options, 'culture', sfContext::getInstance()->getUser()->getCulture());

  // set it back for month tag
  $options['culture'] = $culture;

  $I18n_arr = _get_I18n_date_locales($culture);

  $date_seperator = _get_option($options, 'date_seperator', $I18n_arr['date_seperator']);
  $discard_month  = _get_option($options, 'discard_month');
  $discard_day    = _get_option($options, 'discard_day');
  $discard_year   = _get_option($options, 'discard_year');

  // discarding month automatically discards day
  if ($discard_month)
  {
    $discard_day = true;
  }

  $order = _get_option($options, 'order');
  $tags = array();
  if (is_array($order) && count($order) == 3)
  {
    foreach ($order as $v)
    {
      $tags[] = $v[0];
    }
  }
  else
  {
    $tags = $I18n_arr['date_order'];
  }

  if ($include_custom = _get_option($options, 'include_custom'))
  {
    $include_custom_month = is_array($include_custom)
        ? (isset($include_custom['month']) ? array('include_custom' => $include_custom['month']) : array())
        : array('include_custom' => $include_custom);

    $include_custom_day = is_array($include_custom)
        ? (isset($include_custom['day']) ? array('include_custom' => $include_custom['day']) : array())
        : array('include_custom' => $include_custom);

    $include_custom_year = is_array($include_custom)
        ? (isset($include_custom['year']) ? array('include_custom' => $include_custom['year']) : array())
        : array('include_custom' => $include_custom);
  }
  else
  {
    $include_custom_month = array();
    $include_custom_day   = array();
    $include_custom_year  = array();
  }

  $month_name = $name.'[month]';
  $m = !$discard_month ? select_month_tag($month_name, _parse_value_for_date($value, 'month', 'm'), $options + $include_custom_month, $html_options) : '';

  $day_name = $name.'[day]';
  $d = !$discard_day ? select_day_tag($day_name, _parse_value_for_date($value, 'day', 'd'), $options + $include_custom_day, $html_options) : '';

  $year_name = $name.'[year]';
  $y = !$discard_year ? select_year_tag($year_name, _parse_value_for_date($value, 'year', 'Y'), $options + $include_custom_year, $html_options) : '';

  // we have $tags = array ('m','d','y')
  foreach ($tags as $k => $v)
  {
    // $tags['m|d|y'] = $m|$d|$y
    if (strlen($$v))
    {
      $tags[$k] = $$v;
    }
    else
    {
      unset($tags[$k]);
    }
  }

  return implode($date_seperator, $tags);
}

/**
 * Returns a <select> tag populated with 60 seconds (0 - 59).
 *
 * By default, the <i>$value</i> parameter is set to the current second (right now). To override this, simply pass an integer 
 * (0 - 59) to the <i>$value</i> parameter. You can also set the <i>$value</i> parameter to null which will disable
 * the <i>$value</i>, however this will only be useful if you pass 'include_blank' or 'include_custom' to the <i>$options</i>
 * parameter. In many cases, you have no need for all 60 seconds in a minute.  the 'second_step' option in the 
 * <i>$options</i> parameter gives you the ability to define intervals to display.  So for instance you could define 15 as your 
 * 'minute_step' interval and the select tag would return the values 0, 15, 30, and 45. For convenience, Symfony also offers the 
 * select_time_tag select_datetime_tag helper functions which combine other date and time helpers to easily build date and time select boxes.
 *
 * <b>Options:</b>
 * - include_blank  - Includes a blank <option> tag at the beginning of the string with an empty value.
 * - include_custom - Includes an <option> tag with a custom display title at the beginning of the string with an empty value.
 * - second_step    - If set, the seconds will be incremented in blocks of X, where X = 'second_step'
 * 
 * <b>Examples:</b>
 * <code>
 *  echo select_second_tag('second');
 * </code>
 *
 * <code>
 *  echo select_second_tag('second', 15, array('second_step' => 15));
 * </code>
 *
 * @param string $name         field name
 * @param int    $value        selected value (0 - 59)
 * @param array  $options      special options for the select tag
 * @param array  $html_options additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with 60 seconds (0 - 59).
 * @see select_time_tag, select datetime_tag
 */
function select_second_tag($name, $value = null, $options = array(), $html_options = array())
{
  if ($value === null)
  {
    $value = date('s');
  }

  $options = _parse_attributes($options);
  $select_options = array();
  _convert_include_custom_for_select($options, $select_options);

  $second_step = _get_option($options, 'second_step', 1);
  for ($x = 0; $x < 60; $x += $second_step)
  {
    $select_options[$x] = str_pad($x, 2, '0', STR_PAD_LEFT);
  }

  return select_tag($name, options_for_select($select_options, (int) $value), $html_options);
}

/**
 * Returns a <select> tag populated with 60 minutes (0 - 59).
 *
 * By default, the <i>$value</i> parameter is set to the current minute. To override this, simply pass an integer 
 * (0 - 59) to the <i>$value</i> parameter. You can also set the <i>$value</i> parameter to null which will disable
 * the <i>$value</i>, however this will only be useful if you pass 'include_blank' or 'include_custom' to the <i>$options</i>
 * parameter. In many cases, you have no need for all 60 minutes in an hour.  the 'minute_step' option in the 
 * <i>$options</i> parameter gives you the ability to define intervals to display.  So for instance you could define 15 as your 
 * 'minute_step' interval and the select tag would return the values 0, 15, 30, and 45. For convenience, Symfony also offers the 
 * select_time_tag select_datetime_tag helper functions which combine other date and time helpers to easily build date and time select boxes.
 *
 * <b>Options:</b>
 * - include_blank  - Includes a blank <option> tag at the beginning of the string with an empty value.
 * - include_custom - Includes an <option> tag with a custom display title at the beginning of the string with an empty value.
 * - minute_step    - If set, the minutes will be incremented in blocks of X, where X = 'minute_step'
 * 
 * <b>Examples:</b>
 * <code>
 *  echo select_minute_tag('minute');
 * </code>
 *
 * <code>
 *  echo select_minute_tag('minute', 15, array('minute_step' => 15));
 * </code>
 *
 * @param string $name         field name
 * @param int    $value        selected value (0 - 59)
 * @param array  $options      special options for the select tag
 * @param array  $html_options additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with 60 minutes (0 - 59).
 * @see select_time_tag, select datetime_tag
 */
function select_minute_tag($name, $value = null, $options = array(), $html_options = array())
{
  if ($value === null)
  {
    $value = date('i');
  }

  $options = _parse_attributes($options);
  $select_options = array();
  _convert_include_custom_for_select($options, $select_options);

  $minute_step = _get_option($options, 'minute_step', 1);
  for ($x = 0; $x < 60; $x += $minute_step)
  {
    $select_options[$x] = str_pad($x, 2, '0', STR_PAD_LEFT);
  }

  return select_tag($name, options_for_select($select_options, (int) $value), $html_options);
}

/**
 * Returns a <select> tag populated with 24 hours (0 - 23), or optionally 12 hours (1 - 12).
 *
 * By default, the <i>$value</i> parameter is set to the current hour. To override this, simply pass an integer 
 * (0 - 23 or 1 - 12 if '12hour_time' = true) to the <i>$value</i> parameter. You can also set the <i>$value</i> parameter to null which will disable
 * the <i>$value</i>, however this will only be useful if you pass 'include_blank' or 'include_custom' to the <i>$options</i>
 * parameter. For convenience, Symfony also offers the select_time_tag select_datetime_tag helper functions
 * which combine other date and time helpers to easily build date and time select boxes.
 *
 * <b>Options:</b>
 * - include_blank  - Includes a blank <option> tag at the beginning of the string with an empty value.
 * - include_custom - Includes an <option> tag with a custom display title at the beginning of the string with an empty value.
 * - 12hour_time    - If set to true, will return integers 1 through 12 instead of the default 0 through 23 as well as an AM/PM select box.
 * 
 * <b>Examples:</b>
 * <code>
 *  echo select_hour_tag('hour');
 * </code>
 *
 * <code>
 *  echo select_hour_tag('hour', 6, array('12hour_time' => true));
 * </code>
 *
 * @param string $name         field name
 * @param int    $value        selected value (0 - 23 or 1 - 12 if '12hour_time' = true)
 * @param array  $options      special options for the select tag
 * @param array  $html_options additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with 24 hours (0 - 23), or optionally 12 hours (1 - 12).
 * @see select_time_tag, select datetime_tag
 */
function select_hour_tag($name, $value = null, $options = array(), $html_options = array())
{
  $options = _parse_attributes($options);
  $select_options = array();
  _convert_include_custom_for_select($options, $select_options);

  $_12hour_time = _get_option($options, '12hour_time');

  if ($value === null)
  {
    $value = date($_12hour_time ? 'h' : 'H');
  }

  $start_hour = $_12hour_time ? 1  : 0;
  $end_hour   = $_12hour_time ? 12 : 23;

  for ($x = $start_hour; $x <= $end_hour; $x++)
  {
    $select_options[$x] = str_pad($x, 2, '0', STR_PAD_LEFT);
  }

  return select_tag($name, options_for_select($select_options, (int) $value), $html_options);
}

/**
 * Returns a <select> tag populated with AM and PM options for use with 12-Hour time.
 *
 * By default, the <i>$value</i> parameter is set to the correct AM/PM setting based on the current time. 
 * To override this, simply pass either AM or PM to the <i>$value</i> parameter. You can also set the 
 * <i>$value</i> parameter to null which will disable the <i>$value</i>, however this will only be 
 * useful if you pass 'include_blank' or 'include_custom' to the <i>$options</i> parameter. For 
 * convenience, Symfony also offers the select_time_tag select_datetime_tag helper functions
 * which combine other date and time helpers to easily build date and time select boxes.
 *
 * <b>Options:</b>
 * - include_blank  - Includes a blank <option> tag at the beginning of the string with an empty value.
 * - include_custom - Includes an <option> tag with a custom display title at the beginning of the string with an empty value.
 * 
 * <b>Examples:</b>
 * <code>
 *  echo select_ampm_tag('ampm');
 * </code>
 *
 * <code>
 *  echo select_ampm_tag('ampm', 'PM', array('include_blank' => true));
 * </code>
 *
 * @param string $name         field name
 * @param string $value        selected value (AM or PM)
 * @param array  $options      special options for the select tag
 * @param array  $html_options additional HTML compliant <select> tag parameters 
 * @return string <select> tag populated with AM and PM options for use with 12-Hour time.
 * @see select_time_tag, select datetime_tag
 */
function select_ampm_tag($name, $value = null, $options = array(), $html_options = array())
{
  if ($value === null)
  {
    $value = date('A');
  }

  $options = _parse_attributes($options);
  $select_options = array();
  _convert_include_custom_for_select($options, $select_options);

  $select_options['AM'] = 'AM';
  $select_options['PM'] = 'PM';

  return select_tag($name, options_for_select($select_options, $value), $html_options);
}

/**
 * Returns three <select> tags populated with hours, minutes, and optionally seconds.
 *
 * By default, the <i>$value</i> parameter is set to the current hour and minute. To override this, simply pass a valid time
 * or a correctly formatted time array (see example) to the <i>$value</i> parameter. You can also set the <i>$value</i> 
 * parameter to null which will disable the <i>$value</i>, however this will only be useful if you pass 'include_blank' or 
 * 'include_custom' to the <i>$options</i> parameter. To include seconds to the result, use set the 'include_second' option in the 
 * <i>$options</i> parameter to true. <b>Note:</b> The <i>$name</i> parameter will automatically converted to array names. 
 * For example, a <i>$name</i> of "time" becomes:
 * <samp>
 *  <select name="time[hour]">...</select>
 *  <select name="time[minute]">...</select>
 *  <select name="time[second]">...</select>
 * </samp>
 *  
 * <b>Options:</b>
 * - include_blank  - Includes a blank <option> tag at the beginning of the string with an empty value.
 * - include_custom - Includes an <option> tag with a custom display title at the beginning of the string with an empty value.
 * - include_second - If set to true, includes the "seconds" select tag as part of the result.
 * - second_step    - If set, the seconds will be incremented in blocks of X, where X = 'second_step'
 * - minute_step    - If set, the minutes will be incremented in blocks of X, where X = 'minute_step'
 * - 12hour_time    - If set to true, will return integers 1 through 12 instead of the default 0 through 23 as well as an AM/PM select box.
 * - time_seperator - Includes a string of defined text between each generated select tag
 * - ampm_seperator - Includes a string of defined text between the minute/second select box and the AM/PM select box 
 *  
 * <b>Examples:</b>
 * <code>
 *  echo select_time_tag('time');
 * </code>
 *
 * <code>
 *  echo select_time_tag('date', '09:31');
 * </code>
 *
 * <code>
 *  $time = array('hour' => '15', 'minute' => 46, 'second' => 01);
 *  echo select_time_tag('time', $time, array('include_second' => true, '12hour_time' => true));
 * </code>
 *
 * @param string $name         field name (automatically becomes an array of parts: name[hour], name[minute], year[second])
 * @param mixed  $value        accepts a valid time string or properly formatted time array
 * @param array  $options      special options for the select tag
 * @param array  $html_options additional HTML compliant <select> tag parameters
 * @return string three <select> tags populated with a hours, minutes and optionally seconds.
 * @see select datetime_tag, select_hour_tag, select_minute_tag, select_second_tag
 */
function select_time_tag($name, $value = null, $options = array(), $html_options = array())
{
  $options = _parse_attributes($options);

  $time_seperator = _get_option($options, 'time_seperator', ':');
  $ampm_seperator = _get_option($options, 'ampm_seperator', '');
  $include_second = _get_option($options, 'include_second');
  $_12hour_time   = _get_option($options, '12hour_time');

  $options['12hour_time'] = $_12hour_time; // set it back. hour tag needs it.

  if ($include_custom = _get_option($options, 'include_custom'))
  {
    $include_custom_hour = (is_array($include_custom))
        ? ((isset($include_custom['hour'])) ? array('include_custom'=>$include_custom['hour']) : array()) 
        : array('include_custom'=>$include_custom);

    $include_custom_minute = (is_array($include_custom))
        ? ((isset($include_custom['minute'])) ? array('include_custom'=>$include_custom['minute']) : array()) 
        : array('include_custom'=>$include_custom);

    $include_custom_second = (is_array($include_custom))
        ? ((isset($include_custom['second'])) ? array('include_custom'=>$include_custom['second']) : array()) 
        : array('include_custom'=>$include_custom);

    $include_custom_ampm = (is_array($include_custom))
        ? ((isset($include_custom['ampm'])) ? array('include_custom'=>$include_custom['ampm']) : array()) 
        : array('include_custom'=>$include_custom);
  }
  else
  {
    $include_custom_hour = array();
    $include_custom_minute = array();
    $include_custom_second = array();
    $include_custom_ampm = array();
  }

  $tags = array();

  $hour_name = $name.'[hour]';
  $tags[] = select_hour_tag($hour_name, _parse_value_for_date($value, 'hour', $_12hour_time ? 'h' : 'H'), $options + $include_custom_hour, $html_options);

  $minute_name = $name.'[minute]';
  $tags[] = select_minute_tag($minute_name, _parse_value_for_date($value, 'minute', 'i'), $options + $include_custom_minute, $html_options);

  if ($include_second)
  {
    $second_name = $name.'[second]';
    $tags[] = select_second_tag($second_name, _parse_value_for_date($value, 'second', 's'), $options + $include_custom_second, $html_options);
  }

  $time = implode($time_seperator, $tags);

  if ($_12hour_time)
  {
    $ampm_name = $name.'[ampm]';
    $time .=  $ampm_seperator.select_ampm_tag($ampm_name, _parse_value_for_date($value, 'ampm', 'A'), $options + $include_custom_ampm, $html_options);
  }

  return $time;
}

/**
 * Returns a variable number of <select> tags populated with date and time related select boxes.
 *
 * The select_datetime_tag is the culmination of both the select_date_tag and the select_time_tag.
 * By default, the <i>$value</i> parameter is set to the current date and time. To override this, simply pass a valid 
 * date, time, datetime string or correctly formatted array (see example) to the <i>$value</i> parameter. 
 * You can also set the <i>$value</i> parameter to null which will disable the <i>$value</i>, however this 
 * will only be useful if you pass 'include_blank' or 'include_custom' to the <i>$options</i> parameter. 
 * To include seconds to the result, use set the 'include_second' option in the <i>$options</i> parameter to true. 
 * <b>Note:</b> The <i>$name</i> parameter will automatically converted to array names. 
 * For example, a <i>$name</i> of "datetime" becomes:
 * <samp>
 *  <select name="datetime[month]">...</select>
 *  <select name="datetime[day]">...</select>
 *  <select name="datetime[year]">...</select>
 *  <select name="datetime[hour]">...</select>
 *  <select name="datetime[minute]">...</select>
 *  <select name="datetime[second]">...</select>
 * </samp>
 *  
 * <b>Options:</b>
 * - include_blank     - Includes a blank <option> tag at the beginning of the string with an empty value.
 * - include_custom    - Includes an <option> tag with a custom display title at the beginning of the string with an empty value.
 * - include_second    - If set to true, includes the "seconds" select tag as part of the result.
 * - discard_month     - If set to true, will only return select tags for day and year.
 * - discard_day       - If set to true, will only return select tags for month and year.
 * - discard_year      - If set to true, will only return select tags for month and day.
 * - use_month_numbers - If set to true, will show the month's numerical value (1 - 12) instead of the months full name.
 * - use_short_month   - If set to true, will show the month's short name (i.e. Jan, Feb, Mar) instead of its full name. 
 * - year_start        - If set, the range of years will begin at this four-digit date (i.e. 1979)
 * - year_end          - If set, the range of years will end at this four-digit date (i.e. 2025)
 * - second_step       - If set, the seconds will be incremented in blocks of X, where X = 'second_step'
 * - minute_step       - If set, the minutes will be incremented in blocks of X, where X = 'minute_step'
 * - 12hour_time       - If set to true, will return integers 1 through 12 instead of the default 0 through 23.
 * - date_seperator    - Includes a string of defined text between each generated select tag
 * - time_seperator    - Includes a string of defined text between each generated select tag
 * - ampm_seperator    - Includes a string of defined text between the minute/second select box and the AM/PM select box 
 *  
 * <b>Examples:</b>
 * <code>
 *  echo select_datetime_tag('datetime');
 * </code>
 *
 * <code>
 *  echo select_datetime_tag('datetime', '1979-10-30');
 * </code>
 *
 * <code>
 *  $datetime = array('year' => '1979', 'month' => 10, 'day' => 30, 'hour' => '15', 'minute' => 46);
 *  echo select_datetime_tag('time', $datetime, array('use_short_month' => true, '12hour_time' => true));
 * </code>
 *
 * @param string $name         field name (automatically becomes an array of date and time parts)
 * @param mixed  $value        accepts a valid time string or properly formatted time array
 * @param array  $options      special options for the select tagss
 * @param array  $html_options additional HTML compliant <select> tag parameters
 *
 * @return string a variable number of <select> tags populated with date and time related select boxes
 * @see select date_tag, select_time_tag
 */
function select_datetime_tag($name, $value = null, $options = array(), $html_options = array())
{
  $options = _parse_attributes($options);
  $datetime_seperator = _get_option($options, 'datetime_seperator', '');

  $date = select_date_tag($name, $value, $options, $html_options);
  $time = select_time_tag($name, $value, $options, $html_options);

  return $date.$datetime_seperator.$time;
}

/**
 * Returns a <select> tag, populated with a range of numbers
 *
 * By default, the select_number_tag generates a list of numbers from 1 - 10, with an incremental value of 1.  These values
 * can be easily changed by passing one or several <i>$options</i>.  Numbers can be either positive or negative, integers or decimals,
 * and can be incremented by any number, decimal or integer.  If you require the range of numbers to be listed in descending order, pass
 * the 'reverse' option to easily display the list of numbers in the opposite direction.
 * 
 * <b>Options:</b>
 * - include_blank  - Includes a blank <option> tag at the beginning of the string with an empty value.
 * - include_custom - Includes an <option> tag with a custom display title at the beginning of the string with an empty value.
 * - multiple       - If set to true, the select tag will allow multiple numbers to be selected at once.
 * - start          - The first number in the list. If not specified, the default value is 1.
 * - end            - The last number in the list. If not specified, the default value is 10.
 * - increment      - The number by which to increase each number in the list by until the number is greater than or equal to the 'end' option. 
 *                    If not specified, the default value is 1.
 * - reverse        - Reverses the order of numbers so they are display in descending order
 *
 * <b>Examples:</b>
 * <code>
 *  echo select_number_tag('rating', '', array('reverse' => true));
 * </code>
 *
 * <code>
 *  echo echo select_number_tag('tax_rate', '0.07', array('start' => '0.05', 'end' => '0.09', 'increment' => '0.01'));
 * </code>
 *
 * <code>
 *  echo select_number_tag('limit', 5, array('start' => 5, 'end' => 120, 'increment' => 15));
 * </code>
 *
 * @param string $name         field name
 * @param string $value        the selected option
 * @param array  $options      special options for the select tagss
 * @param array  $html_options additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with a range of numbers.
 * @see options_for_select, content_tag
 */
function select_number_tag($name, $value, $options = array(), $html_options = array())
{
  $increment = _get_option($options, 'increment', 1);

  $range = array();
  $max = _get_option($options, 'end', 10) + $increment;
  for ($x = _get_option($options, 'start', 1); $x < $max; $x += $increment)
  {
    $range[(string) $x] = $x;
  }

  if (_get_option($options, 'reverse'))
  {
    $range = array_reverse($range, true);
  }

  return select_tag($name, options_for_select($range, $value, $options), $html_options);
}

/**
 * Returns a <select> tag populated with all the timezones in the world.
 *
 * The select_timezone_tag builds off the traditional select_tag function, and is conveniently populated with 
 * all the timezones in the world (sorted alphabetically). Each option in the list has a unique timezone identifier 
 * for its value and the timezone's locale as its display title.  The timezone data is retrieved via the sfCultureInfo
 * class, which stores a wide variety of i18n and i10n settings for various countries and cultures throughout the world.
 * Here's an example of an <option> tag generated by the select_timezone_tag:
 *
 * <b>Options:</b>
 * - display - 
 *     identifer         - Display the PHP timezone identifier (e.g. America/Denver)
 *     timezone          - Display the full timezone name (e.g. Mountain Standard Time)
 *     timezone_abbr     - Display the timezone abbreviation (e.g. MST)
 *     timzone_dst       - Display the full timezone name with daylight savings time (e.g. Mountain Daylight Time)
 *     timezone_dst_abbr - Display the timezone abbreviation with daylight savings time (e.g. MDT)
 *     city              - Display the city/region that relates to the timezone (e.g. Denver)
 * 
 * <samp>
 *  <option value="America/Denver">America/Denver</option>
 * </samp>
 *
 * <b>Examples:</b>
 * <code>
 *  echo select_timezone_tag('timezone', 'America/Denver');
 * </code>
 *
 * @param string $name     field name 
 * @param string $selected selected field value (timezone identifier)
 * @param array  $options  additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with all the timezones in the world.
 * @see select_tag, options_for_select, sfCultureInfo
 */
function select_timezone_tag($name, $selected = null, $options = array())
{
  static $display_keys = array(
    'identifier'        => 0,
    'timezone'          => 1,
    'timezone_abbr'     => 2,
    'timezone_dst'      => 3,
    'timezone_dst_abbr' => 4,
    'city'              => 5,
  );
  $display = _get_option($options, 'display', 'identifier');
  $display_key = isset($display_keys[$display]) ? $display_keys[$display] : 0;

  $c = sfCultureInfo::getInstance(sfContext::getInstance()->getUser()->getCulture());
  $timezone_groups = $c->getTimeZones();

  $timezones = array();
  foreach ($timezone_groups as $tz_group)
  {
    $array_key = isset($tz_group[0]) ? $tz_group[0] : null;
    if (isset($tz_group[$display_key]) and !empty($tz_group[$display_key]))
    {
      $timezones[$array_key] = $tz_group[$display_key];
    }
  }

  if ($timezone_option = _get_option($options, 'timezones'))
  {
    $timezones = array_intersect_key($timezones, array_flip((array) $timezone_option));
  }

  // Remove duplicate values
  $timezones = array_unique($timezones);
  
  asort($timezones);

  $option_tags = options_for_select($timezones, $selected);

  return select_tag($name, $option_tags, $options);
}

/**
 * Converts date values (<i>$value</i>) into its correct date format (<i>$format_char</i>)
 *
 * This function is primarily used in select_date_tag, select_time_tag and select_datetime_tag.
 *
 * <b>Note:</b> If <i>$value</i> is empty, it will be populated with the current date and time.
 *
 * @param string $value       date or date part
 * @param string $key         custom key for array values
 * @param string $format_char date format
 *
 * @return string properly formatted date part value.
 * @see select_date_tag, select_time_tag, select_datetime_tag
 */
function _parse_value_for_date($value, $key, $format_char)
{
  if (is_array($value))
  {
    return (isset($value[$key])) ? $value[$key] : '';
  }
  else if (is_numeric($value))
  {
    return date($format_char, $value);
  }
  else if ($value === '' || ($key == 'ampm' && ($value == 'AM' || $value == 'PM')))
  {
    return $value;
  }
  else if (empty($value))
  {
    $value = date('Y-m-d H:i:s');
  }

  // english text presentation
  return date($format_char, strtotime($value));
}

/**
 * Retrieves the proper date format based on the specified <i>$culture</i> setting
 *
 * <b>Note:</b> If no <i>$culture</i> is defined, the user's culture setting will be used in its place.
 *
 * @param string $culture two or three character culture setting variable
 *
 * @return string formatted date/time format based on the specified date/time setting
 * @see sfUser
 */
function _get_I18n_date_locales($culture = null)
{
  if (!$culture)
  {
    $culture = sfContext::getInstance()->getUser()->getCulture();
  }

  $retval = array('culture'=>$culture);

  $dateFormatInfo = sfDateTimeFormatInfo::getInstance($culture);
  $date_format = strtolower($dateFormatInfo->getShortDatePattern());

  $retval['dateFormatInfo'] = $dateFormatInfo;

  $match_pattern = "/([dmy]+)(.*?)([dmy]+)(.*?)([dmy]+)/";
  if (!preg_match($match_pattern, $date_format, $match_arr))
  {
    // if matching fails use en shortdate
    preg_match($match_pattern, 'm/d/yy', $match_arr);
  }

  $retval['date_seperator'] = $match_arr[2];

  // unset all but [dmy]+
  unset($match_arr[0], $match_arr[2], $match_arr[4]);

  $retval['date_order'] = array();
  foreach ($match_arr as $v)
  {
    // 'm/d/yy' => $retval[date_order] = array ('m', 'd', 'y');
    $retval['date_order'][] = $v[0];
  }

  return $retval;
}
