<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represent a set of command line options.
 *
 * @package    symfony
 * @subpackage command
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCommandOptionSet.class.php 17858 2009-05-01 21:22:50Z FabianLange $
 */
class sfCommandOptionSet
{
  protected
    $options   = array(),
    $shortcuts = array();

  /**
   * Constructor.
   *
   * @param array $options An array of sfCommandOption objects
   */
  public function __construct($options = array())
  {
    $this->setOptions($options);
  }

  /**
   * Sets the sfCommandOption objects.
   *
   * @param array $options An array of sfCommandOption objects
   */
  public function setOptions($options = array())
  {
    $this->options = array();
    $this->shortcuts = array();
    $this->addOptions($options);
  }

  /**
   * Add an array of sfCommandOption objects.
   *
   * @param array $options An array of sfCommandOption objects
   */
  public function addOptions($options = array())
  {
    foreach ($options as $option)
    {
      $this->addOption($option);
    }
  }

  /**
   * Add a sfCommandOption objects.
   *
   * @param sfCommandOption $option A sfCommandOption object
   */
  public function addOption(sfCommandOption $option)
  {
    if (isset($this->options[$option->getName()]))
    {
      throw new sfCommandException(sprintf('An option named "%s" already exist.', $option->getName()));
    }
    else if (isset($this->shortcuts[$option->getShortcut()]))
    {
      throw new sfCommandException(sprintf('An option with shortcut "%s" already exist.', $option->getShortcut()));
    }

    $this->options[$option->getName()] = $option;
    if ($option->getShortcut())
    {
      $this->shortcuts[$option->getShortcut()] = $option->getName();
    }
  }

  /**
   * Returns an option by name.
   *
   * @param string $name The option name
   *
   * @return sfCommandOption A sfCommandOption object
   */
  public function getOption($name)
  {
    if (!$this->hasOption($name))
    {
      throw new sfCommandException(sprintf('The "--%s" option does not exist.', $name));
    }

    return $this->options[$name];
  }

  /**
   * Returns true if an option object exists by name.
   *
   * @param string $name The option name
   *
   * @return Boolean true if the option object exists, false otherwise
   */
  public function hasOption($name)
  {
    return isset($this->options[$name]);
  }

  /**
   * Gets the array of sfCommandOption objects.
   *
   * @return array An array of sfCommandOption objects
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Returns true if an option object exists by shortcut.
   *
   * @param string $name The option shortcut
   *
   * @return Boolean true if the option object exists, false otherwise
   */
  public function hasShortcut($name)
  {
    return isset($this->shortcuts[$name]);
  }

  /**
   * Gets an option by shortcut.
   *
   * @return sfCommandOption A sfCommandOption object
   */
  public function getOptionForShortcut($shortcut)
  {
    return $this->getOption($this->shortcutToName($shortcut));
  }

  /**
   * Gets an array of default values.
   *
   * @return array An array of all default values
   */
  public function getDefaults()
  {
    $values = array();
    foreach ($this->options as $option)
    {
      $values[$option->getName()] = $option->getDefault();
    }

    return $values;
  }

  /**
   * Returns the option name given a shortcut.
   *
   * @param string $shortcut The shortcut
   *
   * @return string The option name
   */
  protected function shortcutToName($shortcut)
  {
    if (!isset($this->shortcuts[$shortcut]))
    {
      throw new sfCommandException(sprintf('The "-%s" option does not exist.', $shortcut));
    }

    return $this->shortcuts[$shortcut];
  }
}
