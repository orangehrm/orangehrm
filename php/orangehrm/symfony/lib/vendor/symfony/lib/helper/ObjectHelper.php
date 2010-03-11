<?php

require_once dirname(__FILE__).'/FormHelper.php';

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * ObjectHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: ObjectHelper.php 12595 2008-11-03 08:28:49Z dwhittle $
 */

/**
 * Returns a html date control.
 *
 * @param  object $object         An object.
 * @param  string $method         An object column.
 * @param  array  $options        Date options.
 * @param  string $default_value  Date default value.
 *
 * @return string An html string which represents a date control.
 *
 */
function object_input_date_tag($object, $method, $options = array(), $default_value = null)
{
  $options = _parse_attributes($options);

  $value = _get_object_value($object, $method, $default_value, $param = 'Y-m-d G:i');

  return input_date_tag(_convert_method_to_name($method, $options), $value, $options);
}

/**
 * Returns a textarea html tag.
 *
 * @param  object $object         An object.
 * @param  string $method         An object column.
 * @param  array  $options        Textarea options.
 * @param  string $default_value  Textarea default value.
 *
 * @return string An html string which represents a textarea tag.
 *
 */
function object_textarea_tag($object, $method, $options = array(), $default_value = null)
{
  $options = _parse_attributes($options);

  $value = _get_object_value($object, $method, $default_value);

  return textarea_tag(_convert_method_to_name($method, $options), $value, $options);
}

/**
 * Accepts a container of objects, the method name to use for the value,
 * and the method name to use for the display.
 * It returns a string of option tags.
 *
 * NOTE: Only the option tags are returned, you have to wrap this call in a regular HTML select tag.
 */
function objects_for_select($options, $value_method, $text_method = null, $selected = null, $html_options = array())
{
  $select_options = array();
  foreach ($options as $option)
  {
    // text method exists?
    $method_exists = ($text_method == '__toString') ? method_exists($option, $text_method) : is_callable(array($option, $text_method));
    if ($text_method && !$method_exists)
    {
      throw new sfViewException(sprintf('Method "%s" doesn\'t exist for object of class "%s".', $text_method, _get_class_decorated($option)));
    }

    // value method exists?
    $method_exists = ($value_method == '__toString') ? method_exists($option, $value_method) : is_callable(array($option, $value_method));
    if (!$method_exists)
    {
      throw new sfViewException(sprintf('Method "%s" doesn\'t exist for object of class "%s".', $value_method, _get_class_decorated($option)));
    }

    $value = $option->$value_method();
    $key = ($text_method != null) ? $option->$text_method() : $value;

    $select_options[$value] = $key;
  }

  return options_for_select($select_options, $selected, $html_options);
}

/**
 * Returns a list html tag.
 *
 * @param  object $object         An object or the selected value
 * @param  string $method         An object column.
 * @param  array  $options        Input options (related_class option is mandatory).
 * @param  string $default_value  Input default value.
 *
 * @return string A list string which represents an input tag.
 *
 */
function object_select_tag($object, $method, $options = array(), $default_value = null)
{
  $options = _parse_attributes($options);

  $related_class = _get_option($options, 'related_class', false);
  if (false === $related_class && preg_match('/^get(.+?)Id$/', $method, $match))
  {
    $related_class = $match[1];
  }

  $peer_method = _get_option($options, 'peer_method');

  $text_method = _get_option($options, 'text_method');

  $key_method = _get_option($options, 'key_method', 'getPrimaryKey');

  $select_options = _get_options_from_objects(sfContext::getInstance()->retrieveObjects($related_class, $peer_method), $text_method, $key_method);

  if ($value = _get_option($options, 'include_custom'))
  {
    $select_options = array('' => $value) + $select_options;
  }
  else if (_get_option($options, 'include_title'))
  {
    $select_options = array('' => '-- '._convert_method_to_name($method, $options).' --') + $select_options;
  }
  else if (_get_option($options, 'include_blank'))
  {
    $select_options = array('' => '') + $select_options;
  }

  if (is_object($object))
  {
    $value = _get_object_value($object, $method, $default_value);
  }
  else
  {
    $value = $object;
  }

  $option_tags = options_for_select($select_options, $value, $options);

  return select_tag(_convert_method_to_name($method, $options), $option_tags, $options);
}

