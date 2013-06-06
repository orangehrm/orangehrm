<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represent a set of command line arguments.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCommandArgumentSet.class.php 21908 2009-09-11 12:06:21Z fabien $
 */
class sfCommandArgumentSet
{
  protected
    $arguments          = array(),
    $requiredCount      = 0,
    $hasAnArrayArgument = false,
    $hasOptional        = false;

  /**
   * Constructor.
   *
   * @param array $arguments An array of sfCommandArgument objects
   */
  public function __construct($arguments = array())
  {
    $this->setArguments($arguments);
  }

  /**
   * Sets the sfCommandArgument objects.
   *
   * @param array $arguments An array of sfCommandArgument objects
   */
  public function setArguments($arguments = array())
  {
    $this->arguments     = array();
    $this->requiredCount = 0;
    $this->hasOptional   = false;
    $this->addArguments($arguments);
  }

  /**
   * Add an array of sfCommandArgument objects.
   *
   * @param array $arguments An array of sfCommandArgument objects
   */
  public function addArguments($arguments = array())
  {
    if (null !== $arguments)
    {
      foreach ($arguments as $argument)
      {
        $this->addArgument($argument);
      }
    }
  }

  /**
   * Add a sfCommandArgument objects.
   *
   * @param sfCommandArgument $argument A sfCommandArgument object
   */
  public function addArgument(sfCommandArgument $argument)
  {
    if (isset($this->arguments[$argument->getName()]))
    {
      throw new sfCommandException(sprintf('An argument with name "%s" already exist.', $argument->getName()));
    }

    if ($this->hasAnArrayArgument)
    {
      throw new sfCommandException('Cannot add an argument after an array argument.');
    }

    if ($argument->isRequired() && $this->hasOptional)
    {
      throw new sfCommandException('Cannot add a required argument after an optional one.');
    }

    if ($argument->isArray())
    {
      $this->hasAnArrayArgument = true;
    }

    if ($argument->isRequired())
    {
      ++$this->requiredCount;
    }
    else
    {
      $this->hasOptional = true;
    }

    $this->arguments[$argument->getName()] = $argument;
  }

  /**
   * Returns an argument by name.
   *
   * @param string $name The argument name
   *
   * @return sfCommandArgument A sfCommandArgument object
   */
  public function getArgument($name)
  {
    if (!$this->hasArgument($name))
    {
      throw new sfCommandException(sprintf('The "%s" argument does not exist.', $name));
    }

    return $this->arguments[$name];
  }

  /**
   * Returns true if an argument object exists by name.
   *
   * @param string $name The argument name
   *
   * @return Boolean true if the argument object exists, false otherwise
   */
  public function hasArgument($name)
  {
    return isset($this->arguments[$name]);
  }

  /**
   * Gets the array of sfCommandArgument objects.
   *
   * @return array An array of sfCommandArgument objects
   */
  public function getArguments()
  {
    return $this->arguments;
  }

  /**
   * Returns the number of arguments.
   *
   * @return integer The number of arguments
   */
  public function getArgumentCount()
  {
    return $this->hasAnArrayArgument ? PHP_INT_MAX : count($this->arguments);
  }

  /**
   * Returns the number of required arguments.
   *
   * @return integer The number of required arguments
   */
  public function getArgumentRequiredCount()
  {
    return $this->requiredCount;
  }

  /**
   * Gets the default values.
   *
   * @return array An array of default values
   */
  public function getDefaults()
  {
    $values = array();
    foreach ($this->arguments as $argument)
    {
      $values[$argument->getName()] = $argument->getDefault();
    }

    return $values;
  }
}
