<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfCompareValidator compares two different request parameters.
 *
 * <b>Required parameters:</b>
 *
 * # <b>check</b>         - [none]          - A request parameter name
 *
 * <b>Optional parameters:</b>
 *
 * # <b>operator</b>      - [==]            - Comparison operator (available operators: >, >=, <, <=, !==, ==)
 * # <b>compare_error</b> - [Invalid input] - An error message to use when the two given parameters do not match
 *
 * <b>Example:</b>
 *
 * passwordValidator:
 *   class:            sfCompareValidator
 *   param:
 *     check:          password2
 *     operator:       ==
 *     compare_error:  The passwords you entered do not match.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCompareValidator.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
class sfCompareValidator extends sfValidator
{
  /**
   * Executes this validator.
   *
   * @param mixed A file or parameter value/array
   * @param error An error message reference
   *
   * @return bool true, if this validator executes successfully, otherwise false
   */
  public function execute(&$value, &$error)
  {
    $check_param    = $this->getParameter('check');
    $check_operator = $this->getParameter('operator');
    $check_value    = $this->context->getRequest()->getParameter($check_param);

    switch ($check_operator)
    {
      case '>':
        $valid = $value > $check_value;
        break;
      case '>=':
        $valid = $value >= $check_value;
        break;
      case '<':
        $valid = $value < $check_value;
        break;
      case '<=':
        $valid = $value <= $check_value;
        break;
      case '!=':
        $valid = $value != $check_value;
        break;
      case '==':
      default:
        $valid = $value == $check_value;
        break;
    }

    if (!$valid)
    {
      $error = $this->getParameter('compare_error');

      return false;
    }

    return true;
  }

  /**
   * Initializes this validator.
   *
   * @param sfContext The current application context
   * @param array     An associative array of initialization parameters
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);

    // set defaults
    $this->setParameter('operator', '==');
    $this->setParameter('compare_error', 'Invalid input');

    $this->getParameterHolder()->add($parameters);

    if (!$this->hasParameter('check'))
    {
      throw new sfValidatorException('You must specify a "check" parameter for your sfCompareValidator.');
    }

    if (!in_array($this->getParameter('operator'), array('==', '!=', '>', '<', '<=', '>=')))
    {
      throw new sfValidatorException(sprintf('The operator "%s" is not available for your sfCompareValidator.', $this->getParameter('operator')));
    }

    return true;
  }
}
