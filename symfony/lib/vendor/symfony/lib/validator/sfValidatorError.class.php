<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorError represents a validation error.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorError.class.php 15393 2009-02-10 12:58:49Z fabien $
 */
class sfValidatorError extends Exception implements Serializable
{
  protected
    $validator = null,
    $arguments = array();

  /**
   * Constructor.
   *
   * @param sfValidatorBase $validator  An sfValidatorBase instance
   * @param string          $code       The error code
   * @param array           $arguments  An array of named arguments needed to render the error message
   */
  public function __construct(sfValidatorBase $validator, $code, $arguments = array())
  {
    $this->validator = $validator;
    $this->arguments = $arguments;

    // override default exception message and code
    $this->code = $code;

    if (!$messageFormat = $this->getMessageFormat())
    {
      $messageFormat = $code;
    }
    $this->message = strtr($messageFormat, $this->getArguments());
  }

  /**
   * Returns the string representation of the error.
   *
   * @return string The error message
   */
  public function __toString()
  {
    return $this->getMessage();
  }

  /**
   * Returns the input value that triggered this error.
   *
   * @return mixed The input value
   */
  public function getValue()
  {
    return isset($this->arguments['value']) ? $this->arguments['value'] : null;
  }

  /**
   * Returns the validator that triggered this error.
   *
   * @return sfValidatorBase A sfValidatorBase instance
   */
  public function getValidator()
  {
    return $this->validator;
  }

  /**
   * Returns the arguments needed to format the message.
   *
   * @param bool $raw  false to use it as arguments for the message format, true otherwise (default to false)
   *
   * @see getMessageFormat()
   */
  public function getArguments($raw = false)
  {
    if ($raw)
    {
      return $this->arguments;
    }

    $arguments = array();
    foreach ($this->arguments as $key => $value)
    {
      if (is_array($value))
      {
        continue;
      }

      $arguments["%$key%"] = htmlspecialchars($value, ENT_QUOTES, sfValidatorBase::getCharset());
    }

    return $arguments;
  }

  /**
   * Returns the message format for this error.
   *
   * This is the string you need to use if you need to internationalize
   * error messages:
   *
   * $i18n->__($error->getMessageFormat(), $error->getArguments());
   * 
   * If no message format has been set in the validator, the exception standard
   * message is returned.
   *
   * @return string The message format
   */
  public function getMessageFormat()
  {
    $messageFormat = $this->validator->getMessage($this->code);
    if (!$messageFormat)
    {
      $messageFormat = $this->getMessage();
    }

    return $messageFormat;
  }

  /**
   * Serializes the current instance.
   *
   * We must implement the Serializable interface to overcome a problem with PDO
   * used as a session handler.
   *
   * The default serialization process serializes the exception trace, and because
   * the trace can contain a PDO instance which is not serializable, serializing won't
   * work when using PDO.
   *
   * @return string The instance as a serialized string
   */
  public function serialize()
  {
    return serialize(array($this->validator, $this->arguments, $this->code, $this->message));
  }

  /**
   * Unserializes a sfValidatorError instance.
   *
   * @param string $serialized  A serialized sfValidatorError instance
   *
   */
  public function unserialize($serialized)
  {
    list($this->validator, $this->arguments, $this->code, $this->message) = unserialize($serialized);
  }
}
