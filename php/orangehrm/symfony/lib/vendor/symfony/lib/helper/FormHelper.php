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
 * FormHelper.
 *
 * @package    symfony
 * @subpackage helper
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     David Heinemeier Hansson
 * @version    SVN: $Id: FormHelper.php 17858 2009-05-01 21:22:50Z FabianLange $
 */

/**
 * Returns a formatted set of <option> tags based on optional <i>$options</i> array variable.
 *
 * The options_for_select helper is usually called in conjunction with the select_tag helper, as it is relatively
 * useless on its own. By passing an array of <i>$options</i>, the helper will automatically generate <option> tags
 * using the array key as the value and the array value as the display title. Additionally the options_for_select tag is
 * smart enough to detect nested arrays as <optgroup> tags.  If the helper detects that the array value is an array itself,
 * it creates an <optgroup> tag with the name of the group being the key and the contents of the <optgroup> being the array.
 *
 * <b>Options:</b>
 * - include_blank  - Includes a blank <option> tag at the beginning of the string with an empty value
 * - include_custom - Includes an <option> tag with a custom display title at the beginning of the string with an empty value
 *
 * <b>Examples:</b>
 * <code>
 *  echo select_tag('person', options_for_select(array(1 => 'Larry', 2 => 'Moe', 3 => 'Curly')));
 * </code>
 *
 * <code>
 *  $card_list = array('VISA' => 'Visa', 'MAST' => 'MasterCard', 'AMEX' => 'American Express', 'DISC' => 'Discover');
 *  echo select_tag('cc_type', options_for_select($card_list, 'AMEX', array('include_custom' => '-- Select Credit Card Type --')));
 * </code>
 *
 * <code>
 *  $optgroup_array = array(1 => 'Joe', 2 => 'Sue', 'Group A' => array(3 => 'Mary', 4 => 'Tom'), 'Group B' => array(5 => 'Bill', 6 =>'Andy'));
 *  echo select_tag('employee', options_for_select($optgroup_array, null, array('include_blank' => true)), array('class' => 'mystyle'));
 * </code>
 *
 * @param array  $options      dataset to create <option> tags and <optgroup> tags from
 * @param string $selected     selected option value
 * @param array  $html_options additional HTML compliant <option> tag parameters
 *
 * @return string populated with <option> tags derived from the <i>$options</i> array variable
 * @see select_tag
 */
function options_for_select($options = array(), $selected = '', $html_options = array())
{
  $html_options = _parse_attributes($html_options);

  if (!is_array($selected))
  {
    $selected = array($selected);
  }

  $selected = array_map('strval', array_values($selected));
  $selected_set = array_flip($selected);

  $html = '';

  if ($value = _get_option($html_options, 'include_custom'))
  {
    $html .= content_tag('option', $value, array('value' => ''))."\n";
  }
  else if (_get_option($html_options, 'include_blank'))
  {
    $html .= content_tag('option', '', array('value' => ''))."\n";
  }

  foreach ($options as $key => $value)
  {
    if (is_array($value) || $value instanceof sfOutputEscaperArrayDecorator)
    {
      $html .= content_tag('optgroup', options_for_select($value, $selected, $html_options), array('label' => $key))."\n";
    }
    else
    {
      $option_options = array('value' => $key);

      if (isset($selected_set[strval($key)])) {
        $option_options['selected'] = 'selected';
      }

      $html .= content_tag('option', $value, $option_options)."\n";
    }
  }

  return $html;
}

/**
 * Returns an HTML <form> tag that points to a valid action, route or URL as defined by <i>$url_for_options</i>.
 *
 * By default, the form tag is generated in POST format, but can easily be configured along with any additional
 * HTML parameters via the optional <i>$options</i> parameter. If you are using file uploads, be sure to set the 
 * <i>multipart</i> option to true.
 *
 * <b>Options:</b>
 * - multipart - When set to true, enctype is set to "multipart/form-data".
 *
 * <b>Examples:</b>
 *   <code><?php echo form_tag('@myroute'); ?></code>
 *   <code><?php echo form_tag('/module/action', array('name' => 'myformname', 'multipart' => true)); ?></code>
 *
 * @param  string $url_for_options  valid action, route or URL
 * @param  array  $options          optional HTML parameters for the <form> tag
 *
 * @return string opening HTML <form> tag with options
 */
