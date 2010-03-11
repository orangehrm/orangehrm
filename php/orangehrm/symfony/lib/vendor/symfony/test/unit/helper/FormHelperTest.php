<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../bootstrap/unit.php');
require_once($_test_dir.'/unit/sfContextMock.class.php');

class myController
{
  public function genUrl($parameters = array(), $absolute = false)
  {
    return 'module/action';
  }
}

class myUser
{
  public function getCulture()
  {
    return 'en';
  }
}

class myRequest
{
  public function getRelativeUrlRoot()
  {
    return '';
  }
}

class myResponse
{
  public function addJavascript()
  {
  }
}

class ProjectConfiguration extends sfProjectConfiguration
{
}

$configuration = new ProjectConfiguration(dirname(__FILE__).'/../../lib', new sfEventDispatcher());

$t = new lime_test(96, new lime_output_color());

$context = sfContext::getInstance(array(
  'controller' => 'myController',
  'user' => 'myUser',
  'request' => 'myRequest',
  'response' => 'myResponse',
));

require_once(dirname(__FILE__).'/../../../lib/helper/HelperHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/UrlHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/TagHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/FormHelper.php');
require_once(dirname(__FILE__).'/../../../lib/helper/AssetHelper.php');

// options_for_select()
$t->diag('options_for_select()');
$t->is(options_for_select(array('item1', 'item2', 'item3')), "<option value=\"0\">item1</option>\n<option value=\"1\">item2</option>\n<option value=\"2\">item3</option>\n", 'options_for_select() takes an array of options as its first argument');
$t->is(options_for_select(array(1 => 'item1', 2 => 'item2', 'foo' => 'item3')), "<option value=\"1\">item1</option>\n<option value=\"2\">item2</option>\n<option value=\"foo\">item3</option>\n", 'options_for_select() takes an array of options as its first argument');
$t->is(options_for_select(array('item1', 'item2', 'item3'), '0'), "<option value=\"0\" selected=\"selected\">item1</option>\n<option value=\"1\">item2</option>\n<option value=\"2\">item3</option>\n", 'options_for_select() takes the selected index as its second argument');
$t->is(options_for_select(array('item1', 'item2', 'item3'), '2'), "<option value=\"0\">item1</option>\n<option value=\"1\">item2</option>\n<option value=\"2\" selected=\"selected\">item3</option>\n", 'options_for_select() takes the selected index as its second argument');
$t->is(options_for_select(array('item1', 'item2', 'item3'), array('1', '2')), "<option value=\"0\">item1</option>\n<option value=\"1\" selected=\"selected\">item2</option>\n<option value=\"2\" selected=\"selected\">item3</option>\n", 'options_for_select() takes the selected index as its second argument');
$t->is(options_for_select(array('group1' => array('item1', 'item2'), 'bar' => 'item3')), "<optgroup label=\"group1\"><option value=\"0\">item1</option>\n<option value=\"1\">item2</option>\n</optgroup>\n<option value=\"bar\">item3</option>\n", 'options_for_select() can deal with optgroups');

// unit testing for #3923
$escaped = sfOutputEscaper::escape('htmlspecialchars', array('group1' => array('item1', 'item2'), 'bar' => 'item3'));
$t->is(options_for_select($escaped), "<optgroup label=\"group1\"><option value=\"0\">item1</option>\n<option value=\"1\">item2</option>\n</optgroup>\n<option value=\"bar\">item3</option>\n", 'options_for_select() can deal with optgroups of escaped arrays');

// options
$t->is(options_for_select(array('item1'), '', array('include_custom' => 'test')), "<option value=\"\">test</option>\n<option value=\"0\">item1</option>\n", 'options_for_select() can take an "include_custom" option');
$t->is(options_for_select(array('item1'), '', array('include_blank' => true)), "<option value=\"\"></option>\n<option value=\"0\">item1</option>\n", 'options_for_select() can take an "include_blank" option');

// form_tag()
$t->diag('form_tag()');
$t->is(form_tag(), '<form method="post" action="module/action">', 'form_tag() creates a form tag');

