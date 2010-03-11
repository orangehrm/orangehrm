<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(dirname(__FILE__).'/../../../../../../test/bootstrap/unit.php');
require_once(dirname(__FILE__).'/../../../lib/util/sfFillInForm.class.php');

$t = new lime_test(74, new lime_output_color());

$html = <<<EOF
<html>
<body>
  <form name="form1" action="/go" method="POST">
    <input type="hidden" name="hidden_input" value="1" />
    <input type="text" name="empty_input_text" value="" />
    <input type="text" name="input_text" value="default_value" />
    <input type="checkbox" name="input_checkbox" value="1" checked="checked" />
    <input type="checkbox" name="input_checkbox_not_checked" value="1" />
    <input type="checkbox" name="input_checkbox_multiple[]" value="1" checked="checked" />
    <input type="checkbox" name="input_checkbox_multiple[]" value="2" />
    <input type="radio" name="input_radio" value="1" checked="checked" />
    <input type="password" name="password" value="" />
    <textarea name="textarea">content</textarea>
    <select name="select">
      <option value="first">first</option>
      <option value="selected" selected="selected">selected</option>
      <option value="last">last</option>
    </select>
    <select name="select_multiple" multiple="multiple">
      <option value="first">first</option>
      <option value="selected" selected="selected">selected</option>
      <option value="last" selected="selected">last</option>
    </select>
    <input name="article[title]" value="title"/>
    <select name="article[category]" multiple="multiple">
      <option value="1">1</option>
      <option value="2" selected="selected">2</option>
      <option value="3" selected="selected">3</option>
    </select>
    <input name="article[or][much][longer]" value="very long!"/>
    <input name="article[description][]" value="default description" />
    <input name="article[description][]" value="default description2" />
    <input name="multiple_text[]" value="text1"/>
    <input name="multiple_text[]" value="text2"/>
    <textarea name="multiple_textarea[]">area1</textarea>
    <textarea name="multiple_textarea[]">area2</textarea>
    <select name="articles[]" multiple="multiple">
      <option value="1">1</option>
      <option value="2" selected="selected">2</option>
      <option value="3" selected="selected">3</option>
    </select>
    <select name="articles[]" multiple="multiple">
      <option value="1" selected="selected">1</option>
      <option value="2">2</option>
      <option value="3" selected="selected">3</option>
    </select>
    <input type="submit" name="submit" value="submit" />
  </form>

  <form name="foo">
    <input type="text" name="foo" value="bar" />
  </form>

  <form id="bar">
    <input type="text" name="bar" value="foo" />
  </form>
</body>
</html>
EOF;

$dom = new DomDocument('1.0', 'UTF-8');
$dom->loadHTML($html);

// ->fillInDom()
$t->diag('->fillInDom()');
$f = new sfFillInForm();

// default values
$xml = simplexml_import_dom($f->fillInDom(clone $dom, null, null, array()));
$t->is(get_input_value($xml, 'hidden_input'), '1', '->fillInDom() preserves default values for hidden input');
$t->is(get_input_value($xml, 'input_text'), 'default_value', '->fillInDom() preserves default values for text input');
$t->is(get_input_value($xml, 'empty_input_text'), '', '->fillInDom() preserves default values for text input');
$t->is(get_input_value($xml, 'password'), '', '->fillInDom() preserves default values for password input');
$t->is(get_input_value($xml, 'input_checkbox', 'checked'), 'checked', '->fillInDom() preserves default values for checkbox');
$t->is(get_input_value($xml, 'input_checkbox_not_checked', 'checked'), '', '->fillInDom() preserves default values for checkbox');
$t->is(get_input_value($xml, 'input_radio', 'checked'), 'checked', '->fillInDom() preserves default values for radio');
$t->is(get_input_value($xml, 'input_checkbox_multiple[]', 'checked'), array('checked', null), '->fillInDom() preserves default values for multiple checkboxes');
$t->is($xml->xpath('//form[@name="form1"]/textarea[@name="textarea"]'), array('content'), '->fillInDom() preserves default values for textarea');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="select"]/option[@selected="selected"]'), array('selected'), '->fillInDom() preserves default values for select');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="select_multiple"]/option[@selected="selected"]'), array('selected', 'last'), '->fillInDom() preserves default values for multiple select');
$t->is(get_input_value($xml, 'article[title]'), 'title', '->fillInDom() preserves default values for text input');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="article[category]"]/option[@selected="selected"]'), array(2, 3), '->fillInDom() preserves default values for select');
$t->is(get_input_value($xml, 'multiple_text[]'), array('text1', 'text2'), '->fillInDom() preserves default values for multiple text inputs');
$t->is($xml->xpath('//form[@name="form1"]/textarea[@name="multiple_textarea[]"]'), array('area1', 'area2'), '->fillInDom() preserves default values for multiple textareas');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="articles[]"][1]/option[@selected="selected"]'), array(2, 3), '->fillInDom() preserves default values for multiple select');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="articles[]"][2]/option[@selected="selected"]'), array(1, 3), '->fillInDom() preserves default values for multiple select');
$t->is(get_input_value($xml, 'article[description][]'), array('default description', 'default description2'), '->fillInDom() preserves default values for long multiple text inputs (article[description][])');