function form_tag($url_for_options = '', $options = array())
{
  $options = _parse_attributes($options);

  $html_options = $options;

  $html_options['method'] = isset($html_options['method']) ? strtolower($html_options['method']) : 'post';

  if (_get_option($html_options, 'multipart'))
  {
    $html_options['enctype'] = 'multipart/form-data';
  }

  $html_options['action'] = url_for($url_for_options);

  $html = '';
  if (!in_array($html_options['method'], array('get', 'post')))
  {
    $html = tag('input', array('type' => 'hidden', 'name' => 'sf_method', 'value' => $html_options['method']));
    $html_options['method'] = 'post';
  }

  return tag('form', $html_options, true).$html;
}

/**
 * Returns a <select> tag, optionally comprised of <option> tags.
 *
 * The select tag does not generate <option> tags by default.  
 * To do so, you must populate the <i>$option_tags</i> parameter with a string of valid HTML compliant <option> tags.
 * Fortunately, Symfony provides a handy helper function to convert an array of data into option tags (see options_for_select). 
 * If you need to create a "multiple" select tag (ability to select multiple options), set the <i>multiple</i> option to true.  
 * Doing so will automatically convert the name field to an array type variable (i.e. name="name" becomes name="name[]").
 * 
 * <b>Options:</b>
 * - multiple - If set to true, the select tag will allow multiple options to be selected at once.
 *
 * <b>Examples:</b>
 * <code>
 *  $person_list = array(1 => 'Larry', 2 => 'Moe', 3 => 'Curly');
 *  echo select_tag('person', options_for_select($person_list, $sf_params->get('person')), array('class' => 'full'));
 * </code>
 *
 * <code>
 *  echo select_tag('department', options_for_select($department_list), array('multiple' => true));
 * </code>
 *
 * <code>
 *  echo select_tag('url', options_for_select($url_list), array('onChange' => 'Javascript:this.form.submit();'));
 * </code>
 *
 * @param  string $name         field name 
 * @param  mixed  $option_tags  contains a string of valid <option></option> tags, or an array of options that will be passed to options_for_select
 * @param  array  $options      additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag optionally comprised of <option> tags.
 * @see options_for_select, content_tag
 */
function select_tag($name, $option_tags = null, $options = array())
{
  $options = _convert_options($options);
  $id = $name;
  if (isset($options['multiple']) && $options['multiple'] && substr($name, -2) !== '[]')
  {
    $name .= '[]';
  }
  if (is_array($option_tags))
  {
    $option_tags = options_for_select($option_tags);
  }

  return content_tag('select', $option_tags, array_merge(array('name' => $name, 'id' => get_id_from_name($id)), $options));
}

/**
 * Returns a <select> tag populated with all the countries in the world.
 *
 * The select_country_tag builds off the traditional select_tag function, and is conveniently populated with 
 * all the countries in the world (sorted alphabetically). Each option in the list has a two-character country 
 * code for its value and the country's name as its display title.  The country data is retrieved via the sfCultureInfo
 * class, which stores a wide variety of i18n and i10n settings for various countries and cultures throughout the world.
 * Here's an example of an <option> tag generated by the select_country_tag:
 *
 * <samp>
 *  <option value="US">United States</option>
 * </samp>
 *
 * <b>Examples:</b>
 * <code>
 *  echo select_country_tag('country', 'FR');
 * </code>
 * <code>
 *  echo select_country_tag('country', 'de', array('countries' => array('US','FR')));
 * </code>
 *
 * @param  string $name      field name 
 * @param  string $selected  selected field value (two-character country code)
 * @param  array  $options   additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with all the countries in the world.
 * @see select_tag, options_for_select, sfCultureInfo
 */
function select_country_tag($name, $selected = null, $options = array())
{
  $c = sfCultureInfo::getInstance(sfContext::getInstance()->getUser()->getCulture());
  $countries = $c->getCountries();

  if ($country_option = _get_option($options, 'countries'))
  {
    $countries = array_intersect_key($countries, array_flip($country_option)); 
  }

  asort($countries);

  $option_tags = options_for_select($countries, $selected, $options);
  unset($options['include_blank'], $options['include_custom']);

  return select_tag($name, $option_tags, $options);
}

