<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class to manage command line arguments and options.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCommandManager.class.php 21908 2009-09-11 12:06:21Z fabien $
 */
class sfCommandManager
{
  protected
    $arguments            = '',
    $errors               = array(),
    $optionSet            = null,
    $argumentSet          = array(),
    $optionValues         = array(),
    $argumentValues       = array(),
    $parsedArgumentValues = array();

  /**
   * Constructor.
   *
   * @param sfCommandArgumentSet $argumentSet A sfCommandArgumentSet object
   * @param sfCommandOptionSet   $optionSet   A setOptionSet object
   */
  public function __construct(sfCommandArgumentSet $argumentSet = null, sfCommandOptionSet $optionSet = null)
  {
    if (null === $argumentSet)
    {
      $argumentSet = new sfCommandArgumentSet();
    }
    $this->setArgumentSet($argumentSet);

    if (null === $optionSet)
    {
      $optionSet = new sfCommandOptionSet();
    }
    $this->setOptionSet($optionSet);
  }

  /**
   * Sets the argument set.
   *
   * @param sfCommandArgumentSet $argumentSet A sfCommandArgumentSet object
   */
  public function setArgumentSet(sfCommandArgumentSet $argumentSet)
  {
    $this->argumentSet = $argumentSet;
  }

  /**
   * Gets the argument set.
   *
   * @return sfCommandArgumentSet A sfCommandArgumentSet object
   */
  public function getArgumentSet()
  {
    return $this->argumentSet;
  }

  /**
   * Sets the option set.
   *
   * @param sfCommandOptionSet $optionSet A sfCommandOptionSet object
   */
  public function setOptionSet(sfCommandOptionSet $optionSet)
  {
    $this->optionSet = $optionSet;
  }

  /**
   * Gets the option set.
   *
   * @return sfCommandOptionSet A sfCommandOptionSet object
   */
  public function getOptionSet()
  {
    return $this->optionSet;
  }

  /**
   * Processes command line arguments.
   *
   * @param mixed $arguments A string or an array of command line parameters
   */
  public function process($arguments = null)
  {
    if (null === $arguments)
    {
      $arguments = $_SERVER['argv'];

      // we strip command line program
      if (isset($arguments[0]) && '-' != $arguments[0][0])
      {
        array_shift($arguments);
      }
    }
    else if (!is_array($arguments))
    {
      // hack to split arguments with spaces : --test="with some spaces"
      $arguments = preg_replace('/(\'|")(.+?)\\1/e', "str_replace(' ', '=PLACEHOLDER=', '\\2')", $arguments);
      $arguments = preg_split('/\s+/', $arguments);
      $arguments = str_replace('=PLACEHOLDER=', ' ', $arguments);
    }

    $this->arguments            = $arguments;
    $this->optionValues         = $this->optionSet->getDefaults();
    $this->argumentValues       = $this->argumentSet->getDefaults();
    $this->parsedArgumentValues = array();
    $this->errors               = array();

    while (!in_array($argument = array_shift($this->arguments), array('', null)))
    {
      if ('--' == $argument)
      {
        // stop options parsing
        $this->parsedArgumentValues = array_merge($this->parsedArgumentValues, $this->arguments);
        break;
      }

      if ('--' == substr($argument, 0, 2))
      {
        $this->parseLongOption(substr($argument, 2));
      }
      else if ('-' == $argument[0])
      {
        $this->parseShortOption(substr($argument, 1));
      }
      else
      {
        $this->parsedArgumentValues[] = $argument;
      }
    }

    $position = 0;
    foreach ($this->argumentSet->getArguments() as $argument)
    {
      if (array_key_exists($position, $this->parsedArgumentValues))
      {
        if ($argument->isArray())
        {
          $this->argumentValues[$argument->getName()] = array_slice($this->parsedArgumentValues, $position);
          break;
        }
        else
        {
          $this->argumentValues[$argument->getName()] = $this->parsedArgumentValues[$position];
        }
      }
      ++$position;
    }

    $this->arguments = $arguments;

    if (count($this->parsedArgumentValues) < $this->argumentSet->getArgumentRequiredCount())
    {
      $this->errors[] = 'Not enough arguments.';
    }
    else if (count($this->parsedArgumentValues) > $this->argumentSet->getArgumentCount())
    {
      $this->errors[] = sprintf('Too many arguments ("%s" given).', implode(' ', $this->parsedArgumentValues));
    }
  }