// check form selection by name
$xml = simplexml_import_dom($f->fillInDom(clone $dom, 'foo', null, array()));
$t->is(get_input_value($xml, 'foo'), 'bar', '->fillInDom() takes a "name" attribute parameter as its second argument');

try
{
  $xml = simplexml_import_dom($f->fillInDom(clone $dom, 'foobar', null, array()));
  $t->fail('->fillInDom() throws a sfException if the form is not found');
}
catch (sfException $e)
{
  $t->pass('->fillInDom() throws a sfException if the form is not found');
}

// check form selection by id
$xml = simplexml_import_dom($f->fillInDom(clone $dom, null, 'bar', array()));
$t->is(get_input_value($xml, 'bar'), 'foo', '->fillInDom() takes an "id" attribute parameter as its third argument');

try
{
  $xml = simplexml_import_dom($f->fillInDom(clone $dom, null, 'foobar', array()));
  $t->fail('->fillInDom() throws a sfException if the form is not found');
}
catch (sfException $e)
{
  $t->pass('->fillInDom() throws a sfException if the form is not found');
}

// test with article[title]
$values = array(
  'article' => array(
    'title'    => 'my article title',
    'category' => array(1, 2),
  ),
);
$f = new sfFillInForm();
$xml = simplexml_import_dom($f->fillInDom(clone $dom, null, null, $values));
$t->is(get_input_value($xml, 'article[title]'), 'my article title', '->fillInDom() fills in values for article[title] fields');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="article[category]"]/option[@selected="selected"]'), array(1, 2), '->fillInDom() fills in values for article[title] fields');

$values = array(
  'hidden_input' => 2,
  'empty_input_text' => 'input text',
  'input_text' => 'my input text',
  'input_checkbox' => false,
  'input_checkbox_not_checked' => true,
  'input_checkbox_multiple[]' => array(2),
  'password' => 'mypassword',
  'select' => 'first',
  'select_multiple' => array('first', 'last'),
  'textarea' => 'my content',
  'article' => array(
    'title' => 'my article title',
    'category' => array(1, 2),
    'description' => array('new description', 'new description2'),
  ),
  'multiple_text[]' => array('m1', 'm2'),
  'multiple_textarea[]' => array('a1', 'a2'),
  'articles[]' => array(array(1, 2),array(2, 3)),
);

$f = new sfFillInForm();
$xml = simplexml_import_dom($f->fillInDom(clone $dom, null, null, $values));
$t->is(get_input_value($xml, 'hidden_input'), '2', '->fillInDom() fills in values for hidden input');
$t->is(get_input_value($xml, 'input_text'), 'my input text', '->fillInDom() fills in values for text input');
$t->is(get_input_value($xml, 'empty_input_text'), 'input text', '->fillInDom() fills in values for text input');
$t->is(get_input_value($xml, 'password'), 'mypassword', '->fillInDom() fills in values for password input');
$t->is(get_input_value($xml, 'input_checkbox', 'checked'), '', '->fillInDom() fills in values for checkbox');
$t->is(get_input_value($xml, 'input_checkbox_not_checked', 'checked'), 'checked', '->fillInDom() fills in values for checkbox');
$t->is(get_input_value($xml, 'input_checkbox_multiple[]', 'checked'), array(null, 'checked'), '->fillInDom() fills in values for multiple checkboxes');
$t->is($xml->xpath('//form[@name="form1"]/textarea[@name="textarea"]'), array('my content'), '->fillInDom() fills in values for textarea');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="select"]/option[@selected="selected"]'), array('first'), '->fillInDom() fills in values for select');
$t->is(get_input_value($xml, 'article[description][]'), array('new description', 'new description2'), '->fillInDom() fills in values for multiple text inputs in second dimension array');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="select_multiple"]/option[@selected="selected"]'), array('first', 'last'), '->fillInDom() fills in values for multiple select');
$t->is(get_input_value($xml, 'article[title]'), 'my article title', '->fillInDom() fills in values for text input');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="article[category]"]/option[@selected="selected"]'), array(1, 2), '->fillInDom() fills in values for select');
$t->is(get_input_value($xml, 'multiple_text[]'), array('m1', 'm2'), '->fillInDom() fills in values for multiple text inputs');
$t->is($xml->xpath('//form[@name="form1"]/textarea[@name="multiple_textarea[]"]'), array('a1', 'a2'), '->fillInDom() fills in values for multiple textareas');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="articles[]"][1]/option[@selected="selected"]'), array(1, 2), '->fillInDom() fills in values for multiple select');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="articles[]"][2]/option[@selected="selected"]'), array(2, 3), '->fillInDom() fills in values for multiple select');