/**
 * Returns a <select> tag populated with all the languages in the world (or almost).
 *
 * The select_language_tag builds off the traditional select_tag function, and is conveniently populated with 
 * all the languages in the world (sorted alphabetically). Each option in the list has a two or three character 
 * language/culture code for its value and the language's name as its display title.  The country data is 
 * retrieved via the sfCultureInfo class, which stores a wide variety of i18n and i10n settings for various 
 * countries and cultures throughout the world. Here's an example of an <option> tag generated by the select_language_tag:
 *
 * <samp>
 *  <option value="en">English</option>
 * </samp>
 *
 * <b>Examples:</b>
 * <code>
 *  echo select_language_tag('language', 'de');
 * </code>
 * <code>
 *  echo select_language_tag('language', 'de', array('languages' => array('en','fr','fi')));
 * </code>
 *
 * @param  string $name      field name 
 * @param  string $selected  selected field value (two or threecharacter language/culture code)
 * @param  array  $options   additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with all the languages in the world.
 * @see select_tag, options_for_select, sfCultureInfo
 */
function select_language_tag($name, $selected = null, $options = array())
{
  $c = sfCultureInfo::getInstance(sfContext::getInstance()->getUser()->getCulture());
  $languages = $c->getLanguages();

  if ($language_option = _get_option($options, 'languages'))
  {
    $languages = array_intersect_key($languages, array_flip($language_option)); 
  }

  asort($languages);

  $option_tags = options_for_select($languages, $selected, $options);
  unset($options['include_blank'], $options['include_custom']);

  return select_tag($name, $option_tags, $options);
}

/**
 * Returns a <select> tag populated with all the currencies in the world (or almost).
 *
 * The select_currency_tag builds off the traditional select_tag function, and is conveniently populated with 
 * all the currencies in the world (sorted alphabetically). Each option in the list has a three character 
 * currency code for its value and the currency's name as its display title.  The currency data is 
 * retrieved via the sfCultureInfo class, which stores a wide variety of i18n and i10n settings for various 
 * countries and cultures throughout the world. Here's an example of an <option> tag generated by the select_currency_tag:
 *
 * <samp>
 *  <option value="EUR">Euro</option>
 * </samp>
 *
 * <b>Examples:</b>
 * <code>
 *  echo select_currency_tag('currency', 'EUR');
 * </code>
 * <code>
 *  echo select_currency_tag('currency', 'EUR', array('currencies' => array('EUR', 'USD'), 'display' => 'symbol'));
 * </code>
 *
 * @param  string $name      field name 
 * @param  string $selected  selected field value (threecharacter currency code)
 * @param  array  $options   additional HTML compliant <select> tag parameters
 *
 * @return string <select> tag populated with all the currencies in the world.
 * @see select_tag, options_for_select, sfCultureInfo
 */
function select_currency_tag($name, $selected = null, $options = array())
{
  $c = sfCultureInfo::getInstance(sfContext::getInstance()->getUser()->getCulture());
  $currencies = $c->getCurrencies(null, true);

  $currency_option = _get_option($options, 'currencies');
  if ($currency_option)
  {
      $currencies = array_intersect_key($currencies, array_flip($currency_option)); 
  }

  $display_option = _get_option($options, 'display');

  foreach ($currencies as $key => $value)
  {
    switch ($display_option)
    {
      case 'symbol' : $currencies[$key] = $value[0];          break;
      case 'code'   : $currencies[$key] = $key;               break;
      default       : $currencies[$key] = ucfirst($value[1]); break;
    }
  }

  asort($currencies);

  $option_tags = options_for_select($currencies, $selected, $options);
  unset($options['include_blank'], $options['include_custom']);

  return select_tag($name, $option_tags, $options);
}

/**
 * Returns an XHTML compliant <input> tag with type="text".
 *
 * The input_tag helper generates your basic XHTML <input> tag and can utilize any standard <input> tag parameters 
 * passed in the optional <i>$options</i> parameter.
 *
 * <b>Examples:</b>
 * <code>
 *  echo input_tag('name');
 * </code>
 *
 * <code>
 *  echo input_tag('amount', $sf_params->get('amount'), array('size' => 8, 'maxlength' => 8));
 * </code>
 *
 * @param  string $name     field name 
 * @param  string $value    selected field value
 * @param  array  $options  additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with type="text"
 */
