<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorBase is the base class for all validators.
 *
 * It also implements the required option for all validators.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorBase.class.php 23922 2009-11-14 14:58:38Z fabien $
 */
abstract class sfValidatorBase
{
  protected static
    $charset = 'UTF-8',
    $globalDefaultMessages = array('invalid' => 'Invalid.', 'required' => 'Required.');

  protected
    $requiredOptions = array(),
    $defaultMessages = array(),
    $defaultOptions  = array(),
    $messages        = array(),
    $options         = array();

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * required:    true if the value is required, false otherwise (default to true)
   *  * trim:        true if the value must be trimmed, false otherwise (default to false)
   *  * empty_value: empty value when value is not required
   *
   * Available error codes:
   *
   *  * required
   *  * invalid
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   */
  public function __construct($options = array(), $messages = array())
  {
    $this->options  = array_merge(array('required' => true, 'trim' => false, 'empty_value' => null), $this->options);
    $this->messages = array_merge(array('required' => self::$globalDefaultMessages['required'], 'invalid' => self::$globalDefaultMessages['invalid']), $this->messages);

    $this->configure($options, $messages);

    $this->setDefaultOptions($this->getOptions());
    $this->setDefaultMessages($this->getMessages());

    $currentOptionKeys = array_keys($this->options);
    $optionKeys = array_keys($options);

    // check option names
    if ($diff = array_diff($optionKeys, array_merge($currentOptionKeys, $this->requiredOptions)))
    {
      throw new InvalidArgumentException(sprintf('%s does not support the following options: \'%s\'.', get_class($this), implode('\', \'', $diff)));
    }

    // check error code names
    if ($diff = array_diff(array_keys($messages), array_keys($this->messages)))
    {
      throw new InvalidArgumentException(sprintf('%s does not support the following error codes: \'%s\'.', get_class($this), implode('\', \'', $diff)));
    }

    // check required options
    if ($diff = array_diff($this->requiredOptions, array_merge($currentOptionKeys, $optionKeys)))
    {
      throw new RuntimeException(sprintf('%s requires the following options: \'%s\'.', get_class($this), implode('\', \'', $diff)));
    }

    $this->options  = array_merge($this->options, $options);
    $this->messages = array_merge($this->messages, $messages);
  }

  /**
   * Configures the current validator.
   *
   * This method allows each validator to add options and error messages
   * during validator creation.
   *
   * If some options and messages are given in the sfValidatorBase constructor
   * they will take precedence over the options and messages you configure
   * in this method.
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see __construct()
   */
  protected function configure($options = array(), $messages = array())
  {
  }

  /**
   * Returns an error message given an error code.
   *
   * @param  string $name  The error code
   *
   * @return string The error message, or the empty string if the error code does not exist
   */
  public function getMessage($name)
  {
    return isset($this->messages[$name]) ? $this->messages[$name] : '';
  }

  /**
   * Adds a new error code with a default error message.
   *
   * @param string $name   The error code
   * @param string $value  The error message
   *
   * @return sfValidatorBase The current validator instance
   */
  public function addMessage($name, $value)
  {
    $this->messages[$name] = isset(self::$globalDefaultMessages[$name]) ? self::$globalDefaultMessages[$name] : $value;

    return $this;
  }

  /**
   * Changes an error message given the error code.
   *
   * @param string $name   The error code
   * @param string $value  The error message
   *
   * @return sfValidatorBase The current validator instance
   */
  public function setMessage($name, $value)
  {
    if (!in_array($name, array_keys($this->messages)))
    {
      throw new InvalidArgumentException(sprintf('%s does not support the following error code: \'%s\'.', get_class($this), $name));
    }

    $this->messages[$name] = $value;

    return $this;
  }

  /**
   * Returns an array of current error messages.
   *
   * @return array An array of messages
   */
  public function getMessages()
  {
    return $this->messages;
  }

  /**
   * Changes all error messages.
   *
   * @param array $values  An array of error messages
   *
   * @return sfValidatorBase The current validator instance
   */
  public function setMessages($values)
  {
    $this->messages = $values;

    return $this;
  }

