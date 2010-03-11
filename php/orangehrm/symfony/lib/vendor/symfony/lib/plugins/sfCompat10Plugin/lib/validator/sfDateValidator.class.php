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
 * sfDateValidator verifies a parameter is of a date format.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Nick Lane <nick.lane@internode.on.net>
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfDateValidator.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
class sfDateValidator extends sfValidator
{
  /**
   * Execute this validator.
   *
   * @param mixed A file or parameter value/array
   * @param error An error message reference
   *
   * @return bool true, if this validator executes successfully, otherwise false
   */
  public function execute(&$value, &$error)
  {
    $culture = $this->context->getUser()->getCulture();

    // Validate the given date
    $value1 = $this->getValidDate($value, $culture);
    if (!$value1)
    {
      $error = $this->getParameter('date_error');

      return false;
    }

    // Is there a compare to do?
    $compareDateParam = $this->getParameter('compare');
    $compareDate = $this->context->getRequest()->getParameter($compareDateParam);

    // If the compare date is given
    if ($compareDate)
    {
      $operator = trim($this->getParameter('operator', '=='), '\'" ');
      $value2 = $this->getValidDate($compareDate, $culture);

      // If the check date is valid, compare it. Otherwise ignore the comparison
      if ($value2)
      {
        $valid = false;
        switch ($operator)
        {
          case '>':
            $valid = $value1 >  $value2;
            break;
          case '>=':
            $valid = $value1 >= $value2;
            break;
          case '==':
            $valid = $value1 == $value2;
            break;          
          case '<=':
            $valid = $value1 <= $value2;
            break;
          case '<':
            $valid = $value1 <  $value2;
            break;

          default:
            throw new sfValidatorException(sprintf('Invalid date comparison operator "%s".', $operator));
        }

        if (!$valid)
        {
          $error = $this->getParameter('compare_error');

          return false;
        }
      }
    }

    return true;
  }

  /**
   * Converts the given date into a Unix timestamp.
   * 
   * Returns null if the date is invalid
   * 
   * @param $value    Date to convert
   * @param $culture  Language culture to use
   */
  protected function getValidDate($value, $culture)
  {
    // Use the language culture date format
    $result = $this->context->getI18N()->getDateForCulture($value, $culture);
    list($d, $m, $y) = $result;

    // Make sure the date is a valid gregorian calendar date also
    if ($result === null || !checkdate($m, $d, $y))
    {
      return null;
    }

    return strtotime("$y-$m-$d 00:00");
  }

  /**
   * Initializes the validator.
   *
   * @param sfContext The current application context
   * @param array   An associative array of initialization parameters
   *
   * @return bool true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $parameters = null)
  {
    // Initialize parent
    parent::initialize($context, $parameters);

    // Set defaults
    $this->getParameterHolder()->set('date_error', 'Invalid date');
    $this->getParameterHolder()->set('compare_error', 'Compare failed');

    $this->getParameterHolder()->add($parameters);

    return true;
  }
}