function input_tag($name, $value = null, $options = array())
{
  return tag('input', array_merge(array('type' => 'text', 'name' => $name, 'id' => get_id_from_name($name, $value), 'value' => $value), _convert_options($options)));
}

/**
 * Returns an XHTML compliant <input> tag with type="hidden".
 *
 * Similar to the input_tag helper, the input_hidden_tag helper generates an XHTML <input> tag and can utilize 
 * any standard <input> tag parameters passed in the optional <i>$options</i> parameter.  The only difference is 
 * that it creates the tag with type="hidden", meaning that is not visible on the page.
 *
 * <b>Examples:</b>
 * <code>
 *  echo input_hidden_tag('id', $id);
 * </code>
 *
 * @param  string $name     field name 
 * @param  string $value    populated field value
 * @param  array  $options  additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with type="hidden"
 */
function input_hidden_tag($name, $value = null, $options = array())
{
  $options = _parse_attributes($options);

  $options['type'] = 'hidden';
  return input_tag($name, $value, $options);
}

/**
 * Returns an XHTML compliant <input> tag with type="file".
 *
 * Similar to the input_tag helper, the input_hidden_tag helper generates your basic XHTML <input> tag and can utilize
 * any standard <input> tag parameters passed in the optional <i>$options</i> parameter.  The only difference is that it 
 * creates the tag with type="file", meaning that next to the field will be a "browse" (or similar) button. 
 * This gives the user the ability to choose a file from there computer to upload to the web server.  Remember, if you 
 * plan to upload files to your website, be sure to set the <i>multipart</i> option form_tag helper function to true 
 * or your files will not be properly uploaded to the web server.
 *
 * <b>Examples:</b>
 * <code>
 *  echo input_file_tag('filename', array('size' => 30));
 * </code>
 *
 * @param string $name    field name 
 * @param array  $options additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with type="file"
 * @see input_tag, form_tag
 */
function input_file_tag($name, $options = array())
{
  $options = _parse_attributes($options);

  $options['type'] = 'file';
  return input_tag($name, null, $options);
}

/**
 * Returns an XHTML compliant <input> tag with type="password".
 *
 * Similar to the input_tag helper, the input_hidden_tag helper generates your basic XHTML <input> tag and can utilize
 * any standard <input> tag parameters passed in the optional <i>$options</i> parameter.  The only difference is that it 
 * creates the tag with type="password", meaning that the text entered into this field will not be visible to the end user.
 * In most cases it is replaced by  * * * * * * * *.  Even though this text is not readable, it is recommended that you do not 
 * populate the optional <i>$value</i> option with a plain-text password or any other sensitive information, as this is a 
 * potential security risk.
 *
 * <b>Examples:</b>
 * <code>
 *  echo input_password_tag('password');
 *  echo input_password_tag('password_confirm');
 * </code>
 *
 * @param string $name    field name
 * @param string $value   populated field value
 * @param array  $options additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with type="password"
 * @see input_tag
 */
function input_password_tag($name = 'password', $value = null, $options = array())
{
  $options = _parse_attributes($options);

  $options['type'] = 'password';
  return input_tag($name, $value, $options);
}

/**
 * Returns a <textarea> tag, optionally wrapped with an inline rich-text JavaScript editor.
 *
 * The texarea_tag helper generates a standard HTML <textarea> tag and can be manipulated with
 * any number of standard HTML parameters via the <i>$options</i> array variable.  However, the 
 * textarea tag also has the unique capability of being transformed into a WYSIWYG rich-text editor
 * such as TinyMCE (http://tinymce.moxiecode.com) very easily with the use of some specific options:
 *
 * <b>Options:</b>
 *  - rich: A rich text editor class (for example sfRichTextEditorTinyMCE for TinyMCE).
 *
 * <b>Examples:</b>
 * <code>
 *  echo textarea_tag('notes');
 * </code>
 *
 * <code>
 *  echo textarea_tag('description', 'This is a description', array('rows' => 10, 'cols' => 50));
 * </code> 
 *
 * @param  string $name     field name
 * @param  string $content  populated field value
 * @param  array  $options  additional HTML compliant <textarea> tag parameters
 *
 * @return string <textarea> tag optionally wrapped with a rich-text WYSIWYG editor
 */