// options
$t->is(form_tag('', array('class' => 'foo')), '<form class="foo" method="post" action="module/action">', 'form_tag() takes an array of attribute options');
$t->is(form_tag('', array('method' => 'get')), '<form method="get" action="module/action">', 'form_tag() takes a "method" as an option');
$t->is(form_tag('', array('multipart' => true)), '<form method="post" enctype="multipart/form-data" action="module/action">', 'form_tag() takes a "multipart" boolean option');
$t->is(form_tag('', array('method' => 'put')), '<form method="post" action="module/action"><input type="hidden" name="sf_method" value="put" />', 'form_tag() creates a hidden field for methods different from GET or POST');

// select_tag()
$t->diag('select_tag()');
$t->is(select_tag('name'), '<select name="name" id="name"></select>', 'select_tag() takes a name as its first argument');
$option_for_select = options_for_select(array('item1'));
$t->is(select_tag('name', $option_for_select), '<select name="name" id="name">'.$option_for_select.'</select>', 'select_tag() takes an HTML string of options as its second argument');
$t->is(select_tag('name', array('item1')), '<select name="name" id="name">'.$option_for_select.'</select>', 'select_tag() takes an array of options as its second argument');

// options
$t->is(select_tag('name', $option_for_select, array('class' => 'foo')), '<select name="name" id="name" class="foo">'.$option_for_select.'</select>', 'select_tag() takes an array of attribute options as its third argument');
$t->is(select_tag('name', $option_for_select, array('multiple' => true)), '<select name="name[]" id="name" multiple="multiple">'.$option_for_select.'</select>', 'select_tag() takes a "multiple" boolean option');
$t->is(select_tag('name[]', $option_for_select, array('multiple' => true)), '<select name="name[]" id="name" multiple="multiple">'.$option_for_select.'</select>', 'select_tag() takes a "multiple" boolean option');
$t->is(select_tag('name', $option_for_select, array('multiple' => false)), '<select name="name" id="name">'.$option_for_select.'</select>', 'select_tag() takes a "multiple" boolean option');
$t->is(select_tag('name', $option_for_select, array('id' => 'bar')), '<select name="name" id="bar">'.$option_for_select.'</select>', 'select_tag() can take a "id" option');

// select_country_tag()
$t->diag('select_country_tag()');
$t->like(select_country_tag('name'), '/'.preg_quote('<select name="name" id="name">').'/', 'select_country_tag() takes a name as its first argument');
$t->cmp_ok(preg_match_all('/<option/', select_country_tag('name'), $matches), '>', 200, 'select_country_tag() takes a name as its first argument');
$t->like(select_country_tag('name', 'FR'), '/'.preg_quote('<option value="FR" selected="selected">').'/', 'select_country_tag() takes an ISO code for the selected country as its second argument');

// options
$t->like(select_country_tag('name', null, array('class' => 'foo')), '/'.preg_quote('<select name="name" id="name" class="foo">').'/', 'select_country_tag() takes an array of options as its third argument');
$t->is(preg_match_all('/<option/', select_country_tag('name', null, array('countries' => array('FR', 'GB'))), $matches), 2, 'select_country_tag() takes a "countries" option');

// select_language_tag()
$t->diag('select_language_tag()');
$t->like(select_language_tag('name'), '/'.preg_quote('<select name="name" id="name">').'/', 'select_language_tag() takes a name as its first argument');
$t->cmp_ok(preg_match_all('/<option/', select_language_tag('name'), $matches), '>', 200, 'select_language_tag() takes a name as its first argument');
$t->like(select_language_tag('name', 'fr'), '/'.preg_quote('<option value="fr" selected="selected">').'/', 'select_language_tag() takes an ISO code for the selected language as its second argument');

// option
$t->like(select_language_tag('name', null, array('class' => 'foo')), '/'.preg_quote('<select name="name" id="name" class="foo">').'/', 'select_language_tag() takes an array of options as its third argument');
$t->is(preg_match_all('/<option/', select_language_tag('name', null, array('languages' => array('fr', 'en'))), $matches), 2, 'select_language_tag() takes a "languages" option');

