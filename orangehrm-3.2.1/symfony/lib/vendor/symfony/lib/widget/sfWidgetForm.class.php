<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetForm is the base class for all form widgets.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetForm.class.php 33388 2012-03-15 15:01:41Z fabien $
 */
abstract class sfWidgetForm extends sfWidget
{
  protected
    $parent   = null;

  /**
   * Constructor.
   *
   * Available options:
   *
   *  * id_format:       The format for the generated HTML id attributes (%s by default)
   *  * is_hidden:       true if the form widget must be hidden, false otherwise (false by default)
   *  * needs_multipart: true if the form widget needs a multipart form, false otherwise (false by default)
   *  * default:         The default value to use when rendering the widget
   *  * label:           The label to use when the widget is rendered by a widget schema
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $this->addOption('id_format', '%s');
    $this->addOption('is_hidden', false);
    $this->addOption('needs_multipart', false);
    $this->addOption('default', null);
    $this->addOption('label', null);

    parent::__construct($options, $attributes);
  }

  /**
   * Sets the default value for the widget.
   *
   * @param string $value The default value
   *
   * @return sfWidget The current widget instance
   */
  public function setDefault($value)
  {
    $this->setOption('default', $value);

    return $this;
  }

  /**
   * Returns the default value for the widget.
   *
   * @return string The default value
   */
  public function getDefault()
  {
    return $this->getOption('default');
  }

  /**
   * Sets the label for the widget.
   *
   * @param string $value The label
   *
   * @return sfWidget The current widget instance
   */
  public function setLabel($value)
  {
    $this->setOption('label', $value);

    return $this;
  }

  /**
   * Returns the label for the widget.
   *
   * @return string The label
   */
  public function getLabel()
  {
    return $this->getOption('label');
  }

  /**
   * Sets the format for HTML id attributes.
   *
   * @param string $format  The format string (must contain a %s for the id placeholder)
   *
   * @return sfWidget The current widget instance
   */
  public function setIdFormat($format)
  {
    $this->setOption('id_format', $format);

    return $this;
  }

  /**
   * Gets the HTML format string for id attributes.
   *
   * @return string The format string
   */
  public function getIdFormat()
  {
    return $this->getOption('id_format');
  }

  /**
   * Returns true if the widget is hidden.
   *
   * @return Boolean true if the widget is hidden, false otherwise
   */
  public function isHidden()
  {
    return $this->getOption('is_hidden');
  }

  /**
   * Sets the hidden flag for the widget.
   *
   * @param bool $boolean  true if the widget must be hidden, false otherwise
   *
   * @return sfWidget The current widget instance
   */
  public function setHidden($boolean)
  {
    $this->setOption('is_hidden', (boolean) $boolean);

    return $this;
  }

  /**
   * Returns true if the widget needs a multipart form.
   *
   * @return bool true if the widget needs a multipart form, false otherwise
   */
  public function needsMultipartForm()
  {
    return $this->getOption('needs_multipart');
  }

  /**
   * Renders a HTML tag.
   *
   * The id attribute is added automatically to the array of attributes if none is specified.
   * If uses for "id_format" option to generate the id.
   *
   * @param  string $tag        The tag name
   * @param  array  $attributes An array of HTML attributes to be merged with the default HTML attributes
   *
   * @return string An HTML tag string
   */
  public function renderTag($tag, $attributes = array())
  {
    if (empty($tag))
    {
      return '';
    }

    $attributes = $this->fixFormId($attributes);

    return parent::renderTag($tag, $attributes);
  }

  /**
   * Renders a HTML content tag.
   *
   * The id attribute is added automatically to the array of attributes if none is specified.
   * If uses for "id_format" option to generate the id.
   *
   * @param  string $tag         The tag name
   * @param  string $content     The content of the tag
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   *
   * @return string An HTML tag string
   */
  public function renderContentTag($tag, $content = null, $attributes = array())
  {
    return parent::renderContentTag($tag, $content, $this->fixFormId($attributes));
  }