function textarea_tag($name, $content = null, $options = array())
{
  $options = _parse_attributes($options);

  if ($size = _get_option($options, 'size'))
  {
    list($options['cols'], $options['rows']) = explode('x', $size, 2);
  }

  // rich control?
  if ($rich = _get_option($options, 'rich', false))
  {
    if (true === $rich)
    {
      $rich = sfConfig::get('sf_rich_text_editor_class', 'TinyMCE');
    }

    // switch for backward compatibility
    switch ($rich)
    {
      case 'tinymce':
        $rich = 'TinyMCE';
        break;
      case 'fck':
        $rich = 'FCK';
        break;
    }

    $editorClass = 'sfRichTextEditor'.$rich;

    if (!class_exists($editorClass))
    {
      throw new sfConfigurationException(sprintf('The rich text editor "%s" does not exist.', $editorClass));
    }

    $sfEditor = new $editorClass();
    if (!in_array('sfRichTextEditor', class_parents($sfEditor)))
    {
      throw new sfConfigurationException(sprintf('The editor "%s" must extend sfRichTextEditor.', $editorClass));
    }
    $sfEditor->initialize($name, $content, $options);

    return $sfEditor->toHTML();
  }

  return content_tag('textarea', escape_once((is_object($content)) ? $content->__toString() : $content), array_merge(array('name' => $name, 'id' => get_id_from_name(_get_option($options, 'id', $name), null)), _convert_options($options)));
}

/**
 * Returns an XHTML compliant <input> tag with type="checkbox".
 *
 * When creating multiple checkboxes with the same name, be sure to use an array for the
 * <i>$name</i> parameter (i.e. 'name[]').  The checkbox_tag is smart enough to create unique ID's
 * based on the <i>$value</i> parameter like so:
 *
 * <samp>
 *  <input type="checkbox" name="status[]" id="status_3" value="3" />
 *  <input type="checkbox" name="status[]" id="status_4" value="4" />
 * </samp>
 * 
 * <b>Examples:</b>
 * <code>
 *  echo checkbox_tag('newsletter', 1, $sf_params->get('newsletter'));
 * </code>
 *
 * <code>
 *  echo checkbox_tag('option_a', 'yes', true, array('class' => 'style_a'));
 * </code>
 *
 * <code>
 *  // one request variable with an array of checkbox values
 *  echo checkbox_tag('choice[]', 1);
 *  echo checkbox_tag('choice[]', 2);
 *  echo checkbox_tag('choice[]', 3);
 *  echo checkbox_tag('choice[]', 4); 
 * </code>
 *
 * <code>
 *  // assuming you have Prototype.js enabled, you could do this
 *  echo checkbox_tag('show_tos', 1, false, array('onclick' => "Element.toggle('tos'); return false;"));
 * </code>
 *
 * @param  string $name     field name 
 * @param  string $value    checkbox value (if checked)
 * @param  bool   $checked  is the checkbox checked? (1 or 0)
 * @param  array  $options  additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with type="checkbox"
 */
function checkbox_tag($name, $value = '1', $checked = false, $options = array())
{
  $html_options = array_merge(array('type' => 'checkbox', 'name' => $name, 'id' => get_id_from_name($name, $value), 'value' => $value), _convert_options($options));

  if ($checked)
  {
    $html_options['checked'] = 'checked';
  }

  return tag('input', $html_options);
}

/**
 * Returns an XHTML compliant <input> tag with type="radio".
 *
 * <b>Examples:</b>
 * <code>
 *  echo ' Yes '.radiobutton_tag('newsletter', 1);
 *  echo ' No '.radiobutton_tag('newsletter', 0); 
 * </code>
 *
 * @param  string $name     field name 
 * @param  string $value    radio button value (if selected)
 * @param  bool   $checked  is the radio button selected? (1 or 0)
 * @param  array  $options  additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with type="radio"
 */
function radiobutton_tag($name, $value, $checked = false, $options = array())
{
  $html_options = array_merge(array('type' => 'radio', 'name' => $name, 'id' => get_id_from_name($name.'[]', $value), 'value' => $value), _convert_options($options));

  if ($checked)
  {
    $html_options['checked'] = 'checked';
  }

  return tag('input', $html_options);
}