// select_currency_tag()
$t->diag('select_currency_tag()');
$t->like(select_currency_tag('name'), '/'.preg_quote('<select name="name" id="name">').'/', 'select_currency_tag() takes a name as its first argument');
$t->cmp_ok(preg_match_all('/<option/', select_currency_tag('name'), $matches), '>', 200, 'select_currency_tag() takes a name as its first argument');
$t->like(select_currency_tag('name', 'EUR'), '/'.preg_quote('<option value="EUR" selected="selected">').'/', 'select_currency_tag() takes the selected currency as its second argument');

// option
$t->like(select_currency_tag('name', null, array('class' => 'foo')), '/'.preg_quote('<select name="name" id="name" class="foo">').'/', 'select_currency_tag() takes an array of options as its third argument');
$t->is(preg_match_all('/<option/', select_currency_tag('name', null, array('currencies' => array('EUR', 'USD'))), $matches), 2, 'select_currency_tag() takes a "currencies" option');
$t->like(select_currency_tag('name', 'USD', array('currencies' => array('USD'), 'display' => 'symbol')), '/'.preg_quote('<option value="USD" selected="selected">US$<').'/' , 'select_currency_tag() takes a "display" option');
$t->like(select_currency_tag('name', 'USD', array('currencies' => array('USD'), 'display' => 'code')), '/'.preg_quote('<option value="USD" selected="selected">USD<').'/' , 'select_currency_tag() takes a "display" option');

// input_tag()
$t->diag('input_tag()');
$t->is(input_tag('name'), '<input type="text" name="name" id="name" value="" />', 'input_tag() takes a name as its first argument');
$t->is(input_tag('name', 'foo'), '<input type="text" name="name" id="name" value="foo" />', 'input_tag() takes a value as its second argument');

// options
$t->is(input_tag('name', null, array('class' => 'foo')), '<input type="text" name="name" id="name" value="" class="foo" />', 'input_tag() takes an array of attribute options as its third argument');
$t->is(input_tag('name', null, array('type' => 'password')), '<input type="password" name="name" id="name" value="" />', 'input_tag() can override the "type" attribute');
$t->is(input_tag('name', null, array('id' => 'foo')), '<input type="text" name="name" id="foo" value="" />', 'input_tag() can override the "id" attribute');

// input_hidden_tag()
$t->diag('input_hidden_tag()');
$t->is(input_hidden_tag('name'), '<input type="hidden" name="name" id="name" value="" />', 'input_hidden_tag() takes a name as its first argument');
$t->is(input_hidden_tag('name', 'foo'), '<input type="hidden" name="name" id="name" value="foo" />', 'input_hidden_tag() takes a value as its second argument');
$t->is(input_hidden_tag('name', null, array('class' => 'foo')), '<input type="hidden" name="name" id="name" value="" class="foo" />', 'input_hidden_tag() takes an array of attribute options as its third argument');

// input_file_tag()
$t->diag('input_file_tag()');
$t->is(input_file_tag('name'), '<input type="file" name="name" id="name" value="" />', 'input_file_tag() takes a name as its first argument');
$t->is(input_file_tag('name', array('class' => 'foo')), '<input type="file" name="name" id="name" value="" class="foo" />', 'input_hidden_tag() takes an array of attribute options as its second argument');

// input_password_tag()
$t->diag('input_password_tag()');
$t->is(input_password_tag('name'), '<input type="password" name="name" id="name" value="" />', 'input_password_tag() takes a name as its first argument');
$t->is(input_password_tag('name', 'foo'), '<input type="password" name="name" id="name" value="foo" />', 'input_password_tag() takes a value as its second argument');
$t->is(input_password_tag('name', null, array('class' => 'foo')), '<input type="password" name="name" id="name" value="" class="foo" />', 'input_password_tag() takes an array of attribute options as its third argument');

// textarea_tag()
$t->diag('textarea_tag()');
$t->is(textarea_tag('name'), '<textarea name="name" id="name"></textarea>', 'textarea_tag() takes a name as its first argument');
$t->is(textarea_tag('name', 'content'), '<textarea name="name" id="name">content</textarea>', 'textarea_tag() takes a value as its second argument');
$t->is(textarea_tag('name', '<p>foo</p>'), '<textarea name="name" id="name">&lt;p&gt;foo&lt;/p&gt;</textarea>', 'textarea_tag() escapes the content');
$t->is(textarea_tag('name', '&lt;p&gt;foo&lt;/p&gt;'), '<textarea name="name" id="name">&lt;p&gt;foo&lt;/p&gt;</textarea>', 'textarea_tag() does not escape an already escaped content');

