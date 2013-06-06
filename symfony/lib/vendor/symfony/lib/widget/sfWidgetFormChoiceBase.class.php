<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormChoiceBase is the base class for all choice/select widgets
 *
 * @package    symfony
 * @subpackage widget
 * @author     Bernhard Schussek <bernhard.schussek@symfony-project.com>
 * @version    SVN: $Id$
 */
abstract class sfWidgetFormChoiceBase extends sfWidgetForm
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:         An array of possible choices (required)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('choices');
    $this->addOption('translate_choices', true);
  }

  /**
   * Returns the translated choices configured for this widget
   *
   * @return array  An array of strings
   */
  public function getChoices()
  {
    $choices = $this->getOption('choices');

    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    if (!$this->getOption('translate_choices'))
    {
      return $choices;
    }

    $results = array();
    foreach ($choices as $key => $choice)
    {
      if (is_array($choice))
      {
        $results[$this->translate($key)] = $this->translateAll($choice);
      }
      else
      {
        $results[$key] = $this->translate($choice);
      }
    }

    return $results;
  }

  /**
   * Clones this object
   */
  public function __clone()
  {
    if ($this->getOption('choices') instanceof sfCallable)
    {
      $callable = $this->getOption('choices')->getCallable();
      if (is_array($callable) && $callable[0] instanceof self)
      {
        $callable[0] = $this;
        $this->setOption('choices', new sfCallable($callable));
      }
    }
  }
}