/**
 * Returns two XHTML compliant <input> tags to be used as a free-text date fields for a date range.
 * 
 * Built on the input_date_tag, the input_date_range_tag combines two input tags that allow the user
 * to specify a from and to date.  
 * You can easily implement a JavaScript calendar by enabling the 'rich' option in the 
 * <i>$options</i> parameter.  This includes a button next to the field that when clicked, 
 * will open an inline JavaScript calendar.  When a date is selected, it will automatically
 * populate the <input> tag with the proper date, formatted to the user's culture setting.
 *
 * <b>Note:</b> The <i>$name</i> parameter will automatically converted to array names. 
 * For example, a <i>$name</i> of "date" becomes date[from] and date[to]
 * 
 * <b>Options:</b>
 * - rich - If set to true, includes an inline JavaScript calendar can auto-populate the date field with the chosen date
 * - before - string to be displayed before the input_date_range_tag
 * - middle - string to be displayed between the from and to tags
 * - after - string to be displayed after the input_date_range_tag
 *
 * <b>Examples:</b>
 * <code>
 *  $date = array('from' => '2006-05-15', 'to' => '2006-06-15');
 *  echo input_date_range_tag('date', $date, array('rich' => true));
 * </code>
 *
 * <code>
 *  echo input_date_range_tag('date', null, array('middle' => ' through ', 'rich' => true));
 * </code>
 *
 * @param  string $name     field name 
 * @param  array  $value    dates: $value['from'] and $value['to']
 * @param  array  $options  additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with optional JS calendar integration
 * @see input_date_tag
 */
function input_date_range_tag($name, $value, $options = array())
{
  $options = _parse_attributes($options);

  $before = _get_option($options, 'before', '');
  $middle = _get_option($options, 'middle', '');
  $after  = _get_option($options, 'after', '');

  return $before.
         input_date_tag($name.'[from]', isset($value['from']) ? $value['from'] : null, $options).
         $middle.
         input_date_tag($name.'[to]',   isset($value['to'])   ? $value['to']   : null, $options).
         $after;
}

/**
 * Returns an XHTML compliant <input> tag to be used as a free-text date field.
 * 
 * You can easily implement a JavaScript calendar by enabling the 'rich' option in the 
 * <i>$options</i> parameter.  This includes a button next to the field that when clicked, 
 * will open an inline JavaScript calendar.  When a date is selected, it will automatically
 * populate the <input> tag with the proper date, formatted to the user's culture setting. 
 * Symfony also conveniently offers the input_date_range_tag, that allows you to specify a to
 * and from date.
 *
 * <b>Options:</b>
 * - rich - If set to true, includes an inline JavaScript calendar can auto-populate the date field with the chosen date
 *
 * <b>Examples:</b>
 * <code>
 *  echo input_date_tag('date', null, array('rich' => true));
 * </code>
 *
 * @param string $name    field name 
 * @param string $value   date
 * @param array  $options additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with optional JS calendar integration
 * @see input_date_range_tag
 */