// options
$t->is(textarea_tag('name', null, array('class' => 'foo')), '<textarea name="name" id="name" class="foo"></textarea>', 'textarea_tag() takes an array of attribute options as its third argument');
$t->is(textarea_tag('name', null, array('id' => 'foo')), '<textarea name="name" id="foo"></textarea>', 'textarea_tag() can override the "id" attribute');
$t->is(textarea_tag('name', null, array('size' => '5x20')), '<textarea name="name" id="name" rows="20" cols="5"></textarea>', 'textarea_tag() can take a "size" attribute');

require_once(sfConfig::get('sf_symfony_lib_dir').'/helper/sfRichTextEditor.class.php');
require_once(sfConfig::get('sf_symfony_lib_dir').'/helper/sfRichTextEditorTinyMCE.class.php');
sfConfig::set('sf_web_dir', dirname(__FILE__));
sfConfig::set('sf_rich_text_js_dir', 'fixtures');
$t->like(textarea_tag('name', 'content', array('rich' => 'TinyMCE')), '/tinyMCE\.init/', 'textarea_tag() can create a rich textarea tag based on tinyMCE');
$t->like(textarea_tag('name', 'content', array('rich' => true)), '/tinyMCE\.init/', 'textarea_tag() can create a rich textarea tag based on tinyMCE');
//regression for http://trac.symfony-project.com/ticket/3474
$t->like(textarea_tag('name[]', 'content', array('rich' => true)), '/elements: "name",/', 'textarea_tag() can create a rich textarea tag based on tinyMCE using correct id');
$t->like(textarea_tag('name[]', 'content', array('rich' => true, 'id' => 'test')), '/elements: "test",/', 'textarea_tag() can create a rich textarea tag based on tinyMCE using correct id');

class sfRichTextEditorSample extends sfRichTextEditor
{
  public function toHTML()
  {
    return 'fake editor';
  }
}
$t->like(textarea_tag('name', 'content', array('rich' => 'Sample')), '/fake editor/', 'textarea_tag() can create a rich textarea tag based on a custom editor');
sfConfig::set('sf_rich_text_editor_class', 'Sample');
$t->like(textarea_tag('name', 'content', array('rich' => true)), '/fake editor/', 'textarea_tag() can be configured to change the default editor by configuration');

class sfRichTextEditorSampleBis
{
}
try
{
  textarea_tag('name', 'content', array('rich' => 'SampleBis'));
  $t->fail('textarea_tag() custom editor must extends sfRichTextEditor');
}
catch (sfConfigurationException $e)
{
  $t->pass('textarea_tag() custom editor must extends sfRichTextEditor');
}

// checkbox_tag()
$t->diag('checkbox_tag()');
$t->is(checkbox_tag('name'), '<input type="checkbox" name="name" id="name" value="1" />', 'checkbox_tag() takes a name as its first argument');
$t->is(checkbox_tag('name', 'foo'), '<input type="checkbox" name="name" id="name" value="foo" />', 'checkbox_tag() takes a value as its second argument');
$t->is(checkbox_tag('name', null, true), '<input type="checkbox" name="name" id="name" value="" checked="checked" />', 'checkbox_tag() takes a boolean as its third argument');

// options
$t->is(checkbox_tag('name', null, false, array('class' => 'foo')), '<input type="checkbox" name="name" id="name" value="" class="foo" />', 'checkbox_tag() takes an array of attribute options as its fourth argument');
$t->is(checkbox_tag('name', null, false, array('id' => 'foo')), '<input type="checkbox" name="name" id="foo" value="" />', 'checkbox_tag() can override the "id" attribute');

// radiobutton_tag()
$t->diag('radiobutton_tag()');
$t->is(radiobutton_tag('name', 1), '<input type="radio" name="name" id="name_1" value="1" />', 'radiobutton_tag() takes a name as its first argument');
$t->is(radiobutton_tag('name', 2), '<input type="radio" name="name" id="name_2" value="2" />', 'radiobutton_tag() takes a value as its second argument');
$t->is(radiobutton_tag('name', null, true), '<input type="radio" name="name" id="name" value="" checked="checked" />', 'radiobutton_tag() takes a boolean as its third argument');

