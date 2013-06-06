<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorChoice validates than the value is one of the expected values.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorChoice.class.php 22264 2009-09-23 05:54:32Z fabien $
 */
class sfValidatorChoice extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * choices:  An array of expected values (required)
   *  * multiple: true if the select tag must allow multiple selections
   *  * min:      The minimum number of values that need to be selected (this option is only active if multiple is true)
   *  * max:      The maximum number of values that need to be selected (this option is only active if multiple is true)
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('choices');
    $this->addOption('multiple', false);
    $this->addOption('min');
    $this->addOption('max');

    $this->addMessage('min', 'At least %min% values must be selected (%count% values selected).');
    $this->addMessage('max', 'At most %max% values must be selected (%count% values selected).');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $choices = $this->getChoices();

    if ($this->getOption('multiple'))
    {
      $value = $this->cleanMultiple($value, $choices);
    }
    else
    {
      if (!self::inChoices($value, $choices))
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }

    return $value;
  }

  public function getChoices()
  {
    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    return $choices;
  }

  /**
   * Cleans a value when multiple is true.
   *
   * @param  mixed $value The submitted value
   *
   * @return array The cleaned value
   */
  protected function cleanMultiple($value, $choices)
  {
    if (!is_array($value))
    {
      $value = array($value);
    }

    foreach ($value as $v)
    {
      if (!self::inChoices($v, $choices))
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $v));
      }
    }

    $count = count($value);

    if ($this->hasOption('min') && $count < $this->getOption('min'))
    {
      throw new sfValidatorError($this, 'min', array('count' => $count, 'min' => $this->getOption('min')));
    }

    if ($this->hasOption('max') && $count > $this->getOption('max'))
    {
      throw new sfValidatorError($this, 'max', array('count' => $count, 'max' => $this->getOption('max')));
    }

    return $value;
  }

  /**
   * Checks if a value is part of given choices (see bug #4212)
   *
   * @param  mixed $value   The value to check
   * @param  array $choices The array of available choices
   *
   * @return Boolean
   */
  static protected function inChoices($value, array $choices = array())
  {
    foreach ($choices as $choice)
    {
      if ((string) $choice == (string) $value)
      {
        return true;
      }
    }

    return false;
  }
}