function input_date_tag($name, $value = null, $options = array())
{
  $options = _parse_attributes($options);

  $context = sfContext::getInstance();

  $culture = _get_option($options, 'culture', $context->getUser()->getCulture());

  $withTime = _get_option($options, 'withtime', false);

  // rich control?
  if (!_get_option($options, 'rich', false))
  {
    require_once dirname(__FILE__).'/DateFormHelper.php';

    // set culture for month tag
    $options['culture'] = $culture;

    if ($withTime)
    {
      return select_datetime_tag($name, $value, $options, isset($options['html']) ? $options['html'] : array());
    }
    else
    {
      return select_date_tag($name, $value, $options, isset($options['html']) ? $options['html'] : array());
    }
  }

  $pattern = _get_option($options, 'format', $withTime ? 'g' : 'd');

  $dateFormat = new sfDateFormat($culture);

  $pattern = $dateFormat->getInputPattern($pattern);

  // parse date
  if ($value === null || $value === '')
  {
    $value = '';
  }
  else
  {
    $value = $dateFormat->format($value, $pattern);
  }

  // register our javascripts and stylesheets
  $langFile = sfConfig::get('sf_calendar_web_dir').'/lang/calendar-'.$culture;
  if ((!is_readable(sfConfig::get('sf_web_dir').'/'.$langFile.'.js')) &&
     (!is_readable(sfConfig::get('sf_symfony_lib_dir').'/../data/web/'.$langFile.'.js')) &&
     (!is_readable(sfConfig::get('sf_symfony_lib_dir').'/../data/symfony/web/'.$langFile.'.js')))
  {
   $langFile = sfConfig::get('sf_calendar_web_dir').'/lang/calendar-'.substr($culture,0,2);
   if ((!is_readable(sfConfig::get('sf_web_dir').'/'.$langFile.'.js')) &&
      (!is_readable(sfConfig::get('sf_symfony_lib_dir').'/../data/web/'.$langFile.'.js')) &&
      (!is_readable(sfConfig::get('sf_symfony_lib_dir').'/../data/symfony/web/'.$langFile.'.js')))
   {
     $langFile = sfConfig::get('sf_calendar_web_dir').'/lang/calendar-en';
   }
  }

  $jss = array(
    sfConfig::get('sf_calendar_web_dir').'/calendar',
    $langFile,
    sfConfig::get('sf_calendar_web_dir').'/calendar-setup',
  );
  foreach ($jss as $js)
  {
    $context->getResponse()->addJavascript($js);
  }

  // css
  if ($calendar_style = _get_option($options, 'css', 'skins/aqua/theme'))
  {
    $context->getResponse()->addStylesheet(sfConfig::get('sf_calendar_web_dir').'/'.$calendar_style);
  }

  // date format
  $date_format = $dateFormat->getPattern($pattern);

  // calendar date format
  $calendar_date_format = $date_format;
  $calendar_date_format = strtr($date_format, array('yyyy' => 'Y', 'yy'=>'y', 'MM' => 'm', 'M'=>'m', 'dd'=>'d', 'd'=>'e', 'HH'=>'H', 'H'=>'k', 'hh'=>'I', 'h'=>'l', 'mm'=>'M', 'ss'=>'S', 'a'=>'p'));

  $calendar_date_format = preg_replace('/([mdyhklspe])+/i', '%\\1', $calendar_date_format);

  $id_inputField = isset($options['id']) ? $options['id'] : get_id_from_name($name);
  $id_calendarButton = 'trigger_'.$id_inputField;
  $js = '
    document.getElementById("'.$id_calendarButton.'").disabled = false;
    Calendar.setup({
      inputField : "'.$id_inputField.'",
      ifFormat : "'.$calendar_date_format.'",
      daFormat : "'.$calendar_date_format.'",
      button : "'.$id_calendarButton.'"';
  
  if ($withTime)
  {
    $js .= ",\n showsTime : true";
  }

  // calendar options
  if ($calendar_options = _get_option($options, 'calendar_options'))
  {
    $js .= ",\n".$calendar_options;
  }

  $js .= '
    });
  ';

  // calendar button
  $calendar_button = '...';
  $calendar_button_type = 'txt';
  if ($calendar_button_img = _get_option($options, 'calendar_button_img'))
  {
    $calendar_button = $calendar_button_img;
    $calendar_button_type = 'img';
  }
  else if ($calendar_button_txt = _get_option($options, 'calendar_button_txt'))
  {
    $calendar_button = $calendar_button_txt;
    $calendar_button_type = 'txt';
  }

  // construct html
  if (!isset($options['size']))
  {
    // educated guess about the size
    $options['size'] = strlen($date_format)+2;
  }
  $html = input_tag($name, $value, $options);

  if ($calendar_button_type == 'img')
  {
    $html .= image_tag($calendar_button, array('id' => $id_calendarButton, 'style' => 'cursor: pointer; vertical-align: middle'));
  }
  else
  {
    $html .= content_tag('button', $calendar_button, array('type' => 'button', 'disabled' => 'disabled', 'onclick' => 'return false', 'id' => $id_calendarButton));
  }

  if (_get_option($options, 'with_format'))
  {
    $html .= '('.$date_format.')';
  }

  // add javascript
  $html .= content_tag('script', $js, array('type' => 'text/javascript'));

  return $html;
}