// options
$t->is(radiobutton_tag('name', null, false, array('class' => 'foo')), '<input type="radio" name="name" id="name" value="" class="foo" />', 'radiobutton_tag() takes an array of attribute options as its fourth argument');
$t->is(radiobutton_tag('name', null, false, array('id' => 'foo')), '<input type="radio" name="name" id="foo" value="" />', 'radiobutton_tag() can override the "id" attribute');

// input_date_range_tag()
$t->diag('input_date_range_tag()');
$t->unlike(input_date_range_tag('date', array('from' => time(), 'to' => time()), array('after' => 'foo')), '/after/', 'input_date_range_tag() output date fields for a date range');

// input_date_tag()
$t->diag('input_date_tag()');
$t->todo('input_date_tag()');

// submit_tag()
$t->diag('submit_tag()');
$t->is(submit_tag(), '<input type="submit" name="commit" value="Save changes" />', 'submit_tag() default value is "Save changes"');
$t->is(submit_tag("save"), '<input type="submit" name="commit" value="save" />', 'submit_tag() takes a value as its first argument');

// options
$t->is(submit_tag('save', array('class' => 'foo')), '<input type="submit" name="commit" value="save" class="foo" />', 'submit_tag() takes an array of attribute options as its second argument');
$t->is(submit_tag('save', array('name' => 'foo')), '<input type="submit" name="foo" value="save" />', 'submit_tag() can override the "name" attribute');

// reset_tag()
$t->diag('reset_tag()');
$t->is(reset_tag(), '<input type="reset" name="reset" value="Reset" />', 'reset_tag() default value is "Reset"');
$t->is(reset_tag("save"), '<input type="reset" name="reset" value="save" />', 'reset_tag() takes a value as its first argument');

// options
$t->is(reset_tag('save', array('class' => 'foo')), '<input type="reset" name="reset" value="save" class="foo" />', 'reset_tag() takes an array of attribute options as its second argument');
$t->is(reset_tag('save', array('name' => 'foo')), '<input type="reset" name="foo" value="save" />', 'reset_tag() can override the "name" attribute');

// submit_image_tag()
$t->diag('submit_image_tag()');
$t->is(submit_image_tag('submit'), '<input type="image" name="commit" src="/images/submit.png" alt="Submit" />', 'submit_image_tag() takes an image source as its first argument');
$t->is(submit_image_tag('/img/submit.gif'), '<input type="image" name="commit" src="/img/submit.gif" alt="Submit" />', 'submit_image_tag() takes an image source as its first argument');

// options
$t->is(submit_image_tag('submit', array('class' => 'foo')), '<input type="image" name="commit" src="/images/submit.png" class="foo" alt="Submit" />', 'submit_image_tag() takes an array of attribute options as its second argument');
$t->is(submit_image_tag('submit', array('alt' => 'foo')), '<input type="image" name="commit" src="/images/submit.png" alt="foo" />', 'reset_tag() can override the "alt" attribute');
$t->is(submit_image_tag('submit', array('name' => 'foo')), '<input type="image" name="foo" src="/images/submit.png" alt="Submit" />', 'reset_tag() can override the "name" attribute');

// label_for()
$t->diag('label_for()');
$t->todo('label_for()');

// get_id_from_name()
$t->diag('get_id_from_name()');
$t->is(get_id_from_name('foo'), 'foo', 'get_id_from_name() returns the id if there is no [] in the id');
$t->is(get_id_from_name('foo[]', 'name'), 'foo_name', 'get_id_from_name() removes all [] from ids');

// _convert_options()
$t->diag('_convert_options()');
$t->is(_convert_options(array('class' => 'foo', 'multiple' => true)), array('class' => 'foo', 'multiple' => 'multiple'), '_convert_options() converts some options for XHTML compliance');
$t->is(_convert_options(array('class' => 'foo', 'multiple' => false)), array('class' => 'foo'), '_convert_options() converts some options for XHTML compliance');