// ->setTypes()
$t->diag('->setTypes()');
$f = new sfFillInForm();
$f->setTypes(array('text', 'checkbox', 'radio'));
$xml = simplexml_import_dom($f->fillInDom(clone $dom, null, null, $values));
$t->is(get_input_value($xml, 'hidden_input'), '1', '->setTypes() allows to prevent some input fields from being filled');
$t->is(get_input_value($xml, 'password'), '', '->setTypes() allows to prevent some input fields from being filled');
$t->is(get_input_value($xml, 'input_text'), 'my input text', '->setTypes() allows to prevent some input fields from being filled');

// ->setSkipFields()
$t->diag('->setSkipFields()');
$f = new sfFillInForm();
$f->setSkipFields(array('input_text', 'input_checkbox', 'textarea', 'select_multiple', 'article[title]'));
$xml = simplexml_import_dom($f->fillInDom(clone $dom, null, null, $values));
$t->is(get_input_value($xml, 'hidden_input'), '2', '->setSkipFields() allows to prevent some fields to be filled');
$t->is(get_input_value($xml, 'input_text'), 'default_value', '->setSkipFields() allows to prevent some fields to be filled');
$t->is(get_input_value($xml, 'empty_input_text'), 'input text', '->setSkipFields() allows to prevent some fields to be filled');
$t->is(get_input_value($xml, 'password'), 'mypassword', '->setSkipFields() allows to prevent some fields to be filled');
$t->is(get_input_value($xml, 'input_checkbox', 'checked'), 'checked', '->setSkipFields() allows to prevent some fields to be filled');
$t->is(get_input_value($xml, 'input_checkbox_not_checked', 'checked'), 'checked', '->setSkipFields() allows to prevent some fields to be filled');
$t->is($xml->xpath('//form[@name="form1"]/textarea[@name="textarea"]'), array('content'), '->setSkipFields() allows to prevent some fields to be filled');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="select"]/option[@selected="selected"]'), array('first'), '->setSkipFields() allows to prevent some fields to be filled');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="select_multiple"]/option[@selected="selected"]'), array('selected', 'last'), '->setSkipFields() allows to prevent some fields to be filled');
$t->is(get_input_value($xml, 'article[title]'), 'title', '->setSkipFields() allows to prevent some fields to be filled');
$t->is($xml->xpath('//form[@name="form1"]/select[@name="article[category]"]/option[@selected="selected"]'), array(1, 2), '->setSkipFields() allows to prevent some fields to be filled');

// ->addconverter()
$t->diag('->addConverter()');
$f = new sfFillInForm();
$f->addConverter('str_rot13', array('input_text', 'textarea'));
$xml = simplexml_import_dom($f->fillInDom(clone $dom, null, null, $values));
$t->is(get_input_value($xml, 'input_text'), str_rot13('my input text'), '->addConverter() register a callable to be called for each value');
$t->is(get_input_value($xml, 'empty_input_text'), 'input text', '->addConverter() register a callable to be called for each value');
$t->is(get_input_value($xml, 'input_checkbox', 'checked'), '', '->addConverter() register a callable to be called for each value');
$t->is($xml->xpath('//form[@name="form1"]/textarea[@name="textarea"]'), array(str_rot13('my content')), '->addConverter() register a callable to be called for each value');

