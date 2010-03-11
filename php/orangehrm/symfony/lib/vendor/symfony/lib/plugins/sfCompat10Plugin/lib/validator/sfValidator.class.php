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
 * sfValidatorBase allows you to apply constraints to user entered parameters.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <sean@code-box.org>
 * @version    SVN: $Id: sfValidator.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
abstract class sfValidator
{
  protected
    $parameterHolder = null,
    $context         = null;

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct($context, $parameters = array())
  {
    $this->initialize($context, $parameters);
  }

  /**
   * Initializes this validator.
   *
   * @param sfContext The current application context
   * @param array   An associative array of initialization parameters
   *
   * @return bool true, if initialization completes successfully, otherwise false
   */
  public function initialize($context, $parameters = array())
  {
    $this->context = $context;

    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);

    return true;
  }

  /**
   * Executes this validator.
   *
   * @param mixed A file or parameter value/array
   * @param string An error message reference
   *
   * @return bool true, if this validator executes successfully, otherwise false
   */
  abstract function execute(&$value, &$error);

  /**
   * Retrieves the current application context.
   *
   * @return sfContext The current sfContext instance
   */
  public final function getContext()
  {
    return $this->context;
  }

  /**
   * Retrieves the parameters from the validator.
   *
   * @return sfParameterHolder List of parameters
   */
  public function getParameterHolder()
  {
    return $this->parameterHolder;
  }

  /**
   * Retrieves a parameter from the validator.
   *
   * @param string Parameter name
   * @param mixed A default parameter value
   *
   * @return mixed A parameter value
   */
  public function getParameter($name, $default = null)
  {
    return $this->parameterHolder->get($name, $default);
  }

  /**
   * Indicates whether or not a parameter exist for the validator.
   *
   * @param string A parameter name
   *
   * @return boolean true, if parameter exists, otherwise false
   */
  public function hasParameter($name)
  {
    return $this->parameterHolder->has($name);
  }

  /**
   * Sets a parameter for the validator.
   *
   * @param string A parameter name
   * @param mixed A parameter value
   */
  public function setParameter($name, $value)
  {
    $this->parameterHolder->set($name, $value);
  }
}
