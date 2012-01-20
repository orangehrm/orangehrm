<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorDecorator decorates another validator.
 *
 * This validator has exactly the same behavior as the Decorator validator.
 *
 * The options and messages are proxied from the decorated validator.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorDecorator.class.php 7902 2008-03-15 13:17:33Z fabien $
 */
abstract class sfValidatorDecorator extends sfValidatorBase
{
  protected
    $validator = null;

  /**
   * @see sfValidatorBase
   */
  public function __construct($options = array(), $messages = array())
  {
    $this->validator = $this->getValidator();

    if (!$this->validator instanceof sfValidatorBase)
    {
      throw new RuntimeException('The getValidator() method must return a sfValidatorBase instance.');
    }

    foreach ($options as $key => $value)
    {
      $this->validator->setOption($key, $value);
    }

    foreach ($messages as $key => $value)
    {
      $this->validator->setMessage($key, $value);
    }
  }

  /**
   * Returns the decorated validator.
   *
   * Every subclass must implement this method.
   *
   * @return sfValidatorBase A sfValidatorBase instance
   */
  abstract protected function getValidator();

  /**
   * @see sfValidatorBase
   */
  public function clean($value)
  {
    return $this->doClean($value);
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    return $this->validator->clean($value);
  }

  /**
   * @see sfValidatorBase
   */
  public function getMessage($name)
  {
    return $this->validator->getMessage($name);
  }

  /**
   * @see sfValidatorBase
   */
  public function setMessage($name, $value)
  {
    $this->validator->setMessage($name, $value);
  }

  /**
   * @see sfValidatorBase
   */
  public function getMessages()
  {
    return $this->validator->getMessages();
  }

  /**
   * @see sfValidatorBase
   */
  public function setMessages($values)
  {
    return $this->validator->setMessages($values);
  }

  /**
   * @see sfValidatorBase
   */
  public function getOption($name)
  {
    return $this->validator->getOption($name);
  }

  /**
   * @see sfValidatorBase
   */
  public function setOption($name, $value)
  {
    $this->validator->setOption($name, $value);
  }

  /**
   * @see sfValidatorBase
   */
  public function hasOption($name)
  {
    return $this->validator->hasOption($name);
  }

  /**
   * @see sfValidatorBase
   */
  public function getOptions()
  {
    return $this->validator->getOptions();
  }

  /**
   * @see sfValidatorBase
   */
  public function setOptions($values)
  {
    $this->validator->setOptions($values);
  }

  /**
   * @see sfValidatorBase
   */
  public function asString($indent = 0)
  {
    return $this->validator->asString($indent);
  }

  /**
   * @see sfValidatorBase
   */
  public function getDefaultOptions()
  {
    return $this->validator->getDefaultOptions();
  }

  /**
   * @see sfValidatorBase
   */
  public function getDefaultMessages()
  {
    return $this->validator->getDefaultMessages();
  }
}