/**
 * Returns an XHTML compliant <input> tag with type="submit".
 * 
 * By default, this helper creates a submit tag with a name of <em>commit</em> to avoid
 * conflicts with other parts of the framework.  It is recommended that you do not use the name
 * "submit" for submit tags unless absolutely necessary. Also, the default <i>$value</i> parameter
 * (title of the button) is set to "Save changes", which can be easily overwritten by passing a 
 * <i>$value</i> parameter.
 *
 * <b>Examples:</b>
 * <code>
 *  echo submit_tag();
 * </code>
 *
 * <code>
 *  echo submit_tag('Update Record');
 * </code>
 *
 * @param string $name    field value (title of submit button)
 * @param array  $options additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with type="submit"
 */
function submit_tag($value = 'Save changes', $options = array())
{
  return tag('input', array_merge(array('type' => 'submit', 'name' => 'commit', 'value' => $value), _convert_options_to_javascript(_convert_options($options))));
}

/**
 * Returns an XHTML compliant <input> tag with type="reset".
 *
 * By default, this helper creates a submit tag with a name of <em>reset</em>.  Also, the default 
 * <i>$value</i> parameter (title of the button) is set to "Reset" which can be easily overwritten 
 * by passing a <i>$value</i> parameter.
 *
 * <b>Examples:</b>
 * <code>
 *  echo reset_tag();
 * </code>
 *
 * <code>
 *  echo reset_tag('Start Over');
 * </code>
 *
 * @param string $name    field value (title of reset button)
 * @param array  $options additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with type="reset"
 */
function reset_tag($value = 'Reset', $options = array())
{
  return tag('input', array_merge(array('type' => 'reset', 'name' => 'reset', 'value' => $value), _convert_options($options)));
}

/**
 * Returns an XHTML compliant <input> tag with type="image".
 *
 * The submit_image_tag is very similar to the submit_tag, the only difference being that it uses an image
 * for the submit button instead of the browser-generated default button. The image is defined by the 
 * <i>$source</i> parameter and must be a valid image, either local or remote (URL). By default, this 
 * helper creates a submit tag with a name of <em>commit</em> to avoid conflicts with other parts of the 
 * framework.  It is recommended that you do not use the name "submit" for submit tags unless absolutely necessary.
 *
 * <b>Examples:</b>
 * <code>
 *  // Assuming your image is in the /web/images/ directory
 *  echo submit_image_tag('my_submit_button.gif');
 * </code>
 *
 * <code>
 *  echo submit_image_tag('http://mydomain.com/my_submit_button.gif');
 * </code>
 *
 * @param string $source  path to image file
 * @param array  $options additional HTML compliant <input> tag parameters
 *
 * @return string XHTML compliant <input> tag with type="image"
 */
function submit_image_tag($source, $options = array())
{
  if (!isset($options['alt']))
  {
    $path_pos = strrpos($source, '/');
    $dot_pos = strrpos($source, '.');
    $begin = $path_pos ? $path_pos + 1 : 0;
    $nb_str = ($dot_pos ? $dot_pos : strlen($source)) - $begin;
    $options['alt'] = ucfirst(substr($source, $begin, $nb_str));
  }

  return tag('input', array_merge(array('type' => 'image', 'name' => 'commit', 'src' => image_path($source)), _convert_options_to_javascript(_convert_options($options))));
}

/**
 * Returns a <label> tag with <i>$label</i> for the specified <i>$id</i> parameter.
 *
 * @param string $id      id
 * @param string $label   label or title
 * @param array  $options additional HTML compliant <label> tag parameters
 *
 * @return string <label> tag with <i>$label</i> for the specified <i>$id</i> parameter.
 */
function label_for($id, $label, $options = array())
{
  $options = _parse_attributes($options);

  if (is_object($label) && method_exists($label, '__toString'))
  {
    $label = $label->__toString();
  }

  return content_tag('label', $label, array_merge(array('for' => get_id_from_name($id, null)), $options));
}

function _convert_include_custom_for_select($options, &$select_options)
{
  if (_get_option($options, 'include_blank'))
  {
    $select_options[''] = '';
  }
  else if ($include_custom = _get_option($options, 'include_custom'))
  {
    $select_options[''] = $include_custom;
  }
}
