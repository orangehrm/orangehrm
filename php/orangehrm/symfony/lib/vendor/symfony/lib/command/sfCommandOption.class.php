<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a command line option.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCommandOption.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfCommandOption
{
  const PARAMETER_NONE     = 1;
  const PARAMETER_REQUIRED = 2;
  const PARAMETER_OPTIONAL = 4;

  const IS_ARRAY = 8;

  protected
    $name     = null,
    $shortcut = null,
    $mode     = null,
    $default  = null,
    $help     = '';

  /**
   * Constructor.
   *
   * @param string  $name     The option name
   * @param string  $shortcut The shortcut (can be null)
   * @param integer $mode     The option mode: self::PARAMETER_REQUIRED, self::PARAMETER_NONE or self::PARAMETER_OPTIONAL
   * @param string  $help     A help text
   * @param mixed   $default  The default value (must be null for self::PARAMETER_REQUIRED or self::PARAMETER_NONE)
   */
  public function __construct($name, $shortcut = null, $mode = null, $help = '', $default = null)
  {
    if ('--' == substr($name, 0, 2))
    {
      $name = substr($name, 2);
    }

    if (empty($shortcut))
    {
      $shortcut = null;
    }

    if (!is_null($shortcut))
    {
      if ('-' == $shortcut[0])
      {
        $shortcut = substr($shortcut, 1);
      }
    }

    if (is_null($mode))
    {
      $mode = self::PARAMETER_NONE;
    }
    else if (is_string($mode) || $mode > 15)
    {
      throw new sfCommandException(sprintf('Option mode "%s" is not valid.', $mode));
    }

    $this->name     = $name;
    $this->shortcut = $shortcut;
    $this->mode     = $mode;
    $this->help     = $help;

    $this->setDefault($default);
  }

  /**
   * Returns the shortcut.
   *
   * @return string The shortcut
   */
  public function getShortcut()
  {
    return $this->shortcut;
  }

  /**
   * Returns the name.
   *
   * @return string The name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Returns true if the option accept a parameter.
   *
   * @return Boolean true if parameter mode is not self::PARAMETER_NONE, false otherwise
   */
  public function acceptParameter()
  {
    return $this->isParameterRequired() || $this->isParameterOptional();
  }

  /**
   * Returns true if the option requires a parameter.
   *
   * @return Boolean true if parameter mode is self::PARAMETER_REQUIRED, false otherwise
   */
  public function isParameterRequired()
  {
    return self::PARAMETER_REQUIRED === (self::PARAMETER_REQUIRED & $this->mode);
  }

  /**
   * Returns true if the option takes an optional parameter.
   *
   * @return Boolean true if parameter mode is self::PARAMETER_OPTIONAL, false otherwise
   */
  public function isParameterOptional()
  {
    return self::PARAMETER_OPTIONAL === (self::PARAMETER_OPTIONAL & $this->mode);
  }

  /**
   * Returns true if the option can take multiple values.
   *
   * @return Boolean true if mode is self::IS_ARRAY, false otherwise
   */
  public function isArray()
  {
    return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
  }

  /**
   * Sets the default value.
   *
   * @param mixed $default The default value
   */
  public function setDefault($default = null)
  {
    if (self::PARAMETER_NONE === (self::PARAMETER_NONE & $this->mode) && !is_null($default))
    {
      throw new sfCommandException('Cannot set a default value when using sfCommandOption::PARAMETER_NONE mode.');
    }

    if ($this->isArray())
    {
      if (is_null($default))
      {
        $default = array();
      }
      else if (!is_array($default))
      {
        throw new sfCommandException('A default value for an array option must be an array.');
      }
    }

    $this->default = $this->acceptParameter() ? $default : false;
  }

  /**
   * Returns the default value.
   *
   * @return mixed The default value
   */
  public function getDefault()
  {
    return $this->default;
  }

  /**
   * Returns the help text.
   *
   * @return string The help text
   */
  public function getHelp()
  {
    return $this->help;
  }
}