  /**
   * Returns true if the current command line options validate the argument and option sets.
   *
   * @return true if there are some validation errors, false otherwise
   */
  public function isValid()
  {
    return count($this->errors) ? false : true;
  }

  /**
   * Gets the current errors.
   *
   * @return array An array of errors
   */
  public function getErrors()
  {
    return $this->errors;
  }

  /**
   * Returns the argument values.
   *
   * @return array An array of argument values
   */
  public function getArgumentValues()
  {
    return $this->argumentValues;
  }

  /**
   * Returns the argument value for a given argument name.
   *
   * @param string $name The argument name
   *
   * @return mixed The argument value
   */
  public function getArgumentValue($name)
  {
    if (!$this->argumentSet->hasArgument($name))
    {
      throw new sfCommandException(sprintf('The "%s" argument does not exist.', $name));
    }

    return $this->argumentValues[$name];
  }

  /**
   * Returns the options values.
   *
   * @return array An array of option values
   */
  public function getOptionValues()
  {
    return $this->optionValues;
  }

  /**
   * Returns the option value for a given option name.
   *
   * @param string $name The option name
   *
   * @return mixed The option value
   */
  public function getOptionValue($name)
  {
    if (!$this->optionSet->hasOption($name))
    {
      throw new sfCommandException(sprintf('The "%s" option does not exist.', $name));
    }

    return $this->optionValues[$name];
  }

  /**
   * Parses a short option.
   *
   * @param string $argument The option argument
   */
  protected function parseShortOption($argument)
  {
    // short option can be aggregated like in -vd (== -v -d)
    for ($i = 0, $count = strlen($argument); $i < $count; $i++)
    {
      $shortcut = $argument[$i];
      $value    = true;

      if (!$this->optionSet->hasShortcut($shortcut))
      {
        $this->errors[] = sprintf('The option "-%s" does not exist.', $shortcut);
        continue;
      }

      $option = $this->optionSet->getOptionForShortcut($shortcut);

      // required argument?
      if ($option->isParameterRequired())
      {
        if ($i + 1 < strlen($argument))
        {
          $value = substr($argument, $i + 1);
          $this->setOption($option, $value);
          break;
        }
        else
        {
          // take next element as argument (if it doesn't start with a -)
          if (count($this->arguments) && $this->arguments[0][0] != '-')
          {
            $value = array_shift($this->arguments);
            $this->setOption($option, $value);
            break;
          }
          else
          {
            $this->errors[] = sprintf('Option "-%s" requires an argument', $shortcut);
            $value = null;
          }
        }
      }
      else if ($option->isParameterOptional())
      {
        if (substr($argument, $i + 1) != '')
        {
          $value = substr($argument, $i + 1);
        }
        else
        {
          // take next element as argument (if it doesn't start with a -)
          if (count($this->arguments) && $this->arguments[0][0] != '-')
          {
            $value = array_shift($this->arguments);
          }
          else
          {
            $value = $option->getDefault();
          }
        }

        $this->setOption($option, $value);
        break;
      }

      $this->setOption($option, $value);
    }
  }

  /**
   * Parses a long option.
   *
   * @param string $argument The option argument
   */
  protected function parseLongOption($argument)
  {
    if (false !== strpos($argument, '='))
    {
      list($name, $value) = explode('=', $argument, 2);

      if (!$this->optionSet->hasOption($name))
      {
        $this->errors[] = sprintf('The "--%s" option does not exist.', $name);
        return;
      }

      $option = $this->optionSet->getOption($name);

      if (!$option->acceptParameter())
      {
        $this->errors[] = sprintf('Option "--%s" does not take an argument.', $name);
        $value = true;
      }
    }
    else
    {
      $name = $argument;

      if (!$this->optionSet->hasOption($name))
      {
        $this->errors[] = sprintf('The "--%s" option does not exist.', $name);
        return;
      }

      $option = $this->optionSet->getOption($name);

      if ($option->isParameterRequired())
      {
        $this->errors[] = sprintf('Option "--%s" requires an argument.', $name);
      }

      $value = $option->acceptParameter() ? $option->getDefault() : true;
    }

    $this->setOption($option, $value);
  }

  public function setOption(sfCommandOption $option, $value)
  {
    if ($option->isArray())
    {
      $this->optionValues[$option->getName()][] = $value;
    }
    else
    {
      $this->optionValues[$option->getName()] = $value;
    }
  }
}
