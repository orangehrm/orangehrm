<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfCallable represents a PHP callable.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCallable.class.php 21875 2009-09-11 05:54:39Z fabien $
 */
class sfCallable
{
  protected
    $callable = null;

  /**
   * Constructor.
   *
   * @param mixed $callable  A valid PHP callable (must be valid when calling the call() method)
   */
  public function __construct($callable)
  {
    $this->callable = $callable;
  }

  /**
   * Returns the current callable.
   *
   * @return mixed The current callable
   */
  public function getCallable()
  {
    return $this->callable;
  }

  /**
   * Calls the current callable with the given arguments.
   *
   * The callable is called with the arguments given to this method.
   *
   * This method throws an exception if the callable is not valid.
   * This check is not done during the object construction to allow
   * you to load the callable as late as possible.
   */
  public function call()
  {
    if (!is_callable($this->callable))
    {
      throw new sfException(sprintf('"%s" is not a valid callable.', is_array($this->callable) ? sprintf('%s:%s', is_object($this->callable[0]) ? get_class($this->callable[0]) : $this->callable[0], $this->callable[1]) : (is_object($this->callable) ? sprintf('Object(%s)', get_class($this->callable)) : var_export($this->callable, true))));
    }

    $arguments = func_get_args();

    return call_user_func_array($this->callable, $arguments);
  }
}
