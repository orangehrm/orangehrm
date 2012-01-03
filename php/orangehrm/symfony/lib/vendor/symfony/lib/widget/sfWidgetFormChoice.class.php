<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormChoice represents a choice widget.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormChoice.class.php 32835 2011-07-27 07:07:00Z fabien $
 */
class sfWidgetFormChoice extends sfWidgetFormChoiceBase
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:          An array of possible choices (required)
   *  * multiple:         true if the select tag must allow multiple selections
   *  * expanded:         true to display an expanded widget
   *                        if expanded is false, then the widget will be a select
   *                        if expanded is true and multiple is false, then the widget will be a list of radio
   *                        if expanded is true and multiple is true, then the widget will be a list of checkbox
   *  * renderer_class:   The class to use instead of the default ones
   *  * renderer_options: The options to pass to the renderer constructor
   *  * renderer:         A renderer widget (overrides the expanded and renderer_options options)
   *                      The choices option must be: new sfCallable($thisWidgetInstance, 'getChoices')
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormChoiceBase
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('multiple', false);
    $this->addOption('expanded', false);
    $this->addOption('renderer_class', false);
    $this->addOption('renderer_options', array());
    $this->addOption('renderer', false);
  }

  /**
   * Sets the format for HTML id attributes. This is made avaiable to the renderer,
   * as this widget does not render itself, but delegates to the renderer instead.
   *
   * @param string $format  The format string (must contain a %s for the id placeholder)
   *
   * @see sfWidgetForm
   */
  public function setIdFormat($format)
  {
    $this->options['renderer_options']['id_format'] = $format;
  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ($this->getOption('multiple'))
    {
      $attributes['multiple'] = 'multiple';

      if ('[]' != substr($name, -2))
      {
        $name .= '[]';
      }
    }

    if (!$this->getOption('renderer') && !$this->getOption('renderer_class') && $this->getOption('expanded'))
    {
      unset($attributes['multiple']);
    }

    return $this->getRenderer()->render($name, $value, $attributes, $errors);
  }

  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return $this->getRenderer()->getStylesheets();
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return $this->getRenderer()->getJavaScripts();
  }

  public function getRenderer()
  {
    if ($this->getOption('renderer'))
    {
      return $this->getOption('renderer');
    }

    if (!$class = $this->getOption('renderer_class'))
    {
      $type = !$this->getOption('expanded') ? '' : ($this->getOption('multiple') ? 'checkbox' : 'radio');
      $class = sprintf('sfWidgetFormSelect%s', ucfirst($type));
    }

    $options = $this->options['renderer_options'];
    $options['choices'] = new sfCallable(array($this, 'getChoices'));

    $renderer = new $class($options, $this->getAttributes());

    // choices returned by the callback will already be translated (so we need to avoid double-translation)
    if ($renderer->hasOption('translate_choices')) {
        $renderer->setOption('translate_choices', false);
    }

    $renderer->setParent($this->getParent());

    return $renderer;
  }
}
