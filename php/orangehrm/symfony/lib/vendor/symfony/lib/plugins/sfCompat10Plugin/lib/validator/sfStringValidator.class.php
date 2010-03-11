<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr <sean@code-box.org>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfStringValidator allows you to apply string-related constraints to a
 * parameter.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>insensitive</b>  - [false]              - Whether or not the value check
 *                                                against the array of values is
 *                                                case-insensitive. <b>Note:</b>
 *                                                When using this option, values
 *                                                in the values array must be
 *                                                entered in lower-case.
 * # <b>max</b>          - [none]               - Maximum string length.
 * # <b>max_error</b>    - [Input is too long]  - An error message to use when
 *                                                input is too long.
 * # <b>min</b>          - [none]               - Minimum string length.
 * # <b>min_error</b>    - [Input is too short] - An error message to use when
 *                                                input is too short.
 * # <b>values</b>       - [none]               - An array of values the input
 *                                                is allowed to match.
 * # <b>values_error</b> - [Invalid selection]  - An error message to use when
 *                                                input does not match a value
 *                                                listed in the values array.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfStringValidator.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
class sfStringValidator extends sfValidator
{
  /**
   * Executes this validator.
   *
   * @param mixed A parameter value
   * @param error An error message reference
   *
   * @return bool true, if this validator executes successfully, otherwise false
   */
  public function execute(&$value, &$error)
  {
    $decodedValue = sfToolkit::isUTF8($value) && function_exists('utf8_decode') ? utf8_decode($value) : $value;

    $min = $this->getParameterHolder()->get('min');
    if ($min !== null && strlen(trim($decodedValue)) < $min)
    {
      // too short
      $error = $this->getParameterHolder()->get('min_error');

      return false;
    }

    $max = $this->getParameterHolder()->get('max');
    if ($max !== null && strlen(trim($decodedValue)) > $max)
    {
      // too long
      $error = $this->getParameterHolder()->get('max_error');

      return false;
    }

    $values = $this->getParameterHolder()->get('values');
    if ($values !== null)
    {
      if ($this->getParameterHolder()->get('insensitive'))
      {
        $value = strtolower($value);
        $found = false;
        foreach ($values as $avalue)
        {
          if ($value == strtolower($avalue))
          {
            $found = true;
            break;
          }
        }
        if (!$found)
        {
          // can't find a match
          $error = $this->getParameterHolder()->get('values_error');

          return false;
        }
      }
      else
      {
        if (!in_array($value, (array) $values))
        {
          // can't find a match
          $error = $this->getParameterHolder()->get('values_error');

          return false;
        }
      }
    }

    return true;
  }

  /**
   * Initializes this validator.
   *
   * @param sfContext The current application context
   * @param array   An associative array of initialization parameters
   *
   * @return bool true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);

    // set defaults
    $this->getParameterHolder()->set('insensitive',  false);
    $this->getParameterHolder()->set('max',          null);
    $this->getParameterHolder()->set('max_error',    'Input is too long');
    $this->getParameterHolder()->set('min',          null);
    $this->getParameterHolder()->set('min_error',    'Input is too short');
    $this->getParameterHolder()->set('values',       null);
    $this->getParameterHolder()->set('values_error', 'Invalid selection');

    $this->getParameterHolder()->add($parameters);

    return true;
  }
}
