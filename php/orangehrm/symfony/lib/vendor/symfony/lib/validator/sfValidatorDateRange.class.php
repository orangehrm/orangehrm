<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorDateRange validates a range of date. It also converts the input values to valid dates.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorDateRange.class.php 11671 2008-09-19 14:07:21Z fabien $
 */
class sfValidatorDateRange extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * from_date:   The from date validator (required)
   *  * to_date:     The to date validator (required)
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('invalid', 'The begin date must be before the end date.');

    $this->addRequiredOption('from_date');
    $this->addRequiredOption('to_date');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $value['from'] = $this->getOption('from_date')->clean(isset($value['from']) ? $value['from'] : null);
    $value['to']   = $this->getOption('to_date')->clean(isset($value['to']) ? $value['to'] : null);

    if ($value['from'] && $value['to'])
    {
      $v = new sfValidatorSchemaCompare('from', sfValidatorSchemaCompare::LESS_THAN_EQUAL, 'to', array('throw_global_error' => true), array('invalid' => $this->getMessage('invalid')));
      $v->clean($value);
    }

    return $value;
  }
}
