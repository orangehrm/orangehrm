<?php

/**
 * Model generator field.
 *
 * @package    symfony
 * @subpackage generator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfModelGeneratorConfigurationField.class.php 21908 2009-09-11 12:06:21Z fabien $
 */
class sfModelGeneratorConfigurationField
{
  protected
    $name   = null,
    $config = null;

  /**
   * Constructor.
   *
   * @param string $name   The field name
   * @param array  $config The configuration for this field
   */
  public function __construct($name, $config)
  {
    $this->name = $name;
    $this->config = $config;

    if (isset($this->config['flag']))
    {
      $this->setFlag($this->config['flag']);
      unset($this->config['flag']);
    }
  }

  /**
   * Returns the name of the field.
   *
   * @return string The field name
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Returns the configuration value for a given key.
   *
   * If the key is null, the method returns all the configuration array.
   *
   * @param string  $key     A key string
   * @param mixed   $default The default value if the key does not exist
   * @param Boolean $escaped Whether to escape single quote (false by default)
   *
   * @return mixed The configuration value associated with the key
   */
  public function getConfig($key = null, $default = null, $escaped = false)
  {
    if (null === $key)
    {
      return $this->config;
    }

    $value = sfModelGeneratorConfiguration::getFieldConfigValue($this->config, $key, $default);

    return $escaped ? str_replace("'", "\\'", $value) : $value;
  }

  /**
   * Returns the type of the field.
   *
   * @return string The field type
   */
  public function getType()
  {
    return $this->config['type'];
  }

  /**
   * Returns true if the column maps a database column.
   *
   * @return boolean true if the column maps a database column, false otherwise
   */
  public function isReal()
  {
    return isset($this->config['is_real']) ? $this->config['is_real'] : false;
  }

  /**
   * Returns true if the column is a partial.
   *
   * @return boolean true if the column is a partial, false otherwise
   */
  public function isPartial()
  {
    return isset($this->config['is_partial']) ? $this->config['is_partial'] : false;
  }

  /**
   * Sets or unsets the partial flag.
   *
   * @param Boolean $boolean true if the field is a partial, false otherwise
   */
  public function setPartial($boolean)
  {
    $this->config['is_partial'] = $boolean;
  }

  /**
   * Returns true if the column is a component.
   *
   * @return boolean true if the column is a component, false otherwise
   */
  public function isComponent()
  {
    return isset($this->config['is_component']) ? $this->config['is_component'] : false;
  }

  /**
   * Sets or unsets the component flag.
   *
   * @param Boolean $boolean true if the field is a component, false otherwise
   */
  public function setComponent($boolean)
  {
    $this->config['is_component'] = $boolean;
  }

  /**
   * Returns true if the column has a link.
   *
   * @return boolean true if the column has a link, false otherwise
   */
  public function isLink()
  {
    return isset($this->config['is_link']) ? $this->config['is_link'] : false;
  }

  /**
   * Sets or unsets the link flag.
   *
   * @param Boolean $boolean true if the field is a link, false otherwise
   */
  public function setLink($boolean)
  {
    $this->config['is_link'] = $boolean;
  }

  /**
   * Sets the list renderer for the field.
   *
   * @param mixed $renderer A PHP callable
   */
  public function setRenderer($renderer)
  {
    $this->config['renderer'] = $renderer;
  }

  /**
   * Gets the list renderer for the field.
   *
   * @return mixed A PHP callable
   */
  public function getRenderer()
  {
    return isset($this->config['renderer']) ? $this->config['renderer'] : null;
  }

  /**
   * Sets the list renderer arguments for the field.
   *
   * @param array $arguments An array of arguments to pass to the renderer
   */
  public function setRendererArguments(array $arguments)
  {
    $this->config['renderer_arguments'] = $arguments;
  }

  /**
   * Gets the list renderer arguments for the field.
   *
   * @return array An array of arguments to pass to the renderer
   */
  public function getRendererArguments()
  {
    return isset($this->config['renderer_arguments']) ? $this->config['renderer_arguments'] : array();
  }

  static public function splitFieldWithFlag($field)
  {
    if (in_array($flag = $field[0], array('=', '_', '~')))
    {
      $field = substr($field, 1);
    }
    else
    {
      $flag = null;
    }

    return array($field, $flag);
  }

  /**
   * Sets a flag.
   *
   * The flag can be =, _, or ~.
   *
   * @param string $flag The flag
   */
  public function setFlag($flag)
  {
    if (null === $flag)
    {
      return;
    }

    switch ($flag)
    {
      case '=':
        $this->setLink(true);
        break;
      case '_':
        $this->setPartial(true);
        break;
      case '~':
        $this->setComponent(true);
        break;
      default:
        throw new InvalidArgumentException(sprintf('Flag "%s" does not exist.', $flag));
    }
  }

  /**
   * Gets the flag associated with the field.
   *
   * The flag will be
   *
   *   * = for a link
   *   * _ for a partial
   *   * ~ for a component
   *
   * @return string The flag
   */
  public function getFlag()
  {
    if ($this->isLink())
    {
      return '=';
    }
    else if ($this->isPartial())
    {
      return '_';
    }
    else if ($this->isComponent())
    {
      return '~';
    }

    return '';
  }
}
