<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidget is the base class for all widgets.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidget.class.php 33388 2012-03-15 15:01:41Z fabien $
 */
abstract class sfWidget
{
  protected
    $requiredOptions = array(),
    $attributes      = array(),
    $options         = array();

  protected static
    $xhtml   = true,
    $charset = 'UTF-8';

  /**
   * Constructor.
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @throws InvalidArgumentException when a option is not supported
   * @throws RuntimeException         when a required option is not given
   */
  public function __construct($options = array(), $attributes = array())
  {
    $this->configure($options, $attributes);

    $currentOptionKeys = array_keys($this->options);
    $optionKeys = array_keys($options);

    // check option names
    if ($diff = array_diff($optionKeys, array_merge($currentOptionKeys, $this->requiredOptions)))
    {
      throw new InvalidArgumentException(sprintf('%s does not support the following options: \'%s\'.', get_class($this), implode('\', \'', $diff)));
    }

    // check required options
    if ($diff = array_diff($this->requiredOptions, array_merge($currentOptionKeys, $optionKeys)))
    {
      throw new RuntimeException(sprintf('%s requires the following options: \'%s\'.', get_class($this), implode('\', \'', $diff)));
    }

    $this->options = array_merge($this->options, $options);
    $this->attributes = array_merge($this->attributes, $attributes);
  }

  /**
   * Configures the current widget.
   *
   * This method allows each widget to add options or HTML attributes
   * during widget creation.
   *
   * If some options and HTML attributes are given in the sfWidget constructor
   * they will take precedence over the options and HTML attributes you configure
   * in this method.
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of HTML attributes
   *
   * @see __construct()
   */
  protected function configure($options = array(), $attributes = array())
  {
  }

  /**
   * Renders the widget as HTML.
   *
   * All subclasses must implement this method.
   *
   * @param  string $name       The name of the HTML widget
   * @param  mixed  $value      The value of the widget
   * @param  array  $attributes An array of HTML attributes
   * @param  array  $errors     An array of errors
   *
   * @return string A HTML representation of the widget
   */
  abstract public function render($name, $value = null, $attributes = array(), $errors = array());

  /**
   * Adds a required option.
   *
   * @param string $name  The option name
   *
   * @return sfWidget The current widget instance
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
   * Adds a new option value with a default value.
   *
   * @param string $name   The option name
   * @param mixed  $value  The default value
   *
   * @return sfWidget The current widget instance
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
   * @return sfWidget The current widget instance
   *
   * @throws InvalidArgumentException when a option is not supported
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
   * Gets an option value.
   *
   * @param  string $name The option name
   *
   * @return mixed  The option value
   */
  public function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
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
    return array_key_exists($name, $this->options);
  }

  /**
   * Gets all options.
   *
   * @return array  An array of named options
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Sets the options.
   *
   * @param array $options  An array of options
   *
   * @return sfWidget The current widget instance
   */
  public function setOptions($options)
  {
    $this->options = $options;

    return $this;
  }

  /**
   * Returns the default HTML attributes.
   *
   * @param array An array of HTML attributes
   */
  public function getAttributes()
  {
    return $this->attributes;
  }

  /**
   * Sets a default HTML attribute.
   *
   * @param string $name   The attribute name
   * @param string $value  The attribute value
   *
   * @return sfWidget The current widget instance
   */
  public function setAttribute($name, $value)
  {
    $this->attributes[$name] = $value;

    return $this;
  }

  /**
   * Returns the HTML attribute value for a given attribute name.
   *
   * @param  string $name  The attribute name.
   *
   * @return string The attribute value, or null if the attribute does not exist
   */
  public function getAttribute($name)
  {
    return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
  }

  /**
   * Sets the HTML attributes.
   *
   * @param array $attributes  An array of HTML attributes
   *
   * @return sfWidget The current widget instance
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;

    return $this;
  }

  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * The array keys are files and values are the media names (separated by a ,):
   *
   *   array('/path/to/file.css' => 'all', '/another/file.css' => 'screen,print')
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array();
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array();
  }

  /**
   * Sets the charset to use when rendering widgets.
   *
   * @param string $charset  The charset
   */
  static public function setCharset($charset)
  {
    self::$charset = $charset;
  }

  /**
   * Returns the charset to use when rendering widgets.
   *
   * @return string The charset (defaults to UTF-8)
   */
  static public function getCharset()
  {
    return self::$charset;
  }

  /**
   * Sets the XHTML generation flag.
   *
   * @param bool $boolean  true if widgets must be generated as XHTML, false otherwise
   */
  static public function setXhtml($boolean)
  {
    self::$xhtml = (boolean) $boolean;
  }

  /**
   * Returns whether to generate XHTML tags or not.
   *
   * @return bool true if widgets must be generated as XHTML, false otherwise
   */
  static public function isXhtml()
  {
    return self::$xhtml;
  }

  /**
   * Renders a HTML tag.
   *
   * @param string $tag         The tag name
   * @param array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   *
   * @param string An HTML tag string
   */
  public function renderTag($tag, $attributes = array())
  {
    if (empty($tag))
    {
      return '';
    }

    return sprintf('<%s%s%s', $tag, $this->attributesToHtml($attributes), self::$xhtml ? ' />' : (strtolower($tag) == 'input' ? '>' : sprintf('></%s>', $tag)));
  }

  /**
   * Renders a HTML content tag.
   *
   * @param string $tag         The tag name
   * @param string $content     The content of the tag
   * @param array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   *
   * @param string An HTML tag string
   */
  public function renderContentTag($tag, $content = null, $attributes = array())
  {
    if (empty($tag))
    {
      return '';
    }

    return sprintf('<%s%s>%s</%s>', $tag, $this->attributesToHtml($attributes), $content, $tag);
  }

  /**
   * Escapes a string.
   *
   * @param  string $value  string to escape
   * @return string escaped string
   */
  static public function escapeOnce($value)
  {
    return self::fixDoubleEscape(htmlspecialchars((string) $value, ENT_QUOTES, self::getCharset()));
  }

  /**
   * Fixes double escaped strings.
   *
   * @param  string $escaped  string to fix
   * @return string single escaped string
   */
  static public function fixDoubleEscape($escaped)
  {
    return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
  }

  /**
   * Converts an array of attributes to its HTML representation.
   *
   * @param  array  $attributes An array of attributes
   *
   * @return string The HTML representation of the HTML attribute array.
   */
  public function attributesToHtml($attributes)
  {
    $attributes = array_merge($this->attributes, $attributes);

    return implode('', array_map(array($this, 'attributesToHtmlCallback'), array_keys($attributes), array_values($attributes)));
  }

  /**
   * Prepares an attribute key and value for HTML representation.
   *
   * It removes empty attributes, except for the value one.
   *
   * @param  string $k  The attribute key
   * @param  string $v  The attribute value
   *
   * @return string The HTML representation of the HTML key attribute pair.
   */
  protected function attributesToHtmlCallback($k, $v)
  {
    return false === $v || null === $v || ('' === $v && 'value' != $k) ? '' : sprintf(' %s="%s"', $k, $this->escapeOnce($v));
  }
}