  /**
   * Gets an option value.
   *
   * @param  string $name  The option name
   *
   * @return mixed  The option value
   */
  public function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
  }

  /**
   * Adds a new option value with a default value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The default value
   *
   * @return sfValidatorBase The current validator instance
   */
  public function addOption($name, $value = null)
  {
    $this->options[$name] = $value;

    return $this;
  }

  /**
   * Changes an option value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The value
   *
   * @return sfValidatorBase The current validator instance
   */
  public function setOption($name, $value)
  {
    if (!in_array($name, array_merge(array_keys($this->options), $this->requiredOptions)))
    {
      throw new InvalidArgumentException(sprintf('%s does not support the following option: \'%s\'.', get_class($this), $name));
    }

    $this->options[$name] = $value;

    return $this;
  }

  /**
   * Returns true if the option exists.
   *
   * @param  string $name  The option name
   *
   * @return bool true if the option exists, false otherwise
   */
  public function hasOption($name)
  {
    return isset($this->options[$name]);
  }

  /**
   * Returns all options.
   *
   * @return array An array of options
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Changes all options.
   *
   * @param array $values  An array of options
   *
   * @return sfValidatorBase The current validator instance
   */
  public function setOptions($values)
  {
    $this->options = $values;

    return $this;
  }

  /**
   * Adds a required option.
   *
   * @param string $name  The option name
   *
   * @return sfValidatorBase The current validator instance
   */
  public function addRequiredOption($name)
  {
    $this->requiredOptions[] = $name;

    return $this;
  }

  /**
   * Returns all required option names.
   *
   * @return array An array of required option names
   */
  public function getRequiredOptions()
  {
    return $this->requiredOptions;
  }

  /**
   * Sets the default message for a given name.
   *
   * @param string $name    The name of the message
   * @param string $message The default message string
   */
  static public function setDefaultMessage($name, $message)
  {
    self::$globalDefaultMessages[$name] = $message;
  }

  /**
   * Cleans the input value.
   *
   * This method is also responsible for trimming the input value
   * and checking the required option.
   *
   * @param  mixed $value  The input value
   *
   * @return mixed The cleaned value
   *
   * @throws sfValidatorError
   */
  public function clean($value)
  {
    $clean = $value;

    if ($this->options['trim'] && is_string($clean))
    {
      $clean = trim($clean);
    }

    // empty value?
    if ($this->isEmpty($clean))
    {
      // required?
      if ($this->options['required'])
      {
        throw new sfValidatorError($this, 'required');
      }

      return $this->getEmptyValue();
    }

    return $this->doClean($clean);
  }

  /**
   * Cleans the input value.
   *
   * Every subclass must implements this method.
   *
   * @param  mixed $value  The input value
   *
   * @return mixed The cleaned value
   *
   * @throws sfValidatorError
   */
  abstract protected function doClean($value);

  /**
   * Sets the charset to use when validating strings.
   *
   * @param string $charset  The charset
   */
  static public function setCharset($charset)
  {
    self::$charset = $charset;
  }

  /**
   * Returns the charset to use when validating strings.
   *
   * @return string The charset (default to UTF-8)
   */
  static public function getCharset()
  {
    return self::$charset;
  }

  /**
   * Returns true if the value is empty.
   *
   * @param  mixed $value  The input value
   *
   * @return bool true if the value is empty, false otherwise
   */
  protected function isEmpty($value)
  {
    return in_array($value, array(null, '', array()), true);
  }

  /**
   * Returns an empty value for this validator.
   *
   * @return mixed The empty value for this validator
   */
  protected function getEmptyValue()
  {
    return $this->getOption('empty_value');
  }

  /**
   * Returns an array of all error codes for this validator.
   *
   * @return array An array of possible error codes
   *
   * @see getDefaultMessages()
   */
  final public function getErrorCodes()
  {
    return array_keys($this->getDefaultMessages());
  }

  /**
   * Returns default messages for all possible error codes.
   *
   * @return array An array of default error codes and messages
   */
  public function getDefaultMessages()
  {
    return $this->defaultMessages;
  }

  /**
   * Sets default messages for all possible error codes.
   *
   * @param array $messages  An array of default error codes and messages
   */
  protected function setDefaultMessages($messages)
  {
    $this->defaultMessages = $messages;
  }

  /**
   * Returns default option values.
   *
   * @return array An array of default option values
   */
  public function getDefaultOptions()
  {
    return $this->defaultOptions;
  }

  /**
   * Sets default option values.
   *
   * @param array $options  An array of default option values
   */
  protected function setDefaultOptions($options)
  {
    $this->defaultOptions = $options;
  }

  /**
   * Returns a string representation of this validator.
   *
   * @param  int $indent  Indentation (number of spaces before each line)
   *
   * @return string The string representation of the validator
   */
  public function asString($indent = 0)
  {
    $options = $this->getOptionsWithoutDefaults();
    $messages = $this->getMessagesWithoutDefaults();

    return sprintf('%s%s(%s%s)',
      str_repeat(' ', $indent),
      str_replace('sfValidator', '', get_class($this)),
      $options ? sfYamlInline::dump($options) : ($messages ? '{}' : ''),
      $messages ? ', '.sfYamlInline::dump($messages) : ''
    );
  }

  /**
   * Returns all error messages with non default values.
   *
   * @return string A string representation of the error messages
   */
  protected function getMessagesWithoutDefaults()
  {
    $messages = $this->messages;

    // remove default option values
    foreach ($this->getDefaultMessages() as $key => $value)
    {
      if (array_key_exists($key, $messages) && $messages[$key] === $value)
      {
        unset($messages[$key]);
      }
    }

    return $messages;
  }

  /**
   * Returns all options with non default values.
   *
   * @return string  A string representation of the options
   */
  protected function getOptionsWithoutDefaults()
  {
    $options = $this->options;

    // remove default option values
    foreach ($this->getDefaultOptions() as $key => $value)
    {
      if (array_key_exists($key, $options) && $options[$key] === $value)
      {
        unset($options[$key]);
      }
    }

    return $options;
  }
}
