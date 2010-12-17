<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorTime validates a time. It also converts the input value to a valid time.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Fabian Lange <fabian.lange@symfony-project.com>
 * @version    SVN: $Id: sfValidatorTime.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfValidatorTime extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * time_format:       A regular expression that dates must match
   *  * time_output:       The format to use when returning a date with time (default to H:i:s)
   *  * time_format_error: The date format to use when displaying an error for a bad_format error
   *
   * Available error codes:
   *
   *  * bad_format
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('bad_format', '"%value%" does not match the time format (%time_format%).');

    $this->addOption('time_format', null);
    $this->addOption('time_output', 'H:i:s');
    $this->addOption('time_format_error');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    if (is_array($value))
    {
      $clean = $this->convertTimeArrayToTimestamp($value);
    }
    else if ($regex = $this->getOption('time_format'))
    {
      if (!preg_match($regex, $value, $match))
      {
        throw new sfValidatorError($this, 'bad_format', array('value' => $value, 'time_format' => $this->getOption('time_format_error') ? $this->getOption('time_format_error') : $this->getOption('time_format')));
      }

      $clean = $this->convertTimeArrayToTimestamp($match);
    }
    else if (!ctype_digit($value))
    {
      $clean = strtotime($value);
      if (false === $clean)
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }
    else
    {
      $clean = (integer) $value;
    }

    return $clean === $this->getEmptyValue() ? $clean : date($this->getOption('time_output'), $clean);
  }

  /**
   * Converts an array representing a time to a timestamp.
   *
   * The array can contains the following keys: hour, minute, second
   *
   * @param  array $value  An array of date elements
   *
   * @return int A timestamp
   */
  protected function convertTimeArrayToTimestamp($value)
  {
    // all elements must be empty or a number
    foreach (array('hour', 'minute', 'second') as $key)
    {
      if (isset($value[$key]) && !preg_match('#^\d+$#', $value[$key]) && !empty($value[$key]))
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }

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

    $clean = mktime(
      isset($value['hour']) ? intval($value['hour']) : 0,
      isset($value['minute']) ? intval($value['minute']) : 0,
      isset($value['second']) ? intval($value['second']) : 0
    );

    if (false === $clean)
    {
      throw new sfValidatorError($this, 'invalid', array('value' => var_export($value, true)));
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
      // array is not empty when a value is found
      foreach($value as $key => $val)
      {
        // int and string '0' are 'empty' values that are explicitly accepted
        if ($val === 0 || $val === '0' || !empty($val)) return false;
      }
      return true;
    }

    return parent::isEmpty($value);
  }
}
