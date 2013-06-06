<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorDate validates a date. It also converts the input value to a valid date.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorDate.class.php 28959 2010-04-01 14:10:24Z fabien $
 */
class sfValidatorDate extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * date_format:             A regular expression that dates must match
   *                             Note that the regular expression must use named subpatterns like (?P<year>)
   *                             Working example: ~(?P<day>\d{2})/(?P<month>\d{2})/(?P<year>\d{4})~
   *  * with_time:               true if the validator must return a time, false otherwise
   *  * date_output:             The format to use when returning a date (default to Y-m-d)
   *  * datetime_output:         The format to use when returning a date with time (default to Y-m-d H:i:s)
   *  * date_format_error:       The date format to use when displaying an error for a bad_format error (use date_format if not provided)
   *  * max:                     The maximum date allowed (as a timestamp or accecpted date() format)
   *  * min:                     The minimum date allowed (as a timestamp or accecpted date() format)
   *  * date_format_range_error: The date format to use when displaying an error for min/max (default to d/m/Y H:i:s)
   *
   * Available error codes:
   *
   *  * bad_format
   *  * min
   *  * max
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('bad_format', '"%value%" does not match the date format (%date_format%).');
    $this->addMessage('max', 'The date must be before %max%.');
    $this->addMessage('min', 'The date must be after %min%.');

    $this->addOption('date_format', null);
    $this->addOption('with_time', false);
    $this->addOption('date_output', 'Y-m-d');
    $this->addOption('datetime_output', 'Y-m-d H:i:s');
    $this->addOption('date_format_error');
    $this->addOption('min', null);
    $this->addOption('max', null);
    $this->addOption('date_format_range_error', 'd/m/Y H:i:s');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    // check date format
    if (is_string($value) && $regex = $this->getOption('date_format'))
    {
      if (!preg_match($regex, $value, $match))
      {
        throw new sfValidatorError($this, 'bad_format', array('value' => $value, 'date_format' => $this->getOption('date_format_error') ? $this->getOption('date_format_error') : $this->getOption('date_format')));
      }

      $value = $match;
    }

    // convert array to date string
    if (is_array($value))
    {
      $value = $this->convertDateArrayToString($value);
    }

    // convert timestamp to date number format
    if (is_numeric($value))
    {
      $cleanTime = (integer) $value;
      $clean     = date('YmdHis', $cleanTime);
    }
    // convert string to date number format
    else
    {
      try
      {
        $date = new DateTime($value);
        $date->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $clean = $date->format('YmdHis');
      }
      catch (Exception $e)
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }

    // check max
    if ($max = $this->getOption('max'))
    {
      // convert timestamp to date number format
      if (is_numeric($max))
      {
        $maxError = date($this->getOption('date_format_range_error'), $max);
        $max      = date('YmdHis', $max);
      }
      // convert string to date number
      else
      {
        $dateMax  = new DateTime($max);
        $max      = $dateMax->format('YmdHis');
        $maxError = $dateMax->format($this->getOption('date_format_range_error'));
      }

      if ($clean > $max)
      {
        throw new sfValidatorError($this, 'max', array('value' => $value, 'max' => $maxError));
      }
    }

    // check min
    if ($min = $this->getOption('min'))
    {
      // convert timestamp to date number
      if (is_numeric($min))
      {
        $minError = date($this->getOption('date_format_range_error'), $min);
        $min      = date('YmdHis', $min);
      }
      // convert string to date number
      else
      {
        $dateMin  = new DateTime($min);
        $min      = $dateMin->format('YmdHis');
        $minError = $dateMin->format($this->getOption('date_format_range_error'));
      }

      if ($clean < $min)
      {
        throw new sfValidatorError($this, 'min', array('value' => $value, 'min' => $minError));
      }
    }

    if ($clean === $this->getEmptyValue())
    {
      return $cleanTime;
    }

    $format = $this->getOption('with_time') ? $this->getOption('datetime_output') : $this->getOption('date_output');

    return isset($date) ? $date->format($format) : date($format, $cleanTime);
  }

  /**
   * Converts an array representing a date to a timestamp.
   *
   * The array can contains the following keys: year, month, day, hour, minute, second
   *
   * @param  array $value  An array of date elements
   *
   * @return int A timestamp
   */
  protected function convertDateArrayToString($value)
  {
    // all elements must be empty or a number
    foreach (array('year', 'month', 'day', 'hour', 'minute', 'second') as $key)
    {
      if (isset($value[$key]) && !preg_match('#^\d+$#', $value[$key]) && !empty($value[$key]))
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }

    // if one date value is empty, all others must be empty too
    $empties =
      (!isset($value['year']) || !$value['year'] ? 1 : 0) +
      (!isset($value['month']) || !$value['month'] ? 1 : 0) +
      (!isset($value['day']) || !$value['day'] ? 1 : 0)
    ;
    if ($empties > 0 && $empties < 3)
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }
    else if (3 == $empties)
    {
      return $this->getEmptyValue();
    }

    if (!checkdate(intval($value['month']), intval($value['day']), intval($value['year'])))
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }

    if ($this->getOption('with_time'))
    {
      // if second is set, minute and hour must be set
      // if minute is set, hour must be set
      if (
        $this->isValueSet($value, 'second') && (!$this->isValueSet($value, 'minute') || !$this->isValueSet($value, 'hour'))
        ||
        $this->isValueSet($value, 'minute') && !$this->isValueSet($value, 'hour')
      )
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }

      $clean = sprintf(
        "%04d-%02d-%02d %02d:%02d:%02d",
        intval($value['year']),
        intval($value['month']),
        intval($value['day']),
        isset($value['hour']) ? intval($value['hour']) : 0,
        isset($value['minute']) ? intval($value['minute']) : 0,
        isset($value['second']) ? intval($value['second']) : 0
      );
    }
    else
    {
      $clean = sprintf(
        "%04d-%02d-%02d %02d:%02d:%02d",
        intval($value['year']),
        intval($value['month']),
        intval($value['day']),
        0,
        0,
        0
      );
    }

    return $clean;
  }

  protected function isValueSet($values, $key)
  {
    return isset($values[$key]) && !in_array($values[$key], array(null, ''), true);
  }

  /**
   * @see sfValidatorBase
   */
  protected function isEmpty($value)
  {
    if (is_array($value))
    {
      $filtered = array_filter($value);

      return empty($filtered);
    }

    return parent::isEmpty($value);
  }
}