  /**
   * Adds an HTML id attributes to the array of attributes if none is given and a name attribute exists.
   *
   * @param  array $attributes  An array of attributes
   *
   * @return array An array of attributes with an id.
   */
  protected function fixFormId($attributes)
  {
    if (!isset($attributes['id']) && isset($attributes['name']))
    {
      $attributes['id'] = $this->generateId($attributes['name'], isset($attributes['value']) ? $attributes['value'] : null);
    }

    return $attributes;
  }

  /**
   * Returns a formatted id based on the field name and optionally on the field value.
   *
   * This method determines the proper form field id name based on the parameters. If a form field has an
   * array value as a name we need to convert them to proper and unique ids like so:
   *
   * <samp>
   *  name[] => name (if value == null)
   *  name[] => name_value (if value != null)
   *  name[bob] => name_bob
   *  name[item][total] => name_item_total
   * </samp>
   *
   * This method also changes all invalid characters to an underscore (_):
   *
   * Ids must begin with a letter ([A-Za-z]) and may be followed by any number of letters, digits
   * ([0-9]), hyphens ("-"), underscores ("_"), colons (":"), and periods (".").
   *
   * @param  string $name   The field name
   * @param  string $value  The field value
   *
   * @return string The field id or null.
   */
  public function generateId($name, $value = null)
  {
    if (false === $this->getOption('id_format'))
    {
      return null;
    }

    // check to see if we have an array variable for a field name
    if (strstr($name, '['))
    {
      $name = str_replace(array('[]', '][', '[', ']'), array((null !== $value ? '_'.$value : ''), '_', '_', ''), $name);
    }

    if (false !== strpos($this->getOption('id_format'), '%s'))
    {
      $name = sprintf($this->getOption('id_format'), $name);
    }

    // remove illegal characters
    $name = preg_replace(array('/^[^A-Za-z]+/', '/[^A-Za-z0-9\:_\.\-]/'), array('', '_'), $name);

    return $name;
  }

  /**
   * Generates a two chars range
   *
   * @param  int  $start
   * @param  int  $stop
   * @return array
   */
  static protected function generateTwoCharsRange($start, $stop)
  {
    $results = array();
    for ($i = $start; $i <= $stop; $i++)
    {
      $results[$i] = sprintf('%02d', $i);
    }
    return $results;
  }

  /**
   * Sets the parent widget schema.
   *
   * @param  sfWidgetFormSchema|null $widgetSchema
   *
   * @return sfWidgetForm The current widget instance
   */
  public function setParent(sfWidgetFormSchema $widgetSchema = null)
  {
    $this->parent = $widgetSchema;

    return $this;
  }

  /**
   * Returns the parent widget schema.
   *
   * If no schema has been set with setWidgetSchema(), NULL is returned.
   *
   * @return sfWidgetFormSchema|null
   */
  public function getParent()
  {
    return $this->parent;
  }

  /**
   * Translates the given text.
   *
   * @param  string $text       The text with optional placeholders
   * @param  array $parameters  The values to replace the placeholders
   *
   * @return string             The translated text
   *
   * @see sfWidgetFormSchemaFormatter::translate()
   */
  protected function translate($text, array $parameters = array())
  {
    if (null === $this->parent)
    {
      return $text;
    }
    else
    {
      return $this->parent->getFormFormatter()->translate($text, $parameters);
    }
  }

  /**
   * Translates all values of the given array.
   *
   * @param  array $texts       The texts with optional placeholders
   * @param  array $parameters  The values to replace the placeholders
   *
   * @return array              The translated texts
   * 
   * @see sfWidgetFormSchemaFormatter::translate()
   */
  protected function translateAll(array $texts, array $parameters = array())
  {
    if (null === $this->parent)
    {
      return $texts;
    }
    else
    {
      $result = array();

      foreach ($texts as $key => $text)
      {
        $result[$key] = $this->parent->getFormFormatter()->translate($text, $parameters);
      }

      return $result;
    }
  }
}
