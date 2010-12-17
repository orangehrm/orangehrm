<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004 David Heinemeier Hansson
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * JavascriptBaseHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     John Christopher <john.christopher@symfony-project.com>
 * @author     David Heinemeier Hansson
 * @author     Fabian Lange <fabian.lange@symfony-project.com>
 * @version    SVN: $Id: JavascriptBaseHelper.php 24499 2009-11-28 14:12:02Z FabianLange $
 */

/*
 * Provides a set basic of helpers for calling JavaScript functions.
 */

/**
 * Returns a link that will trigger a javascript function using the
 * onclick handler and return false after the fact.
 *
 * Examples:
 *   <?php echo link_to_function('Greeting', "alert('Hello world!')") ?>
 *   <?php echo link_to_function(image_tag('delete'), "do_delete()", array('confirm' => 'Really?')) ?>
 */
function link_to_function($name, $function, $html_options = array())
{
  $html_options = _parse_attributes($html_options);

  $html_options['href'] = isset($html_options['href']) ? $html_options['href'] : '#';
  if ( isset($html_options['confirm']) )
  {
    $confirm = escape_javascript($html_options['confirm']);
    unset($html_options['confirm']);
    $function = "if(window.confirm('$confirm')){ $function;}";
  }
  $html_options['onclick'] = $function.'; return false;';

  return content_tag('a', $name, $html_options);
}

/**
 * Returns a button that will trigger a javascript function using the
 * onclick handler and return false after the fact.
 *
 * Examples:
 *   <?php echo button_to_function('Greeting', "alert('Hello world!')") ?>
 */
function button_to_function($name, $function, $html_options = array())
{
  $html_options = _parse_attributes($html_options);

  $html_options['onclick'] = $function.'; return false;';
  $html_options['type']    = 'button';
  $html_options['value']   = $name;

  return tag('input', $html_options);
}

/**
 * Returns a JavaScript tag with the '$content' inside. If no content is passed, it works as the slot() method and will output everythin between
 * javascript_tag() and end_javascript_tag(),
 * Example:
 *   <?php echo javascript_tag("alert('All is good')") ?>
 *   => <script type="text/javascript">alert('All is good')</script>
 *   <?php javascript_tag() ?>alert('All is good')<?php end_javascript_tag() ?>
 */
function javascript_tag($content = null)
{
  if (null !== $content)
  {
    return content_tag('script', javascript_cdata_section($content), array('type' => 'text/javascript'));
  }
  else
  {
    ob_start();
  }
}

function end_javascript_tag()
{
  echo javascript_tag(ob_get_clean());
}

function javascript_cdata_section($content)
{
  return "\n//".cdata_section("\n$content\n//")."\n";
}

/**
 * Mark the start of a block that should only be shown in the browser if JavaScript
 * is switched on.
 */
function if_javascript()
{
  if (!sfContext::getInstance()->getRequest()->isXmlHttpRequest())
  {
    ob_start();
  }
}

/**
 * Mark the end of a block that should only be shown in the browser if JavaScript
 * is switched on.
 */
function end_if_javascript()
{
  if (!sfContext::getInstance()->getRequest()->isXmlHttpRequest())
  {
    $content = ob_get_clean();
    echo javascript_tag("document.write('" . esc_js_no_entities($content) . "');");
  }
}

/**
 * converts the given PHP array or string to the corresponding javascript array or string.
 * javascript strings need to be single quoted.
 *
 * @param option (typically from option array)
 * @return string javascript string or array equivalent
 */
function array_or_string_for_javascript($option)
{
  if (is_array($option))
  {
    return "['".join('\',\'', $option)."']";
  }
  else if (is_string($option) && $option[0] != "'")
  {
    return "'$option'";
  }
  return $option;
}

/**
* converts the the PHP options array into a javscript array
 *
 * @param array
 * @return string javascript arry equivalent
*/
function options_for_javascript($options)
{
  $opts = array();
  foreach ($options as $key => $value)
  {
    if (is_array($value))
    {
     $value = options_for_javascript($value);
    }
    $opts[] = $key.":".boolean_for_javascript($value);
  }
  sort($opts);

  return '{'.join(', ', $opts).'}';
}

/**
 * converts the given PHP boolean to the corresponding javascript boolean.
 * booleans need to be true or false (php would print 1 or nothing).
 *
 * @param bool (typically from option array)
 * @return string javascript boolean equivalent
 */
function boolean_for_javascript($bool)
{
  if (is_bool($bool))
  {
    return ($bool===true ? 'true' : 'false');
  }
  return $bool;
}
