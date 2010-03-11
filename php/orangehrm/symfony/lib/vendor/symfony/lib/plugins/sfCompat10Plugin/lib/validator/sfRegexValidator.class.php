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
 * sfRegexValidator allows you to match a value against a regular expression
 * pattern.
 *
 * <b>Required parameters:</b>
 *
 * # <b>pattern</b> - [none] - A PCRE, preg_match() style regular expression
 *                             pattern.
 *
 * <b>Optional parameters:</b>
 *
 * # <b>match</b>       - [true]          - Indicates that the pattern must be
 *                                          matched or must not match.
 * # <b>match_error</b> - [Invalid input] - An error message to use when the
 *                                          input does not meet the regex
 *                                          specifications.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfRegexValidator.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
class sfRegexValidator extends sfValidator
{
  /**
   * Executes this validator.
   *
   * @param string A parameter value
   * @param string An error message reference
   *
   * @return bool true, if this validator executes successfully, otherwise false
   */
  public function execute(&$value, &$error)
  {
    $match   = $this->getParameterHolder()->get('match');
    $pattern = $this->getParameterHolder()->get('pattern');

    if (($match && !preg_match($pattern, $value)) ||
        (!$match && preg_match($pattern, $value)))
    {
      $error = $this->getParameterHolder()->get('match_error');

      return false;
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
    $this->getParameterHolder()->set('match',       true);
    $this->getParameterHolder()->set('match_error', 'Invalid input');
    $this->getParameterHolder()->set('pattern',     null);

    $this->getParameterHolder()->add($parameters);

    // check parameters
    if ($this->getParameterHolder()->get('pattern') == null)
    {
      // no pattern specified
      throw new sfValidatorException('Please specify a PCRE regular expression pattern for your registered RegexValidator.');
    }

    return true;
  }
}