function get_input_value($xml, $name, $attribute = 'value', $form = null)
{
  $value = "";

  $xpath = ($form ? '//form[@name="'.$form.'"]' : '//form').sprintf('/input[@name="%s"]', $name);

  $values = $xml->xpath($xpath);

  if (count($values) > 1 || substr($name,-2)=='[]')
  {
    foreach($values as $val)
    {
      $value[] = $val[$attribute];
    }
  }
  else
  {
    $value = (string) $values[0][$attribute];
  }

  return $value;
}

// ->fillInXml()
$t->diag('->fillInXml()');
$f = new sfFillInForm();

$xml = <<<EOF
<html>
  <body>
    <form action="#" method="post" name="form">
      <input type="text" name="foo" />
    </form>
  </body>
</html>
EOF;
$xml = $f->fillInXml($xml, 'form', null, array('foo' => 'bar'));
$t->like($xml, '#<input type="text" name="foo" value="bar"\s*/>#', '->fillInXml() outputs valid XML');

$xml = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <body>
    <form action="#" method="post" name="form">
      <input type="text" name="foo" />
      <select name="select">
        <option value="first">first</option>
        <option value="selected" selected="selected">selected</option>
        <option value="last">last</option>
      </select>
    </form>
  </body>
</html>
EOF;
$xml = $f->fillInXml($xml, 'form', null, array('foo' => 'bar'));
$t->like($xml, '#<input type="text" name="foo" value="bar"\s*/>#', '->fillInXml() outputs valid XML');
$t->like($xml, '#<option value="selected" selected="selected">#', '->fillInXml() outputs valid XML');
$t->like($xml, '#<\?xml version="1.0"\?>#',  '->fillInXml() outputs XML prolog');

// ->fillInXhtml()
$xml = $f->fillInXhtml($xml, 'form', null, array('foo' => 'bar'));
$t->like($xml, '#<input type="text" name="foo" value="bar"\s*/>#', '->fillInXhml() outputs valid XML');
$t->like($xml, '#<option value="selected" selected="selected">#', '->fillInXhml() outputs valid XML');
$t->unlike($xml, '#<\?xml version="1.0"\?>#',  '->fillInXhtml() does not output XML prolog');

// ->fillInHtml()
$t->diag('->fillInHtml()');
$f = new sfFillInForm();

$xml = <<<EOF
<html>
  <body>
    <form action="#" method="post" name="form">
      <input type="text" name="foo">
      <select name="select">
        <option value="first">first</option>
        <option value="selected" selected="selected">selected</option>
        <option value="last">last</option>
      </select>
    </form>
  </body>
</html>
EOF;
$xml = $f->fillInHtml($xml, 'form', null, array('foo' => 'bar'));
$t->like($xml, '#<input type="text" name="foo" value="bar">#', '->fillInHtml() outputs valid HTML');
$t->like($xml, '#<option value="selected" selected>#', '->fillInHtml() outputs valid HTML');

$xml = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
  <body>
    <form action="#" method="post" name="form">
      <input type="text" name="foo">
    </form>
  </body>
</html>
EOF;
$xml = $f->fillInHtml($xml, 'form', null, array('foo' => 'bar'));
$t->like($xml, '#<input type="text" name="foo" value="bar">#', '->fillInHtml() outputs valid HTML with doctype');
$t->unlike($xml, '#<head.*?>#', '->fillInHtml() outputs valid HTML doesnt add head when not in input');
$t->unlike($xml, '#<meta http-equiv="Content-Type" content="text/html; charset=utf-8">#', '->fillInHtml() outputs valid HTML doesnt add meta when not in input');

$xml = <<<EOF
<html>
  <head profile="http://gmpg.org/xfn/11">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  </head>
  <body>
    <form action="#" method="post" name="form">
      <input type="text" name="foo">
    </form>
  </body>
</html>
EOF;
$xml = $f->fillInHtml($xml, 'form', null, array('foo' => 'bar'));
$t->like($xml, '#<input type="text" name="foo" value="bar">#', '->fillInHtml() outputs valid HTML with head present');
$t->like($xml, '#<head.*?>#', '->fillInHtml() outputs valid HTML with head present');
$t->like($xml, '#<meta http-equiv="Content-Type" content="text/html; charset=utf-8">#', '->fillInHtml() outputs valid HTML with meta present');