function _get_options_from_objects($objects, $text_method = null, $key_method = 'getPrimaryKey')
{
  $select_options = array();

  if ($objects)
  {
    // construct select option list
    $first = true;
    foreach ($objects as $tmp_object)
    {
      if ($first)
      {
        // multi primary keys handling
        $multi_primary_keys = is_array($tmp_object->$key_method()) ? true : false;

        // which method to call?
        $methodToCall = '';
        foreach (array($text_method, '__toString', 'toString', $key_method) as $method)
        {
          if (method_exists($tmp_object, $method))
          {
            $methodToCall = $method;
            break;
          }
        }

        $first = false;
      }

      $key   = $multi_primary_keys ? implode('/', $tmp_object->$key_method()) : $tmp_object->$key_method();
      $value = $tmp_object->$methodToCall();

      $select_options[$key] = $value;
    }
  }

  return $select_options;
}

function object_select_country_tag($object, $method, $options = array(), $default_value = null)
{
  $options = _parse_attributes($options);

  $value = _get_object_value($object, $method, $default_value);

  return select_country_tag(_convert_method_to_name($method, $options), $value, $options);
}

function object_select_language_tag($object, $method, $options = array(), $default_value = null)
{
  $options = _parse_attributes($options);

  $value = _get_object_value($object, $method, $default_value);

  return select_language_tag(_convert_method_to_name($method, $options), $value, $options);
}

/**
 * Returns a hidden input html tag.
 *
 * @param  object $object         An object.
 * @param  string $method         An object column.
 * @param  array  $options        Input options.
 * @param  string $default_value  Input default value.
 *
 * @return string An html string which represents a hidden input tag.
 *
 */
function object_input_hidden_tag($object, $method, $options = array(), $default_value = null)
{
  $options = _parse_attributes($options);

  $value = _get_object_value($object, $method, $default_value);

  return input_hidden_tag(_convert_method_to_name($method, $options), $value, $options);
}

/**
 * Returns a input html tag.
 *
 * @param  object $object         An object.
 * @param  string $method         An object column.
 * @param  array  $options        Input options.
 * @param  string $default_value  Input default value.
 *
 * @return string An html string which represents an input tag.
 *
 */
function object_input_tag($object, $method, $options = array(), $default_value = null)
{
  $options = _parse_attributes($options);

  $value = _get_object_value($object, $method, $default_value);

  return input_tag(_convert_method_to_name($method, $options), $value, $options);
}

/**
 * Returns a checkbox html tag.
 *
 * @param  object $object         An object.
 * @param  string $method         An object column.
 * @param  array  $options        Checkbox options.
 * @param  bool   $default_value  Checkbox value.
 *
 * @return string An html string which represents a checkbox tag.
 *
 */
function object_checkbox_tag($object, $method, $options = array(), $default_value = null)
{
  $options = _parse_attributes($options);

  $checked = (boolean) _get_object_value($object, $method, $default_value);

  return checkbox_tag(_convert_method_to_name($method, $options), isset($options['value']) ? $options['value'] : 1, $checked, $options);
}

function _convert_method_to_name($method, &$options)
{
  $name = _get_option($options, 'control_name');

  if (!$name)
  {
    if (is_array($method))
    {
      $name = implode('-',$method[1]);
    }
    else
    {
      $name = sfInflector::underscore($method);
      $name = preg_replace('/^get_?/', '', $name);
    }
  }

  return $name;
}

// returns default_value if object value is null
// method is either a string or: array('method',array('param1','param2'))
function _get_object_value($object, $method, $default_value = null, $param = null)
{
  // compatibility with the array syntax
  if (is_string($method))
  {
    $param = ($param == null ? array() : array($param));
    $method = array($method, $param);
  }

  // method exists?
  if (!is_callable(array($object, $method[0])))
  {
    throw new sfViewException(sprintf('Method "%s" doesn\'t exist for object of class "%s".', $method[0], _get_class_decorated($object)));
  }

  $object_value = call_user_func_array(array($object, $method[0]), $method[1]);

  return ($default_value !== null && $object_value === null) ? $default_value : $object_value;
}

/**
 * Returns the name of the class of an decorated object
 *
 * @param  object $object An object that might be wrapped in an sfOutputEscaperObjectDecorator(-derivative)
 *
 * @return string The name of the class of the object being decorated for escaping, or the class of the object if it isn't decorated
 */
function _get_class_decorated($object)
{
  if ($object instanceof sfOutputEscaperObjectDecorator)
  {
    return sprintf('%s (decorated with %s)', get_class($object->getRawValue()), get_class($object));
  }
  else
  {
    return get_class($object);
  }
}

