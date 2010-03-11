<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This schema validator validates a time interval between two dates, provided
 * by two fields in the schema.
 *
 * Several options are available:
 *
 *  - date_start_field:      The start date field name
 *  - date_end_field:        The end date field name
 *  - min_duration:          The minimum duration of interval between the two dates, in seconds (optional)
 *  - max_duration:          The maximum duration of interval between the two dates, in seconds (optional)
 *  - disallow_future_dates: Disallows dates in the future (defaults to false)
 *  - disallow_past_dates:   Disallows dates in the past (defaults to false)
 *  - throw_global_error:    Throws a global error (defaults to false)
 *
 * These error codes are available:
 *
 *  - future_date:           A date is in the future
 *  - past_date:             A date is in the past
 *  - start_not_prior:       The start date is not prior to end date
 *  - too_short:             The duration is too short
 *  - too_long:              The maximum duration has been exceeded
 *
 * Error messages available arguments:
 *
 *  - date_start:            The submitted start date
 *  - date_end:              The submitted end date
 *
 * @package    symfony
 * @subpackage validator
 * @author     Nicolas Perriault <nicolas.perriault@symfony-project.com>
 */
class sfValidatorSchemaTimeInterval extends sfValidatorSchema
{
  protected
    $dateStart = null,
    $dateEnd   = null;

  /**
   * Public constructor
   *
   * @param  string  $dateStartField  The name of the start date field
   * @param  string  $dateEndField    The name of the end date field
   * @param  array   $options         Options array
   * @param  array   $messages        Error messages array
   */
  public function __construct($dateStartField, $dateEndField, $options = array(), $messages = array())
  {
    // Validator options
    $this->addOption('date_start_field', $dateStartField);
    $this->addOption('date_end_field', $dateEndField);
    $this->addOption('min_duration', null);
    $this->addOption('max_duration', null);
    $this->addOption('disallow_future_dates', false);
    $this->addOption('disallow_past_dates', false);
    $this->addOption('throw_global_error', false);

    // Validation error messages
    $this->addMessage('future_date', 'The date cannot be in the future');
    $this->addMessage('past_date', 'The date cannot be in the past');
    $this->addMessage('too_short', 'The time interval between the two dates is too shortl');
    $this->addMessage('too_long', 'The time interval between the two dates is too long');
    $this->addMessage('start_not_prior', 'The start date must be prior to the end date');

    // Parent constructor call
    parent::__construct(null, $options, $messages);
  }

  /**
   * Cleans the schema values
   *
   * @see sfValidatorSchema
   *
   * @param  array  $values  Values to validate
   * @return array
   * @throws InvalidArgumentException if $values is not an array
   * @throws sfValidatorError
   * @throws sfValidatorErrorSchema
   */
  protected function doClean($values)
  {
    if (is_null($values))
    {
      $values = array();
    }

    if (!is_array($values))
    {
      throw new InvalidArgumentException('You must pass an array parameter to the clean() method');
    }

    $this->dateStart = isset($values[$this->getOption('date_start_field')]) ? strtotime($values[$this->getOption('date_start_field')]) : null;
    $this->dateEnd   = isset($values[$this->getOption('date_end_field')]) ? strtotime($values[$this->getOption('date_end_field')]) : null;

    $errorCode = null;
    $errorField = null;

    if ($this->getOption('disallow_future_dates'))
    {
      if (!is_null($this->dateStart) && $this->dateStart > time())
      {
        $this->throwError('future_date', $this->getOption('date_start_field'));
      }
      else if (!is_null($this->dateEnd) && $this->dateEnd > time())
      {
        $this->throwError('future_date', $this->getOption('date_end_field'));
      }
    }

    if ($this->getOption('disallow_past_dates'))
    {
      if (!is_null($this->dateStart) && $this->dateStart < time())
      {
        $this->throwError('past_date', $this->getOption('date_start_field'));
      }
      else if (!is_null($this->dateEnd) && $this->dateEnd < time())
      {
        $this->throwError('past_date', $this->getOption('date_end_field'));
      }
    }

    // At this point, if either the start or end date is not set we can return values
    if (is_null($this->dateStart) or is_null($this->dateEnd))
    {
      return $values;
    }

    // Duration
    $duration = $this->dateEnd - $this->dateStart;

    if ($this->hasOption('min_duration') && $duration < $this->getOption('min_duration'))
    {
      $this->throwError('too_short', $this->getOption('date_end_field'));
    }

    if ($this->hasOption('max_duration') && $duration > $this->getOption('max_duration'))
    {
      $this->throwError('too_long', $this->getOption('date_end_field'));
    }

    if ($this->dateStart > $this->dateEnd)
    {
      $this->throwError('start_not_prior', $this->getOption('date_start_field'));
    }

    return $values;
  }

  /**
   * Throws a validation error
   *
   * @param  string  $code   The error code
   * @param  string  $field  The field related to the error
   * @throws sfValidatorError
   * @throws sfValidatorErrorSchema
   */
  protected function throwError($code, $field)
  {
    $error = new sfValidatorError($this, $code, array(
      'date_start' => date('Y-m-d', $this->dateStart),
      'date_end'   => date('Y-m-d', $this->dateEnd),
    ));

    if ($this->getOption('throw_global_error'))
    {
      throw $error;
    }

    throw new sfValidatorErrorSchema($this, array($field => $error));
  }
